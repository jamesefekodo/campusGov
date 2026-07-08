<?php

use App\Http\Controllers\SystemSettings\Setup\SystemSetupController;
use Illuminate\Support\Facades\Route;




// installation
Route::get('system_setup', [SystemSetupController::class, 'installSystem'])->name('installSystem');
Route::get('system_setup/check_basic_requrement', [SystemSetupController::class, 'installSystemBasicRequirementCheck'])->name('installSystemBasicRequirementCheck');
Route::get('system_setup/install/step1', [SystemSetupController::class, 'installSystemStep1'])->name('installSystemStep1');
Route::post('system_setup/install/step1/submit', [SystemSetupController::class, 'installSystemSubmitAppSystemInfo'])->name('installSystemSubmitAppSystemInfo');
Route::get('system_setup/install/step2', [SystemSetupController::class, 'installSystemStep2'])->name('installSystemStep2');
Route::post('system_setup/install/step2/submit', [SystemSetupController::class, 'installSystemSubmitDatabaseInfo'])->name('installSystemSubmitDatabaseInfo');
Route::get('system_setup/install/step3', [SystemSetupController::class, 'installSystemStep3'])->name('installSystemStep3');
Route::post('system_setup/install/step3/submit', [SystemSetupController::class, 'installSystemSubmitProductKeys'])->name('installSystemSubmitProductKeys');
Route::get('system_setup/install/step4', [SystemSetupController::class, 'installSystemStep4'])->name('installSystemStep4');
Route::post('system_setup/install/step4/submit', [SystemSetupController::class, 'installSystemFetchLicenceDetails'])->name('installSystemFetchLicenceDetails');
Route::get('system_setup/install/step5', [SystemSetupController::class, 'installSystemStep5'])->name('installSystemStep5');
Route::post('system_setup/install/step5/submit', [SystemSetupController::class, 'setupRootAccount'])->name('setupRootAccount');

Route::get('system_setup/install/step5b', [SystemSetupController::class, 'installSystemStep5b'])->name('installSystemConfigureEmailService');
Route::post('system_setup/install/step5b/submit', [SystemSetupController::class, 'installSystemConfigureEmailServiceSubmit'])->name('installSystemConfigureEmailServiceSubmit');
Route::get('system_setup/install/setupComplete', [SystemSetupController::class, 'installSystemSetupComplete'])->name('installSystemSetupComplete');
Route::get('system_setup/setup_info/refresh', [SystemSetupController::class, 'setupInfoRefresh'])->name('setupInfoRefresh');
Route::post('system_setup/setupInfo/refresh/submit', [SystemSetupController::class, 'setupInfoRefreshSubmit'])->name('setupInfoRefreshSubmit');










// basic/general routes
Route::middleware(['setup_middleware', 'authenticationMiddlewares'])->group(function () {
    Route::get('/', function () {
        return view('welcome');
    });
});
