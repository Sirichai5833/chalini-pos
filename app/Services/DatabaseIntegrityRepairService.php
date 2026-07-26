<?php

namespace App\Services;

use App\Services\DatabaseIntegrityRepair\MovementIntegrityInspector;
use App\Services\DatabaseIntegrityRepair\RoleIntegrityInspector;
use App\Services\DatabaseIntegrityRepair\ShiftIntegrityRepairer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class DatabaseIntegrityRepairService
{
    public const SCOPES = ['shifts', 'roles', 'movements'];

    public function __construct(
        private readonly DatabaseIntegrityAuditor $auditor,
        private readonly ShiftIntegrityRepairer $shiftRepairer,
        private readonly RoleIntegrityInspector $roleInspector,
        private readonly MovementIntegrityInspector $movementInspector,
    ) {
    }

    public function plan(array $scopes): array
    {
        $items = [];

        foreach ($scopes as $scope) {
            $items = [
                ...$items,
                ...match ($scope) {
                    'shifts' => $this->shiftRepairer->inspect(),
                    'roles' => $this->roleInspector->inspect(),
                    'movements' => $this->movementInspector->inspect(),
                },
            ];
        }

        $audit = $this->auditor->audit(1);

        return [
            'summary' => [
                'mode' => 'dry-run',
                'scopes' => array_values($scopes),
                'planned' => collect($items)
                    ->where('status', 'planned')
                    ->count(),
                'manual_only' => collect($items)
                    ->where('status', 'manual-only')
                    ->count(),
                'applied' => 0,
                'critical_before' =>
                    $audit['summary']['critical_issues'],
                'critical_after' => null,
            ],
            'items' => $items,
        ];
    }

    public function apply(array $plan): array
    {
        $criticalBefore = (int) $plan['summary']['critical_before'];

        $result = DB::transaction(function () use (
            $plan,
            $criticalBefore,
        ) {
            $applied = [];
            $plannedItems = collect($plan['items'])
                ->where('status', 'planned')
                ->sortBy('key')
                ->values();

            foreach ($plannedItems as $item) {
                if ($item['scope'] !== 'shifts') {
                    continue;
                }

                $applied[] = $this->shiftRepairer->apply($item);
            }

            $auditAfter = $this->auditor->audit(1);
            $criticalAfter =
                (int) $auditAfter['summary']['critical_issues'];

            if ($criticalAfter > $criticalBefore) {
                throw new RuntimeException(
                    'Repair increased the critical issue count; all changes were rolled back.'
                );
            }

            return [
                'applied' => $applied,
                'critical_after' => $criticalAfter,
            ];
        });

        $appliedByKey = collect($result['applied'])->keyBy('key');
        $items = collect($plan['items'])->map(function ($item) use (
            $appliedByKey,
        ) {
            $applied = $appliedByKey->get($item['key']);

            if (!$applied) {
                return $item;
            }

            $item['status'] = 'applied';
            $item['applied_before'] = $applied['before'];
            $item['applied_after'] = $applied['after'];

            return $item;
        })->all();
        $report = [
            'summary' => [
                ...$plan['summary'],
                'mode' => 'apply',
                'applied' => count($result['applied']),
                'critical_after' => $result['critical_after'],
            ],
            'items' => $items,
        ];

        foreach ($result['applied'] as $applied) {
            Log::info('database_integrity_repair_applied', [
                'repair_key' => $applied['key'],
                'scope' => $applied['scope'],
                'before' => [
                    'shifts' => $this->loggableShiftStates(
                        $applied['before']['shifts'] ?? []
                    ),
                ],
                'after' => [
                    'shifts' => $this->loggableShiftStates(
                        $applied['after']['shifts'] ?? []
                    ),
                ],
                'critical_before' => $criticalBefore,
                'critical_after' => $result['critical_after'],
            ]);
        }

        return $report;
    }

    private function loggableShiftStates(array $shifts): array
    {
        return collect($shifts)->map(fn ($shift) => [
            'id' => $shift['id'],
            'status' => $shift['status'],
            'closed_at' => $shift['closed_at'],
            'updated_at' => $shift['updated_at'],
        ])->all();
    }
}
