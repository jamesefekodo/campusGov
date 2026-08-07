@extends('layouts.emailLayout')
@section('content')
    <p>
        @if ($user->memberPersonalProfile)
            Hello
            {{ $user->memberPersonalProfile->first_name }}
        @else
            Hello
        @endif,
    </p>
    <p>
        You have requested us to send a link to reset the password of your
        {{ basicSystemInfo()->association_acronym ?? config('app.name') }} account. Please
        click the link below to continue.
    </p>
    <p class="mailLink">
        <a href="{{ route('passwordResetLink', ['token' => $user->remember_token]) }}" class="link-button">Reset
            Password</a>
    </p>

    <p>
        If you did not initiate this request, please email or call our support service line immediately so we can
        investigate a possible attempt to breach your account.
    </p>
@endsection
