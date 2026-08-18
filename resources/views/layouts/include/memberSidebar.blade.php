<nav id="app-nav-main" class="app-nav app-nav-main flex-grow-1">
    <ul class="app-menu list-unstyled accordion" id="menu-accordion">
        <li class="nav-item">
            <a class="nav-link {{ Route::is('memberDashboard') ? 'sidebar-active' : '' }}"
                href="{{ route('memberDashboard') }}">
                <span class="nav-icon">
                    <i class="fas fa-tachometer-alt"></i>
                </span>
                <span class="nav-link-text">Dashboard</span>
            </a><!--//nav-link-->
        </li><!--//nav-item-->

        <li class="nav-item has-submenu">
            <a class="nav-link submenu-toggle {{ Str::startsWith(request()->path(), 'student/academic_resources') ? 'sidebar-active-section' : '' }}"
                href="#" data-bs-toggle="collapse" data-bs-target="#submenu-1" aria-expanded="false"
                aria-controls="submenu-1">
                <span class="nav-icon">
                    <i class="fas fa-book"></i>
                </span>
                <span class="nav-link-text">Academic Resources
                </span>
                <span class="submenu-arrow">
                    <i class="fas fa-chevron-down"></i>
                </span><!--//submenu-arrow-->
            </a><!--//nav-link-->
            <div id="submenu-1" class="collapse submenu submenu-1" data-bs-parent="#menu-accordion">
                <ul class="submenu-list list-unstyled">
                    <li class="submenu-item"><a
                            class="submenu-link {{ Str::startsWith(request()->path(), 'student/academic_resources/past_question') ? 'sidebar-active' : '' }}"
                            href="
                            {{-- {{ route('studentPastQuestions') }} --}}
                             ">Past
                            Questions
                        </a>
                    </li>
                    <li class="submenu-item"><a
                            class="submenu-link {{ Str::startsWith(request()->path(), 'student/academic_resources/recommended_book') ? 'sidebar-active' : '' }}
                             "
                            href="
                            {{-- {{ route('studentRecommendedBooks') }} --}}
                             ">Recommended
                            Books &nbsp;
                        </a>
                    </li>
                    <li class="submenu-item"><a
                            class="submenu-link {{ Str::startsWith(request()->path(), 'student/academic_resources/recommended_course') ? 'sidebar-active' : '' }}
                             "
                            href="
                            {{-- {{ route('studentRecommendedCourses') }} --}}
                             ">Recommended
                            Courses</a>
                    </li>
                    <li class="submenu-item"><a
                            class="submenu-link {{ Str::startsWith(request()->path(), 'student/academic_resources/engineering_tech_bodies') ? 'sidebar-active' : '' }}
                             "
                            href="
                            {{-- {{ route('studentEngineeringTechBodies') }} --}}
                             ">Engineering
                            & Tech Bodies</a>
                    </li>
                </ul>
            </div>
        </li><!--//nav-item-->
        {{-- ################# --}}
        <li class="nav-item has-submenu">
            <a class="nav-link submenu-toggle {{ Str::startsWith(request()->path(), 'student/members_directory') ? 'sidebar-active-section' : '' }}"
                href="#" data-bs-toggle="collapse" data-bs-target="#submenu-5" aria-expanded="false"
                aria-controls="submenu-5">
                <span class="nav-icon">
                    <i class="fas fa-users"></i>
                </span>
                <span class="nav-link-text">Members Directory</span>
                <span class="submenu-arrow">
                    <i class="fas fa-chevron-down"></i>
                </span><!--//submenu-arrow-->
            </a><!--//nav-link-->
            <div id="submenu-5" class="collapse submenu submenu-5" data-bs-parent="#menu-accordion">
                <ul class="submenu-list list-unstyled">
                    <li class="submenu-item"><a
                            class="submenu-link  {{ Str::startsWith(request()->path(), 'student/members_directory/members') ? 'sidebar-active' : '' }}"
                            href="
                            {{-- {{ route('membersDirectory') }} --}}
                            ">Members</a>
                    </li>
                </ul>
            </div>
        </li><!--//nav-item-->
        <li class="nav-item has-submenu">
            <a class="nav-link submenu-toggle {{ Str::startsWith(request()->path(), 'student/publications') ? 'sidebar-active-section' : '' }}"
                href="#" data-bs-toggle="collapse" data-bs-target="#submenu-2" aria-expanded="false"
                aria-controls="submenu-2">
                <span class="nav-icon">
                    <i class="fas fa-newspaper"></i>
                </span>
                <span class="nav-link-text">Publications</span>
                <span class="submenu-arrow">
                    <i class="fas fa-chevron-down"></i>
                </span><!--//submenu-arrow-->
            </a><!--//nav-link-->
            <div id="submenu-2" class="collapse submenu submenu-2" data-bs-parent="#menu-accordion">
                <ul class="submenu-list list-unstyled">
                    <li class="submenu-item"><a
                            class="submenu-link  {{ Str::startsWith(request()->path(), 'student/publications/publications') ? 'sidebar-active' : '' }}"
                            href="
                            {{-- {{ route('studentPublications') }} --}}
                             ">Publications</a>
                    </li>
                </ul>
            </div>
        </li><!--//nav-item-->
        <li class="nav-item has-submenu">
            <a class="nav-link submenu-toggle {{ Str::startsWith(request()->path(), 'member/payment') ? 'sidebar-active-section' : '' }}"
                href="#" data-bs-toggle="collapse" data-bs-target="#submenu-3" aria-expanded="false"
                aria-controls="submenu-3">
                <span class="nav-icon">
                    <i class="fas fa-dollar-sign"></i>
                </span>
                <span class="nav-link-text">Payments</span>
                <span class="submenu-arrow">
                    <i class="fas fa-chevron-down bi"></i>
                </span><!--//submenu-arrow-->
            </a><!--//nav-link-->
            <div id="submenu-3" class="collapse submenu submenu-3" data-bs-parent="#menu-accordion">
                <ul class="submenu-list list-unstyled">
                    <li class="submenu-item"><a
                            class="submenu-link {{ Str::startsWith(request()->path(), 'member/payment/dues') ? 'sidebar-active' : '' }}"
                            href="{{ route('myAssociationDues') }}">Dues</a>
                    </li>
                    <li class="submenu-item"><a
                            class="submenu-link {{ Str::startsWith(request()->path(), 'member/payment/transactions') ? 'sidebar-active' : '' }}"
                            href="{{ route('myPaymentTransactions') }}">Transactions</a>
                    </li>
                    <li class="submenu-item"><a
                            class="submenu-link {{ Str::startsWith(request()->path(), 'student/payment/invoices') ? 'sidebar-active' : '' }}"
                            href="
                            {{-- {{ route('studentSelfInvoices') }} --}}
                             ">Invoices</a>
                    </li>
                    <li class="submenu-item"><a
                            class="submenu-link {{ Str::startsWith(request()->path(), 'student/payment/donation') ? 'sidebar-active' : '' }}"
                            href="
                            {{-- {{ route('studentSelfDonations') }} --}}
                             ">Donations</a>
                    </li>
                </ul>
            </div>
        </li><!--//nav-item-->
        <li class="nav-item has-submenu">
            <a class="nav-link submenu-toggle {{ Str::startsWith(request()->path(), 'member/elections') ? 'sidebar-active-section' : '' }}"
                href="#" data-bs-toggle="collapse" data-bs-target="#submenu-7" aria-expanded="false"
                aria-controls="submenu-7">
                <span class="nav-icon">
                    <i class="fas fa-fingerprint"></i>
                </span>
                <span class="nav-link-text">Elections</span>
                <span class="submenu-arrow">
                    <i class="fas fa-chevron-down"></i>
                </span><!--//submenu-arrow-->
            </a><!--//nav-link-->
            <div id="submenu-7" class="collapse submenu submenu-7" data-bs-parent="#menu-accordion">
                <ul class="submenu-list list-unstyled">
                    <li class="submenu-item"><a
                            class="submenu-link text-capitalize {{ Str::startsWith(request()->path(), 'member/elections/new') ? 'sidebar-active' : '' }}"
                            href="
                            {{ route('memberNewElections') }}
                             ">Election</a>
                    </li>
                </ul>
            </div>
        </li><!--//nav-item-->
        <li class="nav-item has-submenu">
            <a class="nav-link submenu-toggle {{ Str::startsWith(request()->path(), 'student/events') ? 'sidebar-active-section' : '' }}"
                href="#" data-bs-toggle="collapse" data-bs-target="#submenu-4" aria-expanded="false"
                aria-controls="submenu-4">
                <span class="nav-icon">
                    <i class="fas fa-calendar"></i>
                </span>
                <span class="nav-link-text">Events</span>
                <span class="submenu-arrow">
                    <i class="fas fa-chevron-down"></i>
                </span><!--//submenu-arrow-->
            </a><!--//nav-link-->
            <div id="submenu-4" class="collapse submenu submenu-4" data-bs-parent="#menu-accordion">
                <ul class="submenu-list list-unstyled">
                    <li class="submenu-item"><a
                            class="submenu-link text-capitalize {{ Str::startsWith(request()->path(), 'student/events/event') ? 'sidebar-active' : '' }}"
                            href="
                            {{-- {{ route('studentEvents') }} --}}
                             ">Events</a>
                    </li>
                </ul>
            </div>
        </li><!--//nav-item-->
        <li class="nav-item">
            <a class="nav-link {{ Str::startsWith(request()->path(), 'member/my_profile') ? 'sidebar-active-section' : '' }}"
                href="{{ route('memberViewSelfProfile') }}">
                <span class="nav-icon">
                    <i class="fas fa-user-circle"></i>
                </span>
                <span class="nav-link-text">My Profile</span>
            </a><!--//nav-link-->
        </li><!--//nav-item-->
        <li class="nav-item has-submenu">
            <a class="nav-link submenu-toggle {{ Str::startsWith(request()->path(), 'admin/marketing') ? 'sidebar-active-section' : '' }}"
                href="#" data-bs-toggle="collapse" data-bs-target="#submenu-6" aria-expanded="false"
                aria-controls="submenu-6">
                <span class="nav-icon">
                    <i class="fas fa-user-check"></i>
                </span>
                <span class="nav-link-text">Clearance</span>
                <span class="submenu-arrow">
                    <i class="fas fa-chevron-down"></i>
                </span><!--//submenu-arrow-->
            </a><!--//nav-link-->
            <div id="submenu-6" class="collapse submenu submenu-6" data-bs-parent="#menu-accordion">
                <ul class="submenu-list list-unstyled">
                    <li class="submenu-item"><a
                            class="submenu-link text-capitalize {{ Str::startsWith(request()->path(), 'admin/marketing/coupons') ? 'sidebar-active' : '' }}"
                            href="
                            {{-- {{ route('couponsHome') }} --}}
                             ">Clearance</a>
                    </li>
                </ul>
            </div>
        </li><!--//nav-item-->






    </ul><!--//app-menu-->
</nav><!--//app-nav-->
<div class="app-sidepanel-footer">
    <hr>
    <nav class="app-nav app-nav-footer">
        <ul class="app-menu footer-menu list-unstyled">
            <li class="nav-item logout_button_sidebar">
                <a class="nav-link" href="{{ route('logoutRequest') }}">
                    <span class="nav-icon">
                        <i class="fas fa-sign-out"></i>
                    </span>
                    <span class="nav-link-text">LOGOUT</span>
                </a><!--//nav-link-->
            </li><!--//nav-item-->

        </ul><!--//footer-menu-->
    </nav>
</div><!--//app-sidepanel-footer-->
