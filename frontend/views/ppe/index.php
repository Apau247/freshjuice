<?php $pageTitle = 'PPE Tracking'; ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4><i class="fas fa-hard-hat me-2"></i>PPE Tracking</h4>
        <?php if (canCreate('ppe')): ?>
        <a href="?route=ppe/create" class="btn btn-primary"><i class="fas fa-plus me-1"></i>Issue PPE</a>
        <?php endif; ?>
    </div>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-bg-warning">
                <div class="card-body text-center">
                    <h5><?php echo sanitize($replacementNeededCount ?? 0); ?></h5>
                    <small>Replacement Needed</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-bg-danger">
                <div class="card-body text-center">
                    <h5><?php echo sanitize($expiringSoonCount ?? 0); ?></h5>
                    <small>Expiring Soon</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-bg-success">
                <div class="card-body text-center">
                    <h5><?php echo sanitize($activeCount ?? 0); ?></h5>
                    <small>Active PPE</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-bg-info">
                <div class="card-body text-center">
                    <h5><?php echo sanitize($totalCount ?? 0); ?></h5>
                    <small>Total Items</small>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <table id="dataTable" class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>PPE_ID</th>
                        <th>Staff</th>
                        <th>Equipment</th>
                        <th>Date Issued</th>
                        <th>Expiry</th>
                        <th>Condition</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($ppeItems)): ?>
                        <?php foreach ($ppeItems as $row): ?>
                            <tr>
                                <td><?php echo sanitize($row['PPE_ID']); ?></td>
                                <td><?php echo sanitize($row['StaffName']); ?></td>
                                <td><?php echo sanitize($row['PPESource']); ?></td>
                                <td><?php echo sanitize($row['DateIssued']); ?></td>
                                <td><?php echo sanitize($row['ExpiryDate']); ?></td>
                                <td>
                                    <?php
                                    $condition = sanitize($row['Condition']);
                                    $cBadge = 'secondary';
                                    if ($condition === 'New' || $condition === 'Good') $cBadge = 'success';
                                    elseif ($condition === 'Fair') $cBadge = 'warning';
                                    elseif ($condition === 'Poor' || $condition === 'Damaged') $cBadge = 'danger';
                                    ?>
                                    <span class="badge bg-<?php echo $cBadge; ?>"><?php echo $condition; ?></span>
                                </td>
                                <td>
                                    <?php if (canEdit('ppe')): ?>
                                    <a href="?route=ppe/edit&id=<?php echo sanitize($row['PPE_ID']); ?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                                    <a href="?route=ppe/delete&id=<?php echo sanitize($row['PPE_ID']); ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this PPE record?')"><i class="fas fa-trash"></i></a>
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
