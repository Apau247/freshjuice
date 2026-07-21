<?php $pageTitle = 'Training Records'; ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4><i class="fas fa-graduation-cap me-2"></i>Training Records</h4>
        <?php if (canCreate('training')): ?>
        <a href="?route=training/create" class="btn btn-primary"><i class="fas fa-plus me-1"></i>Add Training</a>
        <?php endif; ?>
    </div>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-bg-success">
                <div class="card-body text-center">
                    <h5><?php echo sanitize($completedCount ?? 0); ?></h5>
                    <small>Completed</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-bg-info">
                <div class="card-body text-center">
                    <h5><?php echo sanitize($scheduledCount ?? 0); ?></h5>
                    <small>Scheduled</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-bg-warning">
                <div class="card-body text-center">
                    <h5><?php echo sanitize($expiringCertCount ?? 0); ?></h5>
                    <small>Expiring Certs</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-bg-secondary">
                <div class="card-body text-center">
                    <h5><?php echo sanitize($totalCount ?? 0); ?></h5>
                    <small>Total Records</small>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <table id="dataTable" class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>TrainingID</th>
                        <th>Staff</th>
                        <th>Type</th>
                        <th>Date</th>
                        <th>Trainer</th>
                        <th>Certificate</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($trainings)): ?>
                        <?php foreach ($trainings as $row): ?>
                            <tr>
                                <td><?php echo sanitize($row['TrainingID']); ?></td>
                                <td><?php echo sanitize($row['StaffName']); ?></td>
                                <td><?php echo sanitize($row['TrainingType']); ?></td>
                                <td><?php echo sanitize($row['TrainingDate']); ?></td>
                                <td><?php echo sanitize($row['Trainer']); ?></td>
                                <td><?php echo sanitize($row['CertificateNo']); ?></td>
                                <td>
                                    <?php
                                    $status = sanitize($row['Status']);
                                    $statusBadge = 'secondary';
                                    if ($status === 'Completed') $statusBadge = 'success';
                                    elseif ($status === 'Scheduled') $statusBadge = 'info';
                                    elseif ($status === 'In Progress') $statusBadge = 'warning';
                                    ?>
                                    <span class="badge bg-<?php echo $statusBadge; ?>"><?php echo $status; ?></span>
                                </td>
                                <td>
                                    <?php if (canEdit('training')): ?>
                                    <a href="?route=training/edit&id=<?php echo sanitize($row['TrainingID']); ?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                                    <a href="?route=training/delete&id=<?php echo sanitize($row['TrainingID']); ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this training record?')"><i class="fas fa-trash"></i></a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
