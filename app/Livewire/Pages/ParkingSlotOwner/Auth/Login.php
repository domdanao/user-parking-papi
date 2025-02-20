<?php

namespace App\Livewire\Pages\ParkingSlotOwner\Auth;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Layout;

class Login extends Component
{
    public string $email = '';
    public string $password = '';
    public bool $remember = false;

    public function login()
    {
        $this->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::guard('parking-slot-owner')->attempt([
            'email' => $this->email,
            'password' => $this->password,
        ], $this->remember)) {
            session()->regenerate();

            return redirect()->intended(route('parking-slot-owner.dashboard'));
        }

        $this->addError('email', trans('auth.failed'));
    }

    #[Layout('layouts.guest')]
    public function render()
    {
        return view('pages.parking-slot-owner.auth.login');
    }
}
