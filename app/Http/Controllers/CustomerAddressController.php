<?php

namespace App\Http\Controllers;

use App\Models\CustomerAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerAddressController extends Controller
{
    public function index()
    {
        $addresses = CustomerAddress::where('customer_id', Auth::guard('customer')->id())
            ->orderBy('is_default', 'desc')
            ->get();

        return view('frontend.customer.addresses', compact('addresses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required',
            'first_name' => 'required',
            'last_name' => 'required',
            'phone' => 'required',
            'street_address' => 'required',
            'city' => 'required',
        ]);

        $customerId = Auth::guard('customer')->id();

        // If making default, unset others
        if ($request->is_default) {
            CustomerAddress::where('customer_id', $customerId)->update(['is_default' => false]);
        }

        CustomerAddress::create([
            'customer_id' => $customerId,
            'type' => $request->type,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'phone' => $request->phone,
            'street_address' => $request->street_address,
            'city' => $request->city,
            'is_default' => $request->boolean('is_default'),
        ]);

        return back()->with('success', 'Address added successfully.');
    }

    public function update(Request $request, $id)
    {
        $address = CustomerAddress::where('customer_id', Auth::guard('customer')->id())->findOrFail($id);

        if ($request->is_default) {
            CustomerAddress::where('customer_id', Auth::guard('customer')->id())->update(['is_default' => false]);
        }

        $address->update($request->except(['_token', '_method']));

        return back()->with('success', 'Address updated.');
    }

    public function destroy($id)
    {
        CustomerAddress::where('customer_id', Auth::guard('customer')->id())->where('id', $id)->delete();
        return back()->with('success', 'Address deleted.');
    }

    public function setDefault($id)
    {
        $customerId = Auth::guard('customer')->id();
        CustomerAddress::where('customer_id', $customerId)->update(['is_default' => false]);
        CustomerAddress::where('id', $id)->where('customer_id', $customerId)->update(['is_default' => true]);

        return back()->with('success', 'Default address updated.');
    }
}
