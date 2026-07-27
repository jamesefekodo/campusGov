<nav id="app-nav-main" class="app-nav app-nav-main flex-grow-1">
    <ul class="app-menu list-unstyled accordion" id="menu-accordion">
        <li class="nav-item">
            <a class="nav-link {{ Route::is('adminDashboard') ? 'sidebar-active' : '' }}"
                href="{{ route('adminDashboard') }}">
                <span class="nav-icon">
                    <i class="fas fa-tachometer-alt"></i>
                </span>
                <span class="nav-link-text">Dashboard</span>
            </a><!--//nav-link-->
        </li><!--//nav-item-->

        <li class="nav-item has-submenu">
            <a class="nav-link submenu-toggle {{ Str::startsWith(request()->path(), 'admin/user_management') ? 'sidebar-active-section' : '' }}"
                href="#" data-bs-toggle="collapse" data-bs-target="#submenu-1" aria-expanded="false"
                aria-controls="submenu-1">
                <span class="nav-icon">
                    <i class="fas fa-users"></i>
                </span>
                <span class="nav-link-text">Users &nbsp;
                    @php
                        $usersNotifications = countMemberProfilesAwaitingVerification() + 0;
                    @endphp
                    @if ($usersNotifications > 0)
                        <span class="position-relative top-0 translate-middle badge rounded-pill bg-red ml-1"
                            style="font-size: 11px">{{ $usersNotifications }}</span>
                    @endif
                </span>
                <span class="submenu-arrow">
                    <i class="fas fa-chevron-down"></i>
                </span><!--//submenu-arrow-->
            </a><!--//nav-link-->
            <div id="submenu-1" class="collapse submenu submenu-1" data-bs-parent="#menu-accordion">
                <ul class="submenu-list list-unstyled">
                    @if (Auth::user()->hasPermission('member_profile.read'))
                        <li class="submenu-item"><a
                                class="submenu-link {{ Str::startsWith(request()->path(), 'admin/user_management/members') ? 'sidebar-active' : '' }}"
                                href="{{ route('studentMembers') }}">Members
                                &nbsp;
                                @if (countMemberProfilesAwaitingVerification() > 0)
                                    <span
                                        class="position-relative top-0 translate-middle badge rounded-pill bg-red ml-1"
                                        style="font-size: 11px">{{ countMemberProfilesAwaitingVerification() }}</span>
                                @endif
                            </a>
                        </li>
                    @endif
                    <li class="submenu-item"><a
                            class="submenu-link {{ Str::startsWith(request()->path(), 'admin/product_management/categories') ? 'sidebar-active' : '' }}
                             "
                            href="
                            {{-- {{ route('categoriesHome') }} --}}
                             ">Admins</a>
                    </li>
                </ul>
            </div>
        </li><!--//nav-item-->
        <li class="nav-item has-submenu">
            <a class="nav-link submenu-toggle {{ Str::startsWith(request()->path(), 'admin/media') ? 'sidebar-active-section' : '' }}"
                href="#" data-bs-toggle="collapse" data-bs-target="#submenu-2" aria-expanded="false"
                aria-controls="submenu-2">
                <span class="nav-icon">
                    <i class="fas fa-newspaper"></i>
                </span>
                <span class="nav-link-text">Media</span>
                <span class="submenu-arrow">
                    <i class="fas fa-chevron-down"></i>
                </span><!--//submenu-arrow-->
            </a><!--//nav-link-->
            <div id="submenu-2" class="collapse submenu submenu-2" data-bs-parent="#menu-accordion">
                <ul class="submenu-list list-unstyled">
                    @if (Auth::user()->hasPermission('media_platform.read'))
                        <li class="submenu-item"><a
                                class="submenu-link  {{ Str::startsWith(request()->path(), 'admin/media/social_media') ? 'sidebar-active' : '' }}"
                                href="{{ route('recognizedSocialMediaPlatforms') }}">Recognized Platforms</a>
                        </li>
                    @endif
                    <li class="submenu-item"><a
                            class="submenu-link  {{ Str::startsWith(request()->path(), 'admin/orders/all') ? 'sidebar-active' : '' }}"
                            href="
                            {{-- {{ route('orders') }} --}}
                             ">News</a>
                    </li>
                    <li class="submenu-item"><a
                            class="submenu-link  {{ Str::startsWith(request()->path(), 'admin/orders/all') ? 'sidebar-active' : '' }}"
                            href="
                            {{-- {{ route('orders') }} --}}
                             ">Articles</a>
                    </li>
                </ul>
            </div>
        </li><!--//nav-item-->
        <li class="nav-item has-submenu">
            <a class="nav-link submenu-toggle {{ Str::startsWith(request()->path(), 'admin/users') ? 'sidebar-active-section' : '' }}"
                href="#" data-bs-toggle="collapse" data-bs-target="#submenu-3" aria-expanded="false"
                aria-controls="submenu-3">
                <span class="nav-icon">
                    <i class="fas fa-users"></i>
                </span>
                <span class="nav-link-text">Academic Resources</span>
                <span class="submenu-arrow">
                    <i class="fas fa-chevron-down bi"></i>
                </span><!--//submenu-arrow-->
            </a><!--//nav-link-->
            <div id="submenu-3" class="collapse submenu submenu-3" data-bs-parent="#menu-accordion">
                <ul class="submenu-list list-unstyled">
                    <li class="submenu-item"><a
                            class="submenu-link {{ Str::startsWith(request()->path(), 'admin/users/administrators') ? 'sidebar-active' : '' }}"
                            href="
                            {{-- {{ route('administratorsHome') }} --}}
                             ">Past
                            Questions</a>
                    </li>
                    <li class="submenu-item"><a
                            class="submenu-link {{ Str::startsWith(request()->path(), 'admin/users/customers') ? 'sidebar-active' : '' }}"
                            href="
                            {{-- {{ route('customersHome') }} --}}
                             ">Recommended
                            Courses</a>
                    </li>
                    <li class="submenu-item"><a
                            class="submenu-link {{ Str::startsWith(request()->path(), 'admin/users/customers') ? 'sidebar-active' : '' }}"
                            href="
                            {{-- {{ route('customersHome') }} --}}
                             ">Recommended
                            Books</a>
                    </li>
                </ul>
            </div>
        </li><!--//nav-item-->
        @if (Auth::user()->hasPermission('misconducts.read'))
            <li class="nav-item has-submenu">
                <a class="nav-link submenu-toggle {{ Str::startsWith(request()->path(), 'admin/misconducts') ? 'sidebar-active-section' : '' }}"
                    href="#" data-bs-toggle="collapse" data-bs-target="#submenu-7" aria-expanded="false"
                    aria-controls="submenu-7">
                    <span class="nav-icon">
                        <i class="fas fa-gavel"></i>
                    </span>
                    <span class="nav-link-text">Misconducts</span>
                    <span class="submenu-arrow">
                        <i class="fas fa-chevron-down bi"></i>
                    </span><!--//submenu-arrow-->
                </a><!--//nav-link-->
                <div id="submenu-7" class="collapse submenu submenu-7" data-bs-parent="#menu-accordion">
                    <ul class="submenu-list list-unstyled">
                        <li class="submenu-item"><a
                                class="submenu-link {{ Str::startsWith(request()->path(), 'admin/misconducts/active_cases') ? 'sidebar-active' : '' }}"
                                href="{{ route('activeMisconductCases') }}">Active</a>
                        </li>
                        <li class="submenu-item"><a
                                class="submenu-link {{ Str::startsWith(request()->path(), 'admin/misconducts/resolved_cases') ? 'sidebar-active' : '' }}"
                                href="
                            {{-- {{ route('resolvedMisconductCases') }} --}}
                             ">Resolved</a>
                        </li>
                    </ul>
                </div>
            </li><!--//nav-item-->
        @endif
        <li class="nav-item has-submenu">
            <a class="nav-link submenu-toggle {{ Str::startsWith(request()->path(), 'admin/elections') ? 'sidebar-active-section' : '' }}"
                href="#" data-bs-toggle="collapse" data-bs-target="#submenu-9" aria-expanded="false"
                aria-controls="submenu-9">
                <span class="nav-icon">
                    <i class="fas fa-fingerprint"></i>
                </span>
                {{-- @php
                    $totalElectionNotifications =
                        (int) countElectionsAwaitingApproval() +
                        (int) countElectionsPositionAwaitingApproval() +
                        (int) countElectionsApplicationsAwaitingApproval();
                @endphp --}}
                <span class="nav-link-text">Elections &nbsp;
                    {{-- @if ($totalElectionNotifications > 0)
                        <span class="position-relative top-0 translate-middle badge rounded-pill bg-red ml-1"
                            style="font-size: 11px">{{ $totalElectionNotifications }}
                    @endif --}}

                </span>
                <span class="submenu-arrow">
                    <i class="fas fa-chevron-down bi"></i>
                </span><!--//submenu-arrow-->
            </a><!--//nav-link-->
            <div id="submenu-9" class="collapse submenu submenu-9" data-bs-parent="#menu-accordion">
                <ul class="submenu-list list-unstyled">
                    <li class="submenu-item"><a
                            class="submenu-link {{ Str::startsWith(request()->path(), 'admin/elections') || Str::startsWith(request()->path(), 'admin/elections/view') ? 'sidebar-active' : '' }}"
                            href="
                            {{-- {{ route('allElections') }} --}}
                             ">Elections
                            {{-- &nbsp; @if ($totalElectionNotifications > 0)
                                <span class="position-relative top-0 translate-middle badge rounded-pill bg-red ml-1"
                                    style="font-size: 11px">{{ $totalElectionNotifications }} </span>
                            @endif --}}
                        </a>
                    </li>
                </ul>
            </div>
        </li><!--//nav-item-->

        {{-- @php
            $financeNotifications = countPendingPaymentMethodUpdates() + 0;
        @endphp --}}
        <li class="nav-item has-submenu">
            <a class="nav-link submenu-toggle {{ Str::startsWith(request()->path(), 'admin/payments') ? 'sidebar-active-section' : '' }}"
                href="#" data-bs-toggle="collapse" data-bs-target="#submenu-4" aria-expanded="false"
                aria-controls="submenu-4">
                <span class="nav-icon">
                    <i class="fas fa-credit-card"></i>
                </span>
                <span class="nav-link-text">Payment &nbsp;
                    {{-- @if ($financeNotifications > 0)
                        <span class="position-relative top-0 translate-middle badge rounded-pill bg-red ml-1"
                            style="font-size: 11px">{{ $financeNotifications }}</span>
                    @endif --}}
                </span>
                <span class="submenu-arrow">
                    <i class="fas fa-chevron-down"></i>
                </span><!--//submenu-arrow-->
            </a><!--//nav-link-->

            <div id="submenu-4" class="collapse submenu submenu-4" data-bs-parent="#menu-accordion">
                <ul class="submenu-list list-unstyled">
                    <li class="submenu-item"><a
                            class="submenu-link text-capitalize {{ Str::startsWith(request()->path(), 'admin/payments/transactions') ? 'sidebar-active' : '' }}"
                            href="
                            {{-- {{ route('paymentTransactionsHome') }} --}}
                             ">Transactions</a>
                    </li>
                    <li class="submenu-item"><a
                            class="submenu-link text-capitalize {{ Str::startsWith(request()->path(), 'admin/payments/settings') ? 'sidebar-active' : '' }}"
                            href="
                            {{-- {{ route('paymentSettingsPaymentMethods') }} --}}
                            ">Settings</a>
                    </li>

                    <li class="submenu-item"><a
                            class="submenu-link text-capitalize {{ Str::startsWith(request()->path(), 'admin/payments/payment_methods') ? 'sidebar-active' : '' }}"
                            href="
                            {{-- {{ route('adminPaymentSettingsPaymentMethods') }} --}}
                             ">Payment
                            Methods
                        </a>
                    </li>
                </ul>
            </div>
        </li><!--//nav-item-->
        <li class="nav-item has-submenu">
            <a class="nav-link submenu-toggle {{ Str::startsWith(request()->path(), 'admin/marketing') ? 'sidebar-active-section' : '' }}"
                href="#" data-bs-toggle="collapse" data-bs-target="#submenu-6" aria-expanded="false"
                aria-controls="submenu-6">
                <span class="nav-icon">
                    <i class="fas fa-envelope"></i>
                </span>
                <span class="nav-link-text">Notifications</span>
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
                             ">Email</a>
                    </li>
                </ul>
            </div>
        </li><!--//nav-item-->
        <li class="nav-item has-submenu">
            <a class="nav-link submenu-toggle {{ Str::startsWith(request()->path(), 'admin/academic_config') ? 'sidebar-active-section' : '' }}"
                href="#" data-bs-toggle="collapse" data-bs-target="#submenu-8" aria-expanded="false"
                aria-controls="submenu-8">
                <span class="nav-icon">
                    <i class="fas fa-university"></i>
                </span>
                <span class="nav-link-text">Academic Config</span>
                <span class="submenu-arrow">
                    <i class="fas fa-chevron-down"></i>
                </span><!--//submenu-arrow-->
            </a><!--//nav-link-->
            <div id="submenu-8" class="collapse submenu submenu-8" data-bs-parent="#menu-accordion">
                <ul class="submenu-list list-unstyled">
                    @if (Auth::user()->hasPermission('faculty.read'))
                        <li class="submenu-item"><a
                                class="submenu-link text-capitalize {{ Str::startsWith(request()->path(), 'admin/academic_config/faculties') ? 'sidebar-active' : '' }}"
                                href="{{ route('faculties') }}">Faculties</a>
                        </li>
                    @endif
                    @if (Auth::user()->hasPermission('department.read'))
                        <li class="submenu-item"><a
                                class="submenu-link text-capitalize {{ Str::startsWith(request()->path(), 'admin/academic_config/departments') ? 'sidebar-active' : '' }}"
                                href="
                            {{ route('departments') }}
                             ">Departments</a>
                        </li>
                    @endif
                    @if (Auth::user()->hasPermission('program.read'))
                        <li class="submenu-item"><a
                                class="submenu-link text-capitalize {{ Str::startsWith(request()->path(), 'admin/academic_config/programs') ? 'sidebar-active' : '' }}"
                                href="
                            {{ route('programs') }}
                             ">Programs</a>
                        </li>
                    @endif
                </ul>
            </div>
        </li><!--//nav-item-->

        <li class="nav-item has-submenu">
            <a class="nav-link submenu-toggle {{ Str::startsWith(request()->path(), 'admin/config') ? 'sidebar-active-section' : '' }}"
                href="#" data-bs-toggle="collapse" data-bs-target="#submenu-5" aria-expanded="false"
                aria-controls="submenu-5">
                <span class="nav-icon">
                    <i class="fas fa-cogs"></i>
                </span>
                <span class="nav-link-text">Config</span>
                <span class="submenu-arrow">
                    <i class="fas fa-chevron-down"></i>
                </span><!--//submenu-arrow-->
            </a><!--//nav-link-->
            <div id="submenu-5" class="collapse submenu submenu-5" data-bs-parent="#menu-accordion">
                <ul class="submenu-list list-unstyled">
                    @if (Auth::user()->hasPermission('basic_system_config.read'))
                        <li class="submenu-item"><a
                                class="submenu-link text-capitalize {{ Str::startsWith(request()->path(), 'admin/config/basics') ? 'sidebar-active' : '' }}"
                                href="
                            {{ route('configBasicSettings') }}
                             ">Basics</a>
                        </li>
                    @endif
                    @if (Auth::user()->hasPermission('roles.read'))
                        <li class="submenu-item"><a
                                class="submenu-link text-capitalize {{ Str::startsWith(request()->path(), 'admin/config/user_roles') ? 'sidebar-active' : '' }}"
                                href="{{ route('userRoles') }}">User Roles</a>
                        </li>
                    @endif
                    @if (Auth::user()->hasPermission('policy.read'))
                        <li class="submenu-item"><a
                                class="submenu-link text-capitalize {{ Str::startsWith(request()->path(), 'admin/config/policies') ? 'sidebar-active' : '' }}"
                                href="{{ route('systemPolicies') }}">Policies</a>
                        </li>
                    @endif

                    <li class="submenu-item"><a
                            class="submenu-link text-capitalize {{ Str::startsWith(request()->path(), 'admin/config/other_pages') ? 'sidebar-active' : '' }}"
                            href="
                            {{-- {{ route('settingsOtherPages') }} --}}
                            ">Other
                            Pages</a>
                    </li>
                    <li class="submenu-item"><a
                            class="submenu-link text-capitalize {{ Str::startsWith(request()->path(), 'admin/config/seo') ? 'sidebar-active' : '' }}"
                            href="
                            {{-- {{ route('systemSeoSettings') }} --}}
                             ">SEO</a>
                    </li>
                    <li class="submenu-item"><a
                            class="submenu-link text-capitalize {{ Str::startsWith(request()->path(), 'admin/config/google_analytics') ? 'sidebar-active' : '' }}"
                            href="
                        {{-- {{ route('googleAnalyticsSettings') }} --}}
                         ">Google
                            Analytics</a>
                    </li>
                    <li class="submenu-item"><a
                            class="submenu-link {{ Str::startsWith(request()->path(), 'admin/config/server') ? 'sidebar-active' : '' }}"
                            href="
                        {{-- {{ route('basicServerInfo') }} --}}
                         ">Server</a>
                    </li>
                    <li class="submenu-item"><a
                            class="submenu-link {{ Str::startsWith(request()->path(), 'admin/config/system') ? 'sidebar-active' : '' }}"
                            href="
                        {{-- {{ route('systemSettingsHome') }} --}}
                         ">System</a>
                    </li>
                </ul>
            </div>
        </li><!--//nav-item-->
        <li class="nav-item has-submenu">
            <a class="nav-link submenu-toggle {{ Str::startsWith(request()->path(), 'admin/help') ? 'sidebar-active-section' : '' }}"
                href="#" data-bs-toggle="collapse" data-bs-target="#submenu-10" aria-expanded="false"
                aria-controls="submenu-10">
                <span class="nav-icon">
                    <i class="fa-solid fa-circle-question"></i>
                </span>
                <span class="nav-link-text">Support
                </span>
                <span class="submenu-arrow">
                    <i class="fas fa-chevron-down bi"></i>
                </span><!--//submenu-arrow-->
            </a><!--//nav-link-->
            <div id="submenu-10" class="collapse submenu submenu-10" data-bs-parent="#menu-accordion">
                <ul class="submenu-list list-unstyled">
                    <li class="submenu-item"><a
                            class="submenu-link {{ Str::startsWith(request()->path(), 'admin/help/documentation') ? 'sidebar-active' : '' }}"
                            href="
                            {{-- {{ route('allElections') }} --}}
                             ">Documentation
                        </a>
                    </li>
                    <li class="submenu-item"><a
                            class="submenu-link {{ Str::startsWith(request()->path(), 'admin/help/chat') ? 'sidebar-active' : '' }}"
                            href="
                            {{-- {{ route('whatsAppSupportLines') }} --}}
                             ">WhatsApp
                            Chat
                        </a>
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
