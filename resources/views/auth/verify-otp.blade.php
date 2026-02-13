<x-layouts.auth title="Verify OTP" heading="Verify OTP" subheading="Enter the 6-digit code sent to your email.">
    @if(session('error'))
        <p class="mb-4 text-sm text-orange-600">{{ session('error') }}</p>
    @endif

    <form action="{{ route('otp.check') }}" method="POST" class="space-y-4">
        @csrf

        <div>
            <label for="otp" class="mb-1 block text-sm font-medium text-orange-600">OTP Code</label>
            <input
                id="otp"
                type="text"
                name="otp"
                inputmode="numeric"
                pattern="[0-9]*"
                maxlength="6"
                placeholder="123456"
                required
                class="w-full rounded-md border border-gray-300 px-3 py-2 text-center text-sm tracking-[0.35em] text-orange-500 outline-none ring-0 placeholder:text-orange-300 focus:border-orange-500"
            >
            @error('otp')
                <p class="mt-1 text-sm text-orange-600">{{ $message }}</p>
            @enderror
        </div>

        <button
            type="submit"
            class="w-full rounded-md bg-orange-600 px-4 py-2 text-sm font-medium text-white hover:bg-orange-800"
        >
            Verify
        </button>
        <button type="button" onclick="history.back()" class="w-full rounded-md bg-gray-200 px-4 py-2 text-sm font-medium text-gray-900 hover:bg-gray-300">
            Back
        </button>
    </form>

    <p class="mt-4 text-sm text-orange-200">
        Didn't receive a code? Check spam or try again.
    </p>
</x-layouts.auth>
