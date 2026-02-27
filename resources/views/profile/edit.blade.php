@extends('layouts.admin')

@section('header', __('Profile'))

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <div class="p-4 sm:p-8 bg-white shadow sm:rounded-2xl border border-gray-100">
        <div class="max-w-xl text-gray-900">
            @include('profile.partials.update-profile-information-form')
        </div>
    </div>

    <div class="p-4 sm:p-8 bg-white shadow sm:rounded-2xl border border-gray-100">
        <div class="max-w-xl text-gray-900">
            @include('profile.partials.update-password-form')
        </div>
    </div>

    <div class="p-4 sm:p-8 bg-white shadow sm:rounded-2xl border border-gray-100">
        <div class="max-w-xl text-gray-900">
            @include('profile.partials.two-factor-setup')
        </div>
    </div>
</div>
@endsection
