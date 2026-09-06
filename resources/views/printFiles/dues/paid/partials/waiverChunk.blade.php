@php
    $secondaryColour = $data['basicSystemInfo']->secondary_colour ?? '#7a97ff';
@endphp

<table class="report-table" cellpadding="0" cellspacing="0">
    <thead>
        <tr>
            <th width="5%" style="background-color:{{ $secondaryColour }};">#</th>
            <th width="16%" style="background-color:{{ $secondaryColour }};">Photo</th>
            <th width="42%" style="background-color:{{ $secondaryColour }};">Member Details</th>
            <th width="37%" style="background-color:{{ $secondaryColour }};">Waiver Details</th>
        </tr>
    </thead>

    <tbody>
        @foreach ($waivers as $waiver)
            @php
                $ctx = $waiver->academicContext ?? [];

                $program = $ctx['programModel'] ?? null;
                $level = (int) ($ctx['level'] ?? 1);

                $profile = $waiver->personalProfile;

                $fullName = trim(
                    ($profile?->first_name ?? '') .
                        ' ' .
                        ($profile?->middle_name ?? '') .
                        ' ' .
                        ($profile?->last_name ?? ''),
                );

                $photo = $waiver->payer?->photo ?? null;

                $photoPath = $photo
                    ? public_path('assets/tenant_' . $data['tenant']->id . '/uploads/users/members/photo/' . $photo)
                    : null;

                $imageSource =
                    $photoPath && is_file($photoPath)
                        ? base64Image(
                            asset('assets/tenant_' . $data['tenant']->id . '/uploads/users/members/photo/' . $photo),
                        )
                        : base64Image(asset('system/images/default_profile_pic.webp'));
            @endphp

            <tr>
                <td>
                    {{ $serialStart + $loop->iteration }}
                </td>

                <td align="center">
                    <img src="{{ $imageSource }}" class="person-photo" alt="{{ $fullName }}">
                </td>

                <td>
                    <strong>Name:</strong> {{ $fullName ?: 'N/A' }}<br>

                    <strong>Level:</strong>
                    @php
                        $academic = $waiver->registeredPayer?->memberAcademicInfo;

                        $programLevel = studentLevelAtDue($academic, $waiver->due);

                        $program = $programLevel['program'];
                        $level = $programLevel['level'];
                    @endphp

                    @if ($program)
                        {{ currentLevel($program, $level) }}
                    @else
                        <span class="text-danger">
                            Previous programme is missing
                        </span>
                    @endif
                    {{-- @if ($program)
                        {{ currentLevel($program, $level) }}
                    @else
                        N/A
                    @endif --}}
                    <br>

                    <strong>Matric Number:</strong>
                    {{ $ctx['matric_number'] ?? 'N/A' }}<br>

                    <strong>Faculty:</strong>
                    {{ $ctx['faculty'] ?? 'N/A' }}<br>

                    <strong>Department:</strong>
                    {{ $ctx['department'] ?? 'N/A' }}<br>

                    <strong>Programme:</strong>
                    {{ $ctx['program'] ?? 'N/A' }}
                    -
                    {{ $ctx['program_type'] ?? 'N/A' }}
                </td>

                <td>
                    <strong>Waiver ID:</strong>
                    {{ $waiver->waiver_id }}<br>

                    <strong>Reason:</strong>
                    {{ $waiver->reason }}<br>

                    <strong>Date:</strong>
                    {{ $waiver->initiated_at
                        ? \Carbon\Carbon::parse($waiver->initiated_at)->timezone('Africa/Lagos')->format('d M Y, h:i A')
                        : 'N/A' }}<br>

                    <strong>Status:</strong>
                    <span class="status-approved">
                        {{ strtoupper($waiver->approval_status) }}
                    </span>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
