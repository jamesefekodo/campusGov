<div class="mx-2">
    <img src="{{ asset('system/images/default_profile_pic.webp') }}" alt="Default Picture" class="img-fluid w-100">

    <h2 class="individual-review-card-header text-center text-capitalize">

        {{ $data['transaction']->non_registered_payer }}
    </h2>
    <p class="m-0"><span style="font-weight: bold">Name:</span>
        {{ $data['transaction']->non_registered_payer }}
    </p>
    <p class="m-0"><span style="font-weight: bold">Phone:</span>
        {{ $data['transaction']->phone }}
    </p>
    <p class="m-0"><span style="font-weight: bold">Email:</span>
        {{ $data['transaction']->email }}
    </p>
</div>
