<?php $pageTitle = 'Hazard Register'; ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h4><i class="bi bi-exclamation-triangle me-2"></i>Hazard Register</h4>
        <?php if (canCreate('hazards')): ?>
        <a href="?route=safety/hazards/create" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Add Hazard</a>
        <?php endif; ?>
    </div>

    <?php if (!empty($highRisk)): ?>
    <div class="card border-danger mb-4">
        <div class="card-header bg-danger text-white">
            <i class="bi bi-exclamation-octagon me-1"></i>HIGH RISK Hazards - Immediate Attention Required
        </div>
        <div class="card-body p-0">
            <div class="table-responsive"><table class="table table-striped table-hover mb-0">
                <thead class="table-danger">
                    <tr>
                        <th>HazardID</th>
                        <th>Description</th>
                        <th>Risk Category</th>
                        <th>Likelihood</th>
                        <th>Consequence</th>
                        <th>Risk Rating</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($highRisk as $row): ?>
                        <tr>
                            <td><?php echo sanitize($row['HazardID']); ?></td>
                            <td><?php echo sanitize($row['HazardDescription']); ?></td>
                            <td><?php echo sanitize($row['RiskCategory']); ?></td>
                            <td><?php echo sanitize($row['Likelihood']); ?></td>
                            <td><?php echo sanitize($row['Consequence']); ?></td>
                            <td><span class="badge bg-danger"><?php echo sanitize($row['RiskRating']); ?></span></td>
                            <td><span class="badge bg-danger"><?php echo sanitize($row['Status']); ?></span></td>
                            <td>
                                <?php if (canEdit('hazards')): ?>
                                <a href="?route=safety/hazards/edit&id=<?php echo sanitize($row['HazardID']); ?>" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table></div>
        </div>
    </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header">All Hazards</div>
        <div class="card-body">
            <div class="table-responsive"><table id="dataTable" class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>HazardID</th>
                        <th>Description</th>
                        <th>Risk Category</th>
                        <th>Likelihood</th>
                        <th>Consequence</th>
                        <th>Risk Rating</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($hazards)): ?>
                        <?php foreach ($hazards as $row): ?>
                            <tr>
                                <td><?php echo sanitize($row['HazardID']); ?></td>
                                <td><?php echo sanitize($row['HazardDescription']); ?></td>
                                <td><?php echo sanitize($row['RiskCategory']); ?></td>
                                <td><?php echo sanitize($row['Likelihood']); ?></td>
                                <td><?php echo sanitize($row['Consequence']); ?></td>
                                <td>
                                    <?php
                                    $rating = sanitize($row['RiskRating']);
                                    $rBadge = 'success';
                                    if ($rating >= 15) $rBadge = 'danger';
                                    elseif ($rating >= 8) $rBadge = 'warning';
                                    ?>
                                    <span class="badge bg-<?php echo $rBadge; ?>"><?php echo $rating; ?></span>
                                </td>
                                <td>
                                    <?php
                                    $status = sanitize($row['Status']);
                                    $sBadge = 'secondary';
                                    if ($status === 'Active') $sBadge = 'danger';
                                    elseif ($status === 'Mitigated') $sBadge = 'warning';
                                    elseif ($status === 'Closed') $sBadge = 'success';
                                    ?>
                                    <span class="badge bg-<?php echo $sBadge; ?>"><?php echo $status; ?></span>
                                </td>
                                <td>
                                    <?php if (canEdit('hazards')): ?>
                                    <a href="?route=safety/hazards/edit&id=<?php echo sanitize($row['HazardID']); ?>" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></a>
                                    <a href="?route=safety/hazards/delete&id=<?php echo sanitize($row['HazardID']); ?>" class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table></div>
        </div>
    </div>
</div>
