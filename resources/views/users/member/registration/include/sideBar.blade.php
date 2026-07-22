@if ($data['member']->photo && file_exists(public_path('assets/uploads/users/members/photo/' . $data['member']->photo)))
    <img src="{{ asset('assets/uploads/users/members/photo/' . $data['member']->photo) }}"
        alt="{{ $data['member']->username }}" class="img-fluid w-100">
@else
    <img src="{{ asset('system/images/default_profile_pic.webp') }}" alt="Default Picture" class="img-fluid w-100">
@endif
<h2 class="individual-review-card-header text-center text-capitalize">
    {{ $data['member']->username }}
</h2>
<p class="m-0"><span style="font-weight: bold">First Name:</span>
    {{ $data['member']->memberPersonalProfile->first_name ?? '-' }}
</p>
<p class="m-0"><span style="font-weight: bold">Middle Name:</span>
    {{ $data['member']->memberPersonalProfile->middle_name ?? '-' }}
</p>
<p class="m-0"><span style="font-weight: bold">Last Name:</span>
    {{ $data['member']->memberPersonalProfile->last_name ?? '-' }}
</p>
<p class="m-0"><span style="font-weight: bold">Email: </span>
    {{ $data['member']->memberPersonalProfile->email1 ?? '-' }}
</p>
<p class="m-0"><span style="font-weight: bold">Phone: </span>
    {{ $data['member']->memberPersonalProfile->phone1 ?? '-' }}
</p>
<p class="text-capitalize"><span style="font-weight: bold">Account Status:
    </span>
    @switch($data['member']->account_status)
        @case('0')
            Unverified
        @break

        @case('1')
            Awaiting Verification
        @break

        @case('2')
            Active
        @break

        @case('3')
            Disabled
        @break

        @default
            Unknown
    @endswitch
</p>

<a href="{{ route('logoutRequest') }}" id="disableLink">
    <p class="custom-btn bg-red btn-block text-white text-center text-uppercase w-100">
        <i class="fas fa-sign-out"></i>&nbsp; Logout
    </p>
</a>
