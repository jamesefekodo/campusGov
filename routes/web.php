<?php

use App\Http\Controllers\Authentication\AuthController;
use App\Http\Controllers\Config\AcademicConfigController;
use App\Http\Controllers\Config\PermissionAccessController;
use App\Http\Controllers\Config\SystemConfigurationController;
use App\Http\Controllers\Config\SystemPolicyController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Frontend\FrontendController;
use App\Http\Controllers\Member\MemberSelfProfileController;
use App\Http\Controllers\UserManagement\MembersManagementController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

Route::get('/clear-all', function () {
    Artisan::call('config:clear');
    Artisan::call('cache:clear');
    Artisan::call('route:clear');
    return 'All cleared!';
});


// general routes (no middleware).... jqajax, callbacks and so on
Route::get('/fetch-program-types', [AcademicConfigController::class, 'fetchProgramTypes'])
    ->name('fetchProgramTypes');
Route::get('/fetch-departments', [AcademicConfigController::class, 'fetchDepartments'])
    ->name('fetchDepartments');

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
    Route::get('disabled_account', [AuthController::class, 'disabledUserAccount'])->name('disabledUserAccount');
});

// member routes

Route::middleware(['authenticationMiddlewares', 'studentMiddlewares'])->group(function () {
    Route::get('member/dashboard', [DashboardController::class, 'dashboard'])->name('memberDashboard');

    // profile setup
    Route::get('member/account_setup', [MemberSelfProfileController::class, 'studentAccountSetup'])->name('studentAccountSetup');
    Route::get('member/account_setup/personal_profile', [MemberSelfProfileController::class, 'memberCreatePersonalProfile'])->name('memberCreatePersonalProfile');
    Route::post('member/account_setup/personal_profile/submit', [MemberSelfProfileController::class, 'memberSelfCreatePersonalProfileSubmit'])->name('memberSelfCreatePersonalProfileSubmit');

    Route::get('member/account_setup/academic_info', [MemberSelfProfileController::class, 'memberCreateAcademicInfo'])->name('memberCreateAcademicInfo');
    Route::get('member/account_setup/academic_info/2', [MemberSelfProfileController::class, 'memberCreateAcademicInfo'])->name('memberCreateAcademicInfoStep2');
    Route::post('member/account_setup/academic_info/submit', [MemberSelfProfileController::class, 'memberSelfCreateAcademicInfoSubmit'])->name('memberSelfCreateAcademicInfoSubmit');
    Route::get('member/account_setup/submit_for_verification', [MemberSelfProfileController::class, 'memberSubmitSelfAccountForVerification'])->name('memberSubmitSelfAccountForVerification');

    Route::get('member/account_setup/awaiting_verification', [MemberSelfProfileController::class, 'awaitingVerification'])->name('memberAccountAwaitingVerification');

    Route::get('member/my_profile', [MemberSelfProfileController::class, 'memberViewSelfProfile'])->name('memberViewSelfProfile');
    Route::get('member/my_profile/personal_profile', [MemberSelfProfileController::class, 'myPersonalProfile'])->name('myPersonalProfile');
    Route::get('member/my_profile/personal_profile/edit', [MemberSelfProfileController::class, 'memberEditPersonalProfile'])->name('memberEditPersonalProfile');
    Route::put('member/my_profile/personal_profile/update', [MemberSelfProfileController::class, 'memberSelfCreatePersonalProfileSubmit'])->name('memberEditPersonalProfileSubmit');

    Route::get('member/my_profile/academic_info', [MemberSelfProfileController::class, 'myAcademicInfo'])->name('myAcademicInfo');

    Route::get('member/my_profile/academic_history', [MemberSelfProfileController::class, 'myAcademicHistory'])->name('myAcademicHistory');
    Route::post('member/my_profile/academic_history/add_new/submit', [MemberSelfProfileController::class, 'addNewAcademicRecordSubmit'])->name('addNewAcademicRecordSubmit');
    Route::put('member/my_profile/academic_history/update/{id}/submit', [MemberSelfProfileController::class, 'editAcademicRecordSubmit'])->name('editAcademicRecordSubmit');
    Route::get('member/my_profile/academic_history/delete/{id}', [MemberSelfProfileController::class, 'deleteAcademicRecordSubmit'])->name('deleteAcademicRecordSubmit');

    Route::get('member/my_profile/skills', [MemberSelfProfileController::class, 'myAcademicSkills'])->name('myAcademicSkills');
    Route::post('member/my_profile/skills/add_new', [MemberSelfProfileController::class, 'addSkillSubmit'])->name('addSkillSubmit');
    Route::put('member/my_profile/skills/update/{id}/submit', [MemberSelfProfileController::class, 'editSkillSubmit'])->name('editSkillSubmit');
    Route::get('member/my_profile/skills/{id}/delete', [MemberSelfProfileController::class, 'memberSelfDeleteSkill'])->name('memberSelfDeleteSkill');

    Route::get('member/my_profile/business', [MemberSelfProfileController::class, 'myBusiness'])->name('myBusiness');
});



// administrators routes
Route::middleware(['authenticationMiddlewares', 'adminMiddlewares'])->group(function () {
    Route::get('admin/dashboard', [DashboardController::class, 'dashboard'])->name('adminDashboard');


    // user management
    // students
    Route::get('admin/user_management/members/students', [MembersManagementController::class, 'studentMembers'])->name('studentMembers');
    Route::get('admin/user_management/members/students/search', [MembersManagementController::class, 'studentMembers'])->name('studentMembersSearch');


    Route::put('admin/user_management/members/verification/return_for_correction/{uuid}', [MembersManagementController::class, 'updateBasicAccountInfo'])->name('updateBasicAccountInfo');


    // awaiting verification
    Route::get('admin/user_management/members/verification', [MembersManagementController::class, 'memberAwaitingVerification'])->name('memberAwaitingVerification');
    Route::get('admin/user_management/members/verification/search', [MembersManagementController::class, 'memberAwaitingVerification'])->name('memberAwaitingVerificationSearch');
    Route::get('admin/user_management/members/verification/index/{uuid}', [MembersManagementController::class, 'viewMemberAwaitingVerificationIndex'])->name('viewMemberAwaitingVerificationIndex');
    Route::get('admin/user_management/members/verification/return_for_correction/{uuid}', [MembersManagementController::class, 'returnProfileFromVerToReg'])->name('returnProfileFromVerToReg');
    Route::get('admin/user_management/members/verification/personal_profile/{uuid}', [MembersManagementController::class, 'verifyMemberPersonalProfile'])->name('verifyMemberPersonalProfile');
    Route::put('admin/user_management/members/verification/personal_profile/{uuid}', [MembersManagementController::class, 'verifyMemberPersonalProfileSubmit'])->name('verifyMemberPersonalProfileSubmit');

    Route::get('admin/user_management/members/verification/academic_info/{uuid}', [MembersManagementController::class, 'verifyMemberAcademicInfo'])->name('verifyMemberAcademicInfo');
    Route::get('admin/user_management/members/verification/academic_info/s2/{uuid}', [MembersManagementController::class, 'verifyMemberAcademicInfo'])->name('verifyMemberAcademicInfoStep2');

    Route::put('admin/user_management/members/verification/academic_info/verify/{uuid}', [MembersManagementController::class, 'verifyMemberAcademicInfoSubmit'])->name('verifyMemberAcademicInfoSubmit');
    Route::put('admin/user_management/members/verification/academic_info/verify/{uuid}', [MembersManagementController::class, 'verifyMemberAcademicInfoSubmit'])->name('verifyMemberAcademicInfoSubmit');
    Route::get('admin/user_management/members/verification/complete_verification_and_activate/{uuid}', [MembersManagementController::class, 'verifyAndactivateStudentAccount'])->name('verifyAndactivateStudentAccount');

    Route::get('admin/user_management/members/disable/{uuid}', [MembersManagementController::class, 'disableMemberAccount'])->name('disableMemberAccount');





    //










    // academic config
    // faculties
    Route::get('admin/academic_config/faculties', [AcademicConfigController::class, 'faculties'])->name('faculties');
    Route::get('admin/academic_config/faculties/search', [AcademicConfigController::class, 'faculties'])->name('facultiesSearch');
    Route::post('admin/academic_config/faculties/add/submit', [AcademicConfigController::class, 'addFacultySubmit'])->name('addFacultySubmit');
    Route::get('admin/academic_config/faculties/view/{uuid}', [AcademicConfigController::class, 'viewFaculty'])->name('viewFaculty');
    Route::put('admin/academic_config/faculties/edit/submit/{uuid}', [AcademicConfigController::class, 'updateFacultySubmit'])->name('updateFacultySubmit');
    Route::post('admin/academic_config/faculties/head/submit', [AcademicConfigController::class, 'addFacultyDeanSubmit'])->name('addFacultyDeanSubmit');
    Route::put('admin/academic_config/faculties/head/update/submit', [AcademicConfigController::class, 'editFacultyDeanSubmit'])->name('editFacultyDeanSubmit');
    Route::put('admin/academic_config/faculties/head/end_tenure/submit', [AcademicConfigController::class, 'editEndFacultyDeanTenureSubmit'])->name('editEndFacultyDeanTenureSubmit');
    Route::get('admin/academic_config/faculties/past_heads/{uuid}', [AcademicConfigController::class, 'pastFacultyHeads'])->name('pastFacultyHeads');
    Route::get('admin/academic_config/faculties/past_heads/search/{uuid}', [AcademicConfigController::class, 'pastFacultyHeads'])->name('pastFacultyHeadsSearch');
    Route::post('admin/academic_config/faculties/past_heads/create_new/submit', [AcademicConfigController::class, 'addNewPastFacultyHead'])->name('addNewPastFacultyHead');
    Route::put('admin/academic_config/faculties/past_heads/update/{uuid}', [AcademicConfigController::class, 'updatePastFacultyHeawdProfileSubmit'])->name('updatePastFacultyHeawdProfileSubmit');
    Route::get('admin/academic_config/faculties/past_heads/delete/{uuid}', [AcademicConfigController::class, 'deletePastFacultyHead'])->name('deletePastFacultyHead');


    // departments
    Route::get('admin/academic_config/departments', [AcademicConfigController::class, 'departments'])->name('departments');
    Route::get('admin/academic_config/departments/search', [AcademicConfigController::class, 'departments'])->name('departmentsSearch');
    Route::post('admin/academic_config/departments/add_new/submit', [AcademicConfigController::class, 'addNewDepartmentSubmit'])->name('addNewDepartmentSubmit');
    Route::get('admin/academic_config/departments/view/{uuid}', [AcademicConfigController::class, 'viewDepartment'])->name('viewDepartment');
    Route::put('admin/academic_config/departments/edit/submit/{uuid}', [AcademicConfigController::class, 'updateDepartmentSubmit'])->name('updateDepartmentSubmit');
    Route::post('admin/academic_config/departments/head/add_new/submit', [AcademicConfigController::class, 'addDepartmentHodSubmit'])->name('addDepartmentHodSubmit');
    Route::put('admin/academic_config/departments/head/edit/submit', [AcademicConfigController::class, 'editDepartmentHeadSubmit'])->name('editDepartmentHeadSubmit');
    Route::put('admin/academic_config/departments/head/end_tenure/submit', [AcademicConfigController::class, 'endDepertmentHeadTenureSubmit'])->name('endDepertmentHeadTenureSubmit');
    Route::get('admin/academic_config/departments/past_heads/{uuid}', [AcademicConfigController::class, 'pastDepartmentHeads'])->name('pastDepartmentHeads');
    Route::get('admin/academic_config/departments/past_heads/search/{uuid}', [AcademicConfigController::class, 'pastDepartmentHeads'])->name('pastDepartmentHeadsSearch');
    Route::put('admin/academic_config/departments/past_heads/update/{uuid}', [AcademicConfigController::class, 'updatePastDepartmentHeadProfileSubmit'])->name('updatePastDepartmentHeadProfileSubmit');
    Route::get('admin/academic_config/departments/past_heads/delete/{uuid}', [AcademicConfigController::class, 'deletePastDepartmentHead'])->name('deletePastDepartmentHead');
    Route::post('admin/academic_config/departments/past_heads/add_new/submit', [AcademicConfigController::class, 'addNewPastDepartmentHead'])->name('addNewPastDepartmentHead');


    // programs
    Route::get('admin/academic_config/programs', [AcademicConfigController::class, 'programs'])->name('programs');
    Route::get('admin/academic_config/programs/search', [AcademicConfigController::class, 'programs'])->name('programsSearch');
    Route::post('admin/academic_config/programs/create_new/submit', [AcademicConfigController::class, 'addAcademicProgramSubmit'])->name('addAcademicProgramSubmit');
    Route::get('admin/academic_config/programs/view/{uuid}', [AcademicConfigController::class, 'viewProgram'])->name('viewProgram');
    Route::put('admin/academic_config/programs/edit/submit/{uuid}', [AcademicConfigController::class, 'updateAcademicProgramSubmit'])->name('updateAcademicProgramSubmit');
    Route::post('admin/academic_config/programs/type/add_new/submit/{uuid}', [AcademicConfigController::class, 'addNewProgramType'])->name('addNewProgramType');
    Route::put('admin/academic_config/programs/type/edit/submit/{uuid}', [AcademicConfigController::class, 'editProgramType'])->name('editProgramType');
    Route::post('admin/academic_config/programs/duration_override/add_new/submit/{uuid}', [AcademicConfigController::class, 'addNewProgramProgramDurationDurationSubmit'])->name('addNewProgramProgramDurationDurationSubmit');
    Route::put('admin/academic_config/programs/duration_override/edit/submit/{uuid}', [AcademicConfigController::class, 'editProgramDurationOverrideSubmit'])->name('editProgramDurationOverrideSubmit');
    Route::get('admin/academic_config/programs/duration_override/delete/{uuid}', [AcademicConfigController::class, 'removeProgramDurationOverride'])->name('removeProgramDurationOverride');


    //





    //










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
