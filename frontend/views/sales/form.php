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
                <input type="date" name="order_date" class="form-control" max="<?= date('Y-m-d') ?>" value="<?= sanitize($order['OrderDate'] ?? $order['order_date'] ?? date('Y-m-d')) ?>" required>
                <div class="form-text">Cannot be a future date.</div>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Customer <span class="text-danger">*</span></label>
                <select name="customer_id" class="form-select" required>
                    <?= trending_options($customers, 'CustomerID', 'Name', $trends['customer'] ?? null, isset($order) ? ($order['CustomerID'] ?? null) : null, 'Select Customer') ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Finished Good</label>
                <?php
                // Cascade: each option carries its stock + unit so picking a
                // product immediately caps the quantity and shows availability.
                $fgAttrs = [];
                foreach ($finishedGoods as $fg) {
                    $fgAttrs[(string)$fg['FG_ID']] = 'data-qty="' . sanitize((string)($fg['QuantityAvailable'] ?? 0)) . '"'
                        . ' data-unit="' . sanitize((string)($fg['Unit'] ?? '')) . '"'
                        . ' data-flavour="' . sanitize((string)($fg['Flavour'] ?? '')) . '"';
                }
                ?>
                <select name="fg_id" class="form-select" data-stock-for="quantity">
                    <?= trending_options($finishedGoods, 'FG_ID', 'Flavour', $trends['fg'] ?? null, isset($order) ? ($order['FG_ID'] ?? null) : null, 'Select FG', $fgAttrs) ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Quantity <span class="text-danger">*</span></label>
                <input type="number" step="0.01" min="0.01" max="1000000" name="quantity" class="form-control" value="<?= sanitize((string)($order['Quantity'] ?? '')) ?>" required>
                <div class="invalid-feedback">Quantity must be a number greater than zero.</div>
                <div class="form-text" data-stock-hint></div>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Total Amount ($) <span class="text-danger">*</span></label>
                <input type="number" step="0.01" min="0" name="total_amount" class="form-control" value="<?= sanitize((string)($order['TotalAmount'] ?? '0')) ?>" required>
                <div class="invalid-feedback">Total amount cannot be negative.</div>
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
