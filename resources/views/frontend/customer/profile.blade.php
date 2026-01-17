@extends('layouts.frontend')

@section('title', 'Account Details | Tech Hub')

@push('styles')
    <style>
        /* --- PROFILE PAGE SPECIFIC CSS --- */
        .page-header { margin: 30px 0; }
        .page-title { font-size: 1.8rem; font-weight: 800; color: var(--text-main); }

        .account-layout { display: grid; grid-template-columns: 280px 1fr; gap: 30px; margin-bottom: 60px; }

        /* Content Cards */
        .profile-card {
            background: white; border: 1px solid var(--border); border-radius: var(--radius);
            padding: 30px; margin-bottom: 25px; box-shadow: var(--shadow);
        }

        .card-head { font-size: 1.1rem; font-weight: 700; margin-bottom: 25px; padding-bottom: 15px; border-bottom: 1px solid #f1f5f9; color: var(--text-main); }

        /* Avatar Upload (Visual Only for now) */
        .avatar-section { display: flex; align-items: center; gap: 20px; margin-bottom: 30px; }
        .avatar-lg {
            width: 80px; height: 80px; background: #eff6ff; color: var(--brand-deep-blue);
            border-radius: 50%; display: flex; align-items: center; justify-content: center;
            font-size: 2rem; font-weight: 700; border: 2px solid white; box-shadow: 0 0 0 2px var(--border);
        }

        /* Form Styling */
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .form-group { margin-bottom: 15px; }
        .full-width { grid-column: span 2; }

        .form-label { display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 8px; color: var(--text-main); }
        .form-input {
            width: 100%; padding: 12px;
            border: 1px solid var(--border); border-radius: var(--radius);
            font-size: 0.95rem; outline: none; transition: 0.2s; background: #fdfdfd;
        }
        .form-input:focus { border-color: var(--brand-magenta); background: white; box-shadow: 0 0 0 3px rgba(192, 77, 238, 0.1); }

        /* Password Section */
        .password-section { margin-top: 10px; padding-top: 25px; border-top: 1px solid #f1f5f9; }
        .section-sub-title { font-size: 1rem; font-weight: 700; margin-bottom: 20px; color: var(--text-main); }

        /* Action Buttons */
        .form-actions { margin-top: 30px; display: flex; gap: 15px; }
        .btn-save {
            background: var(--brand-deep-blue); color: white; border: none; padding: 12px 30px;
            border-radius: var(--radius); font-weight: 600; cursor: pointer; transition: 0.2s;
        }
        .btn-save:hover { background: var(--brand-magenta); }
        .btn-cancel {
            background: white; color: var(--text-muted); border: 1px solid var(--border); padding: 12px 20px;
            border-radius: var(--radius); font-weight: 600; cursor: pointer; transition: 0.2s;
        }
        .btn-cancel:hover { background: #f8fafc; color: var(--text-main); }

        .text-red-500 { color: #ef4444; font-size: 0.8rem; margin-top: 5px; }

        @media (max-width: 900px) {
            .account-layout { grid-template-columns: 1fr; }
        }
        @media (max-width: 600px) {
            .form-grid { grid-template-columns: 1fr; }
            .full-width { grid-column: span 1; }
        }
    </style>
@endpush

@section('content')
    <div class="container">

        <div class="page-header">
            <h1 class="page-title">Account Details</h1>
        </div>

        <div class="account-layout">

            <!-- SIDEBAR -->
            @include('frontend.customer.partials.sidebar')

            <div class="details-content">

                <div class="profile-card">
                    <div class="card-head">Edit Profile</div>

                    <div class="avatar-section">
                        <div class="avatar-lg">{{ substr($user->name, 0, 2) }}</div>
                        <div>
                            <!-- Optional: Add actual file upload later -->
                            <h4 style="font-weight:700;">{{ $user->name }}</h4>
                            <p class="avatar-note">{{ $user->email }}</p>
                        </div>
                    </div>

                    <form action="{{ route('customer.profile.update') }}" method="POST">
                        @csrf
                        @method('PATCH')

                        <div class="form-grid">
                            <div class="form-group full-width">
                                <label class="form-label">Full Name</label>
                                <input type="text" name="name" class="form-input" value="{{ old('name', $user->name) }}" required>
                                @error('name') <p class="text-red-500">{{ $message }}</p> @enderror
                            </div>

                            <div class="form-group">
                                <label class="form-label">Email Address</label>
                                <input type="email" name="email" class="form-input" value="{{ old('email', $user->email) }}" required>
                                @error('email') <p class="text-red-500">{{ $message }}</p> @enderror
                            </div>

                            <div class="form-group">
                                <label class="form-label">Phone Number</label>
                                <input type="text" name="phone" class="form-input" value="{{ old('phone', $user->phone) }}" required>
                                @error('phone') <p class="text-red-500">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="password-section">
                            <h3 class="section-sub-title">Password Change</h3>
                            <p style="font-size:0.85rem; color:#64748b; margin-bottom:15px;">Leave blank if you don't want to change it.</p>

                            <div class="form-group full-width">
                                <label class="form-label">Current Password</label>
                                <input type="password" name="current_password" class="form-input" placeholder="Current password">
                                @error('current_password') <p class="text-red-500">{{ $message }}</p> @enderror
                            </div>

                            <div class="form-grid">
                                <div class="form-group">
                                    <label class="form-label">New Password</label>
                                    <input type="password" name="password" class="form-input">
                                    @error('password') <p class="text-red-500">{{ $message }}</p> @enderror
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Confirm New Password</label>
                                    <input type="password" name="password_confirmation" class="form-input">
                                </div>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn-save">Save Changes</button>
                            <a href="{{ route('customer.dashboard') }}" class="btn-cancel">Cancel</a>
                        </div>

                    </form>
                </div>

                <!-- Marketing Preferences (Visual Only / Can be linked to a 'subscribed' column later) -->
                <div class="profile-card">
                    <div class="card-head">Marketing Preferences</div>
                    <div style="display:flex; flex-direction:column; gap:15px;">
                        <label style="display:flex; align-items:center; gap:10px; font-size:0.9rem; cursor:pointer;">
                            <input type="checkbox" checked style="width:16px; height:16px; accent-color:var(--brand-deep-blue);">
                            I want to receive the newsletter with the best deals and offers.
                        </label>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
