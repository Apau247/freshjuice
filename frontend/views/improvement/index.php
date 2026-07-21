<?php $pageTitle = 'CAPA / Continuous Improvement'; ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4><i class="fas fa-lightbulb me-2"></i>CAPA / Continuous Improvement</h4>
        <?php if (canCreate('improvement')): ?>
        <a href="?route=improvement/create" class="btn btn-primary"><i class="fas fa-plus me-1"></i>New Initiative</a>
        <?php endif; ?>
    </div>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-bg-info">
                <div class="card-body text-center">
                    <h5><?php echo sanitize($proposedCount ?? 0); ?></h5>
                    <small>Proposed</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-bg-warning">
                <div class="card-body text-center">
                    <h5><?php echo sanitize($inProgressCount ?? 0); ?></h5>
                    <small>In Progress</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-bg-success">
                <div class="card-body text-center">
                    <h5><?php echo sanitize($completedCount ?? 0); ?></h5>
                    <small>Completed</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-bg-danger">
                <div class="card-body text-center">
                    <h5><?php echo sanitize($overdueCount ?? 0); ?></h5>
                    <small>Overdue</small>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <table id="dataTable" class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>InitiativeID</th>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Target Date</th>
                        <th>Responsible</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($initiatives)): ?>
                        <?php foreach ($initiatives as $row): ?>
                            <tr>
                                <td><?php echo sanitize($row['InitiativeID']); ?></td>
                                <td><?php echo sanitize($row['Title']); ?></td>
                                <td><?php echo sanitize($row['Category']); ?></td>
                                <td><?php echo sanitize($row['TargetDate']); ?></td>
                                <td><?php echo sanitize($row['ResponsiblePerson']); ?></td>
                                <td>
                                    <?php
                                    $status = sanitize($row['Status']);
                                    $statusBadge = 'secondary';
                                    if ($status === 'Proposed') $statusBadge = 'info';
                                    elseif ($status === 'In Progress') $statusBadge = 'warning';
                                    elseif ($status === 'Completed') $statusBadge = 'success';
                                    elseif ($status === 'Overdue') $statusBadge = 'danger';
                                    ?>
                                    <span class="badge bg-<?php echo $statusBadge; ?>"><?php echo $status; ?></span>
                                </td>
                                <td>
                                    <?php if (canEdit('improvement')): ?>
                                    <a href="?route=improvement/edit&id=<?php echo sanitize($row['InitiativeID']); ?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                                    <a href="?route=improvement/delete&id=<?php echo sanitize($row['InitiativeID']); ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this initiative?')"><i class="fas fa-trash"></i></a>
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
