<?php $pageTitle = isset($_GET['id']) ? 'Edit Evaluation' : 'New Supplier Evaluation'; ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4><i class="fas fa-star me-2"></i><?php echo $pageTitle; ?></h4>
        <a href="?route=suppliers/evaluations" class="btn btn-secondary"><i class="fas fa-arrow-left me-1"></i>Back to Evaluations</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="?route=suppliers/evaluation_save">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="EvaluationID" class="form-label">Evaluation ID <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="EvaluationID" name="EvaluationID" value="<?php echo sanitize($evaluation['EvaluationID'] ?? ''); ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="SupplierID" class="form-label">Supplier <span class="text-danger">*</span></label>
                        <select class="form-select" id="SupplierID" name="SupplierID" required>
                            <option value="">Select Supplier</option>
                            <?php if (!empty($suppliers)): ?>
                                <?php foreach ($suppliers as $supplier): ?>
                                    <option value="<?php echo sanitize($supplier['SupplierID']); ?>" <?php echo (isset($evaluation['SupplierID']) && $evaluation['SupplierID'] == $supplier['SupplierID']) ? 'selected' : ''; ?>><?php echo sanitize($supplier['SupplierName']); ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="EvaluationDate" class="form-label">Evaluation Date <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" id="EvaluationDate" name="EvaluationDate" value="<?php echo sanitize($evaluation['EvaluationDate'] ?? ''); ?>" required>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="QualityScore" class="form-label">Quality Score (1-5) <span class="text-danger">*</span></label>
                        <select class="form-select" id="QualityScore" name="QualityScore" required onchange="calculateOverall()">
                            <option value="">Select</option>
                            <option value="1" <?php echo (isset($evaluation['QualityScore']) && $evaluation['QualityScore'] == '1') ? 'selected' : ''; ?>>1 - Very Poor</option>
                            <option value="2" <?php echo (isset($evaluation['QualityScore']) && $evaluation['QualityScore'] == '2') ? 'selected' : ''; ?>>2 - Poor</option>
                            <option value="3" <?php echo (isset($evaluation['QualityScore']) && $evaluation['QualityScore'] == '3') ? 'selected' : ''; ?>>3 - Average</option>
                            <option value="4" <?php echo (isset($evaluation['QualityScore']) && $evaluation['QualityScore'] == '4') ? 'selected' : ''; ?>>4 - Good</option>
                            <option value="5" <?php echo (isset($evaluation['QualityScore']) && $evaluation['QualityScore'] == '5') ? 'selected' : ''; ?>>5 - Excellent</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="DeliveryScore" class="form-label">Delivery Score (1-5) <span class="text-danger">*</span></label>
                        <select class="form-select" id="DeliveryScore" name="DeliveryScore" required onchange="calculateOverall()">
                            <option value="">Select</option>
                            <option value="1" <?php echo (isset($evaluation['DeliveryScore']) && $evaluation['DeliveryScore'] == '1') ? 'selected' : ''; ?>>1 - Very Poor</option>
                            <option value="2" <?php echo (isset($evaluation['DeliveryScore']) && $evaluation['DeliveryScore'] == '2') ? 'selected' : ''; ?>>2 - Poor</option>
                            <option value="3" <?php echo (isset($evaluation['DeliveryScore']) && $evaluation['DeliveryScore'] == '3') ? 'selected' : ''; ?>>3 - Average</option>
                            <option value="4" <?php echo (isset($evaluation['DeliveryScore']) && $evaluation['DeliveryScore'] == '4') ? 'selected' : ''; ?>>4 - Good</option>
                            <option value="5" <?php echo (isset($evaluation['DeliveryScore']) && $evaluation['DeliveryScore'] == '5') ? 'selected' : ''; ?>>5 - Excellent</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="PriceScore" class="form-label">Price Score (1-5) <span class="text-danger">*</span></label>
                        <select class="form-select" id="PriceScore" name="PriceScore" required onchange="calculateOverall()">
                            <option value="">Select</option>
                            <option value="1" <?php echo (isset($evaluation['PriceScore']) && $evaluation['PriceScore'] == '1') ? 'selected' : ''; ?>>1 - Very Poor</option>
                            <option value="2" <?php echo (isset($evaluation['PriceScore']) && $evaluation['PriceScore'] == '2') ? 'selected' : ''; ?>>2 - Poor</option>
                            <option value="3" <?php echo (isset($evaluation['PriceScore']) && $evaluation['PriceScore'] == '3') ? 'selected' : ''; ?>>3 - Average</option>
                            <option value="4" <?php echo (isset($evaluation['PriceScore']) && $evaluation['PriceScore'] == '4') ? 'selected' : ''; ?>>4 - Good</option>
                            <option value="5" <?php echo (isset($evaluation['PriceScore']) && $evaluation['PriceScore'] == '5') ? 'selected' : ''; ?>>5 - Excellent</option>
                        </select>
                    </div>
                </div>

                <div class="card bg-light mb-3">
                    <div class="card-body text-center">
                        <small class="text-muted">Overall Score</small>
                        <h4 id="overallDisplay">--</h4>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="Strengths" class="form-label">Strengths</label>
                    <textarea class="form-control" id="Strengths" name="Strengths" rows="3"><?php echo sanitize($evaluation['Strengths'] ?? ''); ?></textarea>
                </div>

                <div class="mb-3">
                    <label for="Weaknesses" class="form-label">Weaknesses</label>
                    <textarea class="form-control" id="Weaknesses" name="Weaknesses" rows="3"><?php echo sanitize($evaluation['Weaknesses'] ?? ''); ?></textarea>
                </div>

                <div class="mb-3">
                    <label for="Recommendations" class="form-label">Recommendations</label>
                    <textarea class="form-control" id="Recommendations" name="Recommendations" rows="3"><?php echo sanitize($evaluation['Recommendations'] ?? ''); ?></textarea>
                </div>

                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Save Evaluation</button>
                <a href="?route=suppliers/evaluations" class="btn btn-secondary ms-2">Cancel</a>
            </form>
        </div>
    </div>
</div>

<script>
function calculateOverall() {
    var q = parseFloat(document.getElementById('QualityScore').value) || 0;
    var d = parseFloat(document.getElementById('DeliveryScore').value) || 0;
    var p = parseFloat(document.getElementById('PriceScore').value) || 0;
    var avg = (q + d + p) / 3;
    document.getElementById('overallDisplay').textContent = avg > 0 ? avg.toFixed(1) + ' / 5' : '--';
}
calculateOverall();
</script>
