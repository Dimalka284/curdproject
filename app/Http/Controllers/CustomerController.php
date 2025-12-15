<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Customer;

class CustomerController extends Controller
{
    public function index(){
        $customers = Customer::all();
        return view('customer',compact('customers'));
    }

    public function store(Request $request){
        $request->validate([
            'name'=>'required|string|max:255|min:4'
        ]);

        Customer::create([
            'name' => $request->name
        ]);
        return redirect()->back()->with('success', 'Customer added successfully');
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|integer|exists:customers,id', // ensures ID exists
            'name' => 'required|string|max:255|min:4',
        ]);

        $customer = Customer::findOrFail($request->id);
        $customer->update([
            'name' => $request->name
        ]);

        return redirect()->back()->with('success', 'Customer updated successfully');
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'id' => 'required|integer|exists:customers,id',
        ]);

        $customer = Customer::findOrFail($request->id);
        $customer->delete();

        return redirect()->back()->with('success','Customer deleted successfully');
    }


     public function apiIndex()
    {
        return Customer::all(); // returns JSON automatically
    }
}
