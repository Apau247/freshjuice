<?php $assetBase = appBaseUrl(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= sanitize($printTitle ?? 'Document') ?> - <?= APP_NAME ?></title>
    <link href="<?= $assetBase ?>/frontend/assets/vendor/fonts/inter.css" rel="stylesheet">
    <link href="<?= $assetBase ?>/frontend/assets/vendor/bootstrap/bootstrap.min.css" rel="stylesheet">
</head>
<body style="background:#fff;color:#111827;font-family:'Inter',system-ui,sans-serif;">

<div class="container py-4" id="print-document">

    <!-- Company letterhead -->
    <div class="doc-letterhead" style="border-bottom:2px solid #16a34a;padding-bottom:12px;margin-bottom:12px;">
        <div class="d-flex justify-content-between align-items-start">
            <div class="d-flex align-items-center gap-2">
                <img src="<?= $assetBase ?>/frontend/assets/img/pf-logo.jpg" alt="PF logo" style="height:52px;width:auto;border-radius:6px;">
                <div>
                    <div class="fw-bolder" style="font-size:1.2rem;line-height:1.2;color:#16a34a;"><?= sanitize(APP_NAME) ?></div>
                    <div style="font-size:0.72rem;color:#d97706;font-weight:600;font-style:italic;"><?= sanitize(APP_TAGLINE) ?></div>
                </div>
            </div>
            <div class="text-end text-muted" style="font-size:0.72rem;line-height:1.6;">
                <div><?= sanitize(APP_ADDRESS) ?></div>
                <div>Tel: <?= sanitize(APP_PHONE) ?></div>
                <div>Email: <?= sanitize(APP_EMAIL) ?></div>
            </div>
        </div>
        <?php if (defined('APP_BANK') && APP_BANK !== ''): ?>
        <div style="font-size:0.65rem;color:#64748b;margin-top:6px;padding-top:6px;border-top:1px dashed #e2e8f0;">
            <strong style="color:#16a34a;">BANK:</strong> <?= sanitize(APP_BANK) ?>
        </div>
        <?php endif; ?>
    </div>

    <!-- Document title -->
    <?php if (!empty($printTitle)): ?>
    <h1 class="text-center fw-bold mt-3 mb-1" style="font-size:1.25rem;text-transform:uppercase;letter-spacing:.06em;">
        <?= sanitize($printTitle) ?>
    </h1>
    <?php endif; ?>

    <!-- Document body: the results and nothing else -->
    <?= $printContent ?>

    <div class="row mt-5 pt-4 doc-signatures" style="font-size:0.78rem;">
        <div class="col-4 text-center">
            <div style="border-top:1px solid #374151;" class="mx-2 pt-1">Prepared By / Signature</div>
        </div>
        <div class="col-4 text-center">
            <div style="border-top:1px solid #374151;" class="mx-2 pt-1">Reviewed By / Signature</div>
        </div>
        <div class="col-4 text-center">
            <div style="border-top:1px solid #374151;" class="mx-2 pt-1">Issuer / Accountant Stamp &amp; Signature</div>
        </div>
    </div>

    <div class="text-center text-muted mt-4 pt-2 border-top" style="font-size:0.68rem;">
        Printed on <?= date('d M Y \a\t H:i') ?>
        &middot; <?= sanitize(currentUser()['name'] ?? 'System') ?>
        &middot; <?= sanitize(APP_NAME) ?> &middot; <?= sanitize(APP_PHONE) ?>
    </div>
</div>

<style>
    body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    #print-document { max-width: 900px; }
    .doc-table { width:100%; border-collapse:collapse; font-size:0.82rem; }
    .doc-table th, .doc-table td { border:1px solid #9ca3af; padding:5px 8px; text-align:left; vertical-align:top; }
    .doc-table thead th { background:#f3f4f6; font-weight:700; }
    .doc-summary { width:100%; border-collapse:collapse; font-size:0.82rem; margin-bottom:14px; }
    .doc-summary th, .doc-summary td { border:1px solid #9ca3af; padding:5px 8px; }

    /* On-screen toolbar: never printed */
    .doc-toolbar { position:fixed; top:12px; right:12px; display:flex; gap:8px; z-index:10; }

    @media print {
        .doc-toolbar, .no-print { display:none !important; }
        .container { max-width:100% !important; padding:0 !important; }
        @page { margin: 12mm; }
    }
</style>

<script>
    // Print immediately once everything (incl. web fonts) has loaded.
    window.addEventListener('load', function () {
        setTimeout(function () { window.print(); }, 350);
    });
</script>
<script src="<?= $assetBase ?>/frontend/assets/vendor/jquery/jquery-3.6.0.min.js"></script>
<script src="<?= $assetBase ?>/frontend/assets/vendor/bootstrap/bootstrap.bundle.min.js"></script>
<div class="doc-toolbar no-print">
    <button type="button" class="btn btn-sm btn-outline-secondary bg-white" onclick="window.print()"><i class="bi bi-printer"></i> Print again</button>
    <button type="button" class="btn btn-sm btn-success" onclick="window.close(); if(!window.closed){ history.back(); }"><i class="bi bi-x-lg"></i> Close</button>
</div>
<link rel="stylesheet" href="<?= $assetBase ?>/frontend/assets/vendor/icons/bootstrap-icons.css">
</body>
</html>
