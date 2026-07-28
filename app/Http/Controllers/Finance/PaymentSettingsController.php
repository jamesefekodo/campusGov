<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Finance\Settings\BankAccount;
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
}
