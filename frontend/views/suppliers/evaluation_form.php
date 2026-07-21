<?php $pageTitle = isset($evaluation) ? 'Edit Evaluation' : 'New Supplier Evaluation'; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0"><i class="bi bi-clipboard-data me-2"></i><?= $pageTitle ?></h5>
    <a href="?route=supplier-evaluations" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Back</a>
</div>
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="?route=<?= isset($evaluation) ? 'supplier-evaluations/edit&id=' . urlencode($evaluation['EvaluationID']) : 'supplier-evaluations/create' ?>" class="row g-3">
            <?= csrfField() ?>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Supplier <span class="text-danger">*</span></label>
                <select name="supplier_id" class="form-select" required>
                    <option value="">Select Supplier</option>
                    <?php if (!empty($suppliers)): foreach ($suppliers as $s): ?>
                    <option value="<?= sanitize($s['SupplierID']) ?>" <?= (isset($evaluation) && ($evaluation['SupplierID'] ?? '') === $s['SupplierID']) ? 'selected' : '' ?>><?= sanitize($s['Name']) ?></option>
                    <?php endforeach; endif; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Evaluation Date <span class="text-danger">*</span></label>
                <input type="date" name="evaluation_date" class="form-control" value="<?= sanitize($evaluation['EvaluationDate'] ?? date('Y-m-d')) ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Quality Score (1-5) <span class="text-danger">*</span></label>
                <select name="quality_score" class="form-select" required onchange="calcOverall()">
                    <option value="">Select</option>
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                    <option value="<?= $i ?>" <?= (isset($evaluation) && (float)($evaluation['QualityScore'] ?? 0) == $i) ? 'selected' : '' ?>><?= $i ?> - <?= ['', 'Very Poor', 'Poor', 'Average', 'Good', 'Excellent'][$i] ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Delivery Score (1-5) <span class="text-danger">*</span></label>
                <select name="delivery_score" class="form-select" required onchange="calcOverall()">
                    <option value="">Select</option>
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                    <option value="<?= $i ?>" <?= (isset($evaluation) && (float)($evaluation['DeliveryScore'] ?? 0) == $i) ? 'selected' : '' ?>><?= $i ?> - <?= ['', 'Very Poor', 'Poor', 'Average', 'Good', 'Excellent'][$i] ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Price Score (1-5) <span class="text-danger">*</span></label>
                <select name="price_score" class="form-select" required onchange="calcOverall()">
                    <option value="">Select</option>
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                    <option value="<?= $i ?>" <?= (isset($evaluation) && (float)($evaluation['PriceScore'] ?? 0) == $i) ? 'selected' : '' ?>><?= $i ?> - <?= ['', 'Very Poor', 'Poor', 'Average', 'Good', 'Excellent'][$i] ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-12">
                <div class="card bg-light">
                    <div class="card-body text-center py-2">
                        <small class="text-muted">Overall Score</small>
                        <h4 class="mb-0" id="overallDisplay"><?php if (isset($evaluation) && ($evaluation['OverallScore'] ?? 0) > 0): ?><?= number_format((float)$evaluation['OverallScore'], 1) ?> / 5<?php else: ?>--<?php endif; ?></h4>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Strengths</label>
                <textarea name="strengths" class="form-control" rows="3"><?= sanitize($evaluation['Strengths'] ?? '') ?></textarea>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Weaknesses</label>
                <textarea name="weaknesses" class="form-control" rows="3"><?= sanitize($evaluation['Weaknesses'] ?? '') ?></textarea>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Recommendations</label>
                <textarea name="recommendations" class="form-control" rows="3"><?= sanitize($evaluation['Recommendations'] ?? '') ?></textarea>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-success"><i class="bi bi-check-lg"></i> <?= isset($evaluation) ? 'Update' : 'Create' ?> Evaluation</button>
                <a href="?route=supplier-evaluations" class="btn btn-outline-secondary ms-2">Cancel</a>
            </div>
        </form>
    </div>
</div>
<script>
function calcOverall() {
    var q = parseFloat(document.querySelector('[name=quality_score]').value) || 0;
    var d = parseFloat(document.querySelector('[name=delivery_score]').value) || 0;
    var p = parseFloat(document.querySelector('[name=price_score]').value) || 0;
    var avg = (q + d + p) / 3;
    document.getElementById('overallDisplay').textContent = avg > 0 ? avg.toFixed(1) + ' / 5' : '--';
}
</script>
