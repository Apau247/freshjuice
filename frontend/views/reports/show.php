<?php $pageTitle = $title; ?>
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <div>
        <h5 class="fw-bold mb-0"><i class="bi bi-file-earmark-bar-graph me-2"></i><?= sanitize($title) ?></h5>
        <?php if (!empty($period)): ?><span class="text-muted" style="font-size:0.82rem;"><?= sanitize($period) ?></span><?php endif; ?>
    </div>
    <div class="d-flex gap-2">
        <a href="?route=reports" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> All Reports</a>
        <a href="?route=reports/print&type=<?= urlencode($type) ?>&from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>"
           target="_blank" rel="noopener"
           class="btn btn-outline-primary btn-sm" title="Opens a clean printable document (company header, report name and results only)">
            <i class="bi bi-printer"></i> Print / PDF
        </a>
        <a href="?route=reports/export&type=<?= urlencode($type) ?>&from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>" class="btn btn-success btn-sm"><i class="bi bi-filetype-csv"></i> Export CSV</a>
    </div>
</div>

<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-2">
        <form method="get" action="" class="row g-2 align-items-end">
            <input type="hidden" name="route" value="reports/view">
            <input type="hidden" name="type" value="<?= sanitize($type) ?>">
            <div class="col-auto">
                <label class="form-label mb-0 text-muted" style="font-size:0.75rem;">From</label>
                <input type="date" name="from" value="<?= sanitize($from) ?>" class="form-control form-control-sm">
            </div>
            <div class="col-auto">
                <label class="form-label mb-0 text-muted" style="font-size:0.75rem;">To</label>
                <input type="date" name="to" value="<?= sanitize($to) ?>" class="form-control form-control-sm">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-sm btn-success"><i class="bi bi-funnel"></i> Apply</button>
            </div>
        </form>
    </div>
</div>

<?php if (!empty($summary)): ?>
<div class="row g-3 mb-3">
    <?php foreach ($summary as $label => $value): ?>
    <div class="col-6 col-md-4 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body py-3">
                <div class="text-muted text-uppercase" style="font-size:0.68rem;letter-spacing:.04em;"><?= sanitize((string)$label) ?></div>
                <div class="fw-bold mt-1" style="font-size:1.05rem;"><?= sanitize((string)$value) ?></div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <?php if (empty($rows)): ?>
        <div class="text-center py-5 text-muted">
            <i class="bi bi-inbox" style="font-size:2.5rem;"></i>
            <p class="mt-2 mb-0">No data for the selected period.</p>
        </div>
        <?php else: ?>
        <table id="dataTable" class="table table-hover align-middle">
            <thead class="table-light">
                <tr><?php foreach ($headers as $h): ?><th><?= sanitize((string)$h) ?></th><?php endforeach; ?></tr>
            </thead>
            <tbody>
                <?php foreach ($rows as $row): ?>
                <tr><?php foreach ($row as $cell): ?><td><?= sanitize((string)$cell) ?></td><?php endforeach; ?></tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>
