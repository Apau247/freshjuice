<?php $pageTitle = 'Waste Records'; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0"><i class="bi bi-trash me-2"></i><?= $pageTitle ?></h5>
    <?php if (canCreate('waste')): ?>
    <a href="?route=waste/create" class="btn btn-success btn-sm"><i class="bi bi-plus-lg"></i> New Record</a>
    <?php endif; ?>
</div>
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <table id="dataTable" class="table table-hover align-middle">
            <thead class="table-light">
                <tr><th>ID</th><th>Date</th><th>Batch</th><th>Flavour</th><th>Type</th><th>Qty</th><th>Disposal</th><th>Recorded By</th><th>Actions</th></tr>
            </thead>
            <tbody>
                <?php foreach ($records as $w): ?>
                <tr>
                    <td><?= sanitize($w['WasteID']) ?></td>
                    <td><?= sanitize($w['Date'] ?? '') ?></td>
                    <td><?= sanitize($w['BatchNumber'] ?? $w['batch_number'] ?? '') ?></td>
                    <td><?= sanitize($w['Flavour'] ?? '') ?></td>
                    <td><span class="badge bg-warning bg-opacity-10 text-warning"><?= sanitize($w['WasteType'] ?? $w['waste_type'] ?? '') ?></span></td>
                    <td><?= number_format((float)($w['Quantity'] ?? 0), 2) ?> <?= sanitize($w['Unit'] ?? 'kg') ?></td>
                    <td><?= sanitize($w['DisposalMethod'] ?? $w['disposal_method'] ?? '') ?></td>
                    <td><?= sanitize($w['RecordedByName'] ?? $w['recorded_by_name'] ?? '') ?></td>
                    <td>
                        <a href="?route=waste/edit&id=<?= urlencode($w['WasteID']) ?>" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a>
                        <a href="?route=waste/delete&id=<?= urlencode($w['WasteID']) ?>" class="btn btn-sm btn-outline-danger" title="Delete" onclick="return confirm('Delete this record?')"><i class="bi bi-trash"></i></a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
