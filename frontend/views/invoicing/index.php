<?php $pageTitle = 'Invoicing'; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0"><i class="bi bi-receipt me-2"></i><?= $pageTitle ?></h5>
    <?php if (canCreate('invoicing')): ?>
    <a href="?route=invoicing/create" class="btn btn-success btn-sm"><i class="bi bi-plus-lg"></i> New Invoice</a>
    <?php endif; ?>
</div>
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <table id="dataTable" class="table table-hover align-middle">
            <thead class="table-light">
                <tr><th>Invoice#</th><th>Date</th><th>Customer</th><th>Amount</th><th>Tax</th><th>Total Due</th><th>Due Date</th><th>Payment</th><th>Actions</th></tr>
            </thead>
            <tbody>
                <?php foreach ($invoices as $inv): ?>
                <tr>
                    <td class="fw-semibold"><?= sanitize($inv['InvoiceID']) ?></td>
                    <td><?= sanitize($inv['InvoiceDate'] ?? '') ?></td>
                    <td><?= sanitize($inv['CustomerName'] ?? $inv['customer_name'] ?? '') ?></td>
                    <td>$<?= number_format((float)($inv['Amount'] ?? 0), 2) ?></td>
                    <td>$<?= number_format((float)($inv['Tax'] ?? 0), 2) ?></td>
                    <td class="fw-bold">$<?= number_format((float)($inv['TotalDue'] ?? 0), 2) ?></td>
                    <td><?= sanitize($inv['DueDate'] ?? $inv['due_date'] ?? '') ?></td>
                    <td>
                        <?php $ps = $inv['PaymentStatus'] ?? ''; $map = ['Paid'=>'success','Partial'=>'warning','Unpaid'=>'danger','Overdue'=>'dark']; ?>
                        <span class="badge bg-<?= $map[$ps] ?? 'secondary' ?>"><?= sanitize($ps) ?></span>
                    </td>
                    <td>
                        <a href="?route=invoicing/edit&id=<?= urlencode($inv['InvoiceID']) ?>" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a>
                        <a href="?route=invoicing/delete&id=<?= urlencode($inv['InvoiceID']) ?>" class="btn btn-sm btn-outline-danger" title="Delete" onclick="return confirm('Delete this invoice?')"><i class="bi bi-trash"></i></a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
