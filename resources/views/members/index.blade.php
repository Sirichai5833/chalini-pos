@extends('layouts.layout')

@php
    use App\Models\Setting;

    $totalFloors = Setting::get('floors', 5);
    $roomsPerFloor = Setting::get('roomsPerFloor', []);

    $totalRooms = 0;

    for ($i = 0; $i < $totalFloors; $i++) {
        $totalRooms += $roomsPerFloor[$i] ?? 24;
    }

    $occupiedRooms = $members->filter(fn($member) => $member !== null)->count();
    $availableRooms = max(0, $totalRooms - $occupiedRooms);
@endphp

@section('flash-messages')
    @include('layouts.partials.flash-messages', [
        'successTitle' => 'สำเร็จ!',
        'errorTitle' => 'เกิดข้อผิดพลาด',
        'confirmButtonText' => 'ตกลง',
    ])
@endsection

@push('styles')
    <style>
        .member-page {
            --yellow: #ffc107;
            --dark: #171717;
            --dark-soft: #202020;
            --border: #383838;
            --text: #f5f5f5;
            --muted: #aaa;
            color: var(--text);
        }

        .member-page .page-container {
            max-width: 1450px;
            margin: auto;
        }

        /* ส่วนหัว */
        .member-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 16px;
            padding: 22px;
            margin-bottom: 20px;
            border: 1px solid var(--border);
            border-left: 5px solid var(--yellow);
            border-radius: 14px;
            background: var(--dark);
        }

        .member-title {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .member-title-icon {
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            background: rgba(255, 193, 7, .13);
            color: var(--yellow);
            font-size: 24px;
        }

        .member-title h2 {
            margin: 0;
            color: #fff;
            font-size: 1.35rem;
            font-weight: 800;
        }

        .member-title p {
            margin: 5px 0 0;
            color: var(--muted);
            font-size: .88rem;
        }

        .member-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .member-buttons .btn {
            min-height: 42px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            border-radius: 9px;
            font-weight: 700;
        }

        .btn-add {
            border: 1px solid var(--yellow);
            background: var(--yellow);
            color: #111;
        }

        .btn-add:hover {
            background: #e5ac00;
            color: #111;
        }

        .btn-reset {
            border: 1px solid #555;
            background: transparent;
            color: #ddd;
        }

        .btn-reset:hover {
            border-color: var(--yellow);
            color: var(--yellow);
        }

        /* กล่องสรุปข้อมูล */
        .summary-card {
            height: 100%;
            padding: 17px;
            border: 1px solid var(--border);
            border-radius: 12px;
            background: var(--dark);
        }

        .summary-card .summary-label {
            color: var(--muted);
            font-size: .85rem;
        }

        .summary-card .summary-value {
            margin-top: 5px;
            color: #fff;
            font-size: 1.6rem;
            font-weight: 800;
        }

        .summary-card .summary-icon {
            width: 43px;
            height: 43px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            background: rgba(255, 193, 7, .12);
            color: var(--yellow);
            font-size: 20px;
        }

        /* ค้นหาและตัวกรอง */
        .filter-panel {
            padding: 18px;
            margin: 22px 0;
            border: 1px solid var(--border);
            border-radius: 12px;
            background: var(--dark);
        }

        .filter-panel label {
            margin-bottom: 7px;
            color: #ddd;
            font-size: .85rem;
            font-weight: 700;
        }

        .filter-control {
            min-height: 44px;
            border: 1px solid #484848;
            border-radius: 9px;
            background: #101010;
            color: #fff;
        }

        .filter-control:focus {
            border-color: var(--yellow);
            background: #101010;
            color: #fff;
            box-shadow: 0 0 0 3px rgba(255, 193, 7, .12);
        }

        .filter-control option {
            background: #171717;
            color: #fff;
        }

        /* หัวข้อแต่ละชั้น */
        .floor-section {
            margin-top: 25px;
        }

        .floor-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
            padding: 12px 15px;
            margin-bottom: 14px;
            border-radius: 9px;
            background: #202020;
            border-left: 4px solid var(--yellow);
        }

        .floor-title {
            margin: 0;
            color: #fff;
            font-size: 1.05rem;
            font-weight: 800;
        }

        .floor-title i {
            margin-right: 7px;
            color: var(--yellow);
        }

        .floor-count {
            color: var(--muted);
            font-size: .82rem;
        }

        /* การ์ดห้อง */
        .room-card {
            height: 100%;
            border: 1px solid var(--border);
            border-radius: 11px;
            background: var(--dark);
            color: var(--text);
            transition: .2s;
        }

        .room-card:hover {
            border-color: var(--yellow);
            transform: translateY(-3px);
        }

        .room-card .card-body {
            padding: 15px;
        }

        .room-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 8px;
            padding-bottom: 12px;
            margin-bottom: 13px;
            border-bottom: 1px solid var(--border);
        }

        .room-number {
            margin: 0;
            color: #fff;
            font-size: 1rem;
            font-weight: 800;
        }

        .room-number i {
            margin-right: 5px;
            color: var(--yellow);
        }

        .room-status {
            padding: 5px 9px;
            border-radius: 20px;
            font-size: .72rem;
            font-weight: 700;
        }

        .status-occupied {
            background: rgba(255, 193, 7, .13);
            color: var(--yellow);
        }

        .status-available {
            background: #303030;
            color: #aaa;
        }

        /* ข้อมูลสมาชิก */
        .member-info {
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 0;
            min-height: 55px;
        }

        .member-avatar {
            width: 45px;
            height: 45px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            background: var(--yellow);
            color: #111;
            font-size: 1.1rem;
            font-weight: 800;
        }

        .member-detail {
            min-width: 0;
        }

        .member-name {
            overflow: hidden;
            margin: 0;
            color: #fff;
            font-size: .9rem;
            font-weight: 700;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .member-email {
            overflow: hidden;
            margin-top: 4px;
            color: var(--muted);
            font-size: .76rem;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .empty-room {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 55px;
            color: #777;
            font-size: .85rem;
        }

        /* ปุ่มจัดการ */
        .room-actions {
            display: flex;
            gap: 7px;
            margin-top: 15px;
        }

        .room-action-btn {
            min-height: 36px;
            flex: 1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            padding: 6px;
            border-radius: 8px;
            font-size: .78rem;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
        }

        .btn-view {
            border: 1px solid var(--yellow);
            background: rgba(255, 193, 7, .08);
            color: var(--yellow);
        }

        .btn-view:hover {
            background: var(--yellow);
            color: #111;
        }

        .btn-edit {
            border: 1px solid #666;
            background: transparent;
            color: #ddd;
        }

        .btn-edit:hover {
            background: #fff;
            color: #111;
        }

        .btn-delete {
            border: 1px solid #dc3545;
            background: rgba(220, 53, 69, .08);
            color: #ff8792;
        }

        .btn-delete:hover {
            background: #dc3545;
            color: #fff;
        }

        /* ห้องพนักงาน */
        .staff-section {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid var(--border);
        }

        .staff-card {
            max-width: 380px;
        }

        .no-result {
            display: none;
            padding: 35px 15px;
            margin-top: 20px;
            border: 1px dashed #555;
            border-radius: 12px;
            color: var(--muted);
            text-align: center;
        }

        @media (max-width: 575px) {
            .member-header {
                padding: 15px;
            }

            .member-title {
                align-items: flex-start;
            }

            .member-title h2 {
                font-size: 1.1rem;
            }

            .member-buttons {
                width: 100%;
            }

            .member-buttons .btn {
                flex: 1;
            }

            .room-actions {
                gap: 5px;
            }

            .room-action-btn {
                font-size: .73rem;
            }
        }
    </style>
@endpush

@section('content')
    <div class="member-page py-4">
        <div class="container-fluid">
            <div class="page-container">

                {{-- หัวข้อหน้า --}}
                <div class="member-header">
                    <div class="member-title">
                        <div class="member-title-icon">
                            <i class="bi bi-building"></i>
                        </div>

                        <div>
                            <h2>รายชื่อสมาชิกตามห้อง</h2>
                            <p>ตรวจสอบห้องพักและจัดการข้อมูลสมาชิก</p>
                        </div>
                    </div>

                    <div class="member-buttons">
                        <a href="{{ route('members.create') }}" class="btn btn-add">
                            <i class="bi bi-person-plus-fill"></i>
                            เพิ่มสมาชิก
                        </a>

                        <button type="button" class="btn btn-reset" id="clearMemberSearch">
                            <i class="bi bi-arrow-clockwise"></i>
                            ล้างการค้นหา
                        </button>
                    </div>
                </div>

                {{-- สรุปข้อมูล --}}
                <div class="row g-3">
                    <div class="col-6 col-lg-3">
                        <div class="summary-card">
                            <div class="d-flex align-items-center gap-3">
                                <div class="summary-icon">
                                    <i class="bi bi-buildings"></i>
                                </div>
                                <div>
                                    <div class="summary-label">จำนวนชั้น</div>
                                    <div class="summary-value">{{ number_format($totalFloors) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-6 col-lg-3">
                        <div class="summary-card">
                            <div class="d-flex align-items-center gap-3">
                                <div class="summary-icon">
                                    <i class="bi bi-door-open"></i>
                                </div>
                                <div>
                                    <div class="summary-label">ห้องทั้งหมด</div>
                                    <div class="summary-value">{{ number_format($totalRooms) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-6 col-lg-3">
                        <div class="summary-card">
                            <div class="d-flex align-items-center gap-3">
                                <div class="summary-icon">
                                    <i class="bi bi-people"></i>
                                </div>
                                <div>
                                    <div class="summary-label">มีสมาชิก</div>
                                    <div class="summary-value">{{ number_format($occupiedRooms) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-6 col-lg-3">
                        <div class="summary-card">
                            <div class="d-flex align-items-center gap-3">
                                <div class="summary-icon">
                                    <i class="bi bi-check-circle"></i>
                                </div>
                                <div>
                                    <div class="summary-label">ห้องว่าง</div>
                                    <div class="summary-value">{{ number_format($availableRooms) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ค้นหาและกรอง --}}
                <div class="filter-panel">
                    <div class="row g-3">
                        <div class="col-lg-8">
                            <label for="memberRoomSearch">ค้นหาสมาชิกหรือหมายเลขห้อง</label>
                            <input type="search" id="memberRoomSearch" class="form-control filter-control"
                                placeholder="ค้นหาจากชื่อ อีเมล หรือเลขห้อง...">
                        </div>

                        <div class="col-lg-4">
                            <label for="roomStatusFilter">สถานะห้อง</label>
                            <select id="roomStatusFilter" class="form-select filter-control">
                                <option value="all">แสดงทุกห้อง</option>
                                <option value="occupied">ห้องที่มีสมาชิก</option>
                                <option value="available">ห้องว่าง</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- รายการห้องแต่ละชั้น --}}
                <div id="roomSections">
                    @for ($floor = 1; $floor <= $totalFloors; $floor++)
                        @php
                            $roomCount = $roomsPerFloor[$floor - 1] ?? 24;
                            $occupiedOnFloor = 0;

                            for ($i = 1; $i <= $roomCount; $i++) {
                                $number = $floor . str_pad($i, 2, '0', STR_PAD_LEFT);

                                if ($members->get($number)) {
                                    $occupiedOnFloor++;
                                }
                            }
                        @endphp

                        <section class="floor-section" data-floor-section>
                            <div class="floor-header">
                                <h3 class="floor-title">
                                    <i class="bi bi-layers-fill"></i>
                                    ชั้น {{ $floor }}
                                </h3>

                                <span class="floor-count">
                                    มีสมาชิก {{ $occupiedOnFloor }} / {{ $roomCount }} ห้อง
                                </span>
                            </div>

                            <div class="row row-cols-1 row-cols-sm-2 row-cols-xl-3 row-cols-xxl-4 g-3">
                                @for ($room = 1; $room <= $roomCount; $room++)
                                    @php
                                        $roomNumber = $floor . str_pad($room, 2, '0', STR_PAD_LEFT);
                                        $member = $members->get($roomNumber);

                                        $searchText = strtolower(
                                            $roomNumber . ' ' . ($member?->name ?? '') . ' ' . ($member?->email ?? ''),
                                        );
                                    @endphp

                                    <div class="col" data-room-card
                                        data-status="{{ $member ? 'occupied' : 'available' }}"
                                        data-search="{{ $searchText }}">
                                        <article class="card room-card">
                                            <div class="card-body">

                                                <div class="room-card-header">
                                                    <h4 class="room-number">
                                                        <i class="bi bi-door-closed"></i>
                                                        ห้อง {{ $roomNumber }}
                                                    </h4>

                                                    @if ($member)
                                                        <span class="room-status status-occupied">
                                                            <i class="bi bi-person-check"></i>
                                                            มีสมาชิก
                                                        </span>
                                                    @else
                                                        <span class="room-status status-available">
                                                            <i class="bi bi-house"></i>
                                                            ห้องว่าง
                                                        </span>
                                                    @endif
                                                </div>

                                                @if ($member)
                                                    <div class="member-info">
                                                        <div class="member-avatar">
                                                            {{ mb_strtoupper(mb_substr($member->name, 0, 1)) }}
                                                        </div>

                                                        <div class="member-detail">
                                                            <h5 class="member-name" title="{{ $member->name }}">
                                                                {{ $member->name }}
                                                            </h5>

                                                            <div class="member-email" title="{{ $member->email }}">
                                                                <i class="bi bi-envelope"></i>
                                                                {{ $member->email ?: 'ไม่ได้ระบุอีเมล' }}
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="room-actions">
                                                        <a href="{{ route('members.show', $member->id) }}"
                                                            class="room-action-btn btn-view">
                                                            <i class="bi bi-eye"></i> ดู
                                                        </a>

                                                        
                                                            <a href="{{ route('members.edit', $member->id) }}"
                                                                class="room-action-btn btn-edit">
                                                                <i class="bi bi-pencil"></i> แก้ไข
                                                            </a>

                                                            <form action="{{ route('members.destroy', $member->id) }}"
                                                                method="POST" class="member-delete-form flex-fill">
                                                                @csrf
                                                                @method('DELETE')

                                                                <button type="submit"
                                                                    class="room-action-btn btn-delete w-100"
                                                                    data-member-name="{{ $member->name }}"
                                                                    data-room-number="{{ $roomNumber }}">
                                                                    <i class="bi bi-trash"></i> ลบ
                                                                </button>
                                                            </form>
                                                        
                                                    </div>
                                                @else
                                                    <div class="empty-room">
                                                        <i class="bi bi-person-dash me-2"></i>
                                                        ยังไม่มีสมาชิก
                                                    </div>
                                                @endif

                                            </div>
                                        </article>
                                    </div>
                                @endfor
                            </div>
                        </section>
                    @endfor

                    {{-- ห้องพนักงาน --}}
                    @php
                        $staffRoomNumber = '601';
                        $staffMember = $members->get($staffRoomNumber);

                        $staffSearch = strtolower(
                            '601 ห้องพนักงาน ' . ($staffMember?->name ?? '') . ' ' . ($staffMember?->email ?? ''),
                        );
                    @endphp

                    <section class="staff-section" data-floor-section>
                        <div class="floor-header">
                            <h3 class="floor-title">
                                <i class="bi bi-person-badge-fill"></i>
                                ห้อง Admin / Staff
                            </h3>

                            <span class="floor-count">ห้องพนักงาน</span>
                        </div>

                        <div class="staff-card">
                            <div data-room-card data-status="{{ $staffMember ? 'occupied' : 'available' }}"
                                data-search="{{ $staffSearch }}">
                                <article class="card room-card">
                                    <div class="card-body">
                                        <div class="room-card-header">
                                            <h4 class="room-number">
                                                <i class="bi bi-shield-lock-fill"></i>
                                                ห้องพนักงาน
                                            </h4>

                                            @if ($staffMember)
                                                <span class="room-status status-occupied">
                                                    <i class="bi bi-person-check"></i>
                                                    มีผู้ใช้งาน
                                                </span>
                                            @else
                                                <span class="room-status status-available">
                                                    ไม่มีผู้ใช้งาน
                                                </span>
                                            @endif
                                        </div>

                                        @if ($staffMember)
                                            <div class="member-info">
                                                <div class="member-avatar">
                                                    {{ mb_strtoupper(mb_substr($staffMember->name, 0, 1)) }}
                                                </div>

                                                <div class="member-detail">
                                                    <h5 class="member-name">
                                                        {{ $staffMember->name }}
                                                    </h5>

                                                    <div class="member-email">
                                                        <i class="bi bi-envelope"></i>
                                                        {{ $staffMember->email ?: 'ไม่ได้ระบุอีเมล' }}
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <div class="empty-room">
                                                <i class="bi bi-person-dash me-2"></i>
                                                ไม่มีข้อมูลพนักงาน
                                            </div>
                                        @endif
                                    </div>
                                </article>
                            </div>
                        </div>
                    </section>
                </div>

                <div class="no-result" id="memberNoResult">
                    <i class="bi bi-search fs-2 d-block mb-2"></i>
                    ไม่พบสมาชิกหรือห้องที่ตรงกับการค้นหา
                </div>

            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('memberRoomSearch');
            const statusFilter = document.getElementById('roomStatusFilter');
            const clearButton = document.getElementById('clearMemberSearch');
            const noResult = document.getElementById('memberNoResult');

            // ค้นหาและกรองห้อง
            function filterRooms() {
                const searchValue = (searchInput?.value ?? '').trim().toLowerCase();
                const statusValue = statusFilter?.value ?? 'all';

                let visibleRooms = 0;

                document.querySelectorAll('[data-room-card]').forEach(function(room) {
                    const roomSearch = (room.dataset.search ?? '').toLowerCase();
                    const roomStatus = room.dataset.status ?? 'available';

                    const matchesSearch =
                        searchValue === '' || roomSearch.includes(searchValue);

                    const matchesStatus =
                        statusValue === 'all' || roomStatus === statusValue;

                    const shouldShow = matchesSearch && matchesStatus;

                    room.style.display = shouldShow ? '' : 'none';

                    if (shouldShow) {
                        visibleRooms++;
                    }
                });

                // ซ่อนชั้นที่ไม่มีห้องตรงกับเงื่อนไข
                document.querySelectorAll('[data-floor-section]').forEach(function(section) {
                    const hasVisibleRoom = Array.from(
                        section.querySelectorAll('[data-room-card]')
                    ).some(room => room.style.display !== 'none');

                    section.style.display = hasVisibleRoom ? '' : 'none';
                });

                if (noResult) {
                    noResult.style.display = visibleRooms === 0 ? 'block' : 'none';
                }
            }

            searchInput?.addEventListener('input', filterRooms);
            statusFilter?.addEventListener('change', filterRooms);

            // ล้างการค้นหา
            clearButton?.addEventListener('click', function() {
                if (searchInput) searchInput.value = '';
                if (statusFilter) statusFilter.value = 'all';

                filterRooms();
                searchInput?.focus();
            });

            // ยืนยันก่อนลบสมาชิก
            document.querySelectorAll('.member-delete-form').forEach(function(form) {
                form.addEventListener('submit', function(event) {
                    event.preventDefault();

                    const button = form.querySelector('.btn-delete');
                    const memberName = button?.dataset.memberName ?? 'สมาชิกนี้';
                    const roomNumber = button?.dataset.roomNumber ?? '-';

                    if (typeof Swal === 'undefined') {
                        if (confirm(`ยืนยันการลบ ${memberName} จากห้อง ${roomNumber} หรือไม่?`)) {
                            form.submit();
                        }
                        return;
                    }

                    Swal.fire({
                        title: 'ยืนยันการลบสมาชิก?',
                        text: `${memberName} จากห้อง ${roomNumber}`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'ยืนยันการลบ',
                        cancelButtonText: 'ยกเลิก',
                        reverseButtons: true,
                        background: '#171717',
                        color: '#fff',
                        confirmButtonColor: '#dc3545',
                        cancelButtonColor: '#555'
                    }).then(function(result) {
                        if (result.isConfirmed) {
                            button.disabled = true;
                            button.innerHTML = `
                        <span class="spinner-border spinner-border-sm"></span>
                        กำลังลบ
                    `;

                            form.submit();
                        }
                    });
                });
            });

            filterRooms();
        });
    </script>
@endpush
