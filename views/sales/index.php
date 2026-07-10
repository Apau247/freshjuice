<?php $pageTitle = 'Sales Orders'; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0"><i class="bi bi-cart me-2"></i><?= $pageTitle ?></h5>
    <?php if (canCreate('sales')): ?>
    <a href="?route=sales/create" class="btn btn-success btn-sm"><i class="bi bi-plus-lg"></i> New Order</a>
    <?php endif; ?>
</div>
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <table id="dataTable" class="table table-hover align-middle">
            <thead class="table-light">
                <tr><th>Order ID</th><th>Date</th><th>Customer</th><th>Flavour</th><th>Qty</th><th>Amount</th><th>Status</th><th>Actions</th></tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $o): ?>
                <tr>
                    <td><?= sanitize($o['OrderID']) ?></td>
                    <td><?= sanitize($o['OrderDate'] ?? '') ?></td>
                    <td class="fw-semibold"><?= sanitize($o['CustomerName'] ?? $o['customer_name'] ?? '') ?></td>
                    <td><?= sanitize($o['FG_Flavour'] ?? $o['fg_flavour'] ?? '') ?></td>
                    <td><?= number_format((float)($o['Quantity'] ?? 0), 1) ?></td>
                    <td>$<?= number_format((float)($o['TotalAmount'] ?? 0), 2) ?></td>
                    <td>
                        <?php $s = $o['Status'] ?? ''; $map = ['Pending'=>'warning','Processing'=>'info','Completed'=>'success','Cancelled'=>'secondary']; ?>
                        <span class="badge bg-<?= $map[$s] ?? 'secondary' ?>"><?= sanitize($s) ?></span>
                    </td>
                    <td>
                        <a href="?route=sales/edit&id=<?= urlencode($o['OrderID']) ?>" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a>
                        <a href="?route=sales/delete&id=<?= urlencode($o['OrderID']) ?>" class="btn btn-sm btn-outline-danger" title="Delete" onclick="return confirm('Delete this order?')"><i class="bi bi-trash"></i></a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
