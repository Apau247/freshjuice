<?php $pageTitle = 'Messages — Sent'; ?>
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <h5 class="fw-bold mb-0"><i class="bi bi-send me-2"></i>Sent Messages</h5>
    <a href="?route=messages/compose" class="btn btn-sm btn-success"><i class="bi bi-pencil-square me-1"></i>Compose</a>
</div>

<ul class="nav nav-tabs mb-3" style="border-bottom:2px solid #e2e8f0;">
    <li class="nav-item">
        <a class="nav-link text-muted" href="?route=messages/inbox" style="font-size:.85rem;">
            <i class="bi bi-inbox me-1"></i>Inbox
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link active fw-semibold" href="?route=messages/sent" style="font-size:.85rem;">
            <i class="bi bi-send me-1"></i>Sent
        </a>
    </li>
</ul>

<div class="card border-0 shadow-sm" style="border-radius:12px !important;overflow:hidden;">
    <div class="card-body p-0">
        <?php if (empty($messages)): ?>
            <div class="text-center py-5 text-muted">
                <i class="bi bi-send" style="font-size:2.5rem;"></i>
                <p class="mt-2 mb-0">No messages sent yet.</p>
            </div>
        <?php else: ?>
            <?php foreach ($messages as $m): ?>
            <a href="?route=messages/view&id=<?= sanitize($m['MessageID']) ?>" class="d-flex align-items-start gap-3 px-3 py-3 border-bottom text-decoration-none" style="transition:background .15s;" onmouseover="this.style.background='#f0fdf4'" onmouseout="this.style.background=''">
                <div style="width:38px;height:38px;border-radius:10px;background:var(--gradient-cool);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:.8rem;flex-shrink:0;"><?= strtoupper(substr($m['ReceiverName'] ?? 'U', 0, 1)) ?></div>
                <div class="flex-grow-1 overflow-hidden">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-medium text-dark" style="font-size:.88rem;">To: <?= sanitize($m['ReceiverName']) ?></span>
                        <small class="text-muted" style="font-size:.7rem;"><?= date('M j, g:i A', strtotime($m['created_at'])) ?></small>
                    </div>
                    <div class="text-dark" style="font-size:.82rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?= sanitize($m['Subject']) ?></div>
                    <div class="text-muted" style="font-size:.76rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?= sanitize(substr($m['Body'] ?? '', 0, 100)) ?></div>
                </div>
                <?php if ($m['IsRead']): ?>
                <span class="text-success" style="font-size:.7rem;align-self:center;"><i class="bi bi-check2-all"></i></span>
                <?php endif; ?>
            </a>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
