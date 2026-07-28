<?php

namespace App\Livewire;

use App\Models\Finance\Settings\ManualPaymentMethod as SettingsManualPaymentMethod;
use Livewire\Component;

class ManualPaymentMethodsToggle extends Component
{
    public bool $cash          = true;
    public bool $bank_transfer = true;
    public bool $cheque        = true;

    public bool $saved = false;

    public function mount(): void
    {
        $settings = SettingsManualPaymentMethod::first();

        if ($settings) {
            $this->cash          = $settings->cash;
            $this->bank_transfer = $settings->bank_transfer;
            $this->cheque        = $settings->cheque;
        }
    }

    public function toggle(string $field): void
    {
        if (!in_array($field, ['cash', 'bank_transfer', 'cheque'])) return;

        $this->$field = !$this->$field;

        SettingsManualPaymentMethod::updateOrCreate([], [
            'cash'          => $this->cash,
            'bank_transfer' => $this->bank_transfer,
            'cheque'        => $this->cheque,
        ]);

        $this->saved = true;
        $this->dispatch('saved');
    }

    public function render()
    {
        return view('livewire.manual-payment-methods-toggle');
    }
}
