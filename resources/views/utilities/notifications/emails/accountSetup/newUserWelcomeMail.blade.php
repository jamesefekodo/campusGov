@extends('layouts.emailLayout')
@section('content')
    <p>
        Hello,
    </p>

    <p>
        Thank you for registering on {{ basicSystemInfo()->association_acronym ?? config('app.name') }} Member Portal. Your registration has been
        received successfully.
    </p>
    <p>
        To proceed with your membership application, kindly log in to your account and complete your profile by providing
        all the required information.
    </p>
    <p>
        Please ensure that the information you submit is accurate, as it will be used during the membership verification and
        approval process.
    </p>
    <p style="margin-bottom: 1px; padding-bottom:0px">
        Once your profile has been submitted:
    </p>
    <ul>
        <li>It will be reviewed by the association.</li>
        <li>You may be contacted if additional information is required.</li>
        <li>You will receive a notification once your membership has been approved.</li>
        <li>Please contact your class representative or the association executives if your profile has not been reviewed
            within 48 hours, or if you have any questions.
        </li>
    </ul>

    <p class="mailLink">

        <a href="{{ route('loginPage') }}" class="link-button">Portal Link</a>
    </p>


    <p>
        Thank you for choosing to be part of {{ basicSystemInfo()->association_name }}
        ({{ basicSystemInfo()->association_acronym }})
    </p>
@endsection
