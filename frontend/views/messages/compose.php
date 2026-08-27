<?php $pageTitle = 'Compose Message'; ?>
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <h5 class="fw-bold mb-0"><i class="bi bi-pencil-square me-2"></i>Compose Message</h5>
    <a href="?route=messages/inbox" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm" style="border-radius:12px !important;">
            <div class="card-body">
                <?php if ($parentMsg): ?>
                <div class="bg-light rounded-3 p-3 mb-3" style="font-size:.82rem;border-left:3px solid var(--brand);">
                    <div class="fw-semibold text-muted mb-1">Replying to: <?= sanitize($parentMsg['Subject']) ?></div>
                    <div class="text-muted"><?= sanitize(substr($parentMsg['Body'], 0, 200)) ?>...</div>
                    <div class="text-muted mt-1" style="font-size:.72rem;">From <?= sanitize($parentMsg['SenderName']) ?> — <?= date('M j, g:i A', strtotime($parentMsg['created_at'])) ?></div>
                </div>
                <?php endif; ?>

                <form method="post" action="?route=messages/send" enctype="multipart/form-data" id="msgForm">
                    <?= csrfField() ?>
                    <?php if ($replyTo): ?>
                    <input type="hidden" name="ParentMessageID" value="<?= sanitize($replyTo) ?>">
                    <?php endif; ?>
                    <div class="mb-3">
                        <label class="form-label">To</label>
                        <select name="ReceiverID" class="form-select" required>
                            <option value="">Select recipient...</option>
                            <?php foreach ($users as $u): ?>
                            <option value="<?= sanitize($u['UserID']) ?>" <?= ($prefillTo ?? '') === $u['UserID'] ? 'selected' : '' ?>>
                                <?= sanitize($u['Name']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Subject</label>
                        <input type="text" name="Subject" class="form-control" value="<?= sanitize($prefillSubject ?? '') ?>" placeholder="Message subject..." required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Message</label>
                        <textarea name="Body" class="form-control" rows="6" placeholder="Type your message..." required style="font-size:.88rem;"></textarea>
                    </div>

                    <!-- Attachments -->
                    <div class="mb-3">
                        <label class="form-label">Attachments</label>
                        <div id="dropZone" class="border border-dashed rounded-3 p-3 text-center" style="border-color:#cbd5e1 !important;cursor:pointer;transition:all .2s;background:#f8fafc;" onclick="document.getElementById('fileInput').click()" ondragover="event.preventDefault();this.style.borderColor='var(--brand)';this.style.background='#f0fdf4'" ondragleave="this.style.borderColor='#cbd5e1';this.style.background='#f8fafc'" ondrop="event.preventDefault();this.style.borderColor='#cbd5e1';this.style.background='#f8fafc';handleDrop(event)">
                            <i class="bi bi-paperclip text-muted" style="font-size:1.4rem;"></i>
                            <div class="text-muted mt-1" style="font-size:.8rem;">Click or drag files here (max 15MB each)</div>
                            <div class="text-muted" style="font-size:.7rem;">PDF, Images, Documents, Audio, Video</div>
                        </div>
                        <input type="file" name="attachments[]" id="fileInput" multiple class="d-none" accept=".jpg,.jpeg,.png,.gif,.webp,.svg,.pdf,.doc,.docx,.xls,.xlsx,.txt,.csv,.webm,.ogg,.mp3,.wav,.mp4" onchange="handleFiles(this.files)">
                        <div id="fileList" class="mt-2"></div>
                    </div>

                    <!-- Voice Recording -->
                    <div class="mb-3">
                        <label class="form-label">Voice Message</label>
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <button type="button" id="voiceBtn" class="btn btn-outline-danger btn-sm" onclick="startVoice()" style="min-width:140px;">
                                <i class="bi bi-mic-fill me-1"></i><span id="voiceBtnText">Record</span>
                            </button>
                            <span id="voiceTimer" class="text-muted" style="font-size:.8rem;font-family:monospace;display:none;">00:00</span>
                            <span id="voiceStatus" class="text-muted" style="font-size:.78rem;"></span>
                            <label for="voiceFallbackInput" class="btn btn-outline-secondary btn-sm mb-0" style="cursor:pointer;font-size:.78rem;" title="Upload audio file instead">
                                <i class="bi bi-upload me-1"></i>Upload Audio
                            </label>
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
                        <a href="?route=messages/inbox" class="btn btn-sm btn-outline-secondary">Cancel</a>
                        <button type="submit" class="btn btn-sm btn-success" id="sendBtn"><i class="bi bi-send me-1"></i>Send Message</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
let selectedFiles = [];
let mediaRecorder = null;
let audioChunks = [];
let recordingStartTime = null;
let timerInterval = null;
let micAvailable = null;

function handleFiles(files) {
    for (const file of files) {
        if (selectedFiles.length >= 5) { alert('Maximum 5 attachments'); break; }
        if (file.size > 15*1024*1024) { alert(file.name + ' is too large (max 15MB)'); continue; }
        selectedFiles.push(file);
    }
    renderFileList();
    document.getElementById('fileInput').value = '';
}

function handleDrop(e) { handleFiles(e.dataTransfer.files); }

function removeFile(i) {
    selectedFiles.splice(i, 1);
    renderFileList();
}

function renderFileList() {
    const el = document.getElementById('fileList');
    if (!selectedFiles.length) { el.innerHTML = ''; return; }
    el.innerHTML = selectedFiles.map((f, i) => {
        const size = f.size < 1024 ? f.size + ' B' : f.size < 1048576 ? (f.size/1024).toFixed(1) + ' KB' : (f.size/1048576).toFixed(1) + ' MB';
        const icon = f.type.startsWith('image/') ? 'bi-image' : f.type.startsWith('audio/') ? 'bi-mic' : f.type.startsWith('video/') ? 'bi-camera-video' : 'bi-file-earmark';
        return '<div class="d-flex align-items-center gap-2 bg-light rounded-2 px-2 py-1 mb-1" style="font-size:.82rem;"><i class="bi ' + icon + ' text-primary"></i><span class="flex-grow-1 text-truncate">' + f.name + '</span><span class="text-muted">' + size + '</span><button type="button" class="btn btn-link btn-sm text-danger p-0" onclick="removeFile(' + i + ')" style="font-size:.7rem;text-decoration:none;">&times;</button></div>';
    }).join('');
}

/* ── Voice ── */
async function startVoice() {
    const btn = document.getElementById('voiceBtn');
    const txt = document.getElementById('voiceBtnText');
    const status = document.getElementById('voiceStatus');

    if (mediaRecorder && mediaRecorder.state === 'recording') {
        mediaRecorder.stop();
        txt.textContent = 'Record';
        btn.classList.remove('btn-danger');
        btn.classList.add('btn-outline-danger');
        clearInterval(timerInterval);
        document.getElementById('voiceTimer').style.display = 'none';
        status.textContent = '';
        return;
    }

    status.textContent = 'Requesting microphone...';
    status.className = 'text-muted';
    status.style.fontSize = '.78rem';

    try {
        const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
        micAvailable = true;
        audioChunks = [];
        mediaRecorder = new MediaRecorder(stream, { mimeType: MediaRecorder.isTypeSupported('audio/webm;codecs=opus') ? 'audio/webm;codecs=opus' : undefined });
        mediaRecorder.ondataavailable = e => { if (e.data.size > 0) audioChunks.push(e.data); };
        mediaRecorder.onstop = () => {
            stream.getTracks().forEach(t => t.stop());
            const blob = new Blob(audioChunks, { type: mediaRecorder.mimeType || 'audio/webm' });
            attachVoiceBlob(blob);
        };
        mediaRecorder.start();
        recordingStartTime = Date.now();
        txt.textContent = 'Stop';
        btn.classList.remove('btn-outline-danger');
        btn.classList.add('btn-danger');
        document.getElementById('voiceTimer').style.display = '';
        status.textContent = 'Recording...';
        status.className = 'text-success';
        let secs = 0;
        timerInterval = setInterval(() => {
            secs++;
            document.getElementById('voiceTimer').textContent = String(Math.floor(secs/60)).padStart(2,'0') + ':' + String(secs%60).padStart(2,'0');
        }, 1000);
    } catch (err) {
        micAvailable = false;
        status.innerHTML = '<i class="bi bi-exclamation-triangle me-1"></i>Microphone unavailable — use <strong>Upload Audio</strong> instead';
        status.className = 'text-danger';
        status.style.fontSize = '.78rem';
    }
}

function handleVoiceFile(file) {
    if (!file) return;
    if (file.size > 15*1024*1024) { alert('Audio file too large (max 15MB)'); return; }
    const dt = new DataTransfer();
    dt.items.add(file);
    document.getElementById('voiceFallbackInput').files = dt.files;
    document.getElementById('voiceDuration').value = '?';
    document.getElementById('voicePreview').classList.remove('d-none');
    document.getElementById('voiceAudio').src = URL.createObjectURL(file);
    document.getElementById('voiceStatus').textContent = 'Audio file attached: ' + file.name;
    document.getElementById('voiceStatus').className = 'text-success';
    document.getElementById('voiceStatus').style.fontSize = '.78rem';
}

function attachVoiceBlob(blob) {
    const url = URL.createObjectURL(blob);
    document.getElementById('voiceAudio').src = url;
    document.getElementById('voicePreview').classList.remove('d-none');
    const duration = Math.round((Date.now() - recordingStartTime) / 1000);
    document.getElementById('voiceDuration').value = duration;
    const ext = mediaRecorder.mimeType.includes('ogg') ? 'ogg' : 'webm';
    const file = new File([blob], 'voice_' + Date.now() + '.' + ext, { type: mediaRecorder.mimeType || 'audio/webm' });
    const dt = new DataTransfer();
    dt.items.add(file);
    const input = document.getElementById('voiceFallbackInput');
    input.name = 'voice_file';
    input.files = dt.files;
    document.getElementById('voiceStatus').textContent = 'Voice recorded (' + duration + 's)';
    document.getElementById('voiceStatus').className = 'text-success';
}

function removeVoice() {
    document.getElementById('voicePreview').classList.add('d-none');
    document.getElementById('voiceAudio').src = '';
    const input = document.getElementById('voiceFallbackInput');
    input.value = '';
    input.name = 'voice_file';
    document.getElementById('voiceDuration').value = '';
    document.getElementById('voiceStatus').textContent = '';
}
</script>
