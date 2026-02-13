<x-layouts.auth title="Register" heading="Create Account" subheading="Sign up to get started.">
    <form method="POST" action="{{ route('register.submit') }}" class="space-y-4">
        @csrf

        <div>
            <label for="name" class="mb-1 block text-sm font-medium text-orange-600">Name</label>
            <input
                id="name"
                name="name"
                type="text"
                value="{{ old('name') }}"
                required
                autofocus
                class="w-full rounded-md border text-orange-500 border-gray-300 px-3 py-2 text-sm outline-none ring-0 focus:border-orange-500"
            >
            @error('name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="email" class="mb-1 block text-sm font-medium text-orange-600">Email</label>
            <input
                id="email"
                name="email"
                type="email"
                value="{{ old('email') }}"
                required
                class="w-full rounded-md border text-orange-500 border-gray-300 px-3 py-2 text-sm outline-none ring-0 focus:border-orange-500"
            >
            @error('email')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password" class="mb-1 block text-sm font-medium text-orange-600">Password</label>
            <input
                id="password"
                name="password"
                type="password"
                required
                class="w-full rounded-md border text-orange-500 border-gray-300 px-3 py-2 text-sm outline-none ring-0 focus:border-orange-500"
            >
            @error('password')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password_confirmation" class="mb-1 block text-sm font-medium text-orange-700">Confirm Password</label>
            <input
                id="password_confirmation"
                name="password_confirmation"
                type="password"
                required
                class="w-full rounded-md border text-orange-500 border-gray-300 px-3 py-2 text-sm outline-none ring-0 focus:border-orange-500"
            >
        </div>

        <button
            type="submit"
            class="w-full rounded-md bg-orange-600 px-4 py-2 text-sm font-medium text-white hover:bg-orange-400"
        >
            Register
        </button>
    </form>

    <p class="mt-4 text-sm text-orange-200">
        Already have an account?
        <a href="{{ route('login') }}" class="font-medium text-orange-600 hover:text-orange-400" data-show-loader="true">Login</a>
    </p>
</x-layouts.auth>
