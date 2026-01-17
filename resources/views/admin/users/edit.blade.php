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
                <p class="text-sm font-bold text-gray-700">Change Password (Optional)</p>
                <div class="mt-2">
                    <label class="block text-xs text-gray-600">New Password</label>
                    <input type="password" name="password" class="w-full border rounded p-2 mt-1">
                </div>
                <div class="mt-2">
                    <label class="block text-xs text-gray-600">Confirm New Password</label>
                    <input type="password" name="password_confirmation" class="w-full border rounded p-2 mt-1">
                </div>
            </div>
        </div>
        
        <div class="flex justify-end mt-6 pt-4 border-t gap-3">
            <a href="{{ route('users.index') }}" class="px-4 py-2 bg-gray-200 rounded">Cancel</a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded font-bold">Update User</button>
        </div>
    </form>
</div>
@endsection