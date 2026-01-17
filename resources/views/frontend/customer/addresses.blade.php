@extends('layouts.frontend')

@section('title', 'My Addresses | Tech Hub')

@push('styles')
    <style>
        /* Same CSS as provided, plus Modal */
        .account-layout { display: grid; grid-template-columns: 280px 1fr; gap: 30px; margin: 40px 0 60px; }

        .address-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px; }

        .addr-card { background: white; border: 1px solid var(--border); border-radius: var(--radius); padding: 25px; position: relative; transition: 0.2s; display: flex; flex-direction: column; }
        .addr-card:hover { box-shadow: var(--shadow); border-color: #cbd5e1; }

        .addr-card.add-new { border: 2px dashed var(--border); background: #f8fafc; align-items: center; justify-content: center; min-height: 220px; cursor: pointer; color: var(--text-muted); }
        .addr-card.add-new:hover { border-color: var(--brand-deep-blue); color: var(--brand-deep-blue); background: #eff6ff; }
        .add-icon { font-size: 2rem; margin-bottom: 10px; }

        .addr-tag { position: absolute; top: 20px; right: 20px; font-size: 0.75rem; font-weight: 600; padding: 4px 8px; border-radius: 4px; background: #eff6ff; color: var(--brand-deep-blue); }
        .addr-name { font-size: 1.1rem; font-weight: 700; margin-bottom: 15px; color: var(--text-main); }
        .addr-lines { font-size: 0.95rem; color: var(--text-muted); line-height: 1.6; flex: 1; margin-bottom: 20px; }
        .addr-phone { display: block; margin-top: 10px; font-weight: 500; color: var(--text-main); }

        .addr-actions { display: flex; gap: 15px; border-top: 1px solid #f1f5f9; padding-top: 15px; }
        .act-btn { font-size: 0.9rem; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 5px; background: none; border: none; }
        .btn-edit { color: var(--brand-deep-blue); }
        .btn-del { color: #ef4444; }

        /* Modal */
        .modal-overlay { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 1000; display: flex; align-items: center; justify-content: center; }
        .modal-box { background: white; width: 500px; max-width: 90%; padding: 30px; border-radius: var(--radius); position: relative; }
        .modal-close { position: absolute; top: 15px; right: 15px; font-size: 1.5rem; cursor: pointer; color: var(--text-muted); }

        .form-group { margin-bottom: 15px; }
        .form-label { display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 5px; }
        .form-input { width: 100%; padding: 10px; border: 1px solid var(--border); border-radius: 6px; }
        .btn-submit { width: 100%; padding: 12px; background: var(--brand-deep-blue); color: white; border: none; border-radius: 6px; font-weight: 700; cursor: pointer; }

        @media (max-width: 900px) { .account-layout { grid-template-columns: 1fr; } }
    </style>
@endpush

@section('content')
    <div class="container" x-data="{ showModal: false, editMode: false, form: { id: null, type: 'Home', first_name: '', last_name: '', phone: '', street_address: '', city: 'Dubai', is_default: false } }">

        <div class="page-header" style="margin: 30px 0;">
            <h1 class="page-title" style="font-size: 1.8rem; font-weight: 800;">My Addresses</h1>
        </div>

        <div class="account-layout">

            @include('frontend.customer.partials.sidebar')

            <div class="addresses-content">
                <div class="address-grid">

                    <!-- Add New Button -->
                    <div class="addr-card add-new" @click="showModal = true; editMode = false; form = { type: 'Home', city: 'Dubai', is_default: false }">
                        <i class="ri-add-circle-line add-icon"></i>
                        <span style="font-weight:600;">Add New Address</span>
                    </div>

                    <!-- Address Cards Loop -->
                    @foreach($addresses as $address)
                        <div class="addr-card">
                    <span class="addr-tag" style="{{ !$address->is_default ? 'background:#f1f5f9; color:#64748b;' : '' }}">
                        {{ $address->is_default ? 'Default ' . $address->type : $address->type }}
                    </span>

                            <div class="addr-name">{{ $address->first_name }} {{ $address->last_name }}</div>

                            <div class="addr-lines">
                                {{ $address->street_address }}<br>
                                {{ $address->city }}, UAE
                                <span class="addr-phone">Phone: {{ $address->phone }}</span>
                            </div>

                            <div class="addr-actions">
                                <button class="act-btn btn-edit"
                                        @click="showModal = true; editMode = true; form = {{ json_encode($address) }}">
                                    <i class="ri-pencil-line"></i> Edit
                                </button>

                                @if(!$address->is_default)
                                    <a href="{{ route('customer.addresses.default', $address->id) }}" class="act-btn" style="color:#64748b; text-decoration:none;">
                                        <i class="ri-check-line"></i> Set Default
                                    </a>

                                    <form action="{{ route('customer.addresses.destroy', $address->id) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="act-btn btn-del">
                                            <i class="ri-delete-bin-line"></i> Delete
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @endforeach

                </div>
            </div>
        </div>

        <!-- MODAL FOR ADD/EDIT -->
        <div class="modal-overlay" x-show="showModal" style="display: none;" x-transition>
            <div class="modal-box" @click.away="showModal = false">
                <span class="modal-close" @click="showModal = false">&times;</span>
                <h3 style="font-weight:700; font-size:1.2rem; margin-bottom:20px;" x-text="editMode ? 'Edit Address' : 'Add New Address'"></h3>

                <form :action="editMode ? '/account/addresses/' + form.id : '{{ route('customer.addresses.store') }}'" method="POST">
                    @csrf
                    <template x-if="editMode"><input type="hidden" name="_method" value="PUT"></template>

                    <div class="form-group" style="display:grid; grid-template-columns:1fr 1fr; gap:15px;">
                        <div>
                            <label class="form-label">First Name</label>
                            <input type="text" name="first_name" class="form-input" x-model="form.first_name" required>
                        </div>
                        <div>
                            <label class="form-label">Last Name</label>
                            <input type="text" name="last_name" class="form-input" x-model="form.last_name" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Mobile Number</label>
                        <input type="text" name="phone" class="form-input" x-model="form.phone" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Street Address</label>
                        <input type="text" name="street_address" class="form-input" x-model="form.street_address" required>
                    </div>

                    <div class="form-group" style="display:grid; grid-template-columns:1fr 1fr; gap:15px;">
                        <div>
                            <label class="form-label">City / Emirate</label>
                            <select name="city" class="form-input" x-model="form.city">
                                <option value="Dubai">Dubai</option>
                                <option value="Abu Dhabi">Abu Dhabi</option>
                                <option value="Sharjah">Sharjah</option>
                                <option value="Ajman">Ajman</option>
                            </select>
                        </div>
                        <div>
                            <label class="form-label">Address Type</label>
                            <select name="type" class="form-input" x-model="form.type">
                                <option value="Home">Home</option>
                                <option value="Work">Work</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label style="display:flex; align-items:center; gap:8px; font-size:0.9rem;">
                            <input type="checkbox" name="is_default" value="1" x-model="form.is_default"> Set as default address
                        </label>
                    </div>

                    <button type="submit" class="btn-submit">Save Address</button>
                </form>
            </div>
        </div>

    </div>

    <!-- Alpine -->
    <script src="//unpkg.com/alpinejs" defer></script>
@endsection
