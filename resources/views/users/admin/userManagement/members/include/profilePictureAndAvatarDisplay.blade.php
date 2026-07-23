 @php
     $initials = '';

     if ($user->memberPersonalProfile->first_name && $user->memberPersonalProfile->last_name) {
         $initials = strtoupper(substr($user->memberPersonalProfile->first_name, 0, 1) . substr($user->memberPersonalProfile->last_name, 0, 1));
     } elseif ($user->username) {
         $initials = strtoupper(substr($user->username, 0, 1));
     } else {
         $initials = 'U';
     }
 @endphp

 @php
     $colors = [
         ['#4e73df', '#224abe'], // blue
         ['#1cc88a', '#13855c'], // green
         ['#e74a3b', '#be2617'], // red
         ['#f6c23e', '#dda20a'], // yellow
         ['#36b9cc', '#258391'], // cyan
         ['#6f42c1', '#4e2d99'], // purple
         ['#fd7e14', '#c96209'], // orange
         ['#e83e8c', '#b8216a'], // pink
         ['#20c9a6', '#169a80'], // teal
         ['#5a5c69', '#373840'], // dark gray
     ];

     // Pick color based on employee id or name so it stays consistent
     $colorIndex = crc32($user->memberPersonalProfile->first_name . $user->memberPersonalProfile->last_name) % count($colors);
     $colorIndex = abs($colorIndex); // ensure positive
     $color = $colors[$colorIndex];

     $initials = strtoupper(substr($user->memberPersonalProfile->first_name, 0, 1) . substr($user->memberPersonalProfile->last_name, 0, 1));
 @endphp



 @if ($user->photo)
     <img src="{{ asset('assets/uploads/users/members/photo/' . $user->photo) }}" class="rounded-circle border"
         width="38" height="38" style="object-fit: cover;">
 @else
     <div class="avatar-initials"
         style="background: linear-gradient(135deg, {{ $color[0] }} 0%, {{ $color[1] }} 100%)">
         {{ $initials }}
     </div>
 @endif

 <style>
     /* 2. AVATAR STYLING */
     .avatar-initials {
         width: 38px;
         height: 38px;
         color: white;
         border-radius: 50%;
         display: flex;
         align-items: center;
         justify-content: center;
         font-weight: 600;
         font-size: 0.85rem;
         border: 2px solid #fff;
         box-shadow: 0 0 0 1px #e3e6f0;
     }
 </style>
