@extends('layouts.admin')
@section('header', 'Add New User')

@section('content')
<div class="max-w-2xl mx-auto bg-white rounded-lg shadow p-6">
    <form action="{{ route('users.store') }}" method="POST">
        @csrf
        <div class="space-y-6">
            <div>
                <label class="block text-sm font-bold text-gray-700">Name</label>
                <input type="text" name="name" class="w-full border rounded p-2 mt-1" required>
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700">Email</label>
                <input type="email" name="email" class="w-full border rounded p-2 mt-1" required>
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700">Role</label>
                <select name="role" class="w-full border rounded p-2 mt-1 bg-white" required>
                    <option value="">Select a Role</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->name }}">{{ $role->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700">Password</label>
                <input type="password" name="password" class="w-full border rounded p-2 mt-1" required>
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700">Confirm Password</label>
                <input type="password" name="password_confirmation" class="w-full border rounded p-2 mt-1" required>
            </div>
        </div>
        <div class="flex justify-end mt-6 pt-4 border-t gap-3">
            <a href="{{ route('users.index') }}" class="px-4 py-2 bg-gray-200 rounded">Cancel</a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded font-bold">Create User</button>
        </div>
    </form>
</div>
@endsection