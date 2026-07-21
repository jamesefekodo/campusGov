<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MemberSelfProfileController extends Controller
{
    public function studentAccountSetup(){
       $member = Auth::user();

       $data = [
        'member' => $member,
       ];
       return view('users.member.registration.registrationIndex', compact('data'));
    }
}
