<?php $pageTitle = 'Traceability Logs'; ?>
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <h5 class="fw-bold mb-0"><i class="bi bi-diagram-3 me-2"></i><?= $pageTitle ?></h5>
    <span class="text-muted" style="font-size:0.8rem;">Full chain: fruit supplier &rarr; materials &rarr; production &rarr; inspections &rarr; finished goods &rarr; sales</span>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <?php if (empty($batches)): ?>
        <div class="text-center py-5 text-muted">
            <i class="bi bi-diagram-3" style="font-size:2.5rem;"></i>
            <p class="mt-2 mb-0">No batches recorded yet.</p>
        </div>
        <?php else: ?>
        <div class="table-responsive"><table id="dataTable" class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Batch</th><th>Date</th><th>Flavour</th><th>Qty</th>
                    <th>Raw Material (Supplier)</th><th>Packaging</th><th>Machine</th>
                    <th>QC Results</th><th>Finished Goods</th><th>Orders</th><th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($batches as $b): ?>
                <?php
                    $qcBadges = '';
                    foreach (array_filter(explode('/', (string)($b['QCResults'] ?? ''))) as $r) {
                        $cls = $r === 'Pass' ? 'success' : ($r === 'Fail' ? 'danger' : 'secondary');
                        $qcBadges .= '<span class="badge bg-' . $cls . ' me-1">' . sanitize($r) . '</span>';
                    }
                ?>
                <tr>
                    <td class="fw-semibold"><?= sanitize($b['BatchNumber']) ?></td>
                    <td><?= sanitize($b['ProductionDate']) ?></td>
                    <td><?= sanitize($b['Flavour']) ?></td>
                    <td><?= number_format((float)$b['Quantity'], 2) ?> <?= sanitize($b['Unit']) ?></td>
                    <td>
                        <?= sanitize($b['RawMaterial'] ?? '-') ?>
                        <?php if (!empty($b['SupplierName'])): ?>
                        <br><small class="text-muted"><i class="bi bi-truck"></i> <?= sanitize($b['SupplierName']) ?><?= $b['DeliveryDate'] ? ' · ' . sanitize($b['DeliveryDate']) : '' ?></small>
                        <?php endif; ?>
                    </td>
                    <td><?= sanitize($b['PackagingMaterial'] ?? '-') ?></td>
                    <td><?= sanitize($b['MachineName'] ?? '-') ?></td>
                    <td><?= $qcBadges !== '' ? $qcBadges : '<span class="badge bg-secondary">None</span>' ?></td>
                    <td>
                        <?php if (!empty($b['FG_ID'])): ?>
                            <span class="fw-semibold"><?= sanitize($b['FG_ID']) ?></span><br>
                            <small class="text-muted">Exp <?= sanitize($b['FGExpiry']) ?> · <?= number_format((float)$b['FGQty'], 2) ?> on hand</small>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ((int)$b['OrderCount'] > 0): ?>
                        <a href="?route=sales" class="badge bg-primary text-decoration-none"><?= (int)$b['OrderCount'] ?> order(s)</a>
                        <?php else: ?>
                        <span class="text-muted">-</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php
                        $statusCls = ['Completed' => 'success', 'In Progress' => 'primary', 'Pending' => 'secondary',
                                      'Rejected' => 'danger', 'Cancelled' => 'dark'][$b['Status']] ?? 'secondary';
                        ?>
                        <span class="badge bg-<?= $statusCls ?>"><?= sanitize($b['Status']) ?></span>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table></div>
        <?php endif; ?>
    </div>
</div>
