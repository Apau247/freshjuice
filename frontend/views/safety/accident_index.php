<?php $pageTitle = 'Accident / Incident Reports'; ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4><i class="bi bi-activity me-2"></i>Accident / Incident Reports</h4>
        <?php if (canCreate('accidents')): ?>
        <a href="?route=safety/accidents/create" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Report Incident</a>
        <?php endif; ?>
    </div>

    <div class="card">
        <div class="card-body">
            <table id="dataTable" class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>AccidentID</th>
                        <th>Date</th>
                        <th>Location</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Reported By</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($accidents)): ?>
                        <?php foreach ($accidents as $row): ?>
                            <tr>
                                <td><?php echo sanitize($row['AccidentID']); ?></td>
                                <td><?php echo sanitize($row['IncidentDate']); ?></td>
                                <td><?php echo sanitize($row['Location']); ?></td>
                                <td><?php echo sanitize($row['IncidentType']); ?></td>
                                <td>
                                    <?php
                                    $status = sanitize($row['Status']);
                                    $statusBadge = 'secondary';
                                    if ($status === 'Reported') $statusBadge = 'danger';
                                    elseif ($status === 'Under Investigation') $statusBadge = 'warning';
                                    elseif ($status === 'Closed') $statusBadge = 'success';
                                    ?>
                                    <span class="badge bg-<?php echo $statusBadge; ?>"><?php echo $status; ?></span>
                                </td>
                                <td><?php echo sanitize($row['ReportedByName'] ?? $row['ReportedBy']); ?></td>
                                <td>
                                    <?php if (canEdit('accidents')): ?>
                                    <a href="?route=safety/accidents/edit&id=<?php echo sanitize($row['AccidentID']); ?>" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></a>
                                    <a href="?route=safety/accidents/delete&id=<?php echo sanitize($row['AccidentID']); ?>" class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></a>
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
