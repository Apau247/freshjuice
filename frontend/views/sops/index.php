<?php $pageTitle = 'SOPs & Checklists'; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0"><i class="bi bi-file-text me-2"></i><?= $pageTitle ?></h5>
    <div>
        <?php if (canCreate('sops')): ?>
        <a href="?route=sops/template/form" class="btn btn-success btn-sm"><i class="bi bi-plus-lg"></i> New Template</a>
        <a href="?route=sops/checklist/form" class="btn btn-outline-primary btn-sm"><i class="bi bi-plus-lg"></i> New Checklist</a>
        <?php endif; ?>
    </div>
</div>

<ul class="nav nav-tabs mb-3" id="sopTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="templates-tab" data-bs-toggle="tab" data-bs-target="#templates" type="button"><i class="bi bi-journal me-1"></i>Templates</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="checklists-tab" data-bs-toggle="tab" data-bs-target="#checklists" type="button"><i class="bi bi-check2-square me-1"></i>Checklists</button>
    </li>
</ul>

<div class="tab-content">
    <div class="tab-pane fade show active" id="templates">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <table id="dataTable" class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr><th>ID</th><th>Title</th><th>Department</th><th>Version</th><th>Effective</th><th>Review</th><th>Status</th><th>Created By</th><th>Actions</th></tr>
                    </thead>
                    <tbody>
                        <?php if (isset($templates)): foreach ($templates as $t): ?>
                        <tr>
                            <td><?= sanitize($t['SOP_ID'] ?? '') ?></td>
                            <td class="fw-semibold"><?= sanitize($t['Title'] ?? '') ?></td>
                            <td><?= sanitize($t['Department'] ?? '') ?></td>
                            <td><span class="badge bg-secondary">v<?= sanitize($t['Version'] ?? '1.0') ?></span></td>
                            <td><?= sanitize($t['EffectiveDate'] ?? '') ?></td>
                            <td><?= sanitize($t['ReviewDate'] ?? '') ?></td>
                            <td>
                                <?php $st = $t['Status'] ?? ''; $map = ['Active'=>'success','Under Review'=>'warning','Archived'=>'secondary']; ?>
                                <span class="badge bg-<?= $map[$st] ?? 'secondary' ?>"><?= sanitize($st) ?></span>
                            </td>
                            <td><?= sanitize($t['CreatedByName'] ?? $t['created_by_name'] ?? '') ?></td>
                            <td>
                                <a href="?route=sops/template/edit&id=<?= urlencode($t['SOP_ID'] ?? '') ?>" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a>
                                <a href="?route=sops/template/delete&id=<?= urlencode($t['SOP_ID'] ?? '') ?>" class="btn btn-sm btn-outline-danger" title="Delete" onclick="return confirm('Delete this template?')"><i class="bi bi-trash"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="tab-pane fade" id="checklists">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <table id="checklistDataTable" class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr><th>ID</th><th>SOP</th><th>Batch</th><th>Date</th><th>Items</th><th>Completed</th><th>Supervisor</th><th>Status</th><th>Actions</th></tr>
                    </thead>
                    <tbody>
                        <?php if (isset($checklists)): foreach ($checklists as $cl): ?>
                        <tr>
                            <td><?= sanitize($cl['ChecklistID'] ?? '') ?></td>
                            <td><?= sanitize($cl['SOPTitle'] ?? $cl['sop_title'] ?? '') ?></td>
                            <td><?= sanitize($cl['BatchNumber'] ?? $cl['batch_number'] ?? '') ?></td>
                            <td><?= sanitize($cl['Date'] ?? '') ?></td>
                            <td><?= (int)($cl['TotalItems'] ?? 0) ?></td>
                            <td><?= (int)($cl['CompletedItems'] ?? 0) ?></td>
                            <td><?= sanitize($cl['SupervisorName'] ?? $cl['supervisor_name'] ?? '') ?></td>
                            <td>
                                <?php $as = $cl['ApprovalStatus'] ?? ''; $map = ['Approved'=>'success','Pending'=>'warning','Rejected'=>'danger']; ?>
                                <span class="badge bg-<?= $map[$as] ?? 'secondary' ?>"><?= sanitize($as) ?></span>
                            </td>
                            <td>
                                <a href="?route=sops/checklist/edit&id=<?= urlencode($cl['ChecklistID'] ?? '') ?>" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a>
                                <a href="?route=sops/checklist/delete&id=<?= urlencode($cl['ChecklistID'] ?? '') ?>" class="btn btn-sm btn-outline-danger" title="Delete" onclick="return confirm('Delete this checklist?')"><i class="bi bi-trash"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof $.fn.DataTable !== 'undefined') {
        $('#checklistDataTable').DataTable({ order: [], pageLength: 25, language: { search: '', searchPlaceholder: 'Search...' }, responsive: true });
    }
});
</script>
