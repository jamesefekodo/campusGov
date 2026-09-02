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

            <th width="42%" style="background-color: {{ $secondaryColour }};">
                Member Details
            </th>

            <th width="37%" style="background-color: {{ $secondaryColour }};">
                Payment Details
            </th>
        </tr>
    </thead>

    <tbody>
        @foreach ($payments as $payment)
            @php
                /*
                |--------------------------------------------------------------------------
                | Pre-resolved academic context
                |--------------------------------------------------------------------------
                |
                | This was attached in the controller:
                |
                | $payment->academicContext = resolveAcademicContextForDue(
                |     $payment->payerAcademicInfo,
                |     $due
                | );
                |
                */
                $ctx = $payment->academicContext ?? [];

                /*
                |--------------------------------------------------------------------------
                | Correct programme and level
                |--------------------------------------------------------------------------
                |
                | programModel must be the actual historical/current Program model.
                | For readmission records, it must be the PREVIOUS programme model.
                |
                */
                $program = $ctx['programModel'] ?? null;
                $level = (int) ($ctx['level'] ?? 1);

                /*
                |--------------------------------------------------------------------------
                | Member name
                |--------------------------------------------------------------------------
                */
                $profile = $payment->payerPersonalProfile;

                $fullName = trim(
                    ($profile?->first_name ?? '') .
                        ' ' .
                        ($profile?->middle_name ?? '') .
                        ' ' .
                        ($profile?->last_name ?? ''),
                );

                if (empty($fullName)) {
                    $fullName = $payment->registered_payer ?? 'N/A';
                }

                /*
                |--------------------------------------------------------------------------
                | Profile photograph
                |--------------------------------------------------------------------------
                |
                | registeredPayer is already eager-loaded by the controller.
                |
                */
                $photo = $payment->registeredPayer?->photo;

                $photoPath = $photo
                    ? public_path('assets/tenant_' . $tenantId . '/uploads/users/members/photo/' . $photo)
                    : null;

                /*
                |--------------------------------------------------------------------------
                | Base64 image source for mPDF
                |--------------------------------------------------------------------------
                */
                $imageSource =
                    $photoPath && is_file($photoPath)
                        ? base64Image(asset('assets/tenant_' . $tenantId . '/uploads/users/members/photo/' . $photo))
                        : base64Image(asset('system/images/default_profile_pic.webp'));

                /*
                |--------------------------------------------------------------------------
                | Payment date
                |--------------------------------------------------------------------------
                */
                $paidAt = $payment->paid_at
                    ? \Carbon\Carbon::parse($payment->paid_at)->timezone('Africa/Lagos')->format('d M Y, h:i A')
                    : 'N/A';

                /*
                |--------------------------------------------------------------------------
                | Display programme source
                |--------------------------------------------------------------------------
                */
                $isReadmission = ($ctx['source'] ?? null) === 'readmission';
            @endphp

            <tr>
                {{-- Continuous serial number, even across chunks --}}
                <td align="center" valign="middle">
                    {{ $serialStart + $loop->iteration }}
                </td>

                {{-- Member photo --}}
                <td align="center" valign="middle">
                    <img src="{{ $imageSource }}" class="person-photo" width="75" height="75"
                        alt="{{ $fullName }}" style="border: 1px solid #cbd5e1;">
                </td>

                {{-- Member and academic details --}}
                <td valign="top">
                    <strong>Name:</strong>
                    {{ $fullName }}
                    <br>

                    <strong>Level:</strong>

                    {{-- @if ($program)
                        {{ currentLevel($program, $level) }}
                    @else
                        N/A
                    @endif

                    @if ($isReadmission)
                        <br>
                        <span class="small-text">
                            Previous programme record
                        </span>
                    @endif --}}

                    {{-- ################## --}}
                    @php
                        $academic = $payment->registeredPayer?->memberAcademicInfo;

                        $programLevel = studentLevelAtDue($academic, $payment->due);

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
                    {{-- #################### --}}
                    <br>

                    <strong>Matric Number:</strong>
                    {{ $ctx['matric_number'] ?? 'N/A' }}
                    <br>

                    <strong>Department:</strong>
                    {{ $ctx['department'] ?? 'N/A' }}
                    <br>

                    <strong>Programme:</strong>
                    {{ $ctx['program'] ?? 'N/A' }}

                    @if (!empty($ctx['program_type']))
                        - {{ $ctx['program_type'] }}
                    @endif
                </td>

                {{-- Payment details --}}
                <td valign="top">
                    <strong>Transaction ID:</strong>
                    <span style="font-size: 8pt;">
                        {{ $payment->payment_transaction_id ?? 'N/A' }}
                    </span>
                    <br>

                    <strong>Payment Type:</strong>
                    {{ ucfirst($payment->payment_type ?? 'N/A') }}
                    <br>

                    <strong>Payment Mode:</strong>
                    {{ ucfirst($payment->payment_mode ?? 'N/A') }}
                    <br>

                    <strong>Amount Paid:</strong>
                    ₦{{ number_format((float) ($payment->amount_payable ?? 0), 2) }}
                    <br>

                    <strong>Paid At:</strong>
                    {{ $paidAt }}
                    <br>

                    <strong>Status:</strong>
                    <span class="status-approved">
                        {{ strtoupper($payment->payment_status ?? 'N/A') }}
                    </span>

                    <span class="small-text">
                        / {{ strtoupper($payment->approval_status ?? 'N/A') }}
                    </span>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
