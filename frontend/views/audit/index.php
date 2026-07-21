<?php $pageTitle = 'Audit Trail'; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0"><i class="bi bi-journal-text me-2"></i><?= $pageTitle ?></h5>
</div>

<div class="card border-0 shadow-sm mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <input type="hidden" name="route" value="audit">
            <div class="col-md-4">
                <label class="form-label fw-semibold small">Module</label>
                <select name="module" class="form-select">
                    <option value="">All Modules</option>
                    <?php foreach ($modules as $mod): ?>
                    <option value="<?= sanitize($mod) ?>" <?= $currentModule === $mod ? 'selected' : '' ?>><?= sanitize(ucfirst($mod)) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold small">Action</label>
                <select name="action" class="form-select">
                    <option value="">All Actions</option>
                    <?php foreach (['CREATE', 'UPDATE', 'DELETE', 'LOGIN', 'LOGOUT'] as $act): ?>
                    <option value="<?= $act ?>" <?= $currentAction === $act ? 'selected' : '' ?>><?= $act ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-funnel me-1"></i> Filter</button>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <table id="dataTable" class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Date</th>
                    <th>User</th>
                    <th>Action</th>
                    <th>Module</th>
                    <th>Record ID</th>
                    <th>Details</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($logs as $log): ?>
                <tr>
                    <td class="text-muted small"><?= sanitize($log['created_at'] ?? '') ?></td>
                    <td class="fw-semibold"><?= sanitize($log['UserName'] ?? $log['UserID'] ?? 'System') ?></td>
                    <td>
                        <?php
                            $actionBadge = match($log['Action'] ?? '') {
                                'CREATE' => 'success',
                                'UPDATE' => 'primary',
                                'DELETE' => 'danger',
                                'LOGIN', 'LOGOUT' => 'info',
                                default => 'secondary',
                            };
                        ?>
                        <span class="badge bg-<?= $actionBadge ?>"><?= sanitize($log['Action'] ?? '') ?></span>
                    </td>
                    <td><span class="badge bg-light text-dark"><?= sanitize($log['Module'] ?? '') ?></span></td>
                    <td><code class="small"><?= sanitize($log['RecordID'] ?? '') ?></code></td>
                    <td class="small"><?= sanitize($log['Details'] ?? '') ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
