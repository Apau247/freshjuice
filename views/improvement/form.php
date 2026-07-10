<?php $pageTitle = isset($_GET['id']) ? 'Edit Initiative' : 'New Initiative'; ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4><i class="fas fa-lightbulb me-2"></i><?php echo $pageTitle; ?></h4>
        <a href="?route=improvement/index" class="btn btn-secondary"><i class="fas fa-arrow-left me-1"></i>Back to CAPA</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="?route=improvement/save">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="InitiativeID" class="form-label">Initiative ID <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="InitiativeID" name="InitiativeID" value="<?php echo sanitize($initiative['InitiativeID'] ?? ''); ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="Category" class="form-label">Category <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="Category" name="Category" value="<?php echo sanitize($initiative['Category'] ?? ''); ?>" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="Title" class="form-label">Title <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="Title" name="Title" value="<?php echo sanitize($initiative['Title'] ?? ''); ?>" required>
                </div>

                <div class="mb-3">
                    <label for="Description" class="form-label">Description</label>
                    <textarea class="form-control" id="Description" name="Description" rows="3"><?php echo sanitize($initiative['Description'] ?? ''); ?></textarea>
                </div>

                <div class="mb-3">
                    <label for="RootCauseAnalysis" class="form-label">Root Cause Analysis</label>
                    <textarea class="form-control" id="RootCauseAnalysis" name="RootCauseAnalysis" rows="3"><?php echo sanitize($initiative['RootCauseAnalysis'] ?? ''); ?></textarea>
                </div>

                <div class="mb-3">
                    <label for="ActionPlan" class="form-label">Action Plan</label>
                    <textarea class="form-control" id="ActionPlan" name="ActionPlan" rows="3"><?php echo sanitize($initiative['ActionPlan'] ?? ''); ?></textarea>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="TargetDate" class="form-label">Target Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="TargetDate" name="TargetDate" value="<?php echo sanitize($initiative['TargetDate'] ?? ''); ?>" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="ResponsiblePerson" class="form-label">Responsible Person</label>
                        <input type="text" class="form-control" id="ResponsiblePerson" name="ResponsiblePerson" value="<?php echo sanitize($initiative['ResponsiblePerson'] ?? ''); ?>">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="Status" class="form-label">Status <span class="text-danger">*</span></label>
                        <select class="form-select" id="Status" name="Status" required>
                            <option value="Proposed" <?php echo (isset($initiative['Status']) && $initiative['Status'] === 'Proposed') ? 'selected' : ''; ?>>Proposed</option>
                            <option value="In Progress" <?php echo (isset($initiative['Status']) && $initiative['Status'] === 'In Progress') ? 'selected' : ''; ?>>In Progress</option>
                            <option value="Completed" <?php echo (isset($initiative['Status']) && $initiative['Status'] === 'Completed') ? 'selected' : ''; ?>>Completed</option>
                            <option value="Overdue" <?php echo (isset($initiative['Status']) && $initiative['Status'] === 'Overdue') ? 'selected' : ''; ?>>Overdue</option>
                            <option value="Cancelled" <?php echo (isset($initiative['Status']) && $initiative['Status'] === 'Cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="Effectiveness" class="form-label">Effectiveness</label>
                    <textarea class="form-control" id="Effectiveness" name="Effectiveness" rows="3"><?php echo sanitize($initiative['Effectiveness'] ?? ''); ?></textarea>
                </div>

                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Save Initiative</button>
                <a href="?route=improvement/index" class="btn btn-secondary ms-2">Cancel</a>
            </form>
        </div>
    </div>
</div>
