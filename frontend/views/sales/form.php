<?php
$pageTitle = isset($order) ? 'Edit Sales Order' : 'New Sales Order';
$isEdit = isset($order);
// VAT rate used on both the screen and the server (Ghana standard rate).
const SALES_VAT_RATE = 0.15;
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0"><i class="bi bi-cart me-2"></i><?= $pageTitle ?></h5>
    <a href="?route=sales" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Back</a>
</div>

<?php if ($isEdit): ?>
<!-- ── EDIT: single line, same flow as before ── -->
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="?route=sales/edit&id=<?= urlencode($order['OrderID']) ?>" class="row g-3">
            <?= csrfField() ?>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Order Date <span class="text-danger">*</span></label>
                <input type="date" name="order_date" class="form-control" max="<?= date('Y-m-d') ?>" value="<?= sanitize($order['OrderDate'] ?? $order['order_date'] ?? date('Y-m-d')) ?>" required>
                <div class="form-text">Cannot be a future date.</div>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Customer <span class="text-danger">*</span></label>
                <select name="customer_id" class="form-select" required>
                    <?= trending_options($customers, 'CustomerID', 'Name', null, $order['CustomerID'] ?? null, 'Select Customer') ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Finished Good</label>
                <?php
                $fgAttrs = [];
                foreach ($finishedGoods as $fg) {
                    $fgAttrs[(string)$fg['FG_ID']] = 'data-qty="' . sanitize((string)($fg['QuantityAvailable'] ?? 0)) . '"'
                        . ' data-unit="' . sanitize((string)($fg['Unit'] ?? '')) . '"'
                        . ' data-flavour="' . sanitize((string)($fg['Flavour'] ?? '')) . '"';
                }
                ?>
                <select name="fg_id" class="form-select" data-stock-for="quantity">
                    <?= trending_options($finishedGoods, 'FG_ID', 'Flavour', null, $order['FG_ID'] ?? null, 'Select FG', $fgAttrs) ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Quantity <span class="text-danger">*</span></label>
                <input type="number" step="0.01" min="0.01" max="1000000" name="quantity" class="form-control" value="<?= sanitize((string)($order['Quantity'] ?? '')) ?>" required>
                <div class="invalid-feedback">Quantity must be a number greater than zero.</div>
                <div class="form-text" data-stock-hint></div>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Total Amount (GH&#8373;) <span class="text-danger">*</span></label>
                <input type="number" step="0.01" min="0" name="total_amount" class="form-control" value="<?= sanitize((string)($order['TotalAmount'] ?? '0')) ?>" required>
                <div class="invalid-feedback">Total amount cannot be negative.</div>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Status</label>
                <select name="status" class="form-select">
                    <?php foreach (['Pending', 'Processing', 'Completed', 'Cancelled'] as $st): ?>
                    <option value="<?= $st ?>" <?= ($order['Status'] ?? '') === $st ? 'selected' : '' ?>><?= $st ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold">Notes</label>
                <textarea name="notes" class="form-control" rows="2"><?= sanitize($order['Notes'] ?? '') ?></textarea>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-success"><i class="bi bi-check-lg"></i> Update Order</button>
                <a href="?route=sales" class="btn btn-outline-secondary ms-2">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php else: ?>
<!-- ── CREATE: POS-style cart — add as many products as needed ── -->
<form method="POST" action="?route=sales/create" id="posForm" novalidate>
    <?= csrfField() ?>
    <input type="hidden" name="cart_items" id="cartItems" value="[]">

    <!-- Order meta -->
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Order Date <span class="text-danger">*</span></label>
                    <input type="date" name="order_date" class="form-control" max="<?= date('Y-m-d') ?>" value="<?= date('Y-m-d') ?>" required>
                    <div class="form-text">Cannot be a future date.</div>
                </div>
                <div class="col-md-5">
                    <label class="form-label fw-semibold">Customer <span class="text-danger">*</span></label>
                    <select name="customer_id" class="form-select" required>
                        <?= trending_options($customers, 'CustomerID', 'Name', $trends['customer'] ?? null, null, 'Select Customer') ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Status</label>
                    <select name="status" class="form-select" id="posStatus">
                        <?= trending_value_options(['Pending', 'Processing', 'Completed', 'Cancelled'], $trends['status'] ?? null, null, '') ?>
                    </select>
                    <div class="form-text">Completed orders deduct stock immediately.</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <!-- Cart -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header d-flex align-items-center justify-content-between py-3">
                    <span class="fw-bold"><i class="bi bi-basket3 me-2"></i>Order Items</span>
                    <span class="badge bg-success bg-opacity-10 text-success" id="posCount">0 items</span>
                </div>
                <div class="card-body">
                    <!-- Product picker -->
                    <div class="row g-2 align-items-end mb-1">
                        <div class="col-md-5">
                            <label class="form-label fw-semibold">Product</label>
                            <?php
                            $fgAttrs = [];
                            foreach ($finishedGoods as $fg) {
                                $fgAttrs[(string)$fg['FG_ID']] = 'data-qty="' . sanitize((string)($fg['QuantityAvailable'] ?? 0)) . '"'
                                    . ' data-unit="' . sanitize((string)($fg['Unit'] ?? '')) . '"';
                            }
                            ?>
                            <select id="posProduct" class="form-select">
                                <?= trending_options($finishedGoods, 'FG_ID', 'Flavour', $trends['fg'] ?? null, null, 'Select product…', $fgAttrs) ?>
                            </select>
                        </div>
                        <div class="col-4 col-md-2">
                            <label class="form-label fw-semibold">Qty</label>
                            <input type="number" id="posQty" class="form-control" min="0.01" step="0.01" placeholder="0">
                        </div>
                        <div class="col-8 col-md-3">
                            <label class="form-label fw-semibold">Unit Price (GH&#8373;)</label>
                            <input type="number" id="posPrice" class="form-control" min="0" step="0.01" placeholder="0.00">
                        </div>
                        <div class="col-md-2 col-12">
                            <button type="button" class="btn btn-success w-100" id="posAdd"><i class="bi bi-plus-lg me-1"></i>Add</button>
                        </div>
                    </div>
                    <div class="form-text mb-3" id="posStockHint"></div>

                    <!-- Cart table -->
                    <div class="table-responsive no-datatable">
                        <table class="table table-sm align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Product</th>
                                    <th style="width:110px;">Qty</th>
                                    <th style="width:130px;">Unit Price (GH&#8373;)</th>
                                    <th class="text-end">Line Total</th>
                                    <th class="text-end" style="width:90px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="cartBody">
                                <tr id="cartEmptyRow">
                                    <td colspan="6" class="text-center text-muted py-4">
                                        <i class="bi bi-inbox fs-4 d-block mb-1"></i>
                                        Cart is empty — add products above.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-transparent d-flex gap-2 py-2">
                    <button type="button" class="btn btn-sm btn-outline-danger" id="posClear"><i class="bi bi-x-circle me-1"></i>Clear Cart</button>
                </div>
            </div>
        </div>

        <!-- Summary & payment -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm position-sticky" style="top:72px;">
                <div class="card-header py-3">
                    <span class="fw-bold"><i class="bi bi-receipt-cutoff me-2"></i>Summary &amp; Payment</span>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted">Subtotal</span>
                        <span class="fw-semibold">GH&#8373; <span id="sumSubtotal">0.00</span></span>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted">VAT (15%)</span>
                        <span class="fw-semibold">GH&#8373; <span id="sumVat">0.00</span></span>
                    </div>
                    <hr class="my-2">
                    <div class="d-flex justify-content-between mb-3">
                        <span class="fw-bold">Total Payable</span>
                        <span class="fw-bold fs-5 text-success">GH&#8373; <span id="sumGrand">0.00</span></span>
                    </div>

                    <label class="form-label fw-semibold">Payment Method</label>
                    <select name="payment_method" class="form-select mb-2" id="payMethod">
                        <option value="Cash">Cash</option>
                        <option value="Mobile Money">Mobile Money</option>
                        <option value="Bank Transfer">Bank Transfer</option>
                        <option value="Cheque">Cheque</option>
                    </select>

                    <label class="form-label fw-semibold">Amount Paid (GH&#8373;)</label>
                    <input type="number" class="form-control mb-2" id="amountPaid" min="0" step="0.01" placeholder="0.00">
                    <div class="d-flex justify-content-between align-items-center p-2 rounded mb-3" style="background:rgba(34,197,94,0.08);">
                        <span class="text-muted small">Change Due</span>
                        <span class="fw-bold" id="changeDue">GH&#8373; 0.00</span>
                    </div>

                    <label class="form-label fw-semibold">Notes</label>
                    <textarea name="notes" class="form-control" rows="2" maxlength="500" placeholder="Optional notes for this order…"></textarea>

                    <button type="submit" class="btn btn-success w-100 mt-3" id="posSubmit">
                        <i class="bi bi-check-lg me-1"></i>Complete Order
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
(function () {
    'use strict';

    var VAT = <?= json_encode(SALES_VAT_RATE) ?>;
    var CART_KEY = 'fj_sales_cart';
    var PRICE_KEY = 'fj_fg_prices';
    var CURRENCY = 'GH\u20B5'; // GH₵
    // Admin-set default prices (Product Prices screen), keyed by FG_ID.
    var SERVER_PRICES = <?= json_encode($priceMap ?? []) ?>;

    var productSel = document.getElementById('posProduct');
    var qtyInput   = document.getElementById('posQty');
    var priceInput = document.getElementById('posPrice');
    var hint       = document.getElementById('posStockHint');
    var body       = document.getElementById('cartBody');
    var emptyRow   = document.getElementById('cartEmptyRow');
    var hidden     = document.getElementById('cartItems');
    var countBadge = document.getElementById('posCount');
    var paidInput  = document.getElementById('amountPaid');

    var cart = [];
    var stockCache = {};   // FG_ID -> available qty
    var unitCache  = {};   // FG_ID -> unit label

    // Stock/unit metadata lives on the <option data-*> attributes.
    Array.prototype.forEach.call(productSel.options, function (o) {
        if (!o.value) return;
        stockCache[o.value] = parseFloat(o.getAttribute('data-qty')) || 0;
        unitCache[o.value]  = o.getAttribute('data-unit') || '';
    });

    var readJSON = function (key, fallback) {
        try { return JSON.parse(localStorage.getItem(key) || sessionStorage.getItem(key) || '') || fallback; }
        catch (e) { return fallback; }
    };
    var prices = readJSON(PRICE_KEY, {});

    var money = function (n) { return (Math.round(n * 100) / 100).toFixed(2); };

    var saveCart = function () {
        try { sessionStorage.setItem(CART_KEY, JSON.stringify(cart)); } catch (e) {}
    };
    var toast = function (icon, title) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({ icon: icon, title: title, toast: true, position: 'top-end', showConfirmButton: false, timer: 2600 });
        }
    };

    /* ── Totals ── */
    // updateRows=false keeps the DOM intact while the user types inline.
    var recomputeTotals = function (updateRows) {
        var subtotal = 0, vat = 0;
        cart.forEach(function (l) {
            var line = l.quantity * l.price;
            var lineVat = Math.round(line * VAT * 100) / 100;
            subtotal += line;
            vat += lineVat;
        });
        document.getElementById('sumSubtotal').textContent = money(subtotal);
        document.getElementById('sumVat').textContent = money(vat);
        document.getElementById('sumGrand').textContent = money(subtotal + vat);
        countBadge.textContent = cart.length + (cart.length === 1 ? ' item' : ' items');
        updateChange();
        if (updateRows !== false) {
            renderCart();
        }
        saveCart();
    };

    var updateChange = function () {
        var grand = parseFloat(document.getElementById('sumGrand').textContent) || 0;
        var paid = parseFloat(paidInput.value) || 0;
        var el = document.getElementById('changeDue');
        var change = paid - grand;
        el.textContent = CURRENCY + ' ' + money(Math.max(change, 0));
        el.classList.toggle('text-danger', change < -0.001 && paid > 0);
    };

    /* ── Cart rendering ── */
    var esc = function (s) {
        return String(s).replace(/[&<>"']/g, function (c) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
        });
    };

    var renderCart = function () {
        body.innerHTML = '';
        if (!cart.length) {
            body.appendChild(emptyRow);
            return;
        }
        cart.forEach(function (line, i) {
            var tr = document.createElement('tr');
            tr.innerHTML =
                '<td>' + (i + 1) + '</td>' +
                '<td><div class="fw-semibold">' + esc(line.name) + '</div>' +
                    '<small class="text-muted">' + esc(unitCache[line.fg_id] || line.unit) + '</small></td>' +
                '<td><input type="number" class="form-control form-control-sm cart-qty" data-i="' + i + '" min="0.01" step="0.01" value="' + line.quantity + '"></td>' +
                '<td><input type="number" class="form-control form-control-sm cart-price" data-i="' + i + '" min="0" step="0.01" value="' + line.price + '"></td>' +
                '<td class="text-end fw-semibold">' + CURRENCY + ' ' + money(line.quantity * line.price * (1 + VAT)) + '</td>' +
                '<td class="text-end">' +
                    '<button type="button" class="btn btn-sm btn-outline-danger cart-del" data-i="' + i + '" title="Remove"><i class="bi bi-trash"></i></button>' +
                '</td>';
            body.appendChild(tr);
        });
    };

    /* ── Product picker behaviour ── */
    var refreshStockHint = function () {
        var fg = productSel.value;
        if (!fg) { hint.textContent = ''; return; }
        var inCart = 0;
        cart.forEach(function (l) { if (l.fg_id === fg) inCart += l.quantity; });
        hint.innerHTML = 'Available stock: <strong>' + stockCache[fg] + '</strong> ' + esc(unitCache[fg] || '') +
            (inCart ? ' · <span class="text-warning">' + inCart + ' already in cart</span>' : '');
    };

    productSel.addEventListener('change', function () {
        var fg = productSel.value;
        // Suggest the set price for this product; fall back to the last price used (POS muscle-memory).
        var suggested = SERVER_PRICES[fg] || prices[fg];
        if (fg && suggested && !priceInput.value) priceInput.value = suggested;
        refreshStockHint();
    });
    priceInput.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') { e.preventDefault(); document.getElementById('posAdd').click(); }
    });

    /* ── Add to cart ── */
    document.getElementById('posAdd').addEventListener('click', function () {
        var fg = productSel.value;
        var qty = parseFloat(qtyInput.value);
        var price = parseFloat(priceInput.value);

        if (!fg) { toast('warning', 'Select a product first.'); return; }
        if (!(qty > 0)) { toast('warning', 'Enter a quantity greater than zero.'); return; }
        if (!(price >= 0)) { toast('warning', 'Enter a valid unit price.'); return; }

        var avail = stockCache[fg];
        var existing = cart.find(function (l) { return l.fg_id === fg; });
        var totalForFg = (existing ? existing.quantity : 0) + qty;
        if (totalForFg > avail) {
            qty = Math.max(avail - (existing ? existing.quantity : 0), 0);
            if (qty <= 0) { toast('error', 'Only ' + avail + ' available — already all in cart.'); return; }
            toast('warning', 'Capped to available stock (' + avail + ').');
        }

        if (existing) {
            existing.quantity += qty;
        } else {
            cart.push({
                fg_id: fg,
                name: productSel.selectedOptions[0].textContent.trim(),
                quantity: qty,
                price: price,
                unit: unitCache[fg]
            });
        }

        prices[fg] = price;
        try { localStorage.setItem(PRICE_KEY, JSON.stringify(prices)); } catch (e) {}

        qtyInput.value = '';
        refreshStockHint();
        recomputeTotals();
    });

    /* ── Inline edit / remove / clear (delegated) ── */
    body.addEventListener('input', function (e) {
        var t = e.target;
        var i = parseInt(t.getAttribute('data-i'), 10);
        if (isNaN(i) || !cart[i]) return;
        var clamped = false;
        if (t.classList.contains('cart-qty')) {
            var v = parseFloat(t.value) || 0;
            var fg = cart[i].fg_id;
            var others = 0;
            cart.forEach(function (l, j) { if (j !== i && l.fg_id === fg) others += l.quantity; });
            if (v + others > stockCache[fg]) {
                v = Math.max(stockCache[fg] - others, 0);
                t.value = v;
                toast('warning', 'Capped to available stock.');
                clamped = true;
            }
            cart[i].quantity = v;
        } else if (t.classList.contains('cart-price')) {
            cart[i].price = parseFloat(t.value) || 0;
        }
        // Refresh just this row's total cell — a full re-render would steal focus.
        var tr = t.closest('tr');
        if (tr) {
            var cell = tr.querySelector('td:nth-child(5)');
            if (cell) cell.textContent = CURRENCY + ' ' + money(cart[i].quantity * cart[i].price * (1 + VAT));
        }
        recomputeTotals(false);
        if (clamped) renderCart();
    });

    body.addEventListener('click', function (e) {
        var btn = e.target.closest('.cart-del');
        if (!btn) return;
        cart.splice(parseInt(btn.getAttribute('data-i'), 10), 1);
        recomputeTotals();
    });

    document.getElementById('posClear').addEventListener('click', function () {
        if (!cart.length) return;
        cart = [];
        recomputeTotals();
        toast('info', 'Cart cleared.');
    });

    paidInput.addEventListener('input', updateChange);

    /* ── Submit: serialise the cart ── */
    document.getElementById('posForm').addEventListener('submit', function (e) {
        if (!cart.length) {
            e.preventDefault();
            toast('warning', 'Add at least one product to the cart.');
            return;
        }
        var ok = true;
        ['order_date', 'customer_id'].forEach(function (n) {
            var f = this.querySelector('[name="' + n + '"]');
            if (f && !f.value) { f.classList.add('is-invalid'); ok = false; }
        }, this);
        if (!ok) { e.preventDefault(); toast('warning', 'Fill in the order details first.'); return; }

        hidden.value = JSON.stringify(cart.map(function (l) {
            return { fg_id: l.fg_id, quantity: l.quantity, unit_price: l.price };
        }));
        try { sessionStorage.removeItem(CART_KEY); } catch (e) {}
    });

    /* ── Boot: restore an unfinished cart ── */
    cart = readJSON(CART_KEY, []);
    cart = cart.filter(function (l) { return l && stockCache[l.fg_id] !== undefined; });
    recomputeTotals();
})();
</script>
<?php endif; ?>
