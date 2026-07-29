<div class="app-header-inner">
    <div class="container-fluid py-2">
        <div class="app-header-content">
            <div class="row justify-content-between align-items-center">

                <div class="col-auto">
                    <a id="sidepanel-toggler" class="sidepanel-toggler d-inline-block d-xl-none" href="#">
                        <i class="fas fa-bars"></i>
                    </a>
                </div><!--//col-->
                {{-- <div class="search-mobile-trigger d-sm-none col">
                    <i class="search-mobile-trigger-icon fa-solid fa-magnifying-glass"></i>
                </div><!--//col-->
                <div class="app-search-box col">
                    <form class="app-search-form">
                        <input type="text" placeholder="Search..." name="search" class="form-control search-input">
                        <button type="submit" class="btn search-btn btn-primary" value="Search"><i
                                class="fa-solid fa-magnifying-glass"></i></button>
                    </form>
                </div><!--//app-search-box--> --}}

                <div class="app-utilities col-auto">
                    {{-- <div class="app-utility-item">
                        <a href="
                            #
                             "
                            title="Cart">
                            <i class="fas fa-bell"></i>
                            &nbsp;
                        </a>
                    </div> --}}
                    <!--//app-utility-item-->
                    <div class="app-utility-item">
                        @switch(getUserType())
                            @case('association')
                            @case('association_admin')
                                Reg->
                                <a href="#" title="Balance">
                                    ₦{{ number_format(getCurrentAssociation()->associationProfile->regular_account_balance, 2) }}
                                </a>
                                ::Alu
                                <a href="#" title="Balance">
                                    ₦{{ number_format(getCurrentAssociation()->associationProfile->alumni_account_balance, 2) }}
                                </a>
                            @break

                            @default
                        @endswitch

                    </div><!--//app-utility-item-->



                    <div class="app-utility-item app-user-dropdown dropdown">
                        <a class="dropdown-toggle" id="user-dropdown-toggle" data-bs-toggle="dropdown" href="#"
                            role="button" aria-expanded="false">

                            @php
                                $user = Auth::user();
                                $profileRoute = 'adminDashboard';
                                $image = asset('system/images/default_profile_pic.webp');

                                if (getUserType() == 'root' || getUserType() == 'admin') {
                                    $profileRoute = 'adminDashboard';
                                    $image = associationLogo();
                                }

                                if (getUserType() == 'member') {
                                    $profileRoute = 'memberViewSelfProfile';

                                    if (
                                        $user->photo &&
                                        file_exists(public_path('assets/uploads/users/members/photo/' . $user->photo))
                                    ) {
                                        $image = asset('assets/uploads/users/members/photo/' . $user->photo);
                                    }
                                }
                            @endphp



                            <img src="{{ $image }}" alt="{{ $user->username }}" width="30px">



                        </a>
                        <ul class="dropdown-menu" aria-labelledby="user-dropdown-toggle">



                            <li><a class="dropdown-item"
                                    href="
                                {{ route($profileRoute) }}
                                 ">Manage
                                    Profile</a>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li class="bg-red text-white"><a class="dropdown-item text-white"
                                    href="
                                {{ route('logoutRequest') }}
                                 ">Logout</a>
                            </li>
                        </ul>
                    </div><!--//app-user-dropdown-->
                </div><!--//app-utilities-->
            </div><!--//row-->
        </div><!--//app-header-content-->
    </div><!--//container-fluid-->
</div><!--//app-header-inner-->
