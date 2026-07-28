<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Finance\Settings\BankAccount;
use App\Models\Finance\Settings\ElectronicPaymentConfig;
use App\Models\Finance\Settings\ElectronicPaymentMethod;
use App\Models\Finance\Settings\ManualPaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Contracts\Service\Attribute\Required;

class PaymentSettingsController extends Controller
{
    public function manualPaymentMethods()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('manage_payment_settings'),
            403
        );

        $manualMethodSetting = ManualPaymentMethod::firstOrCreate();
        $bankAccounts = BankAccount::orderBy('account_name')->get();
        $data = [
            'manualMethodSetting' => $manualMethodSetting,
            'bankAccounts' => $bankAccounts,
        ];

        return view('users.admin.finance.settings.paymentMethods.manualMethods', compact('data'));
    }
    public function addNewBankAccountSubmit(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('manage_payment_settings'),
            403
        );

        $request->validate([
            'account_number' => 'numeric',
            'account_name' => 'required',
            'bank' => 'required',
        ]);

        BankAccount::create(
            [
                'account_number' => trim($request->account_number),
                'account_name' => trim($request->account_name),
                'bank' => trim($request->bank),
            ]
        );
        return redirect()->back()->with('success', 'Done!');
    }
    public function updateBankAccount($id, Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('manage_payment_settings'),
            403
        );
        $request->validate([
            'account_number' => 'numeric',
            'account_name' => 'required',
            'bank' => 'required',
            'is_active' => 'required',
        ]);


        $account = BankAccount::where('id', $id)->first();
        if (!$account) {
            return redirect()->back()->with('error', 'Records not found.');
        }

        $account->update([
            'account_number' => trim($request->account_number),
            'account_name' => trim($request->account_name),
            'bank' => trim($request->bank),
            'is_active' => $request->is_active,
        ]);
        return redirect()->back()->with('success', 'Done!');
    }
    public function removeBankAccount($id)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('manage_payment_settings'),
            403
        );

        $account = BankAccount::where('id', $id)->first();
        if (!$account) {
            return redirect()->back()->with('error', 'Records not found.');
        }
        $account->delete();
        return redirect()->back()->with('success', 'Done!');
    }

    public function electronicPaymentMethods()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('manage_payment_settings'),
            403
        );

        $electronicPaymentMethods = ElectronicPaymentMethod::orderBy('name')->get();
        $data = [
            'electronicPaymentMethods' => $electronicPaymentMethods,
        ];

        return view('users.admin.finance.settings.paymentMethods.electronicMethods', compact('data'));
    }
    public function viewElectronicPaymentMethod($uuid)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('manage_payment_settings'),
            403
        );

        $method = ElectronicPaymentMethod::where('uuid', $uuid)->first();
        if (!$method) {
            return redirect()->back()->with('error', 'Records not found.');
        }

        $data = [
            'method' => $method,
        ];

        return view('users.admin.finance.settings.paymentMethods.viewElectronicMethod', compact('data'));
    }
    public function updateElectronicPaymentApiConfig($id, Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('manage_payment_settings'),
            403
        );
        $request->validate([
            'client_id' => 'nullable',
            'public_key' => 'nullable',
            'secret_key' => 'nullable',
            'encryption_key' => 'nullable',
            'is_active' => 'required',
            'default_status' => 'nullable',
        ]);

        $config = ElectronicPaymentConfig::where('id', $id)->first();
        if (!$config) {
            return redirect()->back()->with('error', 'Records not found.');
        }
        $config->update(
            [
                'client_id' => $request->client_id,
                'public_key' => $request->public_key,
                'secret_key' => $request->secret_key,
                'encryption_key' => $request->encryption_key,
                'is_active' => $request->is_active,
            ]
        );

        if ($request->filled('default_status') && $request->default_status == 'default' && $config->paymentMethod && $config->paymentMethod->default_api !== $config->id && $config->is_active == true && $config->paymentMethod->apiConfigs->count() > 1) {
            $config->paymentMethod->update([
                'default_api' => $config->id,
            ]);
        }


        if ($config->paymentMethod->defaultApi->is_active !== true) {
            $config->paymentMethod->defaultApi->update([
                'is_active' => true,
            ]);
        }

        return redirect()->back()->with('success', 'Done!');
    }
}
