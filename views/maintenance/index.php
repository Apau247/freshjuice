<?php $pageTitle = 'Maintenance Records'; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0"><i class="bi bi-wrench me-2"></i><?= $pageTitle ?></h5>
    <a href="?route=maintenance/create" class="btn btn-success btn-sm"><i class="bi bi-plus-lg"></i> New Record</a>
</div>
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <table id="dataTable" class="table table-hover align-middle">
            <thead class="table-light">
                <tr><th>ID</th><th>Machine</th><th>Type</th><th>Date</th><th>Cost</th><th>Downtime</th><th>Technician</th><th>Status</th><th>Next Service</th><th>Actions</th></tr>
            </thead>
            <tbody>
                <?php foreach ($records as $r): ?>
                <tr>
                    <td><?= sanitize($r['MaintenanceID']) ?></td>
                    <td class="fw-semibold"><?= sanitize($r['MachineName'] ?? $r['machine_name'] ?? '') ?></td>
                    <td><span class="badge bg-info bg-opacity-10 text-info"><?= sanitize($r['MaintenanceType'] ?? $r['maintenance_type'] ?? '') ?></span></td>
                    <td><?= sanitize($r['MaintenanceDate'] ?? $r['maintenance_date'] ?? '') ?></td>
                    <td>$<?= number_format((float)($r['Cost'] ?? 0), 2) ?></td>
                    <td><?= number_format((float)($r['Downtime'] ?? 0), 1) ?> hrs</td>
                    <td><?= sanitize($r['TechnicianName'] ?? $r['technician_name'] ?? '') ?></td>
                    <td>
                        <?php $st = $r['Status'] ?? ''; $map = ['Scheduled'=>'secondary','In Progress'=>'info','Completed'=>'success','Cancelled'=>'danger']; ?>
                        <span class="badge bg-<?= $map[$st] ?? 'secondary' ?>"><?= sanitize($st) ?></span>
                    </td>
                    <td><?= sanitize($r['NextServiceDate'] ?? '') ?></td>
                    <td>
                        <a href="?route=maintenance/edit&id=<?= urlencode($r['MaintenanceID']) ?>" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a>
                        <a href="?route=maintenance/delete&id=<?= urlencode($r['MaintenanceID']) ?>" class="btn btn-sm btn-outline-danger" title="Delete" onclick="return confirm('Delete this record?')"><i class="bi bi-trash"></i></a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
