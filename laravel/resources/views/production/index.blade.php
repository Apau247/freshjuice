@extends('layouts.app')
@section('head')
<link rel="stylesheet" href="{{ asset('css/app.css') }}">
@endsection
@section('content')
<div class="page-header">
    <h2><i class="bi bi-gear-wide-connected"></i> Production Batches</h2>
    <a href="{{ route('production.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> New Batch</a>
</div>

<div class="glass-flat">
    <table class="data-table">
        <thead>
            <tr>
                <th>Batch #</th>
                <th>Date</th>
                <th>Flavour</th>
                <th>Quantity</th>
                <th>Machine</th>
                <th>Raw Material</th>
                <th>Status</th>
                <th style="width:120px;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($batches as $b)
            <tr>
                <td style="font-weight:600;">{{ $b->BatchNumber }}</td>
                <td>{{ $b->ProductionDate }}</td>
                <td>{{ $b->Flavour }}</td>
                <td>{{ number_format($b->Quantity, 1) }} {{ $b->Unit }}</td>
                <td>{{ $b->MachineName ?? '—' }}</td>
                <td>{{ $b->RawMaterialName ?? '—' }}</td>
                <td>
                    @php $s = $b->Status; @endphp
                    <span class="badge-status {{ match($s) { 'Completed'=>'badge-success', 'In Progress'=>'badge-info', 'Pending'=>'badge-warning', 'Rejected'=>'badge-danger', 'Cancelled'=>'badge-muted', default=>'badge-muted' } }}">{{ $s }}</span>
                </td>
                <td>
                    <div style="display:flex;gap:0.3rem;">
                        <a href="{{ route('production.edit', $b->BatchID) }}" class="btn btn-secondary btn-sm"><i class="bi bi-pencil"></i></a>
                        <form method="POST" action="{{ route('production.destroy', $b->BatchID) }}" onsubmit="return confirm('Delete?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-danger btn-sm"><i class="bi bi-trash3"></i></button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="8" class="empty-state"><i class="bi bi-gear-wide-connected"></i><p>No batches yet</p></td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
