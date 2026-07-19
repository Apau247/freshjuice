@extends('layouts.app')
@section('head')
<link rel="stylesheet" href="{{ asset('css/app.css') }}">
@endsection
@section('content')
<div class="page-header">
    <h2><i class="bi bi-truck"></i> {{ $supplier ? 'Edit Supplier' : 'New Supplier' }}</h2>
    <a href="{{ route('suppliers.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Back</a>
</div>

<div class="glass-flat" style="max-width:700px;">
    <div style="padding:1.5rem;">
        <form method="POST" action="{{ $supplier ? route('suppliers.update', $supplier->SupplierID) : route('suppliers.store') }}">
            @csrf
            @if($supplier) @method('PUT') @endif

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Name <span class="req">*</span></label>
                    <input type="text" name="Name" class="form-input" value="{{ old('Name', $supplier->Name ?? '') }}" required>
                    @error('Name') <div class="form-error">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Type</label>
                    <select name="Type" class="form-input">
                        @php $types = ['Fruit Supplier','Packaging Supplier','Ingredient Supplier','Equipment Supplier','Service Provider']; @endphp
                        @foreach($types as $t)
                            <option value="{{ $t }}" {{ (old('Type', $supplier->Type ?? '') === $t) ? 'selected' : '' }}>{{ $t }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Contact Person</label>
                    <input type="text" name="Contact" class="form-input" value="{{ old('Contact', $supplier->Contact ?? '') }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="Email" class="form-input" value="{{ old('Email', $supplier->Email ?? '') }}">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Phone</label>
                    <input type="text" name="Phone" class="form-input" value="{{ old('Phone', $supplier->Phone ?? '') }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select name="Status" class="form-input">
                        <option value="Active" {{ (old('Status', $supplier->Status ?? 'Active') === 'Active') ? 'selected' : '' }}>Active</option>
                        <option value="Inactive" {{ (old('Status', $supplier->Status ?? '') === 'Inactive') ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Address</label>
                <textarea name="Address" class="form-input" rows="2">{{ old('Address', $supplier->Address ?? '') }}</textarea>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> {{ $supplier ? 'Update' : 'Create' }} Supplier</button>
                <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
