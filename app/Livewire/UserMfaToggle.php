<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;

class UserMfaToggle extends Component
{
    public int  $userId;
    public bool $mfa;
    public bool $saved = false;

    public function mount(int $userId): void
    {
        $this->userId = $userId;
        $this->mfa    = (bool) User::findOrFail($userId)->require_login_mfa;
    }

    public function toggle(): void
    {
        $this->mfa = !$this->mfa;

        User::where('id', $this->userId)
            ->update(['require_login_mfa' => $this->mfa]);

        $this->saved = true;
    }

    public function render()
    {
        return view('livewire.user-mfa-toggle');
    }
}
