<?php $pageTitle = 'Deliveries'; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0"><i class="bi bi-box-seam me-2"></i><?= $pageTitle ?></h5>
    <a href="?route=suppliers/delivery/create" class="btn btn-success btn-sm"><i class="bi bi-plus-lg"></i> New Delivery</a>
</div>
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <table id="dataTable" class="table table-hover align-middle">
            <thead class="table-light"><tr><th>ID</th><th>Supplier</th><th>Item</th><th>Qty</th><th>Date</th><th>Grade</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
                <?php foreach ($deliveries as $d): ?>
                <tr>
                    <td><?= sanitize($d['DeliveryID'] ?? $d['delivery_id'] ?? '') ?></td>
                    <td><?= sanitize($d['SupplierName'] ?? $d['supplier_name'] ?? '') ?></td>
                    <td><?= sanitize($d['ItemName'] ?? $d['item_name'] ?? '') ?></td>
                    <td><?= number_format((float)($d['Quantity'] ?? 0), 2) ?></td>
                    <td><?= sanitize($d['DeliveryDate'] ?? $d['delivery_date'] ?? '') ?></td>
                    <td><span class="badge bg-info bg-opacity-10 text-info"><?= sanitize($d['QualityGrade'] ?? $d['quality_grade'] ?? '') ?></span></td>
                    <td>
                        <?php $st = $d['Status'] ?? ''; $map = ['Received'=>'success','Pending'=>'warning','Rejected'=>'danger']; ?>
                        <span class="badge bg-<?= $map[$st] ?? 'secondary' ?>"><?= sanitize($st) ?></span>
                    </td>
                    <td>
                        <a href="?route=suppliers/delivery/edit&id=<?= urlencode($d['DeliveryID'] ?? $d['delivery_id']) ?>" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a>
                        <a href="?route=suppliers/delivery/delete&id=<?= urlencode($d['DeliveryID'] ?? $d['delivery_id']) ?>" class="btn btn-sm btn-outline-danger" title="Delete" onclick="return confirm('Delete this delivery record?')"><i class="bi bi-trash"></i></a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
