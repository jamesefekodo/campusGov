<?php

use App\Http\Controllers\AcademicResources\IndustryAffiliationController;
use App\Http\Controllers\AcademicResources\PastQuestionController;
use App\Http\Controllers\AcademicResources\RecommendedBooksController;
use App\Http\Controllers\AcademicResources\RecommendedCourseController;
use App\Http\Controllers\Archive\ArchiveController;
use App\Http\Controllers\Authentication\AuthController;
use App\Http\Controllers\Config\AcademicConfigController;
use App\Http\Controllers\Config\PermissionAccessController;
use App\Http\Controllers\Config\SystemConfigurationController;
use App\Http\Controllers\Config\SystemPolicyController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Election\ElectionController;
use App\Http\Controllers\Election\MemberElectionController;
use App\Http\Controllers\Finance\AssociationDueController;
use App\Http\Controllers\Finance\DonationController;
use App\Http\Controllers\Finance\FinancialClaimController;
use App\Http\Controllers\Finance\InvoiceController;
use App\Http\Controllers\Finance\PaymentSettingsController;
use App\Http\Controllers\Finance\PaymentTransasctionController;
use App\Http\Controllers\Frontend\FrontendController;
use App\Http\Controllers\Leadership\LeadershipController;
use App\Http\Controllers\Media\MediaPublicationController;
use App\Http\Controllers\Media\SocialMediaController;
use App\Http\Controllers\Member\MemberSelfProfileController;
use App\Http\Controllers\Misconduct\MisconductController;
use App\Http\Controllers\Sharing\SharingController;
use App\Http\Controllers\Support\SupportController;
use App\Http\Controllers\UserManagement\AdminsManagementController;
use App\Http\Controllers\UserManagement\MembersManagementController;
use App\Models\Config\AccessControl\Role;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;






Route::post('manual_sync_election_temp', [ElectionController::class, 'manualSyncElectionTemp'])->name('manualSyncElectionTemp');



// clear cache
Route::get('/clear-cache', function () {
    Artisan::call('cache:clear');
    Artisan::call('route:clear');
    Artisan::call('config:clear');
    Artisan::call('view:clear');
    Artisan::call('optimize:clear');

    if (function_exists('opcache_reset')) {
        opcache_reset();
    }
    // Redirect back with a success message
    return redirect()->back()->with('success', 'Application cache cleared successfully');
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

// ##############################################################################
Route::get('/temporary_upload', [FrontendController::class, 'showUploadForm']);
Route::post('/process_temporary_upload', [FrontendController::class, 'processTemporaryUpload'])->name('processTemporaryUpload');

// ##############################################################################
// sharing
Route::get('/sharing/media_publication/{uuid}', [SharingController::class, 'sharedMediaPublication'])->name('sharedMediaPublication');
Route::get('/sharing/past_questions/{uuid}', [SharingController::class, 'sharePastQuestion'])->name('sharePastQuestion');
Route::get('/sharing/recommended_books/{slug}', [SharingController::class, 'sharedRecommendedBook'])->name('sharedRecommendedBook');
Route::get('/sharing/recommended_courses/{slug}', [SharingController::class, 'sharedRecommendedCourse'])->name('sharedRecommendedCourse');
Route::get('/sharing/industry_affiliate/{slug}', [SharingController::class, 'sharedIndustryAffiliate'])->name('sharedIndustryAffiliate');
Route::get('/sharing/archive_file/{uuid}', [SharingController::class, 'sharedArchiveFile'])->name('sharedArchiveFile');





Route::middleware(['authenticationMiddlewares'])->group(function () {

    Route::get('/', function () {
        return redirect()->route('homepage');
        // return redirect()->route('loginPage');
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







    // oppen routes - but requirs auth protection
    Route::get('/verify/payments/waiver/{uuid}', [PaymentTransasctionController::class, 'verifyAssociationDuePaymentWaiver'])->name('verifyAssociationDuePaymentWaiver');
    Route::get('/verify/payments/receipt/{payment_transaction_id}', [PaymentTransasctionController::class, 'verifyPaymentReceipt'])->name('verifyPaymentReceipt');


    // publication
    Route::post('/publications/{uuid}/like', [MediaPublicationController::class, 'togglePublicationLike'])->name('publication.like.toggle');
    Route::post('/publications/{publicationUuid}/comments', [MediaPublicationController::class, 'storeComment'])->name('publication.comment.store');
    Route::post('/publications/{publicationUuid}/comments/{commentUuid}/reply', [MediaPublicationController::class, 'storeReply'])->name('publication.comment.reply.store');
    Route::post('/publication-comments/{commentUuid}/like', [MediaPublicationController::class, 'toggleCommentLike'])->name('publication.comment.like.toggle');
    Route::put('/publication-comments/{commentUuid}', [MediaPublicationController::class, 'updateComment'])->name('publication.comment.update');
    Route::delete('/publication-comments/{commentUuid}', [MediaPublicationController::class, 'destroyComment'])->name('publication.comment.destroy');


    // past questions
    Route::post('/past-questions/{pastQuestion:uuid}/contributions', [PastQuestionController::class, 'storeContribution'])->name('pastQuestion.contribution.store');

    Route::put('/past-question-contributions/{contribution:uuid}', [PastQuestionController::class, 'updateContribution'])->name('pastQuestion.contribution.update');

    Route::delete('/past-question-contributions/{contribution:uuid}', [PastQuestionController::class, 'destroyContribution'])->name('pastQuestion.contribution.destroy');

    Route::post('/past-question-contributions/{contribution:uuid}/like', [PastQuestionController::class, 'toggleLikeContribution'])->name('pastQuestion.contribution.like.toggle');


    // context switching
    Route::post('/user-context/{context}', [PermissionAccessController::class, 'toggleLikeContribution'])->whereIn('context', ['personal', 'admin'])->name('user.context.switch');
});

// Route::middleware('auth')
//     ->post(
//         '/user-context/{context}',
//         [PermissionAccessController::class, 'switchUserContext']
//     )
//     ->whereIn('context', ['personal', 'admin'])
//     ->name('user.context.switch');

// });

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

    Route::post('member/my_profile/change_password/submit', [MemberSelfProfileController::class, 'changeSelfPasswordSubmit'])->name('changeSelfPasswordSubmit');





    // payment
    Route::get('member/payment/dues', [MemberSelfProfileController::class, 'myAssociationDues'])->name('myAssociationDues');
    Route::get('member/payment/transactions', [MemberSelfProfileController::class, 'myPaymentTransactions'])->name('myPaymentTransactions');
    Route::get('member/payment/transactions/view/{payment_transaction_id}', [MemberSelfProfileController::class, 'viewMyPaymentTransaction'])->name('viewMyPaymentTransaction');
    Route::get('member/payment/transactions/download/{payment_transaction_id}', [PaymentTransasctionController::class, 'downloadPaymentReceipt'])->name('memberDownloadPaymentReceipt');
    Route::get('member/payment/association_dues/waiver_notice/download/{uuid}', [MembersManagementController::class, 'downloadDuePaymentWaiverNotice'])->name('memberDownloadDuePaymentWaiverNotice');





    // invoice
    Route::get('member/payment/invoices', [InvoiceController::class, 'myInvoices'])->name('myInvoices');
    Route::get('member/payment/invoices/view/{uuid}', [InvoiceController::class, 'viewMyInvoice'])->name('viewMyInvoice');
    Route::post('member/payment/invoices/pay_now/{uuid}', [InvoiceController::class, 'memberPayInvoice'])->name('memberPayInvoice');

    // memberDonations
    Route::get('member/payment/donations', [DonationController::class, 'memberDonations'])->name('memberDonations');
    Route::get('member/payment/donations/search', [DonationController::class, 'memberDonations'])->name('memberDonationsSearch');
    Route::get('member/payment/donations/view/{uuid}', [DonationController::class, 'memberViewDonation'])->name('memberViewDonation');
    Route::post('member/payment/donations/view/contribute_now/{uuid}', [DonationController::class, 'memberPayForDonation'])->name('donation.contribute');


    Route::post('member/association_dues/{uuid}', [PaymentTransasctionController::class, 'memberPayAssociationDue'])->name('memberPayAssociationDue');

    // elections
    Route::get('member/elections/new', [MemberElectionController::class, 'memberNewElections'])->name('memberNewElections');
    Route::get('member/elections/new/search', [MemberElectionController::class, 'memberNewElections'])->name('memberNewElectionsSearch');
    Route::get('member/elections/view/{uuid}', [MemberElectionController::class, 'memberViewElection'])->name('memberViewElection');
    Route::post('member/elections/self_application/initiate/{uuid}', [MemberElectionController::class, 'memberInitiateSelfApplyForPositionSubmit'])->name('memberInitiateSelfApplyForPositionSubmit');
    Route::put('member/elections/self_application/update/{uuid}', [MemberElectionController::class, 'memberUpdateSelfApplyForPositionSubmit'])->name('memberUpdateSelfApplyForPositionSubmit');

    Route::get('member/elections/completed', [MemberElectionController::class, 'memberCompletedElections'])->name('memberCompletedElections');
    Route::get('member/elections/completed/search', [MemberElectionController::class, 'memberCompletedElections'])->name('searchMemberCompletedElections');



    Route::get('member/elections/ongoing', [MemberElectionController::class, 'memberOngoingElections'])->name('memberOngoingElections');
    Route::get('member/elections/ongoing/search', [MemberElectionController::class, 'memberOngoingElections'])->name('memberOngoingElectionsSearch');

    Route::get('member/elections/voting/{uuid}', [MemberElectionController::class, 'memberElectionVoting'])->name('memberElectionVoting');
    Route::get('member/elections/voting/position/{uuid}', [MemberElectionController::class, 'memberViewElectionPositionCandidatesForVote'])->name('memberViewElectionPositionCandidatesForVote');
    Route::post('member/elections/voting/submit_vote/{election}/{position}/{candidate}', [MemberElectionController::class, 'memberSubmitStandardVote'])->name('memberSubmitStandardVote');

    Route::post('member/elections/voting/monetary/submit_vote/{election}/{position}/{candidate}', [MemberElectionController::class, 'memberSubmitMonetaryVote'])->name('memberSubmitMonetaryVote');

    Route::get('member/elections/results/{uuid}', [MemberElectionController::class, 'memberElectionResults'])->name('memberElectionResults');
    Route::get('member/elections/results/view/{election}/{position}', [MemberElectionController::class, 'memberViewElectionResultsForPosition'])->name('memberViewElectionResultsForPosition');





    Route::get('member/elections/my_applications/{uuid}', [MemberElectionController::class, 'myElectionApplications'])->name('myElectionApplications');
    Route::get('member/elections/my_applications/view/{uuid}', [MemberElectionController::class, 'viewMyElectionApplication'])->name('viewMyElectionApplication');
    Route::post('member/elections/my_applications/pay_for_candidacy/{uuid}', [MemberElectionController::class, 'memberPayForElectionCandidacy'])->name('memberPayForElectionCandidacy');

    Route::get('member/elections/my_applications/application/print/{uuid}', [MemberElectionController::class, 'printElectionApplicationEndorsementForm'])->name('printElectionApplicationEndorsementForm');

    Route::get('member/elections/candidates/{uuid}', [MemberElectionController::class, 'memberElectionCandidates'])->name('memberElectionCandidates');
    Route::get('member/elections/candidates/position/{uuid}', [MemberElectionController::class, 'memberViewElectionCandidatesForPosition'])->name('memberViewElectionCandidatesForPosition');


    // publicatons
    Route::get('member/publications/publications', [MediaPublicationController::class, 'memberMediaPublications'])->name('memberMediaPublications');
    Route::get('member/publications/publications/search', [MediaPublicationController::class, 'memberMediaPublications'])->name('memberMediaPublicationsSearch');
    Route::get('member/publications/publications/tags/{slug}', [MediaPublicationController::class, 'memberMediaPublications'])->name('memberMediaPublicationWihTag');
    Route::get('member/publications/publications/view/{uuid}', [MediaPublicationController::class, 'memberViewMediaPublication'])->name('memberViewMediaPublication');

    // past questions
    Route::get('member/academic_resources/past_questions', [PastQuestionController::class, 'memberPastQuestions'])->name('memberPastQuestions');
    Route::get('member/academic_resources/past_questions/view/{uuid}', [PastQuestionController::class, 'memberViewPastQuestion'])->name('memberViewPastQuestion');

    // recommended books
    Route::get('member/academic_resources/recommended_books', [RecommendedBooksController::class, 'memberRecommendedBooks'])->name('memberRecommendedBooks');
    Route::get('member/academic_resources/recommended_books/search', [RecommendedBooksController::class, 'memberRecommendedBooks'])->name('memberRecommendedBooksSearch');
    Route::get('member/academic_resources/recommended_books/filter/tag/{slug}', [RecommendedBooksController::class, 'memberRecommendedBooks'])->name('memberRecommendedBooksFilter');
    Route::get('member/academic_resources/recommended_books/view/{slug}', [RecommendedBooksController::class, 'memberViewRecommendedBook'])->name('memberViewRecommendedBook');


    // recommended courses
    Route::get('member/academic_resources/recommended_courses', [RecommendedCourseController::class, 'memberRecommendedCourses'])->name('memberRecommendedCourses');
    Route::get('member/academic_resources/recommended_courses/search', [RecommendedCourseController::class, 'memberRecommendedCourses'])->name('memberRecommendedCoursesSearch');
    Route::get('member/academic_resources/recommended_courses/filter/tag/{slug}', [RecommendedCourseController::class, 'memberRecommendedCourses'])->name('memberRecommendedCoursesFilter');
    Route::get('member/academic_resources/recommended_courses/view/{slug}', [RecommendedCourseController::class, 'memberViewRecommendedCourse'])->name('memberViewRecommendedCourse');

    // member Industry Affiliates
       Route::get('member/academic_resources/industry_affiliates', [IndustryAffiliationController::class, 'memberIndustryAffiliates'])->name('memberIndustryAffiliates');
    Route::get('member/academic_resources/industry_affiliates/search', [IndustryAffiliationController::class, 'memberIndustryAffiliates'])->name('memberIndustryAffiliatesSearch');
    Route::get('member/academic_resources/industry_affiliates/filter/tag/{slug}', [IndustryAffiliationController::class, 'memberIndustryAffiliates'])->name('memberIndustryAffiliatesFilter');
    Route::get('member/academic_resources/industry_affiliates/view/{slug}', [IndustryAffiliationController::class, 'memberViewIndustryAffiliate'])->name('memberViewIndustryAffiliate');


    // archive files

    Route::get('member/archive', [ArchiveController::class, 'memberArchive'])->name('memberArchive');
    Route::get('member/archive/search', [ArchiveController::class, 'memberArchive'])->name('memberArchiveSearch');
    Route::get('member/archive/file/view/{uuid}', [ArchiveController::class, 'memberViewArchiveFile'])->name('memberViewArchiveFile');



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

    Route::get('admin/user_management/members/view/roles/{uuid}', [MembersManagementController::class, 'viewMemberRoles'])->name('viewMemberRoles');
    Route::post('admin/user_management/members/view/roles/assign_new/{uuid}', [MembersManagementController::class, 'assignUserNewRoleSubmit'])->name('assignUserNewRoleSubmit');
    Route::get('admin/user_management/members/view/roles/un_assign_role/{uuid}/{id}', [MembersManagementController::class, 'unAssignRole'])->name('unAssignRole');





    Route::get('admin/user_management/members/view/association_dues/waiver_notice/download/{uuid}', [MembersManagementController::class, 'downloadDuePaymentWaiverNotice'])->name('downloadDuePaymentWaiverNotice');
    Route::get('admin/user_management/members/view/association_dues/payment_transaction/download/{payment_transaction_id}', [PaymentTransasctionController::class, 'downloadPaymentReceipt'])->name('downloadPaymentReceipt');








    Route::post('admin/user_management/members/view/association_dues/initiate_regularization/{uuid}', [MembersManagementController::class, 'initiatePaymentRegularization'])->name('initiatePaymentRegularization');
    Route::post('admin/user_management/members/view/association_dues/initiate_payment_waiver/{uuid}', [MembersManagementController::class, 'initiatePaymentWaiver'])->name('initiatePaymentWaiver');

    Route::get('admin/user_management/members/view/invoices/{uuid}', [MembersManagementController::class, 'viewMemberInvoices'])->name('viewMemberInvoices');



    // administrators
    Route::get('admin/user_management/admin', [AdminsManagementController::class, 'administrators'])->name('administrators');
    Route::get('admin/user_management/admin/search', [AdminsManagementController::class, 'administrators'])->name('administratorsSearch');
    Route::get('admin/user_management/admin/add_new', [AdminsManagementController::class, 'createNewAdmin'])->name('createNewAdmin');
    Route::post('admin/user_management/admin/add_new/submit', [AdminsManagementController::class, 'createAdminProfileSubmit'])->name('createAdminProfileSubmit');
    Route::get('admin/user_management/admin/view/{uuid}', [AdminsManagementController::class, 'viewAdmin'])->name('viewAdmin');

    Route::put('admin/user_management/admin/view/update/submit/{uuid}', [AdminsManagementController::class, 'updateAdminProfileSubmit'])->name('updateAdminProfileSubmit');
    Route::post('admin/user_management/admin/view/change_password/submit/{uuid}', [AdminsManagementController::class, 'changeAdminPasswordSubmit'])->name('changeAdminPasswordSubmit');

    Route::post('admin/user_management/admin/view/account_status/toggle/{uuid}', [AdminsManagementController::class, 'toggleAdminAccountStatus'])->name('toggleAdminAccountStatus');







    Route::get('admin/profile/view', [AdminsManagementController::class, 'adminSelfProfile'])->name('adminSelfProfile');
    Route::post('admin/profile/view', [MemberSelfProfileController::class, 'changeSelfPasswordSubmit'])->name('adminChangeSelfPasswordSubmit');



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
    Route::get('admin/academic_config/programs/type/remove/submit/{uuid}', [AcademicConfigController::class, 'removeProgramModal'])->name('removeProgramModal');



    Route::post('admin/academic_config/programs/duration_override/add_new/submit/{uuid}', [AcademicConfigController::class, 'addNewProgramProgramDurationDurationSubmit'])->name('addNewProgramProgramDurationDurationSubmit');
    Route::put('admin/academic_config/programs/duration_override/edit/submit/{uuid}', [AcademicConfigController::class, 'editProgramDurationOverrideSubmit'])->name('editProgramDurationOverrideSubmit');
    Route::get('admin/academic_config/programs/duration_override/delete/{uuid}', [AcademicConfigController::class, 'removeProgramDurationOverride'])->name('removeProgramDurationOverride');


    //


    // basic system configuration
    Route::get('admin/config/basics/basics', [SystemConfigurationController::class, 'viewSystemSettings'])->name('configBasicSettings');
    Route::put('admin/config/basics/basics/acedemic_year/update', [SystemConfigurationController::class, 'updateAcademicYear'])->name('updateAcademicYear');
    Route::put('admin/config/basics/basics/maintenance_mode/update', [SystemConfigurationController::class, 'updateMaintenanceMode'])->name('updateMaintenanceMode');
    Route::put('admin/config/basics/basics/system_info/update', [SystemConfigurationController::class, 'updateBasicSystemInfoUpdate'])->name('updateBasicSystemInfoUpdate');
    Route::put('admin/config/basics/basics/email_config/update', [SystemConfigurationController::class, 'updateEmailConfigSubmit'])->name('updateEmailConfigSubmit');

    // homepage sliders
    Route::get('admin/config/basics/sliders', [SystemConfigurationController::class, 'homepageSliders'])->name('homepageSliders');
    Route::post('admin/config/basics/sliders/add_new/submit', [SystemConfigurationController::class, 'addNewSliderSubmit'])->name('addNewSliderSubmit');
    Route::put('admin/config/basics/sliders/update/{id}/submit', [SystemConfigurationController::class, 'editSliderSubmit'])->name('editSliderSubmit');


    // seo settings
    Route::get('admin/config/seo', [SystemConfigurationController::class, 'seoConfig'])->name('seoConfig');
    Route::put('admin/config/seo/update/submit', [SystemConfigurationController::class, 'updateSeoConfigSubmit'])->name('updateSeoConfigSubmit');




    // support/ help
    Route::get('admin/help/chat', [SupportController::class, 'whatsAppSupportLines'])->name('whatsAppSupportLines');
    Route::post('admin/help/chat/add_new/submit', [SupportController::class, 'addNewWhatsAppSupportLineSubmit'])->name('addNewWhatsAppSupportLineSubmit');
    Route::put('admin/help/chat/edit/{id}/submit', [SupportController::class, 'editWhatsAppSupportLineSubmit'])->name('editWhatsAppSupportLineSubmit');






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

    // permissions
    Route::get('/roles/{role:uuid}/permissions', function (Role $role) {
        return view('admin.roles.permissions-edit', compact('role'));
    })->name('role.permissions.edit');


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
    Route::get('admin/finance/dues/variation/delete/{id}', [AssociationDueController::class, 'removeAssociationVariation'])->name('removeAssociationVariation');

    Route::get('admin/finance/dues/view/waiver_and_payment/print/{uuid}', [AssociationDueController::class, 'printAssociationDueWaiverAndPayment'])->name('due.paidOrWaived.download');
    Route::get('admin/finance/dues/view/debtors/print/{uuid}', [AssociationDueController::class, 'printAssociationDueDebtors'])->name('due.outstanding.download');



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

    Route::get('admin/finance/transaction/waivers', [PaymentTransasctionController::class, 'paymentWaiverRequests'])->name('paymentWaiverRequests');
    Route::get('admin/finance/transaction/waivers/search', [PaymentTransasctionController::class, 'paymentWaiverRequests'])->name('paymentWaiverRequestsSearch');
    Route::get('admin/finance/transaction/waivers/view/{uuid}', [PaymentTransasctionController::class, 'viewPaymentWaiver'])->name('viewPaymentWaiver');
    Route::put('admin/finance/transaction/waivers/proccess/{uuid}', [PaymentTransasctionController::class, 'processPaymentWaiverSubmit'])->name('processPaymentWaiverSubmit');


    // financial claims
    Route::get('admin/finance/financial_claims', [FinancialClaimController::class, 'financialClaims'])->name('financialClaims');
    Route::get('admin/finance/financial_claims/search', [FinancialClaimController::class, 'financialClaims'])->name('financialClaimsSearch');
    Route::post('admin/finance/financial_claims/add_new/submit', [FinancialClaimController::class, 'addNewFinancialClaimSubmit'])->name('addNewFinancialClaimSubmit');
    Route::get('admin/finance/financial_claim/approval/approval', [FinancialClaimController::class, 'financialClaimsPendingApproval'])->name('financialClaimsPendingApproval');
    Route::get('admin/finance/financial_claim/approval/approval/search', [FinancialClaimController::class, 'financialClaimsPendingApproval'])->name('financialClaimsPendingApprovalSearch');

    Route::get('admin/finance/financial_claim/view/{uuid}', [FinancialClaimController::class, 'viewFinancialClaim'])->name('viewFinancialClaim');
    Route::put('admin/finance/financial_claim/view/process/{uuid}', [FinancialClaimController::class, 'processFinancialClaimSubmit'])->name('processFinancialClaimSubmit');

    // invoices
    Route::get('admin/finance/other_payments/invoices', [InvoiceController::class, 'invoices'])->name('invoices');
    Route::get('admin/finance/other_payments/invoices/search', [InvoiceController::class, 'invoices'])->name('invoicesSearch');
    Route::post('admin/finance/other_payments/invoices/add_new/submit', [InvoiceController::class, 'addNewInvoiceSubmit'])->name('addNewInvoiceSubmit');
    Route::get('admin/finance/other_payments/invoices/view/{uuid}', [InvoiceController::class, 'viewInvoice'])->name('viewInvoice');
    Route::put('admin/finance/other_payments/invoices/update/submit/{uuid}', [InvoiceController::class, 'updateInvoiceSubmit'])->name('updateInvoiceSubmit');
    Route::put('admin/finance/other_payments/invoices/update/link_to_user/{uuid}', [InvoiceController::class, 'linkInvoiceToRegisteredUser'])->name('linkInvoiceToRegisteredUser');



    // donation
    Route::get('admin/finance/other_payments/donations', [DonationController::class, 'donations'])->name('donations');
    Route::get('admin/finance/other_payments/donations/search', [DonationController::class, 'donations'])->name('donationsSearch');
    Route::post('admin/finance/other_payments/donations/add_new/submit', [DonationController::class, 'addNewDonationSubmit'])->name('addNewDonationSubmit');
    Route::get('admin/finance/other_payments/donations/view/{uuid}', [DonationController::class, 'viewDonation'])->name('viewDonation');
    Route::put('admin/finance/other_payments/donations/update/{uuid}', [DonationController::class, 'updateDonationSubmit'])->name('updateDonationSubmit');
    Route::put('admin/finance/other_payments/donations/process_approval/{uuid}', [DonationController::class, 'processApproveDonationSubmit'])->name('processApproveDonationSubmit');
    Route::get('admin/finance/other_payments/donations/view/reports/{uuid}', [DonationController::class, 'viewDonationReport'])->name('viewDonationReport');
    Route::get('admin/finance/other_payments/donations/print/reports/{uuid}', [DonationController::class, 'downloadDonationReport'])->name('downloadDonationReport');

    // financialReports
    Route::get('admin/finance/reports', [PaymentTransasctionController::class, 'financialReports'])->name('financialReports');
    Route::get('admin/finance/reports/download', [PaymentTransasctionController::class, 'downloadFinancialReports'])->name('downloadFinancialReports');




    // elections
    Route::get('admin/elections', [ElectionController::class, 'allElections'])->name('allElections');
    Route::get('admin/elections/search', [ElectionController::class, 'allElections'])->name('allElectionsSearch');
    Route::post('admin/elections/create_new/submit', [ElectionController::class, 'addNewElectionSubmit'])->name('addNewElectionSubmit');
    Route::get('admin/elections/view/{uuid}', [ElectionController::class, 'viewElection'])->name('viewElection');
    Route::put('admin/elections/election/update/{uuid}', [ElectionController::class, 'updateElectionSubmit'])->name('updateElectionSubmit');




    Route::post('admin/elections/qualified_voter_groups/add_new/{uuid}', [ElectionController::class, 'addNewQualifiedVoterGroupSubmit'])->name('addNewQualifiedVoterGroupSubmit');
    Route::get('admin/elections/qualified_voter_groups/remove/{uuid}', [ElectionController::class, 'removeQualifiedVoterGroup'])->name('removeQualifiedVoterGroup');
    Route::put('admin/elections/qualified_voter_groups/update/submit/{uuid}', [ElectionController::class, 'updateQualifiedVoterGroupSubmit'])->name('updateQualifiedVoterGroupSubmit');

    Route::post('admin/elections/positions/add_new/{uuid}', [ElectionController::class, 'addNewElectionPositionSubmit'])->name('addNewElectionPositionSubmit');
    Route::put('admin/elections/positions/update/submit/{uuid}', [ElectionController::class, 'updateElectionPositionSubmit'])->name('updateElectionPositionSubmit');



    Route::post('admin/elections/positions/qualified_candidate_group/add/{uuid}', [ElectionController::class, 'addNewPositionQualifiedCandidateGroupSubmit'])->name('addNewPositionQualifiedCandidateGroupSubmit');
    Route::put('admin/elections/positions/qualified_candidate_group/update/{uuid}', [ElectionController::class, 'updateQualifiedCandidateGroupSubmit'])->name('updateQualifiedCandidateGroupSubmit');
    Route::get('admin/elections/positions/qualified_candidate_group/remove/{uuid}', [ElectionController::class, 'removeQualifiedCandidateGroup'])->name('removeQualifiedCandidateGroup');
    Route::get('admin/elections/submit_for_approval/remove/{uuid}', [ElectionController::class, 'submitElectionForApproval'])->name('submitElectionForApproval');
    Route::get('admin/elections/concent_and_awareness_form/print/{uuid}', [ElectionController::class, 'printElectionConsentAwarenessForm'])->name('printElectionConsentAwarenessForm');


    Route::get('admin/election/requests', [ElectionController::class, 'electionOperationRequests'])->name('electionOperationRequests');
    Route::put('admin/election/requests/approval/submit/{uuid}', [ElectionController::class, 'approveElectionSubmit'])->name('approveElectionSubmit');

    Route::get('admin/election/request/processed', [ElectionController::class, 'processedElectionOperationRequests'])->name('processedElectionOperationRequests');


    Route::get('admin/election/requests/prospective_candidates/{uuid}', [ElectionController::class, 'viewElectionCandidateApplication'])->name('viewElectionCandidateApplication');
    Route::put('admin/election/requests/prospective_candidates/process/submit/{uuid}', [ElectionController::class, 'processElectionCandidateRequestSubmit'])->name('processElectionCandidateRequestSubmit');

    Route::get('admin/election/requests/candidates/{uuid}', [ElectionController::class, 'electionCandidates'])->name('electionCandidates');
    Route::get('admin/election/requests/candidates/view/{uuid}', [ElectionController::class, 'viewElectionCandidatesForPosition'])->name('viewElectionCandidatesForPosition');

    Route::get('admin/election/results/{uuid}', [ElectionController::class, 'electionResults'])->name('electionResults');
    Route::get('admin/election/results/per_position/{election}/{position}', [ElectionController::class, 'viewElectionResultForPosition'])->name('viewElectionResultForPosition');

    Route::get('admin/election/print_results/{uuid}', [ElectionController::class, 'printElectionResults'])->name('printElectionResults');

    Route::get('admin/election/results/votes/view/{uuid}', [ElectionController::class, 'viewElectionVoters'])->name('viewElectionVoters');


    // media publications
    Route::get('admin/media/publications/home', [MediaPublicationController::class, 'mediaPublications'])->name('mediaPublications');
    Route::get('admin/media/publications/home/search', [MediaPublicationController::class, 'mediaPublications'])->name('mediaPublicationsSearch');
    Route::post('admin/media/publications/add_new/submit', [MediaPublicationController::class, 'addNewMediaPublicationSubmit'])->name('addNewMediaPublicationSubmit');
    Route::get('admin/media/publications/view/{uuid}', [MediaPublicationController::class, 'viewMediaPublication'])->name('viewMediaPublication');
    Route::put('admin/media/publications/update/{uuid}', [MediaPublicationController::class, 'updateMediaPublication'])->name('updateMediaPublication');
    Route::put('admin/media/publications/review/{uuid}', [MediaPublicationController::class, 'reviewMediaPublicationSubmit'])->name('reviewMediaPublicationSubmit');

    Route::get('admin/media/publications/home/tag', [MediaPublicationController::class, 'mediaPublications'])->name('mediaPublicationWihTag');

    Route::get('admin/media/publications/delete/{uuid}', [MediaPublicationController::class, 'deleteMediaPublication'])->name('deleteMediaPublication');




    //

    Route::get('admin/media/publications/approval', [MediaPublicationController::class, 'mediaPublicationsPendingApproval'])->name('mediaPublicationsPendingApproval');
    Route::get('admin/media/publications/approval/search', [MediaPublicationController::class, 'mediaPublicationsPendingApproval'])->name('mediaPublicationsPendingApprovalSearch');

    Route::get('admin/media/publications/draft', [MediaPublicationController::class, 'mediaPublicationsDraft'])->name('mediaPublicationsDraft');
    Route::get('admin/media/publications/draft/search', [MediaPublicationController::class, 'mediaPublicationsDraft'])->name('mediaPublicationsDraftSearch');


    Route::get('admin/media/publication/tag', [MediaPublicationController::class, 'mediaPublicationTags'])->name('mediaPublicationTags');
    Route::post('admin/media/publication/tag/add_new/submit', [MediaPublicationController::class, 'addNewPublicationTagSubmit'])->name('addNewPublicationTagSubmit');
    Route::put('admin/media/publication/tag/update/submit/{slug}', [MediaPublicationController::class, 'editPublicationTagSubmit'])->name('editPublicationTagSubmit');
    Route::get('admin/media/publication/tag/remove/{slug}', [MediaPublicationController::class, 'removePublicationTag'])->name('removePublicationTag');


    Route::get('admin/media/publication/tag', [MediaPublicationController::class, 'mediaPublicationTags'])->name('mediaPublicationTags');

    // academic resources
    // past questions
    Route::get('admin/academic_resources/past_questions/home', [PastQuestionController::class, 'pastQuestionsHome'])->name('pastQuestionsHome');
    Route::get('admin/academic_resources/past_questions/home/new', [PastQuestionController::class, 'createPastQuestion'])->name('createPastQuestion');
    Route::post('admin/academic_resources/past_questions/home/create/submit', [PastQuestionController::class, 'addNewPastQuestionSubmit'])->name('addNewPastQuestionSubmit');

    Route::get('admin/academic_resources/past_questions/view/{uuid}', [PastQuestionController::class, 'viewPastQuestion'])->name('viewPastQuestion');
    Route::put('admin/academic_resources/past_questions/review/submit/{uuid}', [PastQuestionController::class, 'reviewPastQuestionSubmit'])->name('reviewPastQuestionSubmit');

    Route::put('admin/academic_resources/past_questions/update/submit/{uuid}', [PastQuestionController::class, 'updatePastQuestionSubmit'])->name('updatePastQuestionSubmit');
    Route::get('admin/academic_resources/past_questions/delete/{uuid}', [PastQuestionController::class, 'deletePastQuestion'])->name('deletePastQuestion');

    Route::get('admin/academic_resources/past_questions/edit/{uuid}', [PastQuestionController::class, 'editPastQuestion'])->name('editPastQuestion');

    Route::get('admin/academic_resources/past_questions/approval', [PastQuestionController::class, 'pastQuestionsApproval'])->name('pastQuestionsApproval');

    // recommended books
    Route::get('admin/academic_resources/recommended_books', [RecommendedBooksController::class, 'recommendedBooks'])->name('recommendedBooks');
    Route::get('admin/academic_resources/recommended_books/search', [RecommendedBooksController::class, 'recommendedBooks'])->name('recommendedBooksSearch');
    Route::get('admin/academic_resources/recommended_books/tag/{slug}', [RecommendedBooksController::class, 'recommendedBooks'])->name('recommendedBooksWihTag');
    Route::post('admin/academic_resources/recommended_books/add_new/submit', [RecommendedBooksController::class, 'addNewRecommendedBookSubmit'])->name('addNewRecommendedBookSubmit');
    Route::get('admin/academic_resources/recommended_books/view/{slug}', [RecommendedBooksController::class, 'viewRecommendedBook'])->name('viewRecommendedBook');
    Route::put('admin/academic_resources/recommended_books/update/submit/{slug}', [RecommendedBooksController::class, 'updateRecommendedBookSubmit'])->name('updateRecommendedBookSubmit');
    Route::get('admin/academic_resources/recommended_books/delete/{slug}', [RecommendedBooksController::class, 'deleteRecommendedBook'])->name('deleteRecommendedBook');


    Route::get('admin/academic_resources/recommended_book/tag', [RecommendedBooksController::class, 'recommendedBooksTags'])->name('recommendedBooksTags');
    Route::post('admin/academic_resources/recommended_book/tag/add_new/submit', [RecommendedBooksController::class, 'addNewBookTagSubmit'])->name('addNewBookTagSubmit');
    Route::put('admin/academic_resources/recommended_book/tag/edit/submit/{slug}', [RecommendedBooksController::class, 'editBookTagSubmit'])->name('editBookTagSubmit');
    Route::get('admin/academic_resources/recommended_book/tag/delete/{slug}', [RecommendedBooksController::class, 'removeBookTag'])->name('removeBookTag');

    // recommended courses
    Route::get('admin/academic_resources/recommended_courses', [RecommendedCourseController::class, 'recommendedCourses'])->name('recommendedCourses');
    Route::get('admin/academic_resources/recommended_courses/search', [RecommendedCourseController::class, 'recommendedCourses'])->name('recommendedCoursesSearch');
    Route::get('admin/academic_resources/recommended_courses/tag/{slug}', [RecommendedCourseController::class, 'recommendedCourses'])->name('recommendedCoursesWihTag');
    Route::post('admin/academic_resources/recommended_courses/add_new/submit', [RecommendedCourseController::class, 'addNewRecommendedCourseSubmit'])->name('addNewRecommendedCourseSubmit');
    Route::get('admin/academic_resources/recommended_courses/view/{slug}', [RecommendedCourseController::class, 'viewRecommendedCourse'])->name('viewRecommendedCourse');
    Route::put('admin/academic_resources/recommended_courses/edit/{slug}', [RecommendedCourseController::class, 'updateRecommendedCoursesubmit'])->name('updateRecommendedCoursesubmit');
    Route::get('admin/academic_resources/recommended_courses/delete/{slug}', [RecommendedCourseController::class, 'deleteRecommendedCourse'])->name('deleteRecommendedCourse');




    Route::get('admin/academic_resources/recommended_course/tag', [RecommendedCourseController::class, 'recommendedCourseTags'])->name('recommendedCourseTags');
    Route::post('admin/academic_resources/recommended_course/tag/add_new/submit', [RecommendedCourseController::class, 'addNewCourseTagSubmit'])->name('addNewCourseTagSubmit');
    Route::put('admin/academic_resources/recommended_course/tag/edit/submit/{slug}', [RecommendedCourseController::class, 'editCourseTagSubmit'])->name('editCourseTagSubmit');
    Route::get('admin/academic_resources/recommended_course/tag/delete/{slug}', [RecommendedCourseController::class, 'removeCourseTag'])->name('removeCourseTag');


    // industry affiliates
    Route::get('admin/academic_resources/industry_affiliates', [IndustryAffiliationController::class, 'industryAffiliates'])->name('industryAffiliates');
    Route::get('admin/academic_resources/industry_affiliates/search', [IndustryAffiliationController::class, 'industryAffiliates'])->name('industryAffiliatesSearch');
    Route::get('admin/academic_resources/industry_affiliates/tag/{slug}', [IndustryAffiliationController::class, 'industryAffiliates'])->name('industryAffiliatesWihTag');
    Route::post('admin/academic_resources/industry_affiliates/add_new', [IndustryAffiliationController::class, 'addNewIndustryAffiliateSubmit'])->name('addNewIndustryAffiliateSubmit');
    Route::get('admin/academic_resources/industry_affiliates/view/{slug}', [IndustryAffiliationController::class, 'viewIndustryAffiliates'])->name('viewIndustryAffiliates');
    Route::put('admin/academic_resources/industry_affiliates/edit/submit/{slug}', [IndustryAffiliationController::class, 'updateIndustryAffiliateSubmit'])->name('updateIndustryAffiliateSubmit');
    Route::get('admin/academic_resources/industry_affiliates/delete/{slug}', [IndustryAffiliationController::class, 'deleteIndustryAffiliate'])->name('deleteIndustryAffiliate');


    Route::get('admin/academic_resources/industry_affiliate/tag', [IndustryAffiliationController::class, 'industryAffiliatesTags'])->name('industryAffiliatesTags');
    Route::post('admin/academic_resources/industry_affiliate/tag/add_new/submit', [IndustryAffiliationController::class, 'addNewIndAffiliateSubmit'])->name('addNewIndAffiliateSubmit');
    Route::put('admin/academic_resources/industry_affiliate/tag/{slug}/edit/submit', [IndustryAffiliationController::class, 'editIndustryAffilateTagSubmit'])->name('editIndustryAffilateTagSubmit');
    Route::get('admin/academic_resources/industry_affiliate/tag/{slug}/delete/submit', [IndustryAffiliationController::class, 'removeIndustryAffiliatesTag'])->name('removeIndustryAffiliatesTag');

// archive
    Route::get('admin/archive', [ArchiveController::class, 'archive'])->name('archive');
    Route::get('admin/archive/search', [ArchiveController::class, 'archive'])->name('archiveSearch');
    Route::post('admin/archive/file/add_new/submit', [ArchiveController::class, 'addNewArchiveFileSubmit'])->name('addNewArchiveFileSubmit');
    Route::get('admin/archive/file/view/{uuid}', [ArchiveController::class, 'viewArchiveFile'])->name('viewArchiveFile');
    Route::put('admin/archive/file/edit/submit/{uuid}', [ArchiveController::class, 'updateArchiveFileSubmit'])->name('updateArchiveFileSubmit');
    Route::get('admin/archive/file/delete/{uuid}', [ArchiveController::class, 'deleteArchiveFile'])->name('deleteArchiveFile');


    // leadership

    Route::get('admin/leadership/student_reps', [LeadershipController::class, 'studentLeadership'])->name('studentLeadership');

    Route::get('admin/leadership/student_rep/config/tenures', [LeadershipController::class, 'studentLeadershipTenures'])->name('studentLeadershipTenures');
    Route::post('admin/leadership/student_rep/config/tenures/add_new', [LeadershipController::class, 'addNewStudentLeadershipTenureSubmit'])->name('addNewStudentLeadershipTenureSubmit');
    Route::put('admin/leadership/student_rep/config/tenures/edit/{slug}', [LeadershipController::class, 'editStudentLeadershipTenureSubmit'])->name('editStudentLeadershipTenureSubmit');
    Route::get('admin/leadership/student_rep/config/tenures/delete/{slug}', [LeadershipController::class, 'deleteStudentLeadershipTenure'])->name('deleteStudentLeadershipTenure');

    // studentLeadershipBodies
    Route::get('admin/leadership/student_rep/config/arms', [LeadershipController::class, 'studentLeadershipBodies'])->name('studentLeadershipBodies');
    Route::post('admin/leadership/student_rep/config/arms/add_new/submit', [LeadershipController::class, 'addNewStudentLeadershipArmSubmit'])->name('addNewStudentLeadershipArmSubmit');
    Route::put('admin/leadership/student_rep/config/arms/edit/{slug}/submit', [LeadershipController::class, 'editStudentLeadershipArmSubmit'])->name('editStudentLeadershipArmSubmit');
    Route::get('admin/leadership/student_rep/config/arms/delete/{slug}/submit', [LeadershipController::class, 'deleteStudentLeadershipArm'])->name('deleteStudentLeadershipArm');

    


});
