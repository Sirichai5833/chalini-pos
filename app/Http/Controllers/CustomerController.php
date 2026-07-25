<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;

class CustomerController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255'
        ]);

        $customer = Customer::create([

            'name' => $request->name,

            'phone' => $request->phone,

            'credit_limit' => 0,

            'current_debt' => 0,

            'is_active' => 1
        ]);

        return response()->json([
            'success' => true,
            'customer' => $customer
        ]);
    }

    public function suspend($id)
{
    $customer = Customer::findOrFail($id);

    $customer->is_active = false;

    $customer->save();

    return response()->json([
        'success' => true
    ]);
}

public function restore($id)
{
    $customer = Customer::findOrFail($id);

    $customer->is_active = true;

    $customer->save();

    return response()->json([
        'success' => true
    ]);
}
}