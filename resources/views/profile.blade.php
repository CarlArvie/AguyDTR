<x-layouts.app title="Profile">
    <div class="rounded-lg border border-orange-500 bg-[rgb(38,38,38)] p-6">
        <h1 class="text-xl font-semibold text-orange-600">Profile</h1>
        <p class="mt-1 text-sm text-orange-400">Update your name, target hours, and password.</p>

        @if (session('status'))
            <div class="mt-4 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('profile.update') }}" class="mt-6 space-y-4">
            @csrf
            @method('PATCH')

            <div>
                <label for="name" class="mb-1 block text-sm font-medium text-orange-600">Name</label>
                <input
                    id="name"
                    name="name"
                    type="text"
                    value="{{ old('name', $user->name) }}"
                    required
                    class="w-full rounded-md border border-orange-500 px-3 py-2 text-sm text-orange-600 focus:border-orange-500 focus:outline-none focus:ring-1 focus:ring-orange-500"
                >
                @error('name')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="target_hours" class="mb-1 block text-sm font-medium text-orange-600">Target Hours</label>
                <input
                    id="target_hours"
                    name="target_hours"
                    type="number"
                    step="0.01"
                    min="0.01"
                    value="{{ old('target_hours', number_format((float) ($user->target_hours ?? 486), 2, '.', '')) }}"
                    required
                    class="w-full rounded-md border border-orange-500 px-3 py-2 text-sm text-orange-600 focus:border-orange-500 focus:outline-none focus:ring-1 focus:ring-orange-500"
                >
                @error('target_hours')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="border-t border-orange-800 pt-4">
                <p class="mb-3 text-sm font-medium text-orange-600">Change Password (optional)</p>

                <div class="space-y-4">
                    <div>
                        <label for="current_password" class="mb-1 block text-sm font-medium text-orange-600">Current Password</label>
                        <input
                            id="current_password"
                            name="current_password"
                            type="password"
                            class="w-full rounded-md border border-orange-500 px-3 py-2 text-sm text-orange-600 focus:border-orange-500 focus:outline-none focus:ring-1 focus:ring-orange-500"
                        >
                        @error('current_password')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="new_password" class="mb-1 block text-sm font-medium text-orange-600">New Password</label>
                        <input
                            id="new_password"
                            name="new_password"
                            type="password"
                            class="w-full rounded-md border border-orange-500 px-3 py-2 text-sm text-orange-600 focus:border-orange-500 focus:outline-none focus:ring-1 focus:ring-orange-500"
                        >
                        @error('new_password')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="new_password_confirmation" class="mb-1 block text-sm font-medium text-orange-600">Confirm New Password</label>
                        <input
                            id="new_password_confirmation"
                            name="new_password_confirmation"
                            type="password"
                            class="w-full rounded-md border border-orange-500 px-3 py-2 text-sm text-orange-600 focus:border-orange-500 focus:outline-none focus:ring-1 focus:ring-orange-500"
                        >
                    </div>
                </div>
            </div>

            <div class="pt-2">
                <button
                    type="submit"
                    class="rounded-md bg-orange-600 px-4 py-2 text-sm font-medium text-white hover:bg-orange-700"
                >
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</x-layouts.app>
