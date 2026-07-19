@extends('layouts.app')
@section('head')
<link rel="stylesheet" href="{{ asset('css/app.css') }}">
@endsection
@section('content')
<div class="page-header">
    <h2><i class="bi bi-patch-check"></i> Quality Inspections</h2>
    <a href="{{ route('quality.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> New Inspection</a>
</div>

<div class="glass-flat">
    <table class="data-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Type</th>
                <th>Date</th>
                <th>Batch</th>
                <th>Flavour</th>
                <th>Result</th>
                <th>Status</th>
                <th style="width:120px;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($inspections as $i)
            <tr>
                <td style="font-weight:600;">{{ $i->InspectionID }}</td>
                <td><span class="badge-status badge-info">{{ $i->InspectionType }}</span></td>
                <td>{{ $i->InspectionDate }}</td>
                <td>{{ $i->BatchNumber ?? '—' }}</td>
                <td>{{ $i->Flavour ?? '—' }}</td>
                <td>
                    @php $r = $i->Result; @endphp
                    <span class="badge-status {{ match($r) { 'Pass'=>'badge-success', 'Fail'=>'badge-danger', default=>'badge-warning' } }}">{{ $r }}</span>
                </td>
                <td>
                    @php $s = $i->Status; @endphp
                    <span class="badge-status {{ match($s) { 'Closed'=>'badge-success', 'In Progress'=>'badge-info', default=>'badge-warning' } }}">{{ $s }}</span>
                </td>
                <td>
                    <div style="display:flex;gap:0.3rem;">
                        <a href="{{ route('quality.edit', $i->InspectionID) }}" class="btn btn-secondary btn-sm"><i class="bi bi-pencil"></i></a>
                        <form method="POST" action="{{ route('quality.destroy', $i->InspectionID) }}" onsubmit="return confirm('Delete?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-danger btn-sm"><i class="bi bi-trash3"></i></button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="8" class="empty-state"><i class="bi bi-patch-check"></i><p>No inspections yet</p></td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
