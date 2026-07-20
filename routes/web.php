<?php

use App\Http\Controllers\Authentication\AuthController;
use App\Http\Controllers\Config\AcademicConfigController;
use App\Http\Controllers\Config\PermissionAccessController;
use App\Http\Controllers\Config\SystemConfigurationController;
use App\Http\Controllers\Config\SystemPolicyController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Frontend\FrontendController;
use App\Http\Controllers\Member\MemberSelfProfileController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

Route::get('/clear-all', function () {
    Artisan::call('config:clear');
    Artisan::call('cache:clear');
    Artisan::call('route:clear');
    return 'All cleared!';
});

// basic/general routes
Route::middleware(['authenticationMiddlewares'])->group(function () {

    Route::get('/', function () {
        return redirect()->route('homepage');
    });
    Route::get('/home', [FrontendController::class, 'homepage'])->name('homepage');

    Route::get('register', [AuthController::class, 'register'])->name('registrationPage');
    Route::post('register/submit', [AuthController::class, 'submitUserRegistrationRequest'])->name('submitUserRegistrationRequest');
    Route::get('login', [AuthController::class, 'login'])->name('loginPage');
    Route::post('login/submit', [AuthController::class, 'authLogin'])->name('submitLoginRequest');
    Route::get('logout', [AuthController::class, 'AuthLogout'])->name('logoutRequest');
    Route::get('forgot_password', [AuthController::class, 'forgotPassword'])->name('forgotPassword');
    Route::post('forgot_password/send_password_reset_link', [AuthController::class, 'requestPasswordResetLink'])->name('requestPasswordResetLink');
    Route::get('reset_password/{token}', [AuthController::class, 'passwordResetPage'])->name('passwordResetLink');
    Route::post('reset_password/change_password/{token}', [AuthController::class, 'submitPasswordResetRequest'])->name('submitPasswordResetRequest');

    Route::get('maintenance_mode', [AuthController::class, 'maintenanceMode'])->name('maintenanceMode');

    Route::get('verify_email', [AuthController::class, 'verifyUserEmail'])->name('verifyUserEmail');
    Route::get('verify_email/resend_otp', [AuthController::class, 'resendEmailVerificationCodeEmailtoUser'])->name('verifyUserEmailResendOtp');
    Route::post('verify_emal/submit', [AuthController::class, 'verifyUserEmailSubmit'])->name('verifyUserEmailSubmit');

    Route::get('login/authenticate', [AuthController::class, 'authenticateLogin'])->name('authenticateLogin');
    Route::post('login/authenticate/submit', [AuthController::class, 'verifyLoginOtpSubmit'])->name('verifyLoginOtpSubmit');
    Route::get('login/authenticate/resend_otp', [AuthController::class, 'resendLoginOtp'])->name('resendLoginOtp');

    // Policy Agreement
    Route::get('system/configuration/policies_and_terms/user/manage', [SystemPolicyController::class, 'manageSystemPolicyAgreement'])->name('manageSystemPolicyAgreement');
    Route::post('system/configuration/policies_and_terms/user/agree', [SystemPolicyController::class, 'userAgreeToSystemPolicy'])->name('userAgreeToSystemPolicy');

    // session management
    Route::get('manage_sessions', [AuthController::class, 'manageSession'])->name('manageSession');
    Route::get('end_other_sessions', [AuthController::class, 'endOtherSessions'])->name('endOtherSessions');
});

Route::middleware(['authenticationMiddlewares', 'studentMiddlewares'])->group(function () {
    Route::get('member/dashboard', [DashboardController::class, 'dashboard'])->name('memberDashboard');

    // profile setup
    Route::get('student/account_setup', [MemberSelfProfileController::class, 'studentAccountSetup'])->name('studentAccountSetup');
});



Route::middleware(['authenticationMiddlewares', 'adminMiddlewares'])->group(function () {
    Route::get('admin/dashboard', [DashboardController::class, 'dashboard'])->name('adminDashboard');



    // academic config
    Route::get('admin/academic_config/faculties', [AcademicConfigController::class, 'faculties'])->name('faculties');
    Route::get('admin/academic_config/faculties/search', [AcademicConfigController::class, 'faculties'])->name('facultiesSearch');
    Route::post('admin/academic_config/faculties/add/submit', [AcademicConfigController::class, 'addFacultySubmit'])->name('addFacultySubmit');
    Route::get('admin/academic_config/faculties/view/{uuid}', [AcademicConfigController::class, 'viewFaculty'])->name('viewFaculty');
    Route::put('admin/academic_config/faculties/edit/submit/{uuid}', [AcademicConfigController::class, 'updateFacultySubmit'])->name('updateFacultySubmit');
    Route::post('admin/academic_config/faculties/head/submit', [AcademicConfigController::class, 'addFacultyDeanSubmit'])->name('addFacultyDeanSubmit');
    Route::put('admin/academic_config/faculties/head/update/submit', [AcademicConfigController::class, 'editFacultyDeanSubmit'])->name('editFacultyDeanSubmit');
    Route::put('admin/academic_config/faculties/head/end_tenure/submit', [AcademicConfigController::class, 'editEndFacultyDeanTenureSubmit'])->name('editEndFacultyDeanTenureSubmit');
    Route::get('admin/academic_config/faculties/past_heads/{uuid}', [AcademicConfigController::class, 'pastFacultyHeads'])->name('pastFacultyHeads');







    // basic system configuration
    Route::get('admin/config/basics', [SystemConfigurationController::class, 'viewSystemSettings'])->name('configBasicSettings');
    Route::put('admin/config/basics/maintenance_mode/update', [SystemConfigurationController::class, 'updateMaintenanceMode'])->name('updateMaintenanceMode');
    Route::put('admin/config/basics/system_info/update', [SystemConfigurationController::class, 'updateBasicSystemInfoUpdate'])->name('updateBasicSystemInfoUpdate');
    Route::put('admin/config/basics/email_config/update', [SystemConfigurationController::class, 'updateEmailConfigSubmit'])->name('updateEmailConfigSubmit');

    // policy setup
    Route::get('admin/config/policies', [SystemPolicyController::class, 'systemPolicies'])->name('systemPolicies');
    Route::get('admin/config/policies/search', [SystemPolicyController::class, 'systemPolicies'])->name('systemPoliciesSearch');
    Route::post('admin/config/policies/create/submit', [SystemPolicyController::class, 'systemAddNewSystemPolicySubmities'])->name('addNewSystemPolicySubmit');
    Route::get('admin/config/policies/view/{slug}', [SystemPolicyController::class, 'viewPolicy'])->name('viewPolicy');
    Route::put('admin/config/policies/update/{slug}', [SystemPolicyController::class, 'updatePolicySubmit'])->name('updatePolicySubmit');
    Route::get('admin/config/policies/delete/{slug}', [SystemPolicyController::class, 'deletePolicy'])->name('deletePolicy');




    // user roles and permissions
    Route::get('admin/config/user_roles', [PermissionAccessController::class, 'viewUserRoles'])->name('userRoles');
    Route::get('admin/config/user_roles/search', [PermissionAccessController::class, 'viewUserRoles'])->name('rolesSearch');
    Route::post('admin/config/user_roles/add/submit', [PermissionAccessController::class, 'addNewRoleSubmit'])->name('addNewRoleSubmit');
    Route::get('admin/config/user_roles/view/{uuid}', [PermissionAccessController::class, 'viewRole'])->name('viewRole');
    Route::put('admin/config/user_roles/update/{uuid}', [PermissionAccessController::class, 'updateRoleSubmit'])->name('updateRoleSubmit');
    Route::get('admin/config/user_roles/delete/{uuid}', [PermissionAccessController::class, 'deleteRole'])->name('deleteRole');
});
