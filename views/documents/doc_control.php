<?php $pageTitle = 'Document Control Panel'; ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4><i class="fas fa-folder-open me-2"></i>Document Control Panel</h4>
        <a href="?route=documents/create" class="btn btn-primary"><i class="fas fa-plus me-1"></i>Add Document</a>
    </div>

    <div class="row mb-4">
        <div class="col-md-2">
            <div class="card text-bg-info">
                <div class="card-body text-center">
                    <h4><?php echo sanitize($totalCount ?? 0); ?></h4>
                    <small>Total</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-bg-secondary">
                <div class="card-body text-center">
                    <h4><?php echo sanitize($draftCount ?? 0); ?></h4>
                    <small>Draft</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-bg-warning">
                <div class="card-body text-center">
                    <h4><?php echo sanitize($underReviewCount ?? 0); ?></h4>
                    <small>Under Review</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-bg-success">
                <div class="card-body text-center">
                    <h4><?php echo sanitize($approvedCount ?? 0); ?></h4>
                    <small>Approved</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-bg-danger">
                <div class="card-body text-center">
                    <h4><?php echo sanitize($obsoleteCount ?? 0); ?></h4>
                    <small>Obsolete</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-bg-primary">
                <div class="card-body text-center">
                    <h4><?php echo sanitize($dueForReviewCount ?? 0); ?></h4>
                    <small>Due for Review</small>
                </div>
            </div>
        </div>
    </div>

    <?php if (!empty($dueForReviewDocs)): ?>
    <div class="card border-warning mb-4">
        <div class="card-header bg-warning text-dark">
            <i class="fas fa-clock me-1"></i>Documents Due for Review
        </div>
        <div class="card-body p-0">
            <table class="table table-striped table-hover mb-0">
                <thead class="table-warning">
                    <tr>
                        <th>DocID</th>
                        <th>Title</th>
                        <th>Type</th>
                        <th>Version</th>
                        <th>Review Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($dueForReviewDocs as $row): ?>
                        <tr>
                            <td><?php echo sanitize($row['DocID']); ?></td>
                            <td><?php echo sanitize($row['Title']); ?></td>
                            <td><?php echo sanitize($row['DocType']); ?></td>
                            <td><?php echo sanitize($row['Version']); ?></td>
                            <td><?php echo sanitize($row['ReviewDate']); ?></td>
                            <td>
                                <a href="?route=documents/create&id=<?php echo sanitize($row['DocID']); ?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i> Review</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header">All Documents</div>
        <div class="card-body">
            <table id="dataTable" class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>DocID</th>
                        <th>Title</th>
                        <th>Type</th>
                        <th>Version</th>
                        <th>Department</th>
                        <th>Status</th>
                        <th>Effective Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($allDocuments)): ?>
                        <?php foreach ($allDocuments as $row): ?>
                            <tr>
                                <td><?php echo sanitize($row['DocID']); ?></td>
                                <td><?php echo sanitize($row['Title']); ?></td>
                                <td><?php echo sanitize($row['DocType']); ?></td>
                                <td><?php echo sanitize($row['Version']); ?></td>
                                <td><?php echo sanitize($row['Department']); ?></td>
                                <td>
                                    <?php
                                    $status = sanitize($row['Status']);
                                    $statusBadge = 'secondary';
                                    if ($status === 'Approved') $statusBadge = 'success';
                                    elseif ($status === 'Under Review') $statusBadge = 'warning';
                                    elseif ($status === 'Draft') $statusBadge = 'info';
                                    elseif ($status === 'Obsolete') $statusBadge = 'danger';
                                    ?>
                                    <span class="badge bg-<?php echo $statusBadge; ?>"><?php echo $status; ?></span>
                                </td>
                                <td><?php echo sanitize($row['EffectiveDate']); ?></td>
                                <td>
                                    <a href="?route=documents/create&id=<?php echo sanitize($row['DocID']); ?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                                    <a href="?route=documents/download&id=<?php echo sanitize($row['DocID']); ?>" class="btn btn-sm btn-info"><i class="fas fa-download"></i></a>
                                    <a href="?route=documents/delete&id=<?php echo sanitize($row['DocID']); ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this document?')"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
