<?php $pageTitle = 'Backup & Restore'; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0"><i class="bi bi-cloud-arrow-down me-2"></i><?= $pageTitle ?></h5>
</div>

<div class="row g-3">
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h6 class="fw-bold mb-2"><i class="bi bi-download me-2 text-success"></i>Download Backup</h6>
                <p class="text-muted" style="font-size:0.84rem;">
                    Streams a full SQL dump of all <?= count($stats) ?> tables (structure + data).
                    Store copies off the server — ideally on a different machine or cloud drive.
                </p>
                <form method="post" action="?route=settings/backup" class="d-flex gap-2 align-items-center flex-wrap">
                    <?= csrfField() ?>
                    <button type="submit" class="btn btn-success btn-sm"><i class="bi bi-cloud-arrow-down"></i> Download .sql Backup</button>
                    <span class="text-muted" style="font-size:0.78rem;">Snapshot of <?= count($stats) ?> tables, ~<?= number_format(array_sum($stats)) ?> rows</span>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100 border-start border-4 border-danger">
            <div class="card-body">
                <h6 class="fw-bold mb-2"><i class="bi bi-upload me-2 text-danger"></i>Restore From Backup</h6>
                <div class="alert alert-danger py-2 px-3" style="font-size:0.8rem;">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i>
                    Restoring <strong>replaces every table</strong> with the backup's contents.
                    All data entered after the backup was taken will be lost. A rollback is performed if the restore fails.
                </div>
                <form method="post" action="?route=settings/restore" enctype="multipart/form-data" class="row g-2">
                    <?= csrfField() ?>
                    <div class="col-12">
                        <input type="file" name="backup_file" accept=".sql,.txt" class="form-control form-control-sm" required>
                    </div>
                    <div class="col-12">
                        <input type="text" name="confirm" class="form-control form-control-sm" placeholder='Type RESTORE to confirm' pattern="RESTORE" required>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-danger btn-sm"><i class="bi bi-arrow-counterclockwise"></i> Restore Database</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm mt-3">
    <div class="card-body">
        <h6 class="fw-bold mb-3"><i class="bi bi-database me-2 text-secondary"></i>Database Tables</h6>
        <table id="dataTable" class="table table-hover table-sm align-middle">
            <thead class="table-light">
                <tr><th>Table</th><th style="width:140px;">Rows</th></tr>
            </thead>
            <tbody>
                <?php foreach ($stats as $table => $rows): ?>
                <tr>
                    <td><code><?= sanitize((string)$table) ?></code></td>
                    <td><?= number_format((int)$rows) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
