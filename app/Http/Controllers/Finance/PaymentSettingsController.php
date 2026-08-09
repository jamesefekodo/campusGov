<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Finance\Settings\BankAccount;
use App\Models\Finance\Settings\ElectronicPaymentConfig;
use App\Models\Finance\Settings\ElectronicPaymentMethod;
use App\Models\Finance\Settings\ManualPaymentMethod;
use App\Models\Finance\Settings\Tax;
use App\Models\Finance\Transactions\PaymentRegularization;
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

        // prevent delete if it has been used for regularixzation
        if (
            PaymentRegularization::where('bank_account_id', $account->id)->exists()
        ) {
            return redirect()->back()->with(
                'error',
                'Cannot be removed because this account has been used for payment regularization.'
            );
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

        $config = ElectronicPaymentConfig::where('payment_method', $method->slug)
            ->where('tenant_id', $user->tenant_id)
            ->first();
        if (!$config) {
            return redirect()->back()->with('error', 'Records not found.');
        }

        $data = [
            'method' => $method,
            'config' => $config,
        ];

        return view('users.admin.finance.settings.paymentMethods.viewElectronicMethod', compact('data'));
    }
    public function updateElectronicPaymentApiConfig($id, Request $request)
    {
        // replaced with createUpdateElectronicPaymentApiConfig method for multitenancy
        // /** @var \App\Models\User $user */
        // $user = Auth::user();

        // abort_unless(
        //     $user->hasPermission('manage_payment_settings'),
        //     403
        // );
        // $request->validate([
        //     'client_id' => 'nullable',
        //     'public_key' => 'nullable',
        //     'secret_key' => 'nullable',
        //     'encryption_key' => 'nullable',
        //     'is_active' => 'required',
        //     'default_status' => 'nullable',
        // ]);

        // $config = ElectronicPaymentConfig::where('id', $id)->first();
        // if (!$config) {
        //     return redirect()->back()->with('error', 'Records not found.');
        // }

        // $config->update(
        //     [
        //         'client_id' => $request->client_id,
        //         'public_key' => $request->public_key,
        //         'secret_key' => $request->secret_key,
        //         'encryption_key' => $request->encryption_key,
        //         'is_active' => $request->is_active,
        //     ]
        // );

        // if ($request->filled('default_status') && $request->default_status == 'default' && $config->paymentMethod && $config->paymentMethod->default_api !== $config->id && $config->is_active == true && $config->paymentMethod->apiConfigs->count() > 1) {
        //     $config->paymentMethod->update([
        //         'default_api' => $config->id,
        //     ]);
        // }


        // if ($config->paymentMethod->defaultApi->is_active !== true) {
        //     $config->paymentMethod->defaultApi->update([
        //         'is_active' => true,
        //     ]);
        // }

        return redirect()->back()->with('success', 'Done!');
    }
    public function createUpdateElectronicPaymentApiConfig($uuid, Request $request)
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
        ]);

        $method = ElectronicPaymentMethod::where('uuid', $uuid)->first();
        if (!$method) {
            return redirect()->back()->with('error', 'Records not found.');
        }

        $config = ElectronicPaymentConfig::updateOrCreate(
            [
                'payment_method' => $method->slug,
                'tenant_id' => $user->tenant_id,
            ],
            [
                'client_id' => $request->client_id,
                'public_key' => $request->public_key,
                'secret_key' => $request->secret_key,
                'encryption_key' => $request->encryption_key,
                'is_active' => $request->is_active,
            ]
        );
        return redirect()->back()->with('success', 'Done!');
    }
    public function taxes()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('manage_payment_settings'),
            403
        );

        $taxes = Tax::orderBy('tax_name')->get();

        $data = [
            'taxes' => $taxes,
        ];
        return view('users.admin.finance.settings.taxes.taxes', compact('data'));
    }
    public function addNewTaxSubmit(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('manage_payment_settings'),
            403
        );

        $request->validate([
            'tax_name' => 'required|unique:taxes,tax_name',
            'tax_method' => 'required',
            'tax_rate' => 'required',
        ]);

        Tax::create(
            [
                'tax_name' => trim($request->tax_name),
                'tax_method' => trim($request->tax_method),
                'tax_rate' => trim($request->tax_rate),
            ]
        );
        return redirect()->back()->with('success', 'Done!');
    }
    public function editTaxSubmit($uuid, Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('manage_payment_settings'),
            403
        );
        $tax = Tax::where('uuid', $uuid)->first();
        if (!$tax) {
            return redirect()->back()->with('error', 'Records not found.');
        }

        $request->validate([
            'tax_name' => 'required|unique:taxes,tax_name,' . $tax->id,
            'tax_method' => 'required',
            'tax_rate' => 'required',
            'is_active' => 'required',
        ]);



        $tax->update([
            'tax_name' => $request->tax_name,
            'tax_method' => $request->tax_method,
            'tax_rate' => $request->tax_rate,
            'is_active' => $request->is_active,
        ]);
        return redirect()->back()->with('success', 'Done!');
    }
}
