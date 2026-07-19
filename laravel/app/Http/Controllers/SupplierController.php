<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = DB::table('suppliers')->orderByDesc('created_at')->get();
        return view('suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        return view('suppliers.form', ['supplier' => null]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'Name' => 'required|string|max:150',
            'Type' => 'nullable|string|max:50',
        ]);

        $id = 'SUP-' . substr(uniqid(), -8);

        DB::table('suppliers')->insert([
            'SupplierID' => $id,
            'Name'       => $request->Name,
            'Contact'    => $request->Contact,
            'Email'      => $request->Email,
            'Phone'      => $request->Phone,
            'Address'    => $request->Address,
            'Type'       => $request->Type ?? 'Fruit Supplier',
            'Status'     => $request->Status ?? 'Active',
            'created_at' => now(),
        ]);

        return redirect()->route('suppliers.index')->with('success', 'Supplier created.');
    }

    public function edit($id)
    {
        $supplier = DB::table('suppliers')->where('SupplierID', $id)->first();
        if (!$supplier) return redirect()->route('suppliers.index')->with('error', 'Not found.');

        return view('suppliers.form', compact('supplier'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'Name' => 'required|string|max:150',
        ]);

        DB::table('suppliers')->where('SupplierID', $id)->update([
            'Name'     => $request->Name,
            'Contact'  => $request->Contact,
            'Email'    => $request->Email,
            'Phone'    => $request->Phone,
            'Address'  => $request->Address,
            'Type'     => $request->Type,
            'Status'   => $request->Status,
            'updated_at' => now(),
        ]);

        return redirect()->route('suppliers.index')->with('success', 'Supplier updated.');
    }

    public function destroy($id)
    {
        DB::table('suppliers')->where('SupplierID', $id)->delete();
        return redirect()->route('suppliers.index')->with('success', 'Supplier deleted.');
    }
}
