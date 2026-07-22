<?php

namespace App\Http\Middleware;

use App\Models\Config\BasicSystemSetting;
use App\Models\Config\SystemPolicies\SystemPolicy;
use App\Models\Config\SystemPolicies\UserAgreedPolicy;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class AuthenticationMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        ############
        $response = $next($request);

        // Disable caching for HTML
        if ($response instanceof \Illuminate\Http\Response) {
            $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
            $response->headers->set('Pragma', 'no-cache');
        }

        // verify Mfa
        if (Auth::check()) {
            $user = Auth::user();
            $currentSessionId = Session::getId();
            $currentSession = DB::table('sessions')->where('id', $currentSessionId)->first();
            $userActiveSessions = DB::table('sessions')->where('user_id', $user->id)->count();
            if (!$currentSession) {
                $currentSession = (object) [
                    'id' => $currentSessionId,
                    'user_id' => $user->id,
                    'mfa_verified' => Session::get('mfa_verified', 0),
                    'login_otp' => Session::get('login_otp'),
                    'login_otp_expires_at' => Session::get('login_otp_expires_at'),
                ];
            }


            $basicDefaultSettings = BasicSystemSetting::first();

            $accountType = (int) $user->account_type;
            $accountStatus = (int) $user->account_status;
            $maintenanceModeLock = $basicDefaultSettings->maintenance_mode === true && $accountType !== 0;


            // session control
            $maintenanceStatus = 'waiting';
            $emailVerificationStatus = 'waiting';
            $mfaVerificationStatus = 'waiting';
            $policyGuard = 'waiting';
            $sessionLimitStatus = 'waiting';
            if ($accountType == 2 && $user->admin_privilege === false) {
                $sessionLimitStatus = 'cleared';
            }
            $profileSetupStatus = 'waiting';

            if ($accountType !== 2) {
                $profileSetupStatus = 'cleared';
            }
            // $freshAssociationAccountStatus = 'waiting';
            // if ($accountType !== 3) {
            //     $freshAssociationAccountStatus = 'cleared';
            // }
            // $awaitingVerificationStatus = 'waiting';
            // if (in_array($accountType, [0, 1])) {
            //     $awaitingVerificationStatus = 'cleared';
            // }

            // $disabledAccountStatus = 'waiting';


            // Maintenance mode
            if ($maintenanceModeLock === true) {
                if (
                    !Route::is('maintenanceMode') &&
                    !Route::is('logoutRequest')
                ) {
                    return redirect()->route('maintenanceMode');
                }
            } else {
                $maintenanceStatus = 'cleared';
            }

            // Account Email Verification
            if ($maintenanceStatus === 'cleared') {
                if ($user->email_verification !== true) {
                    if (
                        !Route::is('verifyUserEmail')
                        && !Route::is('logoutRequest')
                        && !Route::is('verifyUserEmailResendOtp')
                        && !Route::is('verifyUserEmailSubmit')
                    ) {
                        return redirect()->route('verifyUserEmail');
                    }
                } else {
                    $emailVerificationStatus = 'cleared';
                }
            }
            // Mfa verification
            if (
                $maintenanceStatus === 'cleared' &&
                $emailVerificationStatus === 'cleared'
            ) {
                if ((bool) $user->require_login_mfa === true) {
                    if (
                        (bool) $currentSession->mfa_verified !== true
                        || $currentSession->login_otp !== null
                        || $currentSession->login_otp_expires_at !== null
                    ) {
                        if (
                            !Route::is('authenticateLogin')
                            && !Route::is('logoutRequest')
                            && !Route::is('resendLoginOtp')
                            && !Route::is('verifyLoginOtp')
                            && !Route::is('verifyLoginOtpSubmit')
                        ) {
                            return redirect()->route('authenticateLogin');
                        }
                    } else {
                        $mfaVerificationStatus = 'cleared';
                    }
                } else {
                    $mfaVerificationStatus = 'cleared';
                }
            }


            // Policy Guard
            // ############# -> Init
            // // 0 - root, 1-admin , 2- student
            $applicableToUserColumn = null;

            // Determine the applicable column based on account type or admin privilege
            if (in_array($user->account_type, [0, 1]) || $user->admin_privilege) {
                $applicableToUserColumn = 'applicable_to_administrator';
            } elseif ($user->account_type == 2) {
                $applicableToUserColumn = 'applicable_to_member';
            }

            // Retrieve the active system policies that are applicable to the user
            $systemPolicies = SystemPolicy::where('is_active', true);

            if ($applicableToUserColumn) {
                $systemPolicies->where($applicableToUserColumn, true);
            }

            $systemPolicies = $systemPolicies->get();

            // Reverify the first functions
            // ############# -> Init

            if (
                $maintenanceStatus === 'cleared' &&
                $emailVerificationStatus === 'cleared' &&
                $mfaVerificationStatus === 'cleared' &&
                $policyGuard === 'waiting'


            ) {
                if (
                    $maintenanceStatus === 'cleared' &&
                    $emailVerificationStatus === 'cleared' &&
                    $mfaVerificationStatus === 'cleared' &&
                    $policyGuard === 'waiting'
                ) {
                    $hasUnagreedPolicy = false;

                    foreach ($systemPolicies as $policy) {
                        $reAgreementRequired = UserAgreedPolicy::where('username', $user->username)
                            ->where('policy', $policy->slug)
                            ->where('agreement_status', '!=', 'agreed')
                            ->exists();

                        $agreementRecordNotFound = !UserAgreedPolicy::where('username', $user->username)
                            ->where('policy', $policy->slug)
                            ->exists();

                        if ($reAgreementRequired || $agreementRecordNotFound) {
                            $hasUnagreedPolicy = true;

                            if (
                                !Route::is('manageSystemPolicyAgreement') &&
                                !Route::is('userAgreeToSystemPolicy') &&
                                !Route::is('logoutRequest')
                            ) {
                                return redirect()->route('manageSystemPolicyAgreement');
                            }

                            break;
                        }
                    }

                    if (!$hasUnagreedPolicy) {
                        $policyGuard = 'cleared';
                    }
                }
            }



            // Session Management
            // Reverify the first functions
            if (
                $maintenanceStatus === 'cleared' &&
                $emailVerificationStatus === 'cleared' &&
                $mfaVerificationStatus === 'cleared' &&
                $policyGuard === 'cleared' &&
                $sessionLimitStatus === 'waiting'
            ) {
                if (
                    $userActiveSessions > 1
                ) {
                    if (
                        !Route::is('manageSession')
                        && !Route::is('logoutRequest')
                        && !Route::is('endOtherSessions')
                    ) {
                        return redirect()->route('manageSession');
                    }
                } else {
                    $sessionLimitStatus = 'cleared';
                }
            }

            // Fresh student account
            // Reverify the first functions
            if (
                $maintenanceStatus === 'cleared' &&
                $emailVerificationStatus === 'cleared' &&
                $mfaVerificationStatus === 'cleared' &&
                $policyGuard === 'cleared' &&
                $sessionLimitStatus === 'cleared' &&
                $profileSetupStatus === 'waiting'

            ) {
                if ($accountType == 2 && $accountStatus == 0) {
                    if (
                        !Route::is('memberCreatePersonalProfile')
                        && !Route::is('studentAccountSetup')
                        && !Route::is('memberSelfCreatePersonalProfileSubmit')
                        // && !Route::is('studentSubmitAccountForVerification')
                        && !Route::is('logoutRequest')
                    ) {
                        return redirect()->route('studentAccountSetup');
                    }
                } else {
                    $profileSetupStatus = 'cleared';
                }
            }

            // // Awaiting Verification
            // // Reverify the first functions
            // if (
            //     $maintenanceStatus === 'cleared' &&
            //     $emailVerificationStatus === 'cleared' &&
            //     $mfaVerificationStatus === 'cleared' &&
            //     $policyGuard === 'cleared' &&
            //     $sessionLimitStatus === 'cleared' &&
            //     $profileSetupStatus === 'cleared'

            // ) {
            //     if ($accountType == 2 && $accountStatus == 1) {
            //         // Student
            //         if (
            //             !Route::is('studentAccountAwaitingVerification')
            //             && !Route::is('logoutRequest')
            //         ) {
            //             return redirect()->route('studentAccountAwaitingVerification');
            //         }
            //     }
            //     // Association
            //     elseif ($accountType == 3 && $accountStatus == 1) {
            //         if (
            //             !Route::is('associationAccountAwaitingVerification')
            //             && !Route::is('logoutRequest')
            //         ) {
            //             return redirect()->route('associationAccountAwaitingVerification');
            //         }
            //     }
            //     $awaitingVerificationStatus = 'cleared';
            // }
            // // Disabled Account
            // // Reverify the first functions
            // if (
            //     $maintenanceStatus === 'cleared' &&
            //     $emailVerificationStatus === 'cleared' &&
            //     $mfaVerificationStatus === 'cleared' &&
            //     $policyGuard === 'cleared' &&
            //     $sessionLimitStatus === 'cleared' &&
            //     $profileSetupStatus === 'cleared' &&
            //     $awaitingVerificationStatus === 'cleared'

            // ) {
            //     if ($accountType == 2 && $accountStatus == 3) {
            //         if (
            //             !Route::is('disabledUserAccount')
            //             && !Route::is('logoutRequest')
            //         ) {
            //             return redirect()->route('disabledUserAccount');
            //         }
            //     } else {
            //         $disabledAccountStatus = 'cleared';
            //     }
            // }

            // // Fresh association account
            // // Reverify the first functions
            // if (
            //     $maintenanceStatus === 'cleared' &&
            //     $emailVerificationStatus === 'cleared' &&
            //     $mfaVerificationStatus === 'cleared' &&
            //     $policyGuard === 'cleared' &&
            //     $sessionLimitStatus === 'cleared' &&
            //     $profileSetupStatus === 'cleared' &&
            //     $awaitingVerificationStatus === 'cleared' &&
            //     $disabledAccountStatus === 'cleared'

            // ) {
            //     if ($accountType == 3 && $accountStatus == 0) {
            //         if (
            //             !Route::is('associationAccountSetup')
            //             && !Route::is('registerAssociationProfileSubmit')
            //             && !Route::is('registerPresidentRemarkSubmit')
            //             && !Route::is('registerNewExecutivesSubmit')
            //             && !Route::is('registerPastExecutivesSubmit')
            //             && !Route::is('registerCurrentPatronsSubmit')
            //             && !Route::is('registerPastPatronsSubmit')
            //             && !Route::is('registerSocialMediaPlatformsSubmit')
            //             && !Route::is('registerAssociationOnlinePaymentMethodSubmit')
            //             && !Route::is('registerAssociationOfflinePaymentSubmit')
            //             && !Route::is('registerAssociationAlumniPurseSubmit')
            //             && !Route::is('registerAssociatioNAwardsSubmit')
            //             && !Route::is('associationSubmitAccountForVerification')
            //             && !Route::is('logoutRequest')

            //             // policy agreement
            //             && !Route::is('manageSystemPolicyAgreement')
            //             && !Route::is('userAgreeToSystemPolicy')
            //         ) {
            //             return redirect()->route('associationAccountSetup');
            //         }
            //     } else {
            //         $freshAssociationAccountStatus = 'cleared';
            //     }
            // }
        }

        return $response;
    }
}
