@extends('users.guests.themes.matrix.layout.matrixLayout')

@section('title', 'About ' . $data['department']->name)

@section('content')

    @php
        $department = $data['department'];
        $faculty = $department->theFaculty;
        $hod = $department->hod;

        $systemInfo = basicSystemInfo();
        $schoolAcronym = $systemInfo->school_acronym ?? null;
    @endphp

    <main class="mx-page pt-5 mt-5">

        {{-- Department overview --}}
        <section class="mx-section mx-section-white">
            <div class="container">
                <div class="row g-4 g-lg-5 align-items-stretch">

                    <div class="col-md-12">
                        <div class="mx-copy">

                            <span class="mx-kicker">Department Overview</span>

                            <h2>
                                About <span>{{ $department->name }}</span>
                            </h2>

                            <div class="mx-prose">
                                @if (!empty($department->description))
                                    {!! $department->description !!}
                                @else
                                    <p>
                                        {{ $department->name }} provides students with
                                        relevant knowledge, practical skills, and
                                        professional development opportunities.
                                    </p>

                                    <p>
                                        The department supports teaching, learning,
                                        research, and student development within its
                                        area of specialisation.
                                    </p>
                                @endif
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </section>

        {{-- Faculty --}}
        @if ($faculty)
            <section class="mx-section mx-section-light">
                <div class="container">

                    <div class="mx-section-heading">
                        <span class="mx-kicker">Academic Structure</span>

                        <h2>{{ $faculty->name }}</h2>

                        <p>
                            {{ $department->name }} operates under
                            {{ $faculty->name }}@if ($faculty->acronym)
                                ({{ $faculty->acronym }})
                            @endif.
                        </p>
                    </div>

                    <div class="row justify-content-center">
                        <div class="col-lg-8">
                            <div class="mx-feature-panel">
                                <div class="mx-prose">
                                    @if (!empty($faculty->description))
                                        {!! $faculty->description !!}
                                    @else
                                        <p>
                                            {{ $faculty->name }} provides academic direction,
                                            coordination, and support for its departments
                                            and students.
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mx-actions">
                        <a {{-- href="{{ route('guestViewFaculty', ['uuid' => $faculty->uuid]) }}" --}} class="mx-button">
                            <i class="fa-solid fa-arrow-right"></i>
                            View {{ $faculty->name }}
                        </a>
                    </div>

                </div>
            </section>
        @endif

        {{-- Current HOD --}}
        <section class="mx-section mx-section-white">
            <div class="container">

                <div class="mx-section-heading">
                    <span class="mx-kicker">Department Leadership</span>

                    <h2>
                        Administrative Head
                        @if ($department->administrative_head_title)
                            ({{ $department->administrative_head_title }})
                        @endif
                    </h2>

                    <p>
                        Academic and administrative leadership for
                        {{ $department->name }}.
                    </p>
                </div>

                @if (!$hod)
                    <div class="row justify-content-center">
                        <div class="col-lg-8">
                            <div class="mx-profile-card">
                                <div class="mx-profile-card-head">
                                    <span class="mx-profile-label">
                                        <i class="fa-solid fa-user-slash"></i>
                                        Leadership
                                    </span>

                                    <span class="mx-profile-number">01</span>
                                </div>

                                <div class="mx-profile-avatar">
                                    <i class="fa-solid fa-user-slash"></i>
                                </div>

                                <span class="mx-profile-role">
                                    No Active Head
                                </span>

                                <h3>No records found</h3>

                                <p class="mx-profile-description">
                                    The administrative head profile for this department
                                    has not been added yet.
                                </p>

                                <div class="mx-profile-footer">
                                    <span>
                                        <i class="fa-solid fa-landmark"></i>
                                        {{ $department->name }}
                                    </span>

                                    <span class="mx-placeholder">
                                        Profile unavailable
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="row justify-content-center">
                        <div class="col-lg-10">
                            <article class="mx-profile-card">

                                <div class="mx-profile-card-head">
                                    <span class="mx-profile-label">
                                        <i class="fa-solid fa-user-tie"></i>
                                        Leadership
                                    </span>

                                    <span class="mx-profile-number">01</span>
                                </div>

                                <div class="row g-4 align-items-center">
                                    <div class="col-md-5">
                                        <div class="mx-profile-avatar mx-auto"
                                            style="width: 190px; height: 190px; overflow: hidden;">
                                            @if ($hod->photo)
                                                <img src="{{ asset('assets/uploads/department/head/photo/' . $hod->photo) }}"
                                                    alt="{{ $hod->name }}" class="w-100 h-100"
                                                    style="object-fit: cover;">
                                            @else
                                                <img src="{{ asset('system/images/hod_default_pic.webp') }}"
                                                    alt="Default profile picture" class="w-100 h-100"
                                                    style="object-fit: cover;">
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-md-7">
                                        <span class="mx-profile-role">
                                            {{ $department->administrative_head_title ?? 'Head of Department' }}
                                        </span>

                                        <h3>
                                            {{ $hod->name }}

                                            @if ($hod->qualifications)
                                                <small class="text-muted" style="font-size: 12px">
                                                    ({{ $hod->qualifications }})
                                                </small>
                                            @endif
                                        </h3>

                                        <div class="mx-prose mt-3">
                                            @if ($hod->profile)
                                                {!! $hod->profile !!}
                                            @else
                                                <p>
                                                    Provides academic and administrative
                                                    leadership, coordinates departmental
                                                    activities, and supports student
                                                    development.
                                                </p>
                                            @endif
                                        </div>

                                        <div class="mt-4">
                                            {{-- @if ($hod->phone)
                                                <a href="tel:{{ $hod->phone }}" class="mx-contact-card d-flex mb-3">
                                                    <div class="mx-contact-icon">
                                                        <i class="fa-solid fa-phone"></i>
                                                    </div>

                                                    <div>
                                                        <h3>Phone</h3>
                                                        <p>{{ $hod->phone }}</p>
                                                    </div>
                                                </a>
                                            @endif

                                            @if ($hod->email)
                                                <a href="mailto:{{ $hod->email }}" class="mx-contact-card d-flex">
                                                    <div class="mx-contact-icon">
                                                        <i class="fa-solid fa-envelope"></i>
                                                    </div>

                                                    <div>
                                                        <h3>Email</h3>
                                                        <p>{{ $hod->email }}</p>
                                                    </div>
                                                </a>
                                            @endif --}}
                                        </div>
                                    </div>
                                </div>

                                <div class="mx-profile-footer mt-4">
                                    <span>
                                        <i class="fa-solid fa-landmark"></i>
                                        {{ $department->name }}
                                    </span>

                                    <span class="mx-placeholder">
                                        Official department profile
                                    </span>
                                </div>

                            </article>
                        </div>
                    </div>
                @endif

            </div>
        </section>

        @if ($department->pastHods->isNotEmpty())
            <section class="mx-section mx-section-light">
                <div class="container">

                    <div class="mx-section-heading">
                        <span class="mx-kicker">Leadership History</span>

                        <h2>Past Heads of Department</h2>

                        <p>
                            Former leaders who contributed to the development of
                            {{ $department->name }}.
                        </p>
                    </div>

                    <div class="row g-4">
                        @foreach ($department->pastHods as $pastHod)
                            <div class="col-12 col-sm-6 col-lg-4">
                                <article class="mx-profile-card mx-profile-card-alt">

                                    <div class="mx-profile-card-head">
                                        <span class="mx-profile-label">
                                            <i class="fa-solid fa-user"></i>
                                            Former Leadership
                                        </span>

                                        <span class="mx-profile-number">
                                            {{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}
                                        </span>
                                    </div>

                                    <div class="mx-profile-avatar mx-auto"
                                        style="width: 130px; height: 130px; overflow: hidden;">
                                        @if ($pastHod->photo)
                                            <img src="{{ asset('assets/uploads/department/head/photo/' . $pastHod->photo) }}"
                                                alt="{{ $pastHod->name }}" class="w-100 h-100" style="object-fit: cover;">
                                        @else
                                            <img src="{{ asset('system/images/hod_default_pic.webp') }}"
                                                alt="Default profile picture" class="w-100 h-100"
                                                style="object-fit: cover;">
                                        @endif
                                    </div>

                                    <span class="mx-profile-role mt-3">
                                        Former Head of Department
                                    </span>

                                    <h3>
                                        {{ $pastHod->name }}

                                        @if ($pastHod->certifications)
                                            <small class="text-muted">
                                                ({{ $pastHod->certifications }})
                                            </small>
                                        @endif
                                    </h3>

                                    <p class="mx-profile-description">
                                        @if ($pastHod->profile)
                                            {{ \Illuminate\Support\Str::words(strip_tags(html_entity_decode($pastHod->profile)), 22, '...') }}
                                        @else
                                            Contributed to the academic and administrative
                                            development of the department.
                                        @endif
                                    </p>

                                    <div class="mx-profile-footer">
                                        <span>
                                            <i class="fa-solid fa-calendar-days"></i>

                                            @if ($pastHod->assumed_office_on)
                                                {{ \Carbon\Carbon::parse($pastHod->assumed_office_on)->format('M Y') }}
                                            @else
                                                —
                                            @endif

                                            –

                                            @if ($pastHod->tenure_end_on)
                                                {{ \Carbon\Carbon::parse($pastHod->tenure_end_on)->format('M Y') }}
                                            @else
                                                Present
                                            @endif
                                        </span>

                                        <span class="mx-placeholder">
                                            Leadership history
                                        </span>
                                    </div>

                                </article>
                            </div>
                        @endforeach
                    </div>

                </div>
            </section>
        @endif

    </main>

@endsection
