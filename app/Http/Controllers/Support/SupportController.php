<?php

namespace App\Http\Controllers\Support;

use App\Http\Controllers\Controller;
use App\Models\Support\WhatsappSupportLine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupportController extends Controller
{
    public function whatsAppSupportLines()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('manage_whatsapp_support'),
            403
        );

        $whatsappLines = WhatsappSupportLine::where('tenant_id', tenant()->id)->orderBy('name')->get();
        $data = [
            'whatsappLines' => $whatsappLines,
        ];

        return view('users.admin.support.whatsappSupportLines', compact('data'));
    }
    public function addNewWhatsAppSupportLineSubmit(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'number' => 'required|string',
        ]);

        $whatsappLines = WhatsappSupportLine::where('tenant_id', tenant()->id)->orderBy('name')->get();

        if (!$whatsappLines >= 4) {
            return redirect()->back()->with('error', 'Limit reached, you cannot have more than 4 WhatsApp support lines!');
        }

        $line = new WhatsappSupportLine();
        $line->name = $request->name;
        $line->number = $request->number;
        $line->save();
        return redirect()->back()->with('success', 'Done!');
    }

    public function editWhatsAppSupportLineSubmit($id, Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'number' => 'required|string',
            'is_active' => 'required',
        ]);
        $line = WhatsappSupportLine::where('id', $id)->first();
        if (!$line) {
            return redirect()->back()->with('error', 'No records found!');
        }
        $line->name = $request->name;
        $line->number = $request->number;
        $line->is_active = $request->is_active;
        $line->save();
        return redirect()->back()->with('success', 'Done!');
    }
}
