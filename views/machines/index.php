<?php $pageTitle = 'Machines'; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0"><i class="bi bi-cpu me-2"></i><?= $pageTitle ?></h5>
    <a href="?route=machines/form" class="btn btn-success btn-sm"><i class="bi bi-plus-lg"></i> New Machine</a>
</div>
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <table id="dataTable" class="table table-hover align-middle">
            <thead class="table-light"><tr><th>ID</th><th>Name</th><th>Type</th><th>Location</th><th>Install Date</th><th>Last Service</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
                <?php foreach ($machines as $m): ?>
                <tr>
                    <td><?= sanitize($m['MachineID']) ?></td>
                    <td class="fw-semibold"><?= sanitize($m['Name']) ?></td>
                    <td><?= sanitize($m['Type'] ?? '') ?></td>
                    <td><?= sanitize($m['Location'] ?? '') ?></td>
                    <td><?= sanitize($m['InstallDate'] ?? '') ?></td>
                    <td><?= sanitize($m['LastService'] ?? '') ?></td>
                    <td>
                        <?php $st = $m['Status'] ?? ''; $map = ['Operational'=>'success','Maintenance'=>'warning','Down'=>'danger','Decommissioned'=>'secondary']; ?>
                        <span class="badge bg-<?= $map[$st] ?? 'secondary' ?>"><?= sanitize($st) ?></span>
                    </td>
                    <td>
                        <a href="?route=machines/edit&id=<?= urlencode($m['MachineID']) ?>" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a>
                        <a href="?route=machines/delete&id=<?= urlencode($m['MachineID']) ?>" class="btn btn-sm btn-outline-danger" title="Delete" onclick="return confirm('Delete this machine?')"><i class="bi bi-trash"></i></a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
