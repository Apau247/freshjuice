@extends('layouts.app')
@section('head')
<link rel="stylesheet" href="{{ asset('css/app.css') }}">
@endsection
@section('content')
<div class="page-header">
    <h2><i class="bi bi-gear-wide-connected"></i> {{ $batch ? 'Edit Batch' : 'New Batch' }}</h2>
    <a href="{{ route('production.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Back</a>
</div>

<div class="glass-flat" style="max-width:800px;">
    <div style="padding:1.5rem;">
        <form method="POST" action="{{ $batch ? route('production.update', $batch->BatchID) : route('production.store') }}">
            @csrf
            @if($batch) @method('PUT') @endif

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Batch Number <span class="req">*</span></label>
                    <input type="text" name="BatchNumber" class="form-input" value="{{ old('BatchNumber', $batch->BatchNumber ?? '') }}" {{ $batch ? 'readonly style="background:#f1f5f9;"' : '' }} required>
                    @error('BatchNumber') <div class="form-error">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Production Date <span class="req">*</span></label>
                    <input type="date" name="ProductionDate" class="form-input" value="{{ old('ProductionDate', $batch->ProductionDate ?? date('Y-m-d')) }}" {{ $batch ? 'readonly style="background:#f1f5f9;"' : '' }} required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Flavour <span class="req">*</span></label>
                    <select name="Flavour" class="form-input" required>
                        <option value="">Select flavour...</option>
                        @php $flavours = ['Orange','Apple','Mango','Pineapple','Guava','Mixed Berry','Watermelon','Passion Fruit','Lemon','Coconut']; @endphp
                        @foreach($flavours as $f)
                            <option value="{{ $f }}" {{ (old('Flavour', $batch->Flavour ?? '') === $f) ? 'selected' : '' }}>{{ $f }}</option>
                        @endforeach
                    </select>
                    @error('Flavour') <div class="form-error">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select name="Status" class="form-input">
                        @php $statuses = ['Pending','In Progress','Completed','Rejected','Cancelled']; @endphp
                        @foreach($statuses as $st)
                            <option value="{{ $st }}" {{ (old('Status', $batch->Status ?? 'Pending') === $st) ? 'selected' : '' }}>{{ $st }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Quantity <span class="req">*</span></label>
                    <input type="number" name="Quantity" class="form-input" step="0.1" min="0.1" value="{{ old('Quantity', $batch->Quantity ?? '') }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Unit</label>
                    <select name="Unit" class="form-input">
                        @php $units = ['litres','bottles','kg','cartons']; @endphp
                        @foreach($units as $u)
                            <option value="{{ $u }}" {{ (old('Unit', $batch->Unit ?? 'litres') === $u) ? 'selected' : '' }}>{{ $u }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Raw Material</label>
                    <select name="RawMaterialID" class="form-input">
                        <option value="">None</option>
                        @foreach($rawMaterials as $rm)
                            <option value="{{ $rm->MaterialID }}" {{ (old('RawMaterialID', $batch->RawMaterialID ?? '') === $rm->MaterialID) ? 'selected' : '' }}>
                                {{ $rm->Name }} ({{ number_format($rm->CurrentStock, 1) }} {{ $rm->Unit }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Packaging Material</label>
                    <select name="PackagingMaterialID" class="form-input">
                        <option value="">None</option>
                        @foreach($packagingMaterials as $pm)
                            <option value="{{ $pm->PackageID }}" {{ (old('PackagingMaterialID', $batch->PackagingMaterialID ?? '') === $pm->PackageID) ? 'selected' : '' }}>
                                {{ $pm->Name }} ({{ number_format($pm->CurrentStock, 1) }} {{ $pm->Unit }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Machine</label>
                <select name="MachineID" class="form-input">
                    <option value="">None</option>
                    @foreach($machines as $m)
                        <option value="{{ $m->MachineID }}" {{ (old('MachineID', $batch->MachineID ?? '') === $m->MachineID) ? 'selected' : '' }}>
                            {{ $m->Name }} — {{ $m->Location ?? 'N/A' }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Notes</label>
                <textarea name="Notes" class="form-input" rows="2">{{ old('Notes', $batch->Notes ?? '') }}</textarea>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> {{ $batch ? 'Update' : 'Create' }} Batch</button>
                <a href="{{ route('production.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
