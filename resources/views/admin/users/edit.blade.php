@extends('layouts.admin')
@section('header', 'Edit User')

@section('content')
<div class="max-w-2xl mx-auto bg-white rounded-lg shadow p-6">
    <form action="{{ route('users.update', $user) }}" method="POST">
        @csrf @method('PUT')
        
        <div class="space-y-6">
            <div>
                <label class="block text-sm font-bold text-gray-700">Name</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" class="w-full border rounded p-2 mt-1" required>
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700">Email</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" class="w-full border rounded p-2 mt-1" required>
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700">Role</label>
                <select name="role" class="w-full border rounded p-2 mt-1 bg-white" required>
                    @foreach($roles as $role)
                        <option value="{{ $role->name }}" {{ $user->hasRole($role->name) ? 'selected' : '' }}>
                            {{ $role->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="border-t pt-4">
                <p class="text-sm font-bold text-gray-700">Security & Two-Factor Authentication</p>
                <div class="mt-4 flex items-center justify-between p-4 bg-gray-50 rounded-xl border border-gray-100">
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">{{ __('2FA Status') }}</p>
                        <p class="text-sm font-bold mt-1 {{ $user->hasTwoFactorEnabled() ? 'text-emerald-600' : 'text-gray-500' }}">
                            {{ $user->hasTwoFactorEnabled() ? __('Enabled') : __('Disabled') }}
                        </p>
                    </div>
                    @if($user->hasTwoFactorEnabled())
                        <button type="button" 
                                class="btn-delete-confirm text-xs font-bold text-red-600 hover:text-red-700 uppercase tracking-widest bg-red-50 px-4 py-2 rounded-lg transition-colors"
                                data-title="Reset 2FA?"
                                data-type="2FA Settings"
                                onclick="event.preventDefault(); document.getElementById('reset-2fa-form-{{ $user->id }}').submit();">
                            {{ __('Reset 2FA Access') }}
                        </button>
                    @endif
                </div>

                <div class="mt-4">
                    <label class="block text-xs text-gray-600 font-bold uppercase tracking-widest mb-2">Preferred 2FA Method</label>
                    <select name="two_factor_type" class="w-full border rounded-xl p-3 mt-1 bg-white text-sm focus:ring-2 focus:ring-[#2dae9a]/20 focus:border-[#2dae9a] transition-all">
                        <option value="totp" {{ $user->two_factor_type === 'totp' ? 'selected' : '' }}>Authenticator App (TOTP)</option>
                        <option value="email" {{ $user->two_factor_type === 'email' ? 'selected' : '' }}>Email Address (OTP)</option>
                    </select>
                </div>

                <div class="mt-6">
                    <p class="text-sm font-bold text-gray-700">Change Password (Optional)</p>
                    <div class="mt-2 text-gray-900">
                        <label class="block text-xs text-gray-600">New Password</label>
                        <input type="password" name="password" class="w-full border rounded p-2 mt-1">
                    </div>
                    <div class="mt-2 text-gray-900">
                        <label class="block text-xs text-gray-600">Confirm New Password</label>
                        <input type="password" name="password_confirmation" class="w-full border rounded p-2 mt-1">
                    </div>
                </div>
            </div>
        </div>
        
        <div class="flex justify-end mt-6 pt-4 border-t gap-3">
            <a href="{{ route('users.index') }}" class="px-4 py-2 bg-gray-200 rounded">Cancel</a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded font-bold">Update User</button>
        </div>
    </form>

    <form id="reset-2fa-form-{{ $user->id }}" action="{{ route('users.reset-2fa', $user) }}" method="POST" class="hidden">
        @csrf
    </form>
</div>
@endsection