<?php $pageTitle = isset($invoice) ? 'Edit Invoice' : 'New Invoice'; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0"><i class="bi bi-receipt me-2"></i><?= $pageTitle ?></h5>
    <a href="?route=invoicing" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Back</a>
</div>
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="?route=<?= isset($invoice) ? 'invoicing/edit&id=' . urlencode($invoice['InvoiceID']) : 'invoicing/create' ?>
            <?= csrfField() ?>" class="row g-3">
            <div class="col-md-4">
                <label class="form-label fw-semibold">Invoice Date <span class="text-danger">*</span></label>
                <input type="date" name="invoice_date" class="form-control" value="<?= sanitize($invoice['InvoiceDate'] ?? $invoice['invoice_date'] ?? date('Y-m-d')) ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Sales Order <span class="text-danger">*</span></label>
                <select name="order_id" class="form-select" required>
                    <option value="">Select Order</option>
                    <?php if (isset($orders)): foreach ($orders as $o): ?>
                    <option value="<?= sanitize($o['OrderID']) ?>" <?= (isset($invoice) && ($invoice['OrderID'] ?? '') === $o['OrderID']) ? 'selected' : '' ?>><?= sanitize($o['OrderID']) ?> - <?= sanitize($o['CustomerName'] ?? $o['customer_name'] ?? '') ?></option>
                    <?php endforeach; endif; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Due Date</label>
                <input type="date" name="due_date" class="form-control" value="<?= sanitize($invoice['DueDate'] ?? $invoice['due_date'] ?? '') ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Amount ($) <span class="text-danger">*</span></label>
                <input type="number" step="0.01" name="amount" class="form-control" value="<?= sanitize((string)($invoice['Amount'] ?? '')) ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Tax ($)</label>
                <input type="number" step="0.01" name="tax" class="form-control" value="<?= sanitize((string)($invoice['Tax'] ?? '0')) ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Total Due ($)</label>
                <input type="number" step="0.01" name="total_due" class="form-control" value="<?= sanitize((string)($invoice['TotalDue'] ?? '0')) ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Payment Status</label>
                <select name="payment_status" class="form-select">
                    <option value="Unpaid" <?= (isset($invoice) && ($invoice['PaymentStatus'] ?? '') === 'Unpaid') ? 'selected' : '' ?>>Unpaid</option>
                    <option value="Partial" <?= (isset($invoice) && ($invoice['PaymentStatus'] ?? '') === 'Partial') ? 'selected' : '' ?>>Partial</option>
                    <option value="Paid" <?= (isset($invoice) && ($invoice['PaymentStatus'] ?? '') === 'Paid') ? 'selected' : '' ?>>Paid</option>
                    <option value="Overdue" <?= (isset($invoice) && ($invoice['PaymentStatus'] ?? '') === 'Overdue') ? 'selected' : '' ?>>Overdue</option>
                </select>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-success"><i class="bi bi-check-lg"></i> <?= isset($invoice) ? 'Update' : 'Create' ?> Invoice</button>
                <a href="?route=invoicing" class="btn btn-outline-secondary ms-2">Cancel</a>
            </div>
        </form>
    </div>
</div>
