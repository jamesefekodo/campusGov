@extends('users.guests.themes.matrix.layout.matrixLayout')

@section('title', 'About ' . $data['faculty']->name)

@section('content')

    @php
        $faculty = $data['faculty'];

        $facultyHead = $faculty->facultyHead;
        $pastFacultyHeads = $faculty->pastFacultyHeads;
        $departments = $faculty->departments()->where('is_active', true)->orderBy('name')->get();

        $systemInfo = basicSystemInfo();
        $schoolAcronym = $systemInfo->school_acronym ?? null;
    @endphp

    <main class="mx-page pt-5 mt-5">

        {{-- Faculty overview --}}
        <section class="mx-section mx-section-white">
            <div class="container">
                <div class="row g-4 g-lg-5 align-items-stretch">

                    <div class="col-md-12">
                        <div class="mx-copy">

                            <span class="mx-kicker">Faculty Overview</span>

                            <h2>
                                About <span>{{ $faculty->name }}</span>
                            </h2>

                            <div class="mx-prose">
                                @if (!empty($faculty->description))
                                    {!! $faculty->description !!}
                                @else
                                    <p>
                                        {{ $faculty->name }} provides academic direction,
                                        coordination, and support for its departments
                                        and students.
                                    </p>

                                    <p>
                                        The faculty promotes teaching, learning, research,
                                        and professional development across its academic
                                        departments.
                                    </p>
                                @endif
                            </div>

                        </div>
                    </div>

                    {{-- <div class="col-lg-5">
                        <div class="mx-feature-panel">

                            <div class="mx-feature-item">
                                <div class="mx-feature-icon">
                                    <i class="fa-solid fa-bullseye"></i>
                                </div>

                                <div>
                                    <h3>Academic Direction</h3>
                                    <p>
                                        Provides coordination, academic leadership,
                                        and institutional support for departments.
                                    </p>
                                </div>
                            </div>

                            <div class="mx-feature-item">
                                <div class="mx-feature-icon">
                                    <i class="fa-solid fa-user-graduate"></i>
                                </div>

                                <div>
                                    <h3>Student Development</h3>
                                    <p>
                                        Supports academic growth, professional
                                        readiness, and student engagement.
                                    </p>
                                </div>
                            </div>

                            <div class="mx-feature-item">
                                <div class="mx-feature-icon">
                                    <i class="fa-solid fa-handshake"></i>
                                </div>

                                <div>
                                    <h3>Collaboration</h3>
                                    <p>
                                        Encourages cooperation among departments,
                                        staff, students, alumni, and stakeholders.
                                    </p>
                                </div>
                            </div>

                        </div>
                    </div> --}}

                </div>
            </div>
        </section>

        {{-- Departments --}}
        <section class="mx-section mx-section-light">
            <div class="container">

                <div class="mx-section-heading">
                    <span class="mx-kicker">Academic Structure</span>

                    <h2>Departments</h2>

                    <p>
                        Departments under {{ $faculty->name }}@if ($faculty->acronym)
                            ({{ $faculty->acronym }})
                        @endif.
                    </p>
                </div>

                @if ($departments->isEmpty())
                    <div class="row justify-content-center">
                        <div class="col-lg-8">
                            <div class="mx-feature-panel">
                                <div class="mx-prose text-center">
                                    <p class="mb-0">
                                        No departments have been added to this faculty yet.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="row g-4">
                        @foreach ($departments as $department)
                            <div class="col-12 col-sm-6 col-lg-4">
                                <article class="mx-profile-card">

                                    <div class="mx-profile-card-head">
                                        <span class="mx-profile-label">
                                            <i class="fa-solid fa-building-columns"></i>
                                            Department
                                        </span>

                                        <span class="mx-profile-number">
                                            {{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}
                                        </span>
                                    </div>

                                    <div class="mx-profile-avatar">
                                        <i class="fa-solid fa-book-open"></i>
                                    </div>

                                    <span class="mx-profile-role">
                                        {{ $faculty->acronym ?? 'Faculty' }}
                                    </span>

                                    <h3>
                                        {{ $department->name }}
                                    </h3>

                                    <p class="mx-profile-description">
                                        @if (!empty($department->description))
                                            {{ \Illuminate\Support\Str::words(strip_tags(html_entity_decode($department->description)), 20, '...') }}
                                        @else
                                            Academic department under
                                            {{ $faculty->name }}.
                                        @endif
                                    </p>

                                    <div class="mx-profile-footer">
                                        <span>
                                            <i class="fa-solid fa-landmark"></i>
                                            {{ $faculty->name }}
                                        </span>

                                        <a href="{{ route('guestViewDepartment', ['uuid' => $department->uuid]) }}" class="mx-button">
                                            View
                                            <i class="fa-solid fa-arrow-right"></i>
                                        </a>
                                    </div>

                                </article>
                            </div>
                        @endforeach
                    </div>
                @endif

            </div>
        </section>

        {{-- Current faculty head --}}
        <section class="mx-section mx-section-white">
            <div class="container">

                <div class="mx-section-heading">
                    <span class="mx-kicker">Faculty Leadership</span>

                    <h2>
                        {{ $faculty->administrative_head_title ?? 'Faculty Head' }}
                    </h2>

                    <p>
                        Academic and administrative leadership for
                        {{ $faculty->name }}.
                    </p>
                </div>

                @if (!$facultyHead)
                    <div class="row justify-content-center">
                        <div class="col-lg-8">
                            <article class="mx-profile-card">

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
                                    The faculty head profile has not been added yet.
                                </p>

                                <div class="mx-profile-footer">
                                    <span>
                                        <i class="fa-solid fa-landmark"></i>
                                        {{ $faculty->name }}
                                    </span>

                                    <span class="mx-placeholder">
                                        Profile unavailable
                                    </span>
                                </div>

                            </article>
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
                                            @if ($facultyHead->photo)
                                                <img src="{{ asset('assets/uploads/faculty/head/photo/' . $facultyHead->photo) }}"
                                                    alt="{{ $facultyHead->name }}" class="w-100 h-100"
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
                                            {{ $faculty->administrative_head_title ?? 'Faculty Head' }}
                                        </span>

                                        <h3>
                                            {{ $facultyHead->name }}

                                            @if ($facultyHead->certifications)
                                                <small class="text-muted" style="font-size: 12px">
                                                    ({{ $facultyHead->certifications }})
                                                </small>
                                            @endif
                                        </h3>

                                        <div class="mx-prose mt-3">
                                            @if (!empty($facultyHead->profile))
                                                {!! $facultyHead->profile !!}
                                            @else
                                                <p>
                                                    Provides academic and administrative
                                                    leadership, coordinates faculty activities,
                                                    and supports departmental development.
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="mx-profile-footer mt-4">
                                    <span>
                                        <i class="fa-solid fa-landmark"></i>
                                        {{ $faculty->name }}
                                    </span>

                                    <span class="mx-placeholder">
                                        Official faculty profile
                                    </span>
                                </div>

                            </article>
                        </div>
                    </div>
                @endif

            </div>
        </section>

        {{-- Past faculty heads --}}
        @if ($pastFacultyHeads->isNotEmpty())
            <section class="mx-section mx-section-light">
                <div class="container">

                    <div class="mx-section-heading">
                        <span class="mx-kicker">Leadership History</span>

                        <h2>Past Faculty Heads</h2>

                        <p>
                            Former leaders who contributed to the development of
                            {{ $faculty->name }}.
                        </p>
                    </div>

                    <div class="row g-4">
                        @foreach ($pastFacultyHeads as $pastHead)
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
                                        @if ($pastHead->photo)
                                            <img src="{{ asset('assets/uploads/faculty/head/photo/' . $pastHead->photo) }}"
                                                alt="{{ $pastHead->name }}" class="w-100 h-100"
                                                style="object-fit: cover;">
                                        @else
                                            <img src="{{ asset('system/images/hod_default_pic.webp') }}"
                                                alt="Default profile picture" class="w-100 h-100"
                                                style="object-fit: cover;">
                                        @endif
                                    </div>

                                    <span class="mx-profile-role mt-3">
                                        Former Faculty Head
                                    </span>

                                    <h3>
                                        {{ $pastHead->name }}

                                        @if ($pastHead->certifications)
                                            <small class="text-muted">
                                                ({{ $pastHead->certifications }})
                                            </small>
                                        @endif
                                    </h3>

                                    <p class="mx-profile-description">
                                        @if (!empty($pastHead->profile))
                                            {{ \Illuminate\Support\Str::words(strip_tags(html_entity_decode($pastHead->profile)), 22, '...') }}
                                        @else
                                            Contributed to the academic and administrative
                                            development of the faculty.
                                        @endif
                                    </p>

                                    <div class="mx-profile-footer">
                                        <span>
                                            <i class="fa-solid fa-calendar-days"></i>

                                            @if ($pastHead->assumed_office_on)
                                                {{ \Carbon\Carbon::parse($pastHead->assumed_office_on)->format('M Y') }}
                                            @else
                                                —
                                            @endif

                                            –

                                            @if ($pastHead->tenure_end_on)
                                                {{ \Carbon\Carbon::parse($pastHead->tenure_end_on)->format('M Y') }}
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
