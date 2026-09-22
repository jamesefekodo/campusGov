<div class="mx-2">
    @if (
        $data['member']->photo &&
            file_exists(public_path('assets/tenant_' . tenant()->id . '/uploads/users/members/photo/' . $data['member']->photo)))
        <img src="{{ asset('assets/tenant_' . tenant()->id . '/uploads/users/members/photo/' . $data['member']->photo) }}"
            alt="{{ $data['member']->photo }}" class="img-fluid w-100">
    @else
        <img src="{{ asset('system/images/default_profile_pic.webp') }}" alt="Default Picture" class="img-fluid w-100">
    @endif

    <h2 class="individual-review-card-header text-center text-capitalize text-wrap">
        @php
            $personal = $data['member']->memberPersonalProfile;
            $middleInitial = $personal?->middle_name ? strtoupper(substr($personal->middle_name, 0, 1)) . '. ' : '';
        @endphp

        {{ ($personal?->first_name ?? $data['member']->username) . ' ' . $middleInitial . ($personal?->last_name ?? '') }}
    </h2>
    <p class="m-0"><span style="font-weight: bold">Matric No:</span>
        <span class="text-uppercase">
            {{ $data['member']?->memberAcademicInfo?->matric_number }}
        </span>
    </p>
    <p class="m-0"><span style="font-weight: bold">Department:</span>
        {{ $data['member']?->memberAcademicInfo?->theDepartment?->name }}
    </p>
    <p class="m-0"><span style="font-weight: bold">Program:</span>
        {{ $data['member']?->memberAcademicInfo?->theProgram?->name }} @if ($data['member']?->memberAcademicInfo?->theProgram?->short_name)
            ({{ $data['member']?->memberAcademicInfo?->theProgram?->short_name }})
        @endif
    </p>
    <p class="m-0"><span style="font-weight: bold">Duration:</span>
        @php
            $academic = $data['member']?->memberAcademicInfo;
        @endphp

        {{ getProgramDuration($academic?->theProgram, $academic?->programType, $academic?->theDepartment) }} Years
    </p>
    <p class="m-0"><span style="font-weight: bold">Level:</span>
        <span style="font-weight: bold">
            @php
                $academic = $data['member']->memberAcademicInfo;
            @endphp

            <span style="font-weight: bold">
                @if ($academic?->academic_status == 'student')
                    {{-- Current Level: --}}
                @elseif($academic?->academic_status == 'alumni')
                    Graduated:
                @else
                    {{ $academic?->academic_status }}
                @endif
            </span>

            @if ($academic?->academic_status == 'student')

                {{ currentLevel($academic?->theProgram, studentCurrentLevel($academic)) }}
            @elseif($academic?->academic_status == 'alumni')
                {{ $academic?->graduation_session
                    ? $academic?->graduation_session . '/' . ($academic?->graduation_session + 1)
                    : '-' }}
            @else
                {{ ucfirst(str_replace('_', ' ', $academic?->academic_status ?? '-')) }}

                @if ($academic?->graduation_session)
                    ({{ $academic?->graduation_session }}/{{ $academic?->graduation_session + 1 }})
                @endif

            @endif
        </span>
    </p>


    @if ($data['member']->roles->isNotEmpty())
        <p class="m-0"><span style="font-weight: bold">Role(s):
            </span>
            @foreach ($data['member']->roles as $role)
                <span class="badge bg-secondary ms-1 text-capitalize">{{ $role->name }}</span>
            @endforeach
        </p>
    @endif
    <p class="m-0"><span style="font-weight: bold">Last Seen:
        </span>
        {{ $data['member']->last_seen_at
            ? (\Carbon\Carbon::parse($data['member']->last_seen_at)->isToday()
                ? 'Today, ' . \Carbon\Carbon::parse($data['member']->last_seen_at)->format('h:i:s A')
                : (\Carbon\Carbon::parse($data['member']->last_seen_at)->isYesterday()
                    ? 'Yesterday, ' . \Carbon\Carbon::parse($data['member']->last_seen_at)->format('h:i:s A')
                    : \Carbon\Carbon::parse($data['member']->last_seen_at)->format('d M Y h:i:s A')))
            : '-' }}
    </p>

    <p class="text-capitalize"><span style="font-weight: bold">Account Status:
        </span>
        @switch($data['member']->account_status)
            @case('0')
                <span class="badge bg-yellow text-black">
                    Unverified
                </span>
            @break

            @case('1')
                <span class="badge bg-blue text-white">
                    Awaiting Verification
                </span>
            @break

            @case('2')
                <span class="badge bg-green text-white">
                    Active
                </span>
            @break

            @case('3')
                <span class="badge bg-red text-white">
                    Disabled
                </span>
            @break

            @default
                <span class="badge bg-red text-white">
                    Unknown
                </span>
        @endswitch
    </p>

</div>
