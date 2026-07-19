@extends('layouts.app')
@section('head')
<link rel="stylesheet" href="{{ asset('css/app.css') }}">
@endsection
@section('content')
<div class="page-header">
    <h2><i class="bi bi-patch-check"></i> {{ $inspection ? 'Edit Inspection' : 'New Inspection' }}</h2>
    <a href="{{ route('quality.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Back</a>
</div>

<div class="glass-flat" style="max-width:700px;">
    <div style="padding:1.5rem;">
        <form method="POST" action="{{ $inspection ? route('quality.update', $inspection->InspectionID) : route('quality.store') }}">
            @csrf
            @if($inspection) @method('PUT') @endif

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Inspection Type <span class="req">*</span></label>
                    <select name="InspectionType" class="form-input" required>
                        @php $types = ['Incoming','In-Process','Finished']; @endphp
                        @foreach($types as $t)
                            <option value="{{ $t }}" {{ (old('InspectionType', $inspection->InspectionType ?? '') === $t) ? 'selected' : '' }}>{{ $t }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Inspection Date <span class="req">*</span></label>
                    <input type="date" name="InspectionDate" class="form-input" value="{{ old('InspectionDate', $inspection->InspectionDate ?? date('Y-m-d')) }}" required>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Batch</label>
                <select name="BatchID" class="form-input">
                    <option value="">None (no batch association)</option>
                    @foreach($batches as $b)
                        <option value="{{ $b->BatchID }}" {{ (old('BatchID', $inspection->BatchID ?? '') === $b->BatchID) ? 'selected' : '' }}>
                            {{ $b->BatchNumber }} — {{ $b->Flavour }} ({{ number_format($b->Quantity, 1) }} {{ $b->Unit }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Result <span class="req">*</span></label>
                    <select name="Result" class="form-input" required>
                        @php $results = ['Pending','Pass','Fail']; @endphp
                        @foreach($results as $r)
                            <option value="{{ $r }}" {{ (old('Result', $inspection->Result ?? 'Pending') === $r) ? 'selected' : '' }}>{{ $r }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Status</label>
                    <input type="text" class="form-input" value="{{ $inspection->Status ?? 'Open' }}" readonly style="background:#f1f5f9;">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Test Results</label>
                <textarea name="TestResults" class="form-input" rows="2" placeholder="pH, turbidity, bacterial count...">{{ old('TestResults', $inspection->TestResults ?? '') }}</textarea>
            </div>

            <div class="form-group">
                <label class="form-label">Defects Found</label>
                <textarea name="DefectsFound" class="form-input" rows="2" placeholder="Describe any defects...">{{ old('DefectsFound', $inspection->DefectsFound ?? '') }}</textarea>
            </div>

            <div class="form-group">
                <label class="form-label">CAPA Required</label>
                <textarea name="CAPA" class="form-input" rows="2" placeholder="Corrective actions if needed...">{{ old('CAPA', $inspection->CAPA ?? '') }}</textarea>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> {{ $inspection ? 'Update' : 'Record' }} Inspection</button>
                <a href="{{ route('quality.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
