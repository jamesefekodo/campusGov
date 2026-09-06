@php
    $secondaryColour = $data['basicSystemInfo']->secondary_colour ?? '#7a97ff';
    $tenantId = $data['tenant']->id ?? tenant()->id;
@endphp

<table class="report-table" width="100%" cellpadding="0" cellspacing="0">
    <thead>
        <tr>
            <th width="5%" style="background-color: {{ $secondaryColour }};">
                #
            </th>

            <th width="16%" style="background-color: {{ $secondaryColour }};">
                Photo
            </th>

            <th width="43%" style="background-color: {{ $secondaryColour }};">
                Member Details
            </th>

            <th width="36%" style="background-color: {{ $secondaryColour }};">
                Outstanding Information
            </th>
        </tr>
    </thead>

    <tbody>
        @foreach ($debtors as $debtor)
            @php
                $profile = $debtor->personalProfile;

                $fullName = trim(
                    ($profile?->first_name ?? '') .
                        ' ' .
                        ($profile?->middle_name ?? '') .
                        ' ' .
                        ($profile?->last_name ?? ''),
                );

                if (empty($fullName)) {
                    $fullName = $debtor->username ?? 'N/A';
                }

                /*
                 * Resolved in the chunk callback.
                 * programModel should be the historical programme
                 * for a readmission-related due.
                 */
                $ctx = $debtor->academicContext ?? [];

                $program = $ctx['programModel'] ?? null;
                $level = (int) ($ctx['level'] ?? 1);

                $photo = $debtor->photo ?? null;

                $photoPath = $photo
                    ? public_path('assets/tenant_' . $tenantId . '/uploads/users/members/photo/' . $photo)
                    : null;

                $imageSource =
                    $photoPath && is_file($photoPath)
                        ? base64Image(asset('assets/tenant_' . $tenantId . '/uploads/users/members/photo/' . $photo))
                        : base64Image(asset('system/images/default_profile_pic.webp'));
            @endphp

            <tr>
                <td align="center" valign="middle">
                    {{ $serialStart + $loop->iteration }}
                </td>

                <td align="center" valign="middle">
                    <img src="{{ $imageSource }}" class="person-photo" width="75" height="75"
                        alt="{{ $fullName }}" style="border: 1px solid #cbd5e1;">
                </td>

                <td valign="top">
                    <strong>Name:</strong>
                    {{ $fullName }}
                    <br>

                    <strong>Matric Number:</strong>
                    {{ $ctx['matric_number'] ?? 'N/A' }}
                    <br>

                    <strong>Level:</strong>
                    {{-- @if ($program)
                        {{ currentLevel($program, $level) }}
                    @else
                        N/A
                    @endif --}}
                    {{-- ####################### --}}
                    @php
                        $academic = $debtor;

                        $programLevel = studentLevelAtDue($academic, $data['due']);

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
                    {{-- ######################## --}}
                    <br>

                    <strong>Faculty:</strong>
                    {{ $ctx['faculty'] ?? 'N/A' }}
                    <br>

                    <strong>Department:</strong>
                    {{ $ctx['department'] ?? 'N/A' }}
                    <br>

                    <strong>Programme:</strong>
                    {{ $ctx['program'] ?? 'N/A' }}

                    @if (!empty($ctx['program_type']))
                        — {{ $ctx['program_type'] }}
                    @endif
                </td>

                <td valign="top">
                    <strong>Due:</strong>
                    {{ $data['due']->title }}
                    <br>

                    <strong>Academic Session:</strong>
                    {{ $data['dueSession'] }}
                    <br>

                    <strong>Amount:</strong>
                    @php
                        $memberDue = $debtor->associationDues()->firstWhere('uuid', $data['due']->uuid);

                        if (!$memberDue) {
                            $memberDue = $debtor
                                ->pastProgramsAssociationDues()
                                ->flatten(1)
                                ->firstWhere('uuid', $uuid);
                        }
                    @endphp
                    ₦{{ number_format((float) $memberDue->payable_amount, 2) }}
                    <br>

                    <strong>Payment Status:</strong>
                    <span style="color: #b91c1c; font-weight: bold;">
                        UNPAID
                    </span>
                    <br>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
