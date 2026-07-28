<?php

namespace App\Livewire;

use App\Models\Finance\Settings\ElectronicPaymentMethod;
use Livewire\Component;

class ElectronicPaymentStatusToggle extends Component
{
    public string $slug;
    public bool $is_active = true;
    public bool $saved = false;

    public function mount(string $slug): void
    {
        $method = ElectronicPaymentMethod::where('slug', $slug)->firstOrFail();
        $this->slug      = $slug;
        $this->is_active = $method->is_active;
    }

    public function toggle(): void
    {
        $this->is_active = !$this->is_active;

        ElectronicPaymentMethod::where('slug', $this->slug)
            ->update(['is_active' => $this->is_active]);

        $this->saved = true;
    }

    public function render()
    {
        return view('livewire.electronic-payment-status-toggle');
    }
}
