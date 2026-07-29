<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PaymentTransasctionController extends Controller
{
    public function memberPayAssociationDue($uuid, Request $request)
    {
        $request->validate([
            'manual_payment_method' => 'required|in:cash,bank_transfer,cheque',
        ]);
    }
}
