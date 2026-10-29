<nav class="navbar navbar-expand-lg navbar-dark fixed-top">
    <div class="container-fluid px-4">
        <a class="navbar-brand" href="{{ route('homepage') }}">
            <img src="{{ associationLogo() }}" alt="{{ $associationAcronym }} Logo" width="80px">
            @if (schoolLogo())
                <img src="{{ schoolLogo() }}" alt="OGITECH" width="65px">
            @endif
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item">
                    <a class="nav-link {{ Str::startsWith(request()->path(), 'home') ? 'active' : '' }}"
                        href="{{ route('homepage') }}">Home</a>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ Str::startsWith(request()->path(), 'about') ? 'active' : '' }}"
                        href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        About Us
                    </a>

                    @php
                        $showfaculties = false;
                        $showDepartment = false;
                        if (basicSystemInfo()->operational_level == 'institute' && activeFaculties()->isNotEmpty()) {
                            $showfaculties = true;
                        } elseif (
                            basicSystemInfo()->operational_level == 'faculty' &&
                            activeDepartments()->isNotEmpty()
                        ) {
                            $showDepartment = true;
                        } elseif (basicSystemInfo()->operational_level == 'department') {
                            $showDepartment = true;
                        }
                    @endphp

                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item {{ Str::startsWith(request()->path(), 'about_us') ? 'active' : '' }}"
                                href="{{ route('guestAboutUs') }}">{{ strtoupper(basicSystemInfo()->association_acronym) }}
                                - {{ strtoupper(basicSystemInfo()->school_acronym) }}</a></li>

                        {{-- school level --}}
                        @if ($showfaculties)
                            @foreach (activeFaculties() as $faculty)
                                <li><a class="dropdown-item {{ Str::startsWith(request()->path(), 'about/faculty/' . $faculty->uuid) ? 'active' : '' }}"
                                        href="{{ route('guestViewFaculty', ['uuid' => $faculty->uuid]) }}">{{ Str::limit($faculty->name, 25) }}
                                        @if ($faculty->acronym)
                                            ({{ $faculty->acronym }})
                                        @endif
                                    </a></li>
                            @endforeach
                        @endif
                        @if ($showDepartment)
                            @foreach (activeDepartments() as $department)
                                <li><a class="dropdown-item {{ Str::startsWith(request()->path(), 'about/department/' . $department->uuid) ? 'active' : '' }}"
                                        href="{{ route('guestViewDepartment', ['uuid' => $department->uuid]) }}">{{ Str::limit($department->name, 25) }}
                                    </a></li>
                            @endforeach
                        @endif
                    </ul>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ Str::startsWith(request()->path(), 'media') ? 'active' : '' }}"
                        href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Media
                    </a>

                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item {{ Str::startsWith(request()->path(), 'media/publications') ? 'active' : '' }}"
                                href="{{ route('newsAndArticles') }}">News & Articles</a></li>
                    </ul>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        Events
                    </a>

                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#">Regular Events</a></li>
                        <li><a class="dropdown-item" href="#">Elections</a></li>
                        <li><a class="dropdown-item" href="#">Upcoming Events</a></li>
                    </ul>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        Academic Resources
                    </a>

                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#">Past Questions</a></li>
                        <li><a class="dropdown-item" href="#">Recommended Courses</a></li>
                        <li><a class="dropdown-item" href="#">Recommended Books</a></li>
                        <li><a class="dropdown-item" href="#">Engineering & Tech Bodies</a></li>
                    </ul>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        Payments
                    </a>

                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#">Invoice</a></li>
                        <li><a class="dropdown-item" href="#">Donation</a></li>
                    </ul>
                </li>
            </ul>

            <a href="{{ route('loginPage') }}" class="login-btn">Login</a>
        </div>
    </div>
</nav>
