<div>
    <div class="sm:mx-auto sm:w-full sm:max-w-sm">
        <h2 class="mt-10 text-center text-2xl font-bold leading-9 tracking-tight text-gray-900">
            Sign in to your account
        </h2>
    </div>

    <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">
        <form wire:submit="login" class="space-y-6">
            <div>
                <x-input-label for="email" value="Email" />
                <div class="mt-2">
                    <x-text-input wire:model="email" id="email" type="email" required class="block w-full" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>
            </div>

            <div>
                <x-input-label for="password" value="Password" />
                <div class="mt-2">
                    <x-text-input wire:model="password" id="password" type="password" required class="block w-full" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>
            </div>

            <div>
                <x-primary-button class="w-full">
                    Sign in
                </x-primary-button>
            </div>
        </form>

        <p class="mt-10 text-center text-sm text-gray-500">
            Not registered yet?
            <a href="{{ route('parking-slot-owner.register') }}" class="font-semibold leading-6 text-indigo-600 hover:text-indigo-500">
                Create an account
            </a>
        </p>
    </div>
</div>
