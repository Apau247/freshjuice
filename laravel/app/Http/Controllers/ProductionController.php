<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductionController extends Controller
{
    public function index()
    {
        $batches = DB::table('production_batches')
            ->leftJoin('raw_materials', 'production_batches.RawMaterialID', '=', 'raw_materials.MaterialID')
            ->leftJoin('machines', 'production_batches.MachineID', '=', 'machines.MachineID')
            ->select('production_batches.*', 'raw_materials.Name as RawMaterialName', 'machines.Name as MachineName')
            ->orderByDesc('production_batches.ProductionDate')
            ->get();

        return view('production.index', compact('batches'));
    }

    public function create()
    {
        $rawMaterials = DB::table('raw_materials')->where('Status', 'Active')->orderBy('Name')->get();
        $packagingMaterials = DB::table('packaging_materials')->where('Status', 'Active')->orderBy('Name')->get();
        $machines = DB::table('machines')->where('Status', 'Operational')->orderBy('Name')->get();

        return view('production.form', [
            'batch' => null,
            'rawMaterials' => $rawMaterials,
            'packagingMaterials' => $packagingMaterials,
            'machines' => $machines,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'BatchNumber' => 'required|string|unique:production_batches,BatchNumber',
            'ProductionDate' => 'required|date',
            'Flavour' => 'required|string|max:100',
            'Quantity' => 'required|numeric|min:0.01',
        ]);

        $id = 'BAT-' . substr(uniqid(), -8);
        $qty = (float) $request->Quantity;

        DB::beginTransaction();
        try {
            if ($request->RawMaterialID && $request->Status !== 'Cancelled') {
                $rm = DB::table('raw_materials')->where('MaterialID', $request->RawMaterialID)->first();
                if ($rm && $rm->CurrentStock < $qty) {
                    DB::rollBack();
                    return back()->withInput()->with('error', "Insufficient raw material stock. Available: {$rm->CurrentStock}");
                }
                DB::table('raw_materials')->where('MaterialID', $request->RawMaterialID)
                    ->decrement('CurrentStock', $qty);
            }

            if ($request->PackagingMaterialID && $request->Status !== 'Cancelled') {
                $pkg = DB::table('packaging_materials')->where('PackageID', $request->PackagingMaterialID)->first();
                if ($pkg && $pkg->CurrentStock < $qty) {
                    DB::rollBack();
                    return back()->withInput()->with('error', "Insufficient packaging stock. Available: {$pkg->CurrentStock}");
                }
                DB::table('packaging_materials')->where('PackageID', $request->PackagingMaterialID)
                    ->decrement('CurrentStock', $qty);
            }

            DB::table('production_batches')->insert([
                'BatchID' => $id,
                'BatchNumber' => $request->BatchNumber,
                'ProductionDate' => $request->ProductionDate,
                'Flavour' => $request->Flavour,
                'Quantity' => $qty,
                'Unit' => $request->Unit ?? 'litres',
                'Status' => $request->Status ?? 'Pending',
                'UserID' => session('user_id'),
                'RawMaterialID' => $request->RawMaterialID ?: null,
                'PackagingMaterialID' => $request->PackagingMaterialID ?: null,
                'MachineID' => $request->MachineID ?: null,
                'Notes' => $request->Notes,
                'created_at' => now(),
            ]);

            DB::commit();
            return redirect()->route('production.index')->with('success', 'Batch created.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to create batch.');
        }
    }

    public function edit($id)
    {
        $batch = DB::table('production_batches')->where('BatchID', $id)->first();
        if (!$batch) return redirect()->route('production.index')->with('error', 'Not found.');

        $rawMaterials = DB::table('raw_materials')->where('Status', 'Active')->orderBy('Name')->get();
        $packagingMaterials = DB::table('packaging_materials')->where('Status', 'Active')->orderBy('Name')->get();
        $machines = DB::table('machines')->orderBy('Name')->get();

        return view('production.form', compact('batch', 'rawMaterials', 'packagingMaterials', 'machines'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'Flavour' => 'required|string|max:100',
            'Quantity' => 'required|numeric|min:0.01',
        ]);

        DB::table('production_batches')->where('BatchID', $id)->update([
            'Flavour' => $request->Flavour,
            'Quantity' => (float) $request->Quantity,
            'Status' => $request->Status,
            'RawMaterialID' => $request->RawMaterialID ?: null,
            'PackagingMaterialID' => $request->PackagingMaterialID ?: null,
            'MachineID' => $request->MachineID ?: null,
            'Notes' => $request->Notes,
            'updated_at' => now(),
        ]);

        return redirect()->route('production.index')->with('success', 'Batch updated.');
    }

    public function destroy($id)
    {
        DB::table('production_batches')->where('BatchID', $id)->delete();
        return redirect()->route('production.index')->with('success', 'Batch deleted.');
    }
}
