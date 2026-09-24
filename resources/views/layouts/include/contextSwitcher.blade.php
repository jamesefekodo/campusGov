 @php
     $userType = getUserType();

     $user = Auth::user();

     $canSwitchContext =
         Auth::check() && (int) $user->account_type === 2 && (bool) $user->admin_privilege && $user->roles()->exists();

     //  all notifications
     $allNotifications =
         countMemberProfilesAwaitingVerification() +
         countPendingReAdmissionRequests() +
         totalElectionsAwaitingApproval() +
         totalElectionCandidatesAwaitingApproval() +
         totalOngoingElection() +
         totalElectionsAwaitingApproval() +
         totalElectionCandidatesAwaitingApproval() +
         totalOngoingElection() +

        //  publications
        countMediaPublicationsPendingApproval() +
        countMyDraftedMediaPublications();

     if ($allNotifications > 0) {
         $showAdminNotification = true;
     } else {
         $showAdminNotification = false;
     }
 @endphp


 @if ($canSwitchContext)
     <div class="dropdown">

         <button
             class="btn btn-sm dropdown-toggle w-100 bg-secondary text-white
                {{-- {{ $userType === 'admin' ? 'btn-primary' : 'btn-outline-primary' }} --}}
                 "
             type="button" data-bs-toggle="dropdown">
             @if ($userType === 'admin')
                 <i class="fas fa-user-shield me-1"></i>
                 Administrator 
             @else
                 <i class="fas fa-user me-1"></i>
                 Personal Functions
             @endif
             &nbsp; &nbsp;
             @if ($showAdminNotification == true)
                 <span class="position-relative top-0 translate-middle badge rounded-pill bg-red ml-1"
                     style="font-size: 11px"><i class="fas fa-info"></i></span>
             @endif
         </button>

         <ul class="dropdown-menu dropdown-menu-end shadow">

             <li>
                 <form method="POST" action="{{ route('user.context.switch', 'personal') }}">
                     @csrf

                     <button type="submit"
                         class="dropdown-item {{ $userType === 'member' ? 'bg-blue text-white' : 'bg-secondary-subtle text-black' }}">
                         <i class="fas fa-user me-2"></i>
                         Personal Portal

                         @if ($userType === 'member')
                             <i class="fas fa-check float-end mt-1"></i>
                         @endif
                     </button>
                 </form>
             </li>

             <li>
                 <form method="POST" action="{{ route('user.context.switch', 'admin') }}">
                     @csrf

                     <button type="submit"
                         class="dropdown-item {{ $userType === 'admin' ? 'bg-blue text-white' : 'bg-secondary-subtle text-black' }}">
                         <i class="fas fa-user-shield me-2"></i>
                         Administrator Portal

                         @if ($userType === 'admin')
                             <i class="fas fa-check float-end mt-1"></i>
                         @endif

                         &nbsp; &nbsp;
                         @if ($showAdminNotification == true)
                             <span class="position-relative top-0 translate-middle badge rounded-pill bg-red ml-1"
                                 style="font-size: 11px"><i class="fas fa-info"></i></span>
                         @endif
                     </button>
                 </form>
             </li>

         </ul>
     </div>
 @endif
