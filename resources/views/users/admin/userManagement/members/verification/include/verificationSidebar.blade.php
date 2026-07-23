@if ($data['member']->photo && file_exists(public_path('assets/uploads/users/members/photo/' . $data['member']->photo)))
    <img src="{{ asset('assets/uploads/users/members/photo/' . $data['member']->photo) }}"
        alt="{{ $data['member']->photo }}" class="img-fluid w-100">
@else
    <img src="{{ asset('system/images/default_profile_pic.webp') }}" alt="Default Picture" class="img-fluid w-100">
@endif

<h2 class="individual-review-card-header text-center text-capitalize">
    @php
        $personal = $data['member']->memberPersonalProfile;
        $middleInitial = $personal?->middle_name ? strtoupper(substr($personal->middle_name, 0, 1)) . '. ' : '';
    @endphp

    {{ ($personal?->first_name ?? $data['member']->username) . ' ' . $middleInitial . ($personal?->last_name ?? '') }}
</h2>
<p class="m-0"><span style="font-weight: bold">Matric No:</span>
    {{ $data['member']->memberAcademicInfo->matric_number }}
</p>
<p class="m-0"><span style="font-weight: bold">Department:</span>
    {{ $data['member']->memberAcademicInfo->theDepartment->name }}
</p>
<p class="m-0"><span style="font-weight: bold">Program:</span>
    {{ $data['member']->memberAcademicInfo->theProgram?->name }} @if ($data['member']->memberAcademicInfo->theProgram?->short_name)
        ({{ $data['member']->memberAcademicInfo->theProgram?->short_name }})
    @endif
</p>
<p class="m-0"><span style="font-weight: bold">Level:</span>
    <span style="font-weight: bold">
        @if ($data['member']->memberAcademicInfo->academic_status == 'student')
            {{-- Current --}}
        @elseif($data['member']->memberAcademicInfo->academic_status == 'alumni')
            Graduated
        @else
            -
        @endif
    </span>

    @php
        $academic = $data['member']->memberAcademicInfo;
    @endphp


    @if ($academic?->academic_status == 'student')
        {{ currentLevel(
            \App\Models\AcademicConfig\Program::whereSlug($academic->program)->first(),
            now()->year - (int) explode('/', $academic->admission_session)[0] + 1,
        ) }}
    @elseif($academic?->academic_status == 'alumni')
        @if ($academic?->graduation_session)
            ({{ $academic->graduation_session }}/{{ $academic->graduation_session + 1 }})
        @endif
    @else
        {{ ucfirst(str_replace('_', ' ', $academic?->academic_status ?? '-')) }}

        @if ($academic?->graduation_session)
            ({{ $academic->graduation_session }}/{{ $academic->graduation_session + 1 }})
        @endif
    @endif
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

<hr>


<a href="#" id="SendBackLink">
    <p class="custom-btn bg-red btn-block text-white text-center text-uppercase w-100 my-2">
        Return for Corrections
    </p>
</a>




<script>
    document.getElementById('SendBackLink').addEventListener('click', function(e) {
        e.preventDefault();

        Swal.fire({
            title: 'Return Application for Corrections',
            html: `
        The member will regain access to edit their registration details.<br><br>
        After making the required corrections, they must submit the application again for verification.<br><br>
        <strong>Do you want to continue?</strong>
    `,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, Continue',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href =
                    "{{ route('returnProfileFromVerToReg', ['uuid' => $data['member']->uuid]) }}";
            }
        });
    });
</script>
