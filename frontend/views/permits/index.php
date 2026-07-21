<?php $pageTitle = 'Permits & Licenses'; ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4><i class="fas fa-certificate me-2"></i>Permits & Licenses</h4>
        <?php if (canCreate('permits')): ?>
        <a href="?route=permits/create" class="btn btn-primary"><i class="fas fa-plus me-1"></i>Add Permit</a>
        <?php endif; ?>
    </div>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-bg-success">
                <div class="card-body text-center">
                    <h5><?php echo sanitize($activeCount ?? 0); ?></h5>
                    <small>Active</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-bg-warning">
                <div class="card-body text-center">
                    <h5><?php echo sanitize($expiringSoonCount ?? 0); ?></h5>
                    <small>Expiring Soon</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-bg-danger">
                <div class="card-body text-center">
                    <h5><?php echo sanitize($expiredCount ?? 0); ?></h5>
                    <small>Expired</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-bg-info">
                <div class="card-body text-center">
                    <h5><?php echo sanitize($totalCount ?? 0); ?></h5>
                    <small>Total Permits</small>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <table id="dataTable" class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>PermitID</th>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Number</th>
                        <th>Issue Date</th>
                        <th>Expiry Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($permits)): ?>
                        <?php foreach ($permits as $row): ?>
                            <tr>
                                <td><?php echo sanitize($row['PermitID']); ?></td>
                                <td><?php echo sanitize($row['PermitName']); ?></td>
                                <td><?php echo sanitize($row['PermitType']); ?></td>
                                <td><?php echo sanitize($row['PermitNumber']); ?></td>
                                <td><?php echo sanitize($row['IssueDate']); ?></td>
                                <td><?php echo sanitize($row['ExpiryDate']); ?></td>
                                <td>
                                    <?php
                                    $status = sanitize($row['Status']);
                                    $statusBadge = 'secondary';
                                    if ($status === 'Active') $statusBadge = 'success';
                                    elseif ($status === 'Expiring Soon') $statusBadge = 'warning';
                                    elseif ($status === 'Expired') $statusBadge = 'danger';
                                    ?>
                                    <span class="badge bg-<?php echo $statusBadge; ?>"><?php echo $status; ?></span>
                                </td>
                                <td>
                                    <?php if (canEdit('permits')): ?>
                                    <a href="?route=permits/edit&id=<?php echo sanitize($row['PermitID']); ?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                                    <a href="?route=permits/delete&id=<?php echo sanitize($row['PermitID']); ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this permit?')"><i class="fas fa-trash"></i></a>
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
