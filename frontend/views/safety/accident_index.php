<?php $pageTitle = 'Accident / Incident Reports'; ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4><i class="fas fa-ambulance me-2"></i>Accident / Incident Reports</h4>
        <a href="?route=safety/accidents/create" class="btn btn-primary"><i class="fas fa-plus me-1"></i>Report Incident</a>
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
                                <td><?php echo sanitize($row['ReportedBy']); ?></td>
                                <td>
                                    <a href="?route=safety/accidents/edit&id=<?php echo sanitize($row['AccidentID']); ?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                                    <a href="?route=safety/accidents/delete&id=<?php echo sanitize($row['AccidentID']); ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this report?')"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
