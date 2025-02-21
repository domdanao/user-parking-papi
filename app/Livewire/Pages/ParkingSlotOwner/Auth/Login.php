<?php

namespace App\Livewire\Pages\ParkingSlotOwner\Auth;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.guest')]
class Login extends Component
{

    public string $email = '';
    public string $password = '';

    public function rules()
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    public function login()
    {
        $credentials = $this->validate();

        if (Auth::guard('parking-slot-owner')->attempt($credentials)) {
            session()->regenerate();

            return redirect()->intended(route('parking-slot-owner.dashboard'));
        }

        $this->addError('email', 'The provided credentials do not match our records.');
    }

    public function render()
    {
        return view('livewire.pages.parking-slot-owner.auth.login');
    }
}
