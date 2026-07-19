@extends('layouts.app')
@section('head')
<link rel="stylesheet" href="{{ asset('css/app.css') }}">
@endsection
@section('content')
<div class="page-header">
    <h2><i class="bi bi-truck"></i> Suppliers</h2>
    <a href="{{ route('suppliers.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Add Supplier</a>
</div>

<div class="glass-flat">
    <table class="data-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Contact</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Type</th>
                <th>Status</th>
                <th style="width:120px;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($suppliers as $s)
            <tr>
                <td>{{ $s->SupplierID }}</td>
                <td style="font-weight:600;">{{ $s->Name }}</td>
                <td>{{ $s->Contact }}</td>
                <td>{{ $s->Email }}</td>
                <td>{{ $s->Phone }}</td>
                <td><span class="badge-status badge-info">{{ $s->Type }}</span></td>
                <td>
                    @if($s->Status === 'Active')
                        <span class="badge-status badge-success">Active</span>
                    @else
                        <span class="badge-status badge-muted">Inactive</span>
                    @endif
                </td>
                <td>
                    <div style="display:flex;gap:0.3rem;">
                        <a href="{{ route('suppliers.edit', $s->SupplierID) }}" class="btn btn-secondary btn-sm" title="Edit">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form method="POST" action="{{ route('suppliers.destroy', $s->SupplierID) }}" onsubmit="return confirm('Delete this supplier?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-danger btn-sm" title="Delete"><i class="bi bi-trash3"></i></button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="8" class="empty-state"><i class="bi bi-truck"></i><p>No suppliers yet</p></td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
