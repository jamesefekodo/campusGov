<?php

use App\Http\Controllers\Authentication\AuthController;
use App\Http\Controllers\Config\AcademicConfigController;
use App\Http\Controllers\Config\PermissionAccessController;
use App\Http\Controllers\Config\SystemConfigurationController;
use App\Http\Controllers\Config\SystemPolicyController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Finance\AssociationDueController;
use App\Http\Controllers\Finance\PaymentSettingsController;
use App\Http\Controllers\Finance\PaymentTransasctionController;
use App\Http\Controllers\Frontend\FrontendController;
use App\Http\Controllers\Media\SocialMediaController;
use App\Http\Controllers\Member\MemberSelfProfileController;
use App\Http\Controllers\Misconduct\MisconductController;
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

Route::get('/unkown_tenant', [AuthController::class, 'unknownTenant'])->name('unknownTenant');

// payment rollback
Route::get('/callback/with_paystack/{transaction_id}', [PaymentTransasctionController::class, 'paystackPaymentCallback'])->name('paystackPaymentCallback');
Route::get('/callback/with_flutterwave', [PaymentTransasctionController::class, 'flutterwavePaymentCallback'])->name('flutterwavePaymentCallback');






Route::middleware(['authenticationMiddlewares'])->group(function () {

    Route::get('/', function () {
        // return redirect()->route('homepage');
        return redirect()->route('loginPage');
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

    // force to update academic status if unknown
    Route::get('member/update_academic_status', [MemberSelfProfileController::class, 'memberUpdateAcademicStatus'])->name('memberUpdateAcademicStatus');
    Route::get('member/update_academic_status/2', [MemberSelfProfileController::class, 'memberUpdateAcademicStatus'])->name('memberUpdateAcademicStatusStep2');
    Route::put('member/update_academic_status/submit', [MemberSelfProfileController::class, 'memberUpdateAcademicStatusSubmit'])->name('memberUpdateAcademicStatusSubmit');

    // apply for re-admission
    Route::get('member/apply_for_re_admission', [MemberSelfProfileController::class, 'memberApplyForReAdmission'])->name('memberApplyForReAdmission');
    Route::post('member/update_academic_status/submit', [MemberSelfProfileController::class, 'memberApplyForReAdmissionSubmit'])->name('memberApplyForReAdmissionSubmit');



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
    Route::post('member/my_profile/business/add_new', [MemberSelfProfileController::class, 'addNewBusinessSubmit'])->name('addNewBusinessSubmit');
    Route::put('member/my_profile/business/update/{id}/submit', [MemberSelfProfileController::class, 'editNewBusinessSubmit'])->name('editNewBusinessSubmit');
    Route::get('member/my_profile/business/delete/{id}', [MemberSelfProfileController::class, 'deleteBusinessRecordSubmit'])->name('deleteBusinessRecordSubmit');

    Route::get('member/my_profile/leadership_positions', [MemberSelfProfileController::class, 'myLeadershipPosition'])->name('myLeadershipPosition');
    Route::post('member/my_profile/leadership_positions/submit', [MemberSelfProfileController::class, 'addLeadershipPositionSubmit'])->name('addLeadershipPositionSubmit');
    Route::put('member/my_profile/leadership_positions/edit/{id}/submit', [MemberSelfProfileController::class, 'editLeadershipPositionSubmit'])->name('editLeadershipPositionSubmit');
    Route::get('member/my_profile/leadership_positions/delete/{id}', [MemberSelfProfileController::class, 'deleteLeadershipPositionSubmit'])->name('deleteLeadershipPositionSubmit');

    Route::get('member/my_profile/awards', [MemberSelfProfileController::class, 'myAwards'])->name('myAwards');
    Route::post('member/my_profile/awards/add_new/submit', [MemberSelfProfileController::class, 'addAwardSubmit'])->name('addAwardSubmit');
    Route::put('member/my_profile/awards/edit/{id}/submit', [MemberSelfProfileController::class, 'editAwardSubmit'])->name('editAwardSubmit');
    Route::get('member/my_profile/awards/delete/{id}', [MemberSelfProfileController::class, 'deleteAwardSubmit'])->name('deleteAwardSubmit');

    Route::get('member/my_profile/social_media_handles', [MemberSelfProfileController::class, 'mySocialMediaHandles'])->name('mySocialMediaHandles');
    Route::post('member/my_profile/social_media_handles/add_new', [MemberSelfProfileController::class, 'addSocialMediaHandleSubmit'])->name('addSocialMediaHandleSubmit');
    Route::put('member/my_profile/social_media_handles/edit/{id}/submit', [MemberSelfProfileController::class, 'editSocialMediaHandleSubmit'])->name('editSocialMediaHandleSubmit');
    Route::get('member/my_profile/social_media_handles/delete/{id}', [MemberSelfProfileController::class, 'employeeDeleteSocialMediaHandleSubmit'])->name('employeeDeleteSocialMediaHandleSubmit');

    Route::get('member/my_profile/misconducts', [MemberSelfProfileController::class, 'myMisconducts'])->name('myMisconducts');

    // payment
    Route::get('student/payment/dues', [MemberSelfProfileController::class, 'myAssociationDues'])->name('myAssociationDues');
    Route::get('student/payment/transactions', [MemberSelfProfileController::class, 'myPaymentTransactions'])->name('myPaymentTransactions');


    Route::post('member/association_dues/{uuid}', [PaymentTransasctionController::class, 'memberPayAssociationDue'])->name('memberPayAssociationDue');
});



// administrators routes
Route::middleware(['authenticationMiddlewares', 'adminMiddlewares'])->group(function () {
    Route::get('admin/dashboard', [DashboardController::class, 'dashboard'])->name('adminDashboard');


    // user management
    Route::get('admin/user_management/members/disable_user/{uuid}', [MembersManagementController::class, 'disableUserAccount'])->name('disableUserAccount');
    Route::get('admin/user_management/members/re_enable_user/{uuid}', [MembersManagementController::class, 'reEnableUserAccount'])->name('reEnableUserAccount');


    Route::get('admin/user_management/members/inactive_users', [MembersManagementController::class, 'inActiveUserAccounts'])->name('inActiveUserAccounts');
    Route::get('admin/user_management/members/inactive_users/search', [MembersManagementController::class, 'inActiveUserAccounts'])->name('inActiveUserAccountsSearch');

    // memberAwaitingRegistration - account created but incomplete registration or not yet submitted for verification
    Route::get('admin/user_management/members/awaiting_registration', [MembersManagementController::class, 'memberAwaitingRegistration'])->name('memberAwaitingRegistration');
    Route::get('admin/user_management/members/awaiting_registration/search', [MembersManagementController::class, 'memberAwaitingRegistration'])->name('memberAwaitingRegistrationSearch');


    // students
    Route::get('admin/user_management/members/students', [MembersManagementController::class, 'studentMembers'])->name('studentMembers');
    Route::get('admin/user_management/members/students/search', [MembersManagementController::class, 'studentMembers'])->name('studentMembersSearch');
    Route::get('admin/user_management/members/students/filter', [MembersManagementController::class, 'studentMembers'])->name('filterStudentMembers');

    Route::get('admin/user_management/members/non_students', [MembersManagementController::class, 'nonStudentMembers'])->name('nonStudentMembers');
    Route::get('admin/user_management/members/non_students/search', [MembersManagementController::class, 'nonStudentMembers'])->name('nonStudentMembersSearch');
    Route::get('admin/user_management/members/non_students/filter', [MembersManagementController::class, 'nonStudentMembers'])->name('filterNonStudentMembers');




    Route::put('admin/user_management/members/verification/return_for_correction/{uuid}', [MembersManagementController::class, 'updateBasicAccountInfo'])->name('updateBasicAccountInfo');

    // re admission requests
    Route::get('admin/user_management/members/re_admission_requests', [MembersManagementController::class, 'reAdmissionRequests'])->name('reAdmissionRequests');
    Route::get('admin/user_management/members/re_admission_requests/search', [MembersManagementController::class, 'reAdmissionRequests'])->name('reAdmissionRequestsSearch');
    Route::get('admin/user_management/members/re_admission_requests/view/{uuid}', [MembersManagementController::class, 'viewReAdmissionRequest'])->name('viewReAdmissionRequest');
    Route::put('admin/user_management/members/re_admission_requests/process/{uuid}/submit', [MembersManagementController::class, 'processReAdmissionRequestSubmit'])->name('processReAdmissionRequestSubmit');



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


    // view members profile
    Route::get('admin/user_management/members/view/{uuid}', [MembersManagementController::class, 'viewMember'])->name('viewMember');
    Route::get('admin/user_management/members/view/personal_info/{uuid}', [MembersManagementController::class, 'viewMemberPersonalProfile'])->name('viewMemberPersonalProfile');
    Route::get('admin/user_management/members/view/academic_info/{uuid}', [MembersManagementController::class, 'viewMemberAcademicInfo'])->name('viewMemberAcademicInfo');
    Route::get('admin/user_management/members/view/academic_info/edit/{uuid}', [MembersManagementController::class, 'editMemberAcademicInfo'])->name('editMemberAcademicInfo');
    Route::get('admin/user_management/members/view/academic_info/edit/{uuid}/2', [MembersManagementController::class, 'editMemberAcademicInfo'])->name('editMemberAcademicInfoStep2');
    Route::put('admin/user_management/members/view/academic_info/update/{uuid}/submit', [MembersManagementController::class, 'updateMemberAcademicInfoSubmit'])->name('updateMemberAcademicInfoSubmit');

    Route::get('admin/user_management/members/view/academic_history/{uuid}', [MembersManagementController::class, 'viewMemberAcademicHistory'])->name('viewMemberAcademicHistory');
    Route::get('admin/user_management/members/view/skills/{uuid}', [MembersManagementController::class, 'viewMemberSkills'])->name('viewMemberSkills');
    Route::get('admin/user_management/members/view/business/{uuid}', [MembersManagementController::class, 'viewMemberBusiness'])->name('viewMemberBusiness');
    Route::get('admin/user_management/members/view/leadership_position/{uuid}', [MembersManagementController::class, 'viewMemberLeadershipPosition'])->name('viewMemberLeadershipPosition');
    Route::get('admin/user_management/members/view/awards/{uuid}', [MembersManagementController::class, 'viewMemberAward'])->name('viewMemberAward');
    Route::get('admin/user_management/members/view/social_media_handles/{uuid}', [MembersManagementController::class, 'viewMemberSocialMediaHandles'])->name('viewMemberSocialMediaHandles');
    Route::get('admin/user_management/members/view/misconducts/{uuid}', [MembersManagementController::class, 'viewMemberMisconducts'])->name('viewMemberMisconducts');

    Route::get('admin/user_management/members/view/association_dues/{uuid}', [MembersManagementController::class, 'viewMemberAssociationDues'])->name('viewMemberAssociationDues');
    Route::post('admin/user_management/members/view/association_dues/initiate_regularization/{uuid}', [MembersManagementController::class, 'initiatePaymentRegularization'])->name('initiatePaymentRegularization');






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


    // basic system configuration
    Route::get('admin/config/basics', [SystemConfigurationController::class, 'viewSystemSettings'])->name('configBasicSettings');
    Route::put('admin/config/basics/acedemic_year/update', [SystemConfigurationController::class, 'updateAcademicYear'])->name('updateAcademicYear');
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


    // recognizedSocialMediaPlatforms

    Route::get('admin/media/social_media/recognized_platforms', [SocialMediaController::class, 'recognizedSocialMediaPlatforms'])->name('recognizedSocialMediaPlatforms');
    Route::get('admin/media/social_media/recognized_platforms/search', [SocialMediaController::class, 'recognizedSocialMediaPlatforms'])->name('recognizedSocialMediaPlatformsSearch');
    Route::post('admin/media/social_media/recognized_platforms/add_new/submit', [SocialMediaController::class, 'addRecognizedSocialMediaPlatform'])->name('addRecognizedSocialMediaPlatform');
    Route::put('admin/media/social_media/recognized_platforms/edit/{slug}/submit', [SocialMediaController::class, 'editRecognizedSocialMediaPlatform'])->name('editRecognizedSocialMediaPlatform');
    Route::get('admin/media/social_media/recognized_platforms/delete/{slug}', [SocialMediaController::class, 'deletePlatform'])->name('deletePlatform');

    Route::get('admin/misconducts/active_cases', [MisconductController::class, 'activeMisconductCases'])->name('activeMisconductCases');
    Route::get('admin/misconducts/active_cases/search', [MisconductController::class, 'activeMisconductCases'])->name('activeMisconductCasesSearch');
    Route::post('admin/misconducts/active_cases/submit', [MisconductController::class, 'addNewMisconductSubmit'])->name('addNewMisconductSubmit');

    Route::get('admin/misconducts/active_cases/view/{uuid}', [MisconductController::class, 'viewMisconductCase'])->name('viewMisconductCase');
    Route::put('admin/misconducts/active_cases/update/{uuid}', [MisconductController::class, 'updateMisconductCaseSubmit'])->name('updateMisconductCaseSubmit');

    Route::get('admin/misconducts/resolved_cases', [MisconductController::class, 'resolvedMisconductCases'])->name('resolvedMisconductCases');
    Route::get('admin/misconducts/resolved_cases/search', [MisconductController::class, 'resolvedMisconductCases'])->name('resolvedMisconductCasesSearch');


    // payment settings
    // payment methods
    Route::get('admin/finance/settings/payment_methods/manual', [PaymentSettingsController::class, 'manualPaymentMethods'])->name('manualPaymentMethods');
    Route::post('admin/finance/settings/payment_methods/manual/bank_accounts/add_new', [PaymentSettingsController::class, 'addNewBankAccountSubmit'])->name('addNewBankAccountSubmit');
    Route::put('admin/finance/settings/payment_methods/manual/bank_accounts/edit/{id}', [PaymentSettingsController::class, 'updateBankAccount'])->name('updateBankAccount');
    Route::get('admin/finance/settings/payment_methods/manual/bank_accounts/remove/{id}', [PaymentSettingsController::class, 'removeBankAccount'])->name('removeBankAccount');

    Route::get('admin/finance/settings/payment_methods/electronic', [PaymentSettingsController::class, 'electronicPaymentMethods'])->name('electronicPaymentMethods');
    Route::get('admin/finance/settings/payment_methods/electronic/view/{uuid}', [PaymentSettingsController::class, 'viewElectronicPaymentMethod'])->name('viewElectronicPaymentMethod');
    Route::put('admin/finance/settings/payment_methods/electronic/update/{id}', [PaymentSettingsController::class, 'updateElectronicPaymentApiConfig'])->name('updateElectronicPaymentApiConfig');

    Route::post('admin/finance/settings/payment_methods/electronic/create_update/{uuid}', [PaymentSettingsController::class, 'createUpdateElectronicPaymentApiConfig'])->name('createUpdateElectronicPaymentApiConfig');

    Route::get('admin/finance/settings/taxes', [PaymentSettingsController::class, 'taxes'])->name('taxes');
    Route::post('admin/finance/settings/taxes/add_new/submit', [PaymentSettingsController::class, 'addNewTaxSubmit'])->name('addNewTaxSubmit');
    Route::put('admin/finance/settings/taxes/edit/{uuid}', [PaymentSettingsController::class, 'editTaxSubmit'])->name('editTaxSubmit');


    Route::get('admin/finance/dues', [AssociationDueController::class, 'associationDues'])->name('associationDues');
    Route::get('admin/finance/dues/search', [AssociationDueController::class, 'associationDues'])->name('associationDuesSearch');
    Route::post('admin/finance/dues/add_new/submit', [AssociationDueController::class, 'addNewAssociationDueSubmit'])->name('addNewAssociationDueSubmit');
    Route::get('admin/finance/dues/view/{uuid}', [AssociationDueController::class, 'viewDue'])->name('viewDue');
    Route::put('admin/finance/dues/edit/{uuid}', [AssociationDueController::class, 'editAssociationDueSubmit'])->name('editAssociationDueSubmit');

    Route::post('admin/finance/dues/variation/create/submit/{uuid}', [AssociationDueController::class, 'createAssociationDueVariation'])->name('createAssociationDueVariation');
    Route::put('admin/finance/dues/variation/edit/submit/{id}', [AssociationDueController::class, 'editAssociationDueVariation'])->name('editAssociationDueVariation');
    Route::get('admin/finance/dues/variation/delet/{id}', [AssociationDueController::class, 'removeAssociationVariation'])->name('removeAssociationVariation');

    // transactions
    Route::get('admin/finance/transactions', [PaymentTransasctionController::class, 'paymentTransactions'])->name('paymentTransactions');
    Route::get('admin/finance/transactions/search', [PaymentTransasctionController::class, 'paymentTransactions'])->name('paymentTransactionsSearch');
    Route::get('admin/finance/transactions/view/{payment_transaction_id}', [PaymentTransasctionController::class, 'viewPaymentTransaction'])->name('viewPaymentTransaction');
    Route::put('admin/finance/transactions/manual/process/{payment_transaction_id}', [PaymentTransasctionController::class, 'processManualPaymentTransaction'])->name('processManualPaymentTransaction');
    Route::put('admin/finance/transactions/electronic/paystack/trans_ref/{payment_transaction_id}', [PaymentTransasctionController::class, 'updatePaystackRefNumber'])->name('updatePaystackRefNumber');

    Route::get('admin/finance/transaction/regularizations', [PaymentTransasctionController::class, 'paymentRegularizationRequests'])->name('paymentRegularizationRequests');
    Route::get('admin/finance/transaction/regularizations/search', [PaymentTransasctionController::class, 'paymentRegularizationRequests'])->name('paymentRegularizationRequestsSearch');
     Route::get('admin/finance/transaction/regularizations/view/{uuid}', [PaymentTransasctionController::class, 'viewPaymentRegularization'])->name('viewPaymentRegularization');
    Route::put('admin/finance/transaction/regularizations/process/{uuid}', [PaymentTransasctionController::class, 'processPaymentRegularizationSubmit'])->name('processPaymentRegularizationSubmit');

});
