<?php $pageTitle = 'Factory Acceptance Testing'; ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4><i class="fas fa-flask me-2"></i>Factory Acceptance Testing</h4>
        <a href="?route=fat/create" class="btn btn-primary"><i class="fas fa-plus me-1"></i>New FAT Record</a>
    </div>

    <div class="card">
        <div class="card-body">
            <table id="dataTable" class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>FAT_ID</th>
                        <th>Machine</th>
                        <th>Test Date</th>
                        <th>Test Type</th>
                        <th>Result</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($records)): ?>
                        <?php foreach ($records as $row): ?>
                            <tr>
                                <td><?php echo sanitize($row['FAT_ID']); ?></td>
                                <td><?php echo sanitize($row['MachineName']); ?></td>
                                <td><?php echo sanitize($row['TestDate']); ?></td>
                                <td><?php echo sanitize($row['TestType']); ?></td>
                                <td>
                                    <?php
                                    $result = sanitize($row['Result']);
                                    $rBadge = 'secondary';
                                    if ($result === 'Pass') $rBadge = 'success';
                                    elseif ($result === 'Fail') $rBadge = 'danger';
                                    elseif ($result === 'Conditional') $rBadge = 'warning';
                                    ?>
                                    <span class="badge bg-<?php echo $rBadge; ?>"><?php echo $result; ?></span>
                                </td>
                                <td>
                                    <?php
                                    $status = sanitize($row['Status']);
                                    $sBadge = 'secondary';
                                    if ($status === 'Scheduled') $sBadge = 'info';
                                    elseif ($status === 'In Progress') $sBadge = 'warning';
                                    elseif ($status === 'Completed') $sBadge = 'success';
                                    ?>
                                    <span class="badge bg-<?php echo $sBadge; ?>"><?php echo $status; ?></span>
                                </td>
                                <td>
                                    <a href="?route=fat/edit&id=<?php echo sanitize($row['FAT_ID']); ?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                                    <a href="?route=fat/delete&id=<?php echo sanitize($row['FAT_ID']); ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this FAT record?')"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
