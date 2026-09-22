<div class="mx-2">
    @if (
        $data['admin']->photo &&
            file_exists(public_path('assets/tenant_' . tenant()->id . '/uploads/users/admin/photo/' . $data['admin']->photo)))
        <img src="{{ asset('assets/tenant_' . tenant()->id . '/uploads/users/admin/photo/' . $data['admin']->photo) }}"
            alt="{{ $data['admin']->photo }}" class="img-fluid w-100">
    @else
        <img src="{{ asset('system/images/default_profile_pic.webp') }}" alt="Default Picture" class="img-fluid w-100">
    @endif

    <h2 class="individual-review-card-header text-center text-wrap">

        {{ $data['admin']?->adminProfile?->name ?? $data['admin']->username }}
    </h2>
    <p class="m-0"><span style="font-weight: bold">Position:</span>
        <span class="text-capitalize">
            {{ $data['admin']?->adminProfile?->position ?? '-' }}
        </span>
    </p>



    @if ($data['admin']->roles->isNotEmpty())
        <p class="m-0"><span style="font-weight: bold">Role(s):
            </span>
            @foreach ($data['admin']->roles as $role)
                <span class="badge bg-secondary ms-1 text-capitalize">{{ $role->name }}</span>
            @endforeach
        </p>
    @endif
    <p class="m-0"><span style="font-weight: bold">Last Seen:
        </span>
        {{ $data['admin']->last_seen_at
            ? (\Carbon\Carbon::parse($data['admin']->last_seen_at)->isToday()
                ? 'Today, ' . \Carbon\Carbon::parse($data['admin']->last_seen_at)->format('h:i:s A')
                : (\Carbon\Carbon::parse($data['admin']->last_seen_at)->isYesterday()
                    ? 'Yesterday, ' . \Carbon\Carbon::parse($data['admin']->last_seen_at)->format('h:i:s A')
                    : \Carbon\Carbon::parse($data['admin']->last_seen_at)->format('d M Y h:i:s A')))
            : '-' }}
    </p>

    <p class="text-capitalize"><span style="font-weight: bold">Account Status:
        </span>
        @switch($data['admin']->account_status)
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
