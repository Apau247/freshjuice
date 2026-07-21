<?php $pageTitle = 'Supplier Evaluations'; ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4><i class="fas fa-star me-2"></i>Supplier Evaluations</h4>
        <a href="?route=supplier-evaluations/create" class="btn btn-primary"><i class="fas fa-plus me-1"></i>New Evaluation</a>
    </div>

    <div class="card">
        <div class="card-body">
            <table id="dataTable" class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>EvaluationID</th>
                        <th>Supplier</th>
                        <th>Date</th>
                        <th>Quality</th>
                        <th>Delivery</th>
                        <th>Price</th>
                        <th>Overall</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($evaluations)): ?>
                        <?php foreach ($evaluations as $row): ?>
                            <?php $overall = isset($row['OverallScore']) ? $row['OverallScore'] : 0; ?>
                            <tr>
                                <td><?php echo sanitize($row['EvaluationID']); ?></td>
                                <td><?php echo sanitize($row['SupplierName']); ?></td>
                                <td><?php echo sanitize($row['EvaluationDate']); ?></td>
                                <td><?php echo sanitize($row['QualityScore']); ?>/5</td>
                                <td><?php echo sanitize($row['DeliveryScore']); ?>/5</td>
                                <td><?php echo sanitize($row['PriceScore']); ?>/5</td>
                                <td>
                                    <?php
                                    $overallBadge = 'secondary';
                                    if ($overall >= 4) $overallBadge = 'success';
                                    elseif ($overall >= 3) $overallBadge = 'info';
                                    elseif ($overall >= 2) $overallBadge = 'warning';
                                    else $overallBadge = 'danger';
                                    ?>
                                    <span class="badge bg-<?php echo $overallBadge; ?>"><?php echo number_format($overall, 1); ?>/5</span>
                                </td>
                                <td>
                                    <a href="?route=supplier-evaluations/edit&id=<?php echo sanitize($row['EvaluationID']); ?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                                    <a href="?route=supplier-evaluations/delete&id=<?php echo sanitize($row['EvaluationID']); ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this evaluation?')"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
