<?php $pageTitle = 'Hazard Register'; ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4><i class="fas fa-exclamation-triangle me-2"></i>Hazard Register</h4>
        <a href="?route=safety/hazard_form" class="btn btn-primary"><i class="fas fa-plus me-1"></i>Add Hazard</a>
    </div>

    <?php if (!empty($highRiskItems)): ?>
    <div class="card border-danger mb-4">
        <div class="card-header bg-danger text-white">
            <i class="fas fa-radiation me-1"></i>HIGH RISK Hazards - Immediate Attention Required
        </div>
        <div class="card-body p-0">
            <table class="table table-striped table-hover mb-0">
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
                    <?php foreach ($highRiskItems as $row): ?>
                        <tr>
                            <td><?php echo sanitize($row['HazardID']); ?></td>
                            <td><?php echo sanitize($row['HazardDescription']); ?></td>
                            <td><?php echo sanitize($row['RiskCategory']); ?></td>
                            <td><?php echo sanitize($row['Likelihood']); ?></td>
                            <td><?php echo sanitize($row['Consequence']); ?></td>
                            <td><span class="badge bg-danger"><?php echo sanitize($row['RiskRating']); ?></span></td>
                            <td><span class="badge bg-danger"><?php echo sanitize($row['Status']); ?></span></td>
                            <td>
                                <a href="?route=safety/hazard_form&id=<?php echo sanitize($row['HazardID']); ?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header">All Hazards</div>
        <div class="card-body">
            <table id="dataTable" class="table table-striped table-hover">
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
                                    <a href="?route=safety/hazard_form&id=<?php echo sanitize($row['HazardID']); ?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                                    <a href="?route=safety/hazard_delete&id=<?php echo sanitize($row['HazardID']); ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this hazard?')"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
