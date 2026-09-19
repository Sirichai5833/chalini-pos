<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class OnlineUserController extends Controller
{
    /**
     * แสดงผู้ใช้งานที่ออนไลน์อยู่ในขณะนี้
     */
    public function index(Request $request)
    {
        $actor = $request->user();

        abort_unless(
            $actor && in_array(
                $actor->role,
                ['admin', 'manager', 'owner'],
                true
            ),
            403,
            'คุณไม่มีสิทธิ์ดูข้อมูลผู้ใช้งานออนไลน์'
        );

        $onlineMinutes = (int) $request->integer(
            'minutes',
            5
        );

        $onlineMinutes = max(
            1,
            min($onlineMinutes, 60)
        );

        $onlineSince = now()
            ->subMinutes($onlineMinutes)
            ->timestamp;

        /*
         * ตาราง sessions อาจมีหลาย session ต่อผู้ใช้หนึ่งคน
         * จึงเลือก session ล่าสุดของแต่ละ user_id
         */
        $latestSessions = DB::table('sessions')
            ->selectRaw(
                'user_id, MAX(last_activity) AS latest_activity'
            )
            ->whereNotNull('user_id')
            ->where(
                'last_activity',
                '>=',
                $onlineSince
            )
            ->groupBy('user_id');

        $onlineUsers = DB::table('sessions')
            ->joinSub(
                $latestSessions,
                'latest_sessions',
                function ($join) {
                    $join
                        ->on(
                            'latest_sessions.user_id',
                            '=',
                            'sessions.user_id'
                        )
                        ->on(
                            'latest_sessions.latest_activity',
                            '=',
                            'sessions.last_activity'
                        );
                }
            )
            ->join(
                'users',
                'users.id',
                '=',
                'sessions.user_id'
            )
            ->leftJoin(
                'shifts',
                function ($join) {
                    $join
                        ->on(
                            'shifts.user_id',
                            '=',
                            'users.id'
                        )
                        ->where(
                            'shifts.status',
                            '=',
                            'open'
                        );
                }
            )
            ->select([
                'users.id',
                'users.name',
                'users.first_name',
                'users.last_name',
                'users.email',
                'users.role',
                'users.image',
                'sessions.id as session_id',
                'sessions.ip_address',
                'sessions.user_agent',
                'sessions.last_activity',
                'shifts.id as open_shift_id',
                'shifts.opened_at',
            ])
            ->orderByDesc(
                'sessions.last_activity'
            )
            ->get()
            ->unique('id')
            ->values()
            ->map(function ($user) {
                $user->display_name =
                    $this->displayName($user);

                $user->last_seen_at =
                    Carbon::createFromTimestamp(
                        (int) $user->last_activity
                    );

                $user->device =
                    $this->detectDevice(
                        $user->user_agent
                    );

                $user->browser =
                    $this->detectBrowser(
                        $user->user_agent
                    );

                $user->role_label =
                    $this->roleLabel(
                        $user->role
                    );

                return $user;
            });

        $summary = [
            'total_online' =>
                $onlineUsers->count(),

            'open_shift_count' =>
                $onlineUsers
                    ->whereNotNull('open_shift_id')
                    ->count(),

            'staff_count' =>
                $onlineUsers
                    ->where('role', 'staff')
                    ->count(),

            'admin_count' =>
                $onlineUsers
                    ->whereIn(
                        'role',
                        ['admin', 'manager', 'owner']
                    )
                    ->count(),
        ];

        return view(
            'admin.online-users.index',
            compact(
                'onlineUsers',
                'summary',
                'onlineMinutes'
            )
        );
    }

    private function displayName(object $user): string
    {
        $fullName = trim(
            ((string) ($user->first_name ?? ''))
            . ' '
            . ((string) ($user->last_name ?? ''))
        );

        return $fullName !== ''
            ? $fullName
            : ((string) ($user->name ?? '-'));
    }

    private function detectDevice(
        ?string $userAgent
    ): string {
        $agent = strtolower(
            (string) $userAgent
        );

        if ($agent === '') {
            return 'ไม่ทราบอุปกรณ์';
        }

        if (
            str_contains($agent, 'iphone')
            || str_contains($agent, 'ipad')
            || str_contains($agent, 'android')
            || str_contains($agent, 'mobile')
        ) {
            return 'โทรศัพท์ / แท็บเล็ต';
        }

        return 'คอมพิวเตอร์';
    }

    private function detectBrowser(
        ?string $userAgent
    ): string {
        $agent = strtolower(
            (string) $userAgent
        );

        return match (true) {
            str_contains($agent, 'edg/') =>
                'Microsoft Edge',

            str_contains($agent, 'opr/')
            || str_contains($agent, 'opera') =>
                'Opera',

            str_contains($agent, 'chrome/')
            && !str_contains($agent, 'edg/') =>
                'Google Chrome',

            str_contains($agent, 'firefox/') =>
                'Mozilla Firefox',

            str_contains($agent, 'safari/')
            && !str_contains($agent, 'chrome/') =>
                'Safari',

            default =>
                'ไม่ทราบเบราว์เซอร์',
        };
    }

    private function roleLabel(
        ?string $role
    ): string {
        return match ($role) {
            'owner' => 'เจ้าของร้าน',
            'admin' => 'ผู้ดูแลระบบ',
            'manager' => 'ผู้จัดการ',
            'staff' => 'พนักงาน',
            default => $role ?: 'ไม่ระบุ',
        };
    }
}
