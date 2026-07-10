<?php $pageTitle = 'Emergency Drills'; ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4><i class="fas fa-fire-extinguisher me-2"></i>Emergency Drills</h4>
        <a href="?route=safety/drill_form" class="btn btn-primary"><i class="fas fa-plus me-1"></i>Schedule Drill</a>
    </div>

    <div class="card">
        <div class="card-body">
            <table id="dataTable" class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>DrillID</th>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Location</th>
                        <th>Participants</th>
                        <th>Outcome</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($drills)): ?>
                        <?php foreach ($drills as $row): ?>
                            <tr>
                                <td><?php echo sanitize($row['DrillID']); ?></td>
                                <td><?php echo sanitize($row['DrillDate']); ?></td>
                                <td><?php echo sanitize($row['DrillType']); ?></td>
                                <td><?php echo sanitize($row['Location']); ?></td>
                                <td><?php echo sanitize($row['ParticipantsCount']); ?></td>
                                <td><?php echo sanitize($row['Outcome']); ?></td>
                                <td>
                                    <?php
                                    $status = sanitize($row['Status']);
                                    $statusBadge = 'secondary';
                                    if ($status === 'Scheduled') $statusBadge = 'info';
                                    elseif ($status === 'Completed') $statusBadge = 'success';
                                    elseif ($status === 'Cancelled') $statusBadge = 'danger';
                                    ?>
                                    <span class="badge bg-<?php echo $statusBadge; ?>"><?php echo $status; ?></span>
                                </td>
                                <td>
                                    <a href="?route=safety/drill_form&id=<?php echo sanitize($row['DrillID']); ?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                                    <a href="?route=safety/drill_delete&id=<?php echo sanitize($row['DrillID']); ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this drill record?')"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
