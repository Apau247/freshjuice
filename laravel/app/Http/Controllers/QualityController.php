<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QualityController extends Controller
{
    public function index()
    {
        $inspections = DB::table('quality_inspections')
            ->leftJoin('production_batches', 'quality_inspections.BatchID', '=', 'production_batches.BatchID')
            ->select('quality_inspections.*', 'production_batches.BatchNumber', 'production_batches.Flavour')
            ->orderByDesc('quality_inspections.InspectionDate')
            ->get();

        return view('quality.index', compact('inspections'));
    }

    public function create()
    {
        $batches = DB::table('production_batches')
            ->whereIn('Status', ['Pending', 'In Progress'])
            ->orderBy('BatchNumber')
            ->get();

        return view('quality.form', ['inspection' => null, 'batches' => $batches]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'InspectionType' => 'required|string',
            'InspectionDate' => 'required|date',
            'Result' => 'required|string|in:Pass,Fail,Pending',
        ]);

        $id = 'QI-' . substr(uniqid(), -8);
        $result = $request->Result;
        $batchId = $request->BatchID ?: null;

        DB::table('quality_inspections')->insert([
            'InspectionID' => $id,
            'InspectionType' => $request->InspectionType,
            'BatchID' => $batchId,
            'InspectionDate' => $request->InspectionDate,
            'Result' => $result,
            'DefectsFound' => $request->DefectsFound,
            'TestResults' => $request->TestResults,
            'CAPA' => $request->CAPA,
            'InspectorID' => session('user_id'),
            'Status' => $result === 'Pass' ? 'Closed' : 'Open',
            'created_at' => now(),
        ]);

        if ($result === 'Pass' && $batchId) {
            $this->createFinishedGoods($batchId);
        }

        return redirect()->route('quality.index')->with('success', 'Inspection recorded.');
    }

    public function edit($id)
    {
        $inspection = DB::table('quality_inspections')->where('InspectionID', $id)->first();
        if (!$inspection) return redirect()->route('quality.index')->with('error', 'Not found.');

        $batches = DB::table('production_batches')->orderBy('BatchNumber')->get();

        return view('quality.form', compact('inspection', 'batches'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'InspectionType' => 'required|string',
            'Result' => 'required|string|in:Pass,Fail,Pending',
        ]);

        $result = $request->Result;

        DB::table('quality_inspections')->where('InspectionID', $id)->update([
            'InspectionType' => $request->InspectionType,
            'Result' => $result,
            'DefectsFound' => $request->DefectsFound,
            'TestResults' => $request->TestResults,
            'CAPA' => $request->CAPA,
            'Status' => $result === 'Pass' ? 'Closed' : 'Open',
            'updated_at' => now(),
        ]);

        if ($result === 'Pass') {
            $item = DB::table('quality_inspections')->where('InspectionID', $id)->first();
            if ($item && $item->BatchID) {
                $this->createFinishedGoods($item->BatchID);
            }
        }

        return redirect()->route('quality.index')->with('success', 'Inspection updated.');
    }

    public function destroy($id)
    {
        DB::table('quality_inspections')->where('InspectionID', $id)->delete();
        return redirect()->route('quality.index')->with('success', 'Inspection deleted.');
    }

    private function createFinishedGoods(string $batchId): void
    {
        $batch = DB::table('production_batches')->where('BatchID', $batchId)->first();
        if (!$batch || $batch->Status === 'Completed') return;

        $existing = DB::table('finished_goods')->where('BatchID', $batchId)->first();
        if ($existing) return;

        DB::table('production_batches')->where('BatchID', $batchId)->update(['Status' => 'Completed']);

        $fgId = 'FG-' . substr(uniqid(), -8);
        $expiry = date('Y-m-d', strtotime('+6 months', strtotime($batch->ProductionDate)));

        DB::table('finished_goods')->insert([
            'FG_ID' => $fgId,
            'BatchID' => $batchId,
            'Flavour' => $batch->Flavour,
            'ExpiryDate' => $expiry,
            'QuantityAvailable' => $batch->Quantity,
            'Unit' => $batch->Unit ?? 'bottles',
            'created_at' => now(),
        ]);
    }
}
