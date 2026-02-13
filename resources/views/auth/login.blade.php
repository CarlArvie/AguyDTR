<x-layouts.auth title="Login" heading="Login" subheading="Sign in to continue.">
    <form method="POST" action="{{ route('login.submit') }}" class="space-y-4">
        @csrf

        <div class="" >
            <label for="email" class="mb-1 block text-sm font-medium text-orange-600">Email</label>
            <input
                id="email"
                name="email"
                type="email"
                value="{{ old('email') }}"
                required
                autofocus
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

        <label class="flex items-center gap-2 text-sm text-orange-600">
            <input type="hidden" name="remember" value="0">
            <input type="checkbox" name="remember" value="1" class="rounded border-orange-300" {{ old('remember', 1) ? 'checked' : '' }}>
            Remember me
        </label>

        <button
            type="submit"
            class="w-full rounded-md bg-orange-600 px-4 py-2 text-sm font-medium text-white hover:bg-orange-800"
        >
            Login
        </button>
    </form>

    <p class="mt-4 text-sm text-orange-200">
        No account yet?
        <a href="{{ route('register') }}" class="font-medium text-orange-600 hover:text-orange-800" data-show-loader="true">Register</a>
    </p>
</x-layouts.auth>
