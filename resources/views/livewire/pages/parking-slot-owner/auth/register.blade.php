<div>
    <div class="sm:mx-auto sm:w-full sm:max-w-2xl">
        <h2 class="mt-10 text-center text-2xl font-bold leading-9 tracking-tight text-gray-900">
            Register as a Parking Slot Owner
        </h2>
    </div>

    <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-2xl">
        <form wire:submit="register" class="space-y-8">
            <!-- Personal Information -->
            <div class="space-y-6">
                <h3 class="text-lg font-medium leading-6 text-gray-900">Personal Information</h3>
                
                <div>
                    <x-input-label for="name" value="Name" />
                    <div class="mt-2">
                        <x-text-input wire:model="name" id="name" type="text" required class="block w-full" />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>
                </div>

                <div>
                    <x-input-label for="email" value="Email" />
                    <div class="mt-2">
                        <x-text-input wire:model="email" id="email" type="email" required class="block w-full" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>
                </div>

                <div>
                    <x-input-label for="contact_number" value="Contact Number" />
                    <div class="mt-2">
                        <x-text-input wire:model="contact_number" id="contact_number" type="text" required class="block w-full" />
                        <x-input-error :messages="$errors->get('contact_number')" class="mt-2" />
                    </div>
                </div>
            </div>

            <!-- Business Information -->
            <div class="space-y-6">
                <h3 class="text-lg font-medium leading-6 text-gray-900">Business Information</h3>
                
                <div>
                    <x-input-label for="business_name" value="Business Name" />
                    <div class="mt-2">
                        <x-text-input wire:model="business_name" id="business_name" type="text" required class="block w-full" />
                        <x-input-error :messages="$errors->get('business_name')" class="mt-2" />
                    </div>
                </div>

                <div>
                    <x-input-label for="business_address" value="Business Address" />
                    <div class="mt-2">
                        <x-text-input wire:model="business_address" id="business_address" type="text" required class="block w-full" />
                        <x-input-error :messages="$errors->get('business_address')" class="mt-2" />
                    </div>
                </div>
            </div>

            <!-- Payment Information -->
            <div class="space-y-6">
                <h3 class="text-lg font-medium leading-6 text-gray-900">Payment Information</h3>
                
                <div>
                    <x-input-label for="payment_details.bank_name" value="Bank Name" />
                    <div class="mt-2">
                        <x-text-input wire:model="payment_details.bank_name" id="payment_details.bank_name" type="text" required class="block w-full" />
                        <x-input-error :messages="$errors->get('payment_details.bank_name')" class="mt-2" />
                    </div>
                </div>

                <div>
                    <x-input-label for="payment_details.account_name" value="Account Name" />
                    <div class="mt-2">
                        <x-text-input wire:model="payment_details.account_name" id="payment_details.account_name" type="text" required class="block w-full" />
                        <x-input-error :messages="$errors->get('payment_details.account_name')" class="mt-2" />
                    </div>
                </div>

                <div>
                    <x-input-label for="payment_details.account_number" value="Account Number" />
                    <div class="mt-2">
                        <x-text-input wire:model="payment_details.account_number" id="payment_details.account_number" type="text" required class="block w-full" />
                        <x-input-error :messages="$errors->get('payment_details.account_number')" class="mt-2" />
                    </div>
                </div>
            </div>

            <!-- Account Security -->
            <div class="space-y-6">
                <h3 class="text-lg font-medium leading-6 text-gray-900">Account Security</h3>
                
                <div>
                    <x-input-label for="password" value="Password" />
                    <div class="mt-2">
                        <x-text-input wire:model="password" id="password" type="password" required class="block w-full" />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>
                </div>

                <div>
                    <x-input-label for="password_confirmation" value="Confirm Password" />
                    <div class="mt-2">
                        <x-text-input wire:model="password_confirmation" id="password_confirmation" type="password" required class="block w-full" />
                    </div>
                </div>
            </div>

            <div>
                <x-primary-button class="w-full">
                    Register
                </x-primary-button>
            </div>
        </form>

        <p class="mt-10 text-center text-sm text-gray-500">
            Already registered?
            <a href="{{ route('parking-slot-owner.login') }}" class="font-semibold leading-6 text-indigo-600 hover:text-indigo-500">
                Sign in here
            </a>
        </p>
    </div>
</div>
