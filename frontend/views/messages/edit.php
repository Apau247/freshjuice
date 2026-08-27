<?php $pageTitle = 'Edit Message'; ?>
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <h5 class="fw-bold mb-0"><i class="bi bi-pencil me-2"></i>Edit Message</h5>
    <a href="?route=messages/view&id=<?= sanitize($message['MessageID']) ?>" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm" style="border-radius:12px !important;">
            <div class="card-body">
                <div class="bg-light rounded-3 p-3 mb-3" style="font-size:.82rem;border-left:3px solid var(--brand);">
                    <div class="text-muted"><strong>To:</strong> <?= sanitize($message['ReceiverName']) ?></div>
                    <div class="text-muted"><strong>Subject:</strong> <?= sanitize($message['Subject']) ?></div>
                    <div class="text-muted"><strong>Sent:</strong> <?= date('M j, g:i A', strtotime($message['created_at'])) ?></div>
                </div>
                <form method="post" action="?route=messages/update" enctype="multipart/form-data">
                    <?= csrfField() ?>
                    <input type="hidden" name="id" value="<?= sanitize($message['MessageID']) ?>">
                    <div class="mb-3">
                        <label class="form-label">Message Body</label>
                        <textarea name="Body" class="form-control" rows="8" required style="font-size:.88rem;"><?= sanitize($message['Body']) ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Add Attachments</label>
                        <input type="file" name="attachments[]" multiple class="form-control form-control-sm" accept=".jpg,.jpeg,.png,.gif,.webp,.pdf,.doc,.docx,.xls,.xlsx,.txt,.csv,.webm,.ogg,.mp3,.wav" style="font-size:.82rem;">
                        <div class="form-text">Optional. Max 15MB per file.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Add Voice Message</label>
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <button type="button" id="voiceBtn" class="btn btn-outline-danger btn-sm" onclick="startVoice()" style="min-width:120px;">
                                <i class="bi bi-mic-fill me-1"></i><span id="voiceBtnText">Record</span>
                            </button>
                            <span id="voiceTimer" class="text-muted" style="font-size:.78rem;font-family:monospace;display:none;">00:00</span>
                            <label for="voiceFallbackInput" class="btn btn-outline-secondary btn-sm mb-0" style="cursor:pointer;font-size:.78rem;" title="Upload audio file">
                                <i class="bi bi-upload me-1"></i>Upload Audio
                            </label>
                            <span id="voiceStatus" class="text-muted" style="font-size:.78rem;"></span>
                        </div>
                        <input type="file" name="voice_file" id="voiceFallbackInput" class="d-none" accept="audio/*" onchange="handleVoiceFile(this.files[0])">
                        <input type="hidden" name="voice_duration" id="voiceDuration" value="">
                        <div id="voicePreview" class="mt-2 d-none">
                            <div class="d-flex align-items-center gap-2 bg-light rounded-3 p-2">
                                <i class="bi bi-mic text-danger"></i>
                                <audio id="voiceAudio" controls class="flex-grow-1" style="height:36px;"></audio>
                                <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeVoice()" style="padding:.15rem .4rem;font-size:.72rem;"><i class="bi bi-x-lg"></i></button>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="?route=messages/view&id=<?= sanitize($message['MessageID']) ?>" class="btn btn-sm btn-outline-secondary">Cancel</a>
                        <button type="submit" class="btn btn-sm btn-success"><i class="bi bi-check-lg me-1"></i>Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
let mediaRecorder = null, audioChunks = [], recStart = null, timerInt = null;

async function startVoice() {
    const btn = document.getElementById('voiceBtn');
    const txt = document.getElementById('voiceBtnText');
    const timer = document.getElementById('voiceTimer');
    const status = document.getElementById('voiceStatus');

    if (mediaRecorder && mediaRecorder.state === 'recording') {
        mediaRecorder.stop();
        txt.textContent = 'Record';
        btn.classList.remove('btn-danger');
        btn.classList.add('btn-outline-danger');
        clearInterval(timerInt);
        timer.style.display = 'none';
        status.textContent = '';
        return;
    }

    status.textContent = 'Requesting mic...';
    status.className = 'text-muted';

    try {
        const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
        audioChunks = [];
        mediaRecorder = new MediaRecorder(stream, { mimeType: MediaRecorder.isTypeSupported('audio/webm;codecs=opus') ? 'audio/webm;codecs=opus' : undefined });
        mediaRecorder.ondataavailable = e => { if (e.data.size > 0) audioChunks.push(e.data); };
        mediaRecorder.onstop = () => {
            stream.getTracks().forEach(t => t.stop());
            const blob = new Blob(audioChunks, { type: mediaRecorder.mimeType || 'audio/webm' });
            document.getElementById('voiceAudio').src = URL.createObjectURL(blob);
            document.getElementById('voicePreview').classList.remove('d-none');
            const dur = Math.round((Date.now() - recStart) / 1000);
            document.getElementById('voiceDuration').value = dur;
            const ext = (mediaRecorder.mimeType || '').includes('ogg') ? 'ogg' : 'webm';
            const file = new File([blob], 'voice_' + Date.now() + '.' + ext, { type: mediaRecorder.mimeType || 'audio/webm' });
            const dt = new DataTransfer();
            dt.items.add(file);
            document.getElementById('voiceFallbackInput').files = dt.files;
            status.textContent = 'Recorded ' + dur + 's';
            status.className = 'text-success';
        };
        mediaRecorder.start();
        recStart = Date.now();
        txt.textContent = 'Stop';
        btn.classList.remove('btn-outline-danger');
        btn.classList.add('btn-danger');
        timer.style.display = '';
        status.textContent = 'Recording...';
        status.className = 'text-success';
        let s = 0;
        timerInt = setInterval(() => { s++; timer.textContent = String(Math.floor(s/60)).padStart(2,'0') + ':' + String(s%60).padStart(2,'0'); }, 1000);
    } catch (e) {
        status.innerHTML = '<i class="bi bi-exclamation-triangle me-1"></i>Mic unavailable — use <strong>Upload Audio</strong>';
        status.className = 'text-danger';
    }
}

function handleVoiceFile(file) {
    if (!file) return;
    if (file.size > 15*1024*1024) { alert('Audio too large (max 15MB)'); return; }
    const dt = new DataTransfer();
    dt.items.add(file);
    document.getElementById('voiceFallbackInput').files = dt.files;
    document.getElementById('voiceDuration').value = '?';
    document.getElementById('voicePreview').classList.remove('d-none');
    document.getElementById('voiceAudio').src = URL.createObjectURL(file);
    document.getElementById('voiceStatus').textContent = file.name;
    document.getElementById('voiceStatus').className = 'text-success';
}

function removeVoice() {
    document.getElementById('voicePreview').classList.add('d-none');
    document.getElementById('voiceAudio').src = '';
    document.getElementById('voiceFallbackInput').value = '';
    document.getElementById('voiceDuration').value = '';
    document.getElementById('voiceStatus').textContent = '';
}
</script>
