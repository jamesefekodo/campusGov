<?php

namespace App\Http\Controllers\Config;

use App\Http\Controllers\Controller;
use App\Models\Config\SystemPolicies\SystemPolicy;
use App\Models\Config\SystemPolicies\UserAgreedPolicy;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class SystemPolicyController extends Controller
{
    public function systemPolicies(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('policy.read'),
            403
        );

        $requestType = '';
        $searchKey = '';
        $pageTitle = '';
        $systemPolicies = '';


        if (Route::currentRouteName() == 'systemPoliciesSearch') {
            $searchKey = $request->input('searchKey');
            $systemPolicies = SystemPolicy::where('title', 'like', "%$searchKey%")
                ->orWhere('policy_statement', 'like', "%$searchKey%")
                ->orWhere('applicable_to_member', 'like', "%$searchKey%")
                ->orWhere('applicable_to_administrator', 'like', "%$searchKey%")
                ->orderBy('title')
                ->paginate(20)->withQueryString();

            $requestType = 'search';
            $pageTitle = 'Search Results :: ' . $searchKey;
        } else {
            $systemPolicies = SystemPolicy::orderBy('title')->paginate(20);
            $requestType = 'all';
            $pageTitle = 'System Policies';
        }

        $data = [
            'systemPolicies' => $systemPolicies,
            'requestType' => $requestType,
            'searchKey' => $searchKey,
            'pageTitle' => $pageTitle,
        ];
        return view('users.admin.config.systemPolicy.systemPolicies', compact('data'));
    }
    public function systemAddNewSystemPolicySubmities(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('policy.create'),
            403
        );

        $request->validate([
            'title' => 'required',
            'policy_statement' => 'required',
            'applicable_to_member' => 'required',
            'applicable_to_administrator' => 'required',
        ]);

        $policy = new SystemPolicy();
        $policy->title = $request->title;
        $policy->policy_statement = $request->policy_statement;
        $policy->applicable_to_member = $request->applicable_to_member;
        $policy->applicable_to_administrator = $request->applicable_to_administrator;
        $policy->save();

        return redirect()->back()->with('success', 'Done!');
    }
    public function viewPolicy($slug)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('policy.read'),
            403
        );

        $policy = SystemPolicy::where('slug', $slug)->first();
        if (!$policy) {
            return redirect()->back()->with('error', 'Policy not found!');
        }

        $data = [
            'policy' => $policy,
        ];
        return view('users.admin.config.systemPolicy.viewSystemPolicy', compact('data'));
    }
    public function updatePolicySubmit(Request $request, $slug)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('policy.update'),
            403
        );

        $request->validate([
            'title' => 'required',
            'policy_statement' => 'required',
            'is_active' => 'required',
            'applicable_to_member' => 'required',
            'applicable_to_administrator' => 'required',
        ]);



        $policy = SystemPolicy::where('slug', $slug)->first();
        if (!$policy) {
            return redirect()->back()->with('error', 'Policy not found!');
        }

        $existingAgreements = UserAgreedPolicy::where('policy', $policy->slug)->get();
        $policy->title = trim($request->title);
        $policy->policy_statement = $request->policy_statement;
        $policy->is_active = $request->is_active;
        $policy->applicable_to_member = $request->applicable_to_member;
        $policy->applicable_to_administrator = $request->applicable_to_administrator;
        $policy->save();


        if ($existingAgreements) {
            foreach ($existingAgreements as $agreement) {
                $agreement->agreement_status = 'updated';
                $agreement->save();
            }
        }

        return redirect()->back()->with('success', 'Done!');
    }
    public function deletePolicy($slug)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('policy.delete'),
            403
        );

        $policy = SystemPolicy::where('slug', $slug)->first();
        if (!$policy) {
            return redirect()->back()->with('error', 'Policy not found!');
        }

        $policy->policyAgreements()->delete();
        $policy->delete();

        return redirect()->route('systemPolicies')->with('success', 'Done!');
    }
    public function manageSystemPolicyAgreement()
    {
        $user = Auth::user();
        $applicableToUserColumn = null;

        // Determine the applicable column based on account type or admin privilege
        if (in_array($user->account_type, [0, 1,90]) || $user->admin_privilege) {
            $applicableToUserColumn = 'applicable_to_administrator';
        } elseif ($user->account_type == 2) {
            $applicableToUserColumn = 'applicable_to_member';
        }

        // Retrieve the active system policies that are applicable to the user
        $systemPolicies = SystemPolicy::where('is_active', true)
            ->where($applicableToUserColumn, 1)
            ->get();

        // Retrieve the user's agreed policies
        $userAgreedPolicies = UserAgreedPolicy::where('username', $user->username)
            ->get()
            ->keyBy('policy'); // Key by policy slug for easier lookup

        $newPolicies = [];
        $updatedPolicies = [];
        $agreedPolicies = [];

        foreach ($systemPolicies as $policy) {
            $userAgreement = $userAgreedPolicies->get($policy->slug);

            if (!$userAgreement) {
                // Policy is in SystemPoliciesAndTerms but not agreed to by the user
                $newPolicies[] = $policy;
            } elseif ($userAgreement->agreement_status == 'updated') {
                // Policy is agreed but updated
                $updatedPolicies[] = $policy;
            } elseif ($userAgreement->agreement_status == 'agreed') {
                // Policy is agreed by the user
                $agreedPolicies[] = $policy;
            }
        }


        // Prepare data for view
        $data = [
            'newPolicies' => $newPolicies,
            'updatedPolicies' => $updatedPolicies,
            'agreedPolicies' => $agreedPolicies,
        ];

        return view('miscellaneous.policyAgreement.agreementIndex', compact('data'));
    }
    public function userAgreeToSystemPolicy(Request $request)
    {
        $request->validate([
            'policy' => 'required|exists:system_policies,slug',
            'agreement_status' => 'required|in:agreed',
        ]);

        $user = Auth::user();
        $today = Carbon::now()->toDateString();

        $policyOnceAgreed = UserAgreedPolicy::where('username', $user->username)->where('policy', $request->policy)->first();
        if ($policyOnceAgreed) {
            // Update the agreement
            $agreement = $policyOnceAgreed;
            $agreement->username = $user->username;
            $agreement->policy = $request->policy;
            $agreement->agreement_status = $request->agreement_status;
            $agreement->agreement_date = $today;
            $agreement->save();
        } else {
            // New agreement
            $agreement = new UserAgreedPolicy();
            $agreement->username = $user->username;
            $agreement->policy = $request->policy;
            $agreement->agreement_status = $request->agreement_status;
            $agreement->agreement_date = $today;
            $agreement->save();
        }
        return redirect()->back()->with('success', 'Done!');
    }
}
