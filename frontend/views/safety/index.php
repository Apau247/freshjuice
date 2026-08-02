<?php $pageTitle = 'Safety Inspections'; ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4><i class="bi bi-shield-check me-2"></i>Safety Inspections</h4>
        <?php if (canCreate('safety')): ?>
        <a href="?route=safety/create" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>New Inspection</a>
        <?php endif; ?>
    </div>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-bg-danger">
                <div class="card-body text-center">
                    <h5><?php echo sanitize($statusCounts['Open'] ?? 0); ?></h5>
                    <small>Open</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-bg-warning">
                <div class="card-body text-center">
                    <h5><?php echo sanitize($statusCounts['InProgress'] ?? 0); ?></h5>
                    <small>In Progress</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-bg-success">
                <div class="card-body text-center">
                    <h5><?php echo sanitize($statusCounts['Closed'] ?? 0); ?></h5>
                    <small>Closed</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-bg-info">
                <div class="card-body text-center">
                    <h5><?php echo sanitize(($statusCounts['Open'] ?? 0) + ($statusCounts['InProgress'] ?? 0) + ($statusCounts['Closed'] ?? 0)); ?></h5>
                    <small>Total Inspections</small>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <table id="dataTable" class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>SafetyID</th>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Area</th>
                        <th>Hazard Level</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($inspections)): ?>
                        <?php foreach ($inspections as $row): ?>
                            <tr>
                                <td><?php echo sanitize($row['SafetyID']); ?></td>
                                <td><?php echo sanitize($row['InspectionDate']); ?></td>
                                <td><?php echo sanitize($row['InspectionType']); ?></td>
                                <td><?php echo sanitize($row['Area']); ?></td>
                                <td>
                                    <?php
                                    $hazardLevel = sanitize($row['HazardLevel']);
                                    $badgeClass = 'secondary';
                                    if ($hazardLevel === 'High') $badgeClass = 'danger';
                                    elseif ($hazardLevel === 'Medium') $badgeClass = 'warning';
                                    elseif ($hazardLevel === 'Low') $badgeClass = 'success';
                                    ?>
                                    <span class="badge bg-<?php echo $badgeClass; ?>"><?php echo $hazardLevel; ?></span>
                                </td>
                                <td>
                                    <?php
                                    $status = sanitize($row['Status']);
                                    $statusBadge = 'secondary';
                                    if ($status === 'Open') $statusBadge = 'danger';
                                    elseif ($status === 'In Progress') $statusBadge = 'warning';
                                    elseif ($status === 'Closed') $statusBadge = 'success';
                                    ?>
                                    <span class="badge bg-<?php echo $statusBadge; ?>"><?php echo $status; ?></span>
                                </td>
                                <td>
                                    <?php if (canEdit('safety')): ?>
                                    <a href="?route=safety/edit&id=<?php echo sanitize($row['SafetyID']); ?>" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></a>
                                    <a href="?route=safety/delete&id=<?php echo sanitize($row['SafetyID']); ?>" class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></a>
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
