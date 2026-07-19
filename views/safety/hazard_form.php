<?php $pageTitle = isset($hazard) ? 'Edit Hazard' : 'New Hazard'; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0"><i class="bi bi-exclamation-triangle me-2"></i><?= $pageTitle ?></h5>
    <a href="?route=safety/hazards" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Back</a>
</div>
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="?route=<?= isset($hazard) ? 'safety/hazards/edit&id=' . urlencode($hazard['HazardID']) : 'safety/hazards/create' ?>" class="row g-3">
            <?= csrfField() ?>
            <div class="col-12">
                <label class="form-label fw-semibold">Hazard Description <span class="text-danger">*</span></label>
                <textarea name="description" class="form-control" rows="3" required><?= sanitize($hazard['HazardDescription'] ?? '') ?></textarea>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Risk Category</label>
                <input type="text" name="risk_category" class="form-control" value="<?= sanitize($hazard['RiskCategory'] ?? '') ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Likelihood <span class="text-danger">*</span></label>
                <select name="likelihood" class="form-select" required id="likelihoodSelect">
                    <option value="">Select</option>
                    <?php $lk = $hazard['Likelihood'] ?? ''; ?>
                    <option value="Rare" <?= $lk === 'Rare' ? 'selected' : '' ?>>Rare</option>
                    <option value="Unlikely" <?= $lk === 'Unlikely' ? 'selected' : '' ?>>Unlikely</option>
                    <option value="Possible" <?= $lk === 'Possible' ? 'selected' : '' ?>>Possible</option>
                    <option value="Likely" <?= $lk === 'Likely' ? 'selected' : '' ?>>Likely</option>
                    <option value="Almost Certain" <?= $lk === 'Almost Certain' ? 'selected' : '' ?>>Almost Certain</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Consequence <span class="text-danger">*</span></label>
                <select name="consequence" class="form-select" required id="consequenceSelect">
                    <option value="">Select</option>
                    <?php $cn = $hazard['Consequence'] ?? ''; ?>
                    <option value="Insignificant" <?= $cn === 'Insignificant' ? 'selected' : '' ?>>Insignificant</option>
                    <option value="Minor" <?= $cn === 'Minor' ? 'selected' : '' ?>>Minor</option>
                    <option value="Moderate" <?= $cn === 'Moderate' ? 'selected' : '' ?>>Moderate</option>
                    <option value="Major" <?= $cn === 'Major' ? 'selected' : '' ?>>Major</option>
                    <option value="Catastrophic" <?= $cn === 'Catastrophic' ? 'selected' : '' ?>>Catastrophic</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Risk Rating</label>
                <div class="form-control bg-light" id="riskRatingDisplay">
                    <?php $rr = $hazard['RiskRating'] ?? 0; ?>
                    <?= $rr > 0 ? $rr . ' - ' . ($rr >= 15 ? 'Extreme' : ($rr >= 8 ? 'High' : ($rr >= 4 ? 'Medium' : 'Low'))) : '--' ?>
                </div>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Responsible Person</label>
                <input type="text" name="responsible_person" class="form-control" value="<?= sanitize($hazard['ResponsiblePerson'] ?? '') ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Review Date</label>
                <input type="date" name="review_date" class="form-control" value="<?= sanitize($hazard['ReviewDate'] ?? '') ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Status</label>
                <select name="status" class="form-select">
                    <?php $st = $hazard['Status'] ?? 'Active'; ?>
                    <option value="Active" <?= $st === 'Active' ? 'selected' : '' ?>>Active</option>
                    <option value="Mitigated" <?= $st === 'Mitigated' ? 'selected' : '' ?>>Mitigated</option>
                    <option value="Closed" <?= $st === 'Closed' ? 'selected' : '' ?>>Closed</option>
                </select>
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold">Control Measures</label>
                <textarea name="control_measures" class="form-control" rows="3"><?= sanitize($hazard['ControlMeasures'] ?? '') ?></textarea>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-success"><i class="bi bi-check-lg"></i> <?= isset($hazard) ? 'Update' : 'Create' ?> Hazard</button>
                <a href="?route=safety/hazards" class="btn btn-outline-secondary ms-2">Cancel</a>
            </div>
        </form>
    </div>
</div>

<script>
const lScores = {'Rare':1,'Unlikely':2,'Possible':3,'Likely':4,'Almost Certain':5};
const cScores = {'Insignificant':1,'Minor':2,'Moderate':3,'Major':4,'Catastrophic':5};
function calculateRisk() {
    var l = document.getElementById('likelihoodSelect').value;
    var c = document.getElementById('consequenceSelect').value;
    var ls = lScores[l] || 0, cs = cScores[c] || 0;
    var rating = ls * cs;
    var display = document.getElementById('riskRatingDisplay');
    if (rating > 0) {
        var label = 'Low', cls = 'text-success';
        if (rating >= 15) { label = 'Extreme'; cls = 'text-danger'; }
        else if (rating >= 8) { label = 'High'; cls = 'text-warning'; }
        else if (rating >= 4) { label = 'Medium'; cls = 'text-info'; }
        display.innerHTML = '<span class="' + cls + ' fw-bold">' + rating + ' - ' + label + '</span>';
    } else { display.innerHTML = '--'; }
}
document.getElementById('likelihoodSelect').addEventListener('change', calculateRisk);
document.getElementById('consequenceSelect').addEventListener('change', calculateRisk);
</script>
