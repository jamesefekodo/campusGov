<?php

namespace App\Livewire;

use App\Models\Finance\Settings\ManualPaymentMethod as SettingsManualPaymentMethod;
use Livewire\Component;

class ManualPaymentMethodsToggle extends Component
{
    public bool $cash = true;
    public bool $bank_transfer = true;
    public bool $cheque = true;
    public bool $allow_manual_dues = true;

    public bool $saved = false;

    public ?int $settingsId = null;

    public function mount(): void
    {
        $settings = SettingsManualPaymentMethod::query()
            ->where('tenant_id', tenant()->id)
            ->first();

        if ($settings) {
            $this->settingsId = $settings->id;

            $this->cash = $settings->cash;
            $this->bank_transfer = $settings->bank_transfer;
            $this->cheque = $settings->cheque;
            $this->allow_manual_dues = $settings->allow_manual_dues;
        }
    }

    public function toggle(string $field): void
    {
        if (!in_array($field, ['cash', 'bank_transfer', 'cheque', 'allow_manual_dues'])) {
            return;
        }

        $this->$field = !$this->$field;

        if ($this->settingsId) {
            // Update the exact settings row loaded for this tenant.
            SettingsManualPaymentMethod::query()
                ->whereKey($this->settingsId)
                ->update([
                    'cash' => $this->cash,
                    'bank_transfer' => $this->bank_transfer,
                    'cheque' => $this->cheque,
                    'allow_manual_dues' => $this->allow_manual_dues,
                ]);
        } else {
            // Create the settings row for this tenant.
            $settings = SettingsManualPaymentMethod::create([
                'tenant_id' => tenant()->id,
                'cash' => $this->cash,
                'bank_transfer' => $this->bank_transfer,
                'cheque' => $this->cheque,
                'allow_manual_dues' => $this->allow_manual_dues,
            ]);

            $this->settingsId = $settings->id;
        }

        $this->saved = true;
        $this->dispatch('saved');
    }

    public function render()
    {
        return view('livewire.manual-payment-methods-toggle');
    }
}
