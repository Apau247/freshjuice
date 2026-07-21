<?php $pageTitle = isset($order) ? 'Edit Sales Order' : 'New Sales Order'; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0"><i class="bi bi-cart me-2"></i><?= $pageTitle ?></h5>
    <a href="?route=sales" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Back</a>
</div>
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="?route=<?= isset($order) ? 'sales/edit&id=' . urlencode($order['OrderID']) : 'sales/create' ?>" class="row g-3">
            <?= csrfField() ?>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Order Date <span class="text-danger">*</span></label>
                <input type="date" name="order_date" class="form-control" value="<?= sanitize($order['OrderDate'] ?? $order['order_date'] ?? date('Y-m-d')) ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Customer <span class="text-danger">*</span></label>
                <select name="customer_id" class="form-select" required>
                    <option value="">Select Customer</option>
                    <?php if (isset($customers)): foreach ($customers as $c): ?>
                    <option value="<?= sanitize($c['CustomerID']) ?>" <?= (isset($order) && ($order['CustomerID'] ?? '') === $c['CustomerID']) ? 'selected' : '' ?>><?= sanitize($c['Name']) ?></option>
                    <?php endforeach; endif; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Finished Good</label>
                <select name="fg_id" class="form-select">
                    <option value="">Select FG</option>
                    <?php if (isset($finishedGoods)): foreach ($finishedGoods as $fg): ?>
                    <option value="<?= sanitize($fg['FG_ID']) ?>" <?= (isset($order) && ($order['FG_ID'] ?? '') === $fg['FG_ID']) ? 'selected' : '' ?>><?= sanitize($fg['Flavour'] ?? '') ?> (<?= sanitize($fg['FG_ID']) ?>)</option>
                    <?php endforeach; endif; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Quantity <span class="text-danger">*</span></label>
                <input type="number" step="0.01" name="quantity" class="form-control" value="<?= sanitize((string)($order['Quantity'] ?? '')) ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Total Amount ($)</label>
                <input type="number" step="0.01" name="total_amount" class="form-control" value="<?= sanitize((string)($order['TotalAmount'] ?? '0')) ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Status</label>
                <select name="status" class="form-select">
                    <option value="Pending" <?= (isset($order) && ($order['Status'] ?? '') === 'Pending') ? 'selected' : '' ?>>Pending</option>
                    <option value="Processing" <?= (isset($order) && ($order['Status'] ?? '') === 'Processing') ? 'selected' : '' ?>>Processing</option>
                    <option value="Completed" <?= (isset($order) && ($order['Status'] ?? '') === 'Completed') ? 'selected' : '' ?>>Completed</option>
                    <option value="Cancelled" <?= (isset($order) && ($order['Status'] ?? '') === 'Cancelled') ? 'selected' : '' ?>>Cancelled</option>
                </select>
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold">Notes</label>
                <textarea name="notes" class="form-control" rows="2"><?= sanitize($order['Notes'] ?? '') ?></textarea>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-success"><i class="bi bi-check-lg"></i> <?= isset($order) ? 'Update' : 'Create' ?> Order</button>
                <a href="?route=sales" class="btn btn-outline-secondary ms-2">Cancel</a>
            </div>
        </form>
    </div>
</div>
