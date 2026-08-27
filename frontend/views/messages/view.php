<?php $pageTitle = $message['Subject'] ?? 'Message'; ?>
<style>
.chat-container{display:flex;flex-direction:column;gap:.5rem;padding:1rem;max-height:65vh;overflow-y:auto}
.chat-row{display:flex;align-items:flex-end;gap:.5rem;max-width:80%}
.chat-row.sent{margin-left:auto;flex-direction:row-reverse}
.chat-row.received{margin-right:auto}
.chat-avatar{width:32px;height:32px;border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:.7rem;flex-shrink:0}
.chat-bubble{padding:.6rem .9rem;border-radius:16px;font-size:.86rem;line-height:1.5;word-break:break-word;position:relative}
.chat-bubble.sent{background:var(--gradient-brand);color:#fff;border-bottom-right-radius:4px}
.chat-bubble.received{background:#f1f5f9;color:#1e293b;border-bottom-left-radius:4px}
.chat-bubble.sent .chat-time{color:rgba(255,255,255,.7)}
.chat-bubble.received .chat-time{color:#94a3b8}
.chat-time{font-size:.62rem;margin-top:.25rem;display:flex;align-items:center;gap:.3rem}
.chat-time .edited{font-style:italic;opacity:.8}
.chat-date-divider{text-align:center;position:relative;margin:.8rem 0}
.chat-date-divider::before{content:"";position:absolute;left:0;right:0;top:50%;height:1px;background:#e2e8f0}
.chat-date-divider span{position:relative;background:var(--glass-bg);padding:0 .8rem;font-size:.68rem;color:#94a3b8;font-weight:500;text-transform:uppercase;letter-spacing:.04em}
.chat-actions{display:flex;gap:.3rem;margin-top:.2rem}
.chat-actions .btn{font-size:.65rem;padding:.1rem .4rem;opacity:.6;transition:opacity .15s}
.chat-bubble.sent .chat-actions .btn{color:rgba(255,255,255,.8);border-color:rgba(255,255,255,.3)}
.chat-bubble.received .chat-actions .btn{color:#64748b}
.chat-actions .btn:hover{opacity:1}
.chat-attachment{margin-top:.4rem}
.chat-attachment audio{height:28px}
.chat-file{display:inline-flex;align-items:center;gap:.4rem;background:rgba(255,255,255,.15);border-radius:8px;padding:.3rem .6rem;margin-top:.3rem;text-decoration:none;color:inherit;font-size:.78rem;max-width:220px;transition:background .15s}
.chat-file:hover{background:rgba(255,255,255,.25)}
.chat-bubble.received .chat-file{background:#e2e8f0}
.chat-bubble.received .chat-file:hover{background:#cbd5e1}
.chat-reply-bar{border-top:1px solid #e2e8f0;background:#fff;border-radius:0 0 16px 16px}
.reply-input{border:none;resize:none;font-size:.85rem;outline:none;background:transparent}
.reply-input:focus{box-shadow:none}
.reply-toolbar{display:flex;align-items:center;gap:.4rem}
.reply-toolbar .btn{font-size:.75rem;padding:.2rem .5rem}
@media(max-width:768px){.chat-row{max-width:90%}}
</style>

<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <h5 class="fw-bold mb-0"><i class="bi bi-envelope-open me-2"></i><?= sanitize($message['Subject']) ?></h5>
    <div class="d-flex gap-2">
        <a href="?route=messages/compose&reply=<?= sanitize($message['MessageID']) ?>" class="btn btn-sm btn-outline-success"><i class="bi bi-reply me-1"></i>Reply</a>
        <a href="?route=messages/inbox" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm" style="border-radius:16px !important;">
            <div class="card-body p-0">
                <div class="chat-container" id="chatContainer">
                    <?php $me = $_SESSION['user_id']; $prevDate = ''; ?>
                    <?php foreach ($thread as $i => $m): ?>
                    <?php
                    $isMe = $m['SenderID'] === $me;
                    $msgDate = date('M j, Y', strtotime($m['created_at']));
                    if ($msgDate !== $prevDate):
                    ?>
                    <div class="chat-date-divider"><span><?= $msgDate === date('M j, Y') ? 'Today' : $msgDate ?></span></div>
                    <?php $prevDate = $msgDate; endif; ?>
                    <div class="chat-row <?= $isMe ? 'sent' : 'received' ?>">
                        <?php if (!$isMe): ?>
                        <div class="chat-avatar" style="background:var(--gradient-brand);"><?= strtoupper(substr($m['SenderName'], 0, 1)) ?></div>
                        <?php endif; ?>
                        <div>
                            <?php if (!$isMe): ?>
                            <div style="font-size:.68rem;font-weight:600;color:#16a34a;margin-bottom:.15rem;padding-left:.3rem;"><?= sanitize($m['SenderName']) ?></div>
                            <?php endif; ?>
                            <div class="chat-bubble <?= $isMe ? 'sent' : 'received' ?>">
                                <div style="white-space:pre-wrap;"><?= sanitize($m['Body']) ?></div>
                                <?php $msgAtts = $attachments[$m['MessageID']] ?? []; ?>
                                <?php if (!empty($msgAtts)): ?>
                                <div class="chat-attachment">
                                    <?php foreach ($msgAtts as $att): ?>
                                    <?php if ($att['FileType'] === 'voice'): ?>
                                    <div class="d-flex align-items-center gap-2 mt-1" style="max-width:260px;">
                                        <div style="width:26px;height:26px;border-radius:50%;background:<?= $isMe ? 'rgba(255,255,255,.2)' : 'var(--gradient-warm)' ?>;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                            <i class="bi bi-mic-fill text-white" style="font-size:.6rem;"></i>
                                        </div>
                                        <audio controls style="height:28px;flex-grow:1;min-width:100px;">
                                            <source src="?route=messages/download&id=<?= sanitize($att['AttachmentID']) ?>" type="<?= sanitize($att['MimeType'] ?: 'audio/webm') ?>">
                                        </audio>
                                        <?php if ($att['Duration']): ?>
                                        <span class="<?= $isMe ? 'text-white-50' : 'text-muted' ?>" style="font-size:.6rem;"><?= sprintf('%d:%02d', floor($att['Duration']/60), $att['Duration']%60) ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <?php else: ?>
                                    <a href="?route=messages/download&id=<?= sanitize($att['AttachmentID']) ?>" class="chat-file">
                                        <?php
                                        $icon = 'bi-file-earmark';
                                        if (str_starts_with($att['MimeType'] ?? '', 'image/')) $icon = 'bi-image';
                                        elseif (str_starts_with($att['MimeType'] ?? '', 'video/')) $icon = 'bi-camera-video';
                                        elseif (str_contains($att['MimeType'] ?? '', 'pdf')) $icon = 'bi-file-pdf';
                                        elseif (str_contains($att['MimeType'] ?? '', 'word') || str_contains($att['MimeType'] ?? '', 'document')) $icon = 'bi-file-word';
                                        elseif (str_contains($att['MimeType'] ?? '', 'excel') || str_contains($att['MimeType'] ?? '', 'sheet')) $icon = 'bi-file-excel';
                                        elseif (str_contains($att['MimeType'] ?? '', 'text')) $icon = 'bi-file-text';
                                        ?>
                                        <i class="bi <?= $icon ?>" style="font-size:.9rem;"></i>
                                        <span class="text-truncate" style="max-width:120px;"><?= sanitize($att['FileName']) ?></span>
                                        <span style="font-size:.6rem;opacity:.7;"><?= $att['FileSize'] > 1048576 ? round($att['FileSize']/1048576, 1) . 'M' : round($att['FileSize']/1024) . 'K' ?></span>
                                    </a>
                                    <?php endif; ?>
                                    <?php endforeach; ?>
                                </div>
                                <?php endif; ?>
                                <div class="chat-time">
                                    <span><?= date('g:i A', strtotime($m['created_at'])) ?></span>
                                    <?php if ($m['IsEdited']): ?><span class="edited">edited</span><?php endif; ?>
                                    <?php if ($isMe): ?>
                                    <span><?= $m['IsRead'] ? '<i class="bi bi-check2-all"></i>' : '<i class="bi bi-check2"></i>' ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php if ($isMe): ?>
                            <div class="chat-actions justify-content-end d-flex">
                                <a href="?route=messages/edit&id=<?= sanitize($m['MessageID']) ?>" class="btn btn-outline-secondary btn-sm" style="font-size:.65rem;padding:.1rem .4rem;"><i class="bi bi-pencil"></i></a>
                                <form method="post" action="?route=messages/delete" class="d-inline" onsubmit="return confirm('Delete this message?')">
                                    <?= csrfField() ?>
                                    <input type="hidden" name="id" value="<?= sanitize($m['MessageID']) ?>">
                                    <button type="submit" class="btn btn-outline-danger btn-sm" style="font-size:.65rem;padding:.1rem .4rem;"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php if ($isMe): ?>
                        <div class="chat-avatar" style="background:var(--gradient-cool);"><?= strtoupper(substr($m['SenderName'], 0, 1)) ?></div>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Reply Bar -->
            <div class="chat-reply-bar p-3">
                <form method="post" action="?route=messages/send" enctype="multipart/form-data" id="replyForm">
                    <?= csrfField() ?>
                    <input type="hidden" name="ReceiverID" value="<?= sanitize($message['SenderID'] === $me ? $message['ReceiverID'] : $message['SenderID']) ?>">
                    <input type="hidden" name="Subject" value="<?= sanitize('Re: ' . ltrim($message['Subject'], 'Re: ')) ?>">
                    <input type="hidden" name="ParentMessageID" value="<?= sanitize($message['ParentMessageID'] ?? $message['MessageID']) ?>">
                    <textarea name="Body" class="form-control reply-input mb-2" rows="2" placeholder="Type your reply..." required style="border:1.5px solid #e2e8f0;border-radius:12px;padding:.5rem .8rem;"></textarea>
                    <div class="reply-toolbar">
                        <label for="replyFileInput" class="btn btn-outline-secondary btn-sm" style="cursor:pointer;font-size:.75rem;padding:.2rem .5rem;" title="Attach file">
                            <i class="bi bi-paperclip"></i>
                        </label>
                        <button type="button" class="btn btn-outline-danger btn-sm" onclick="startReplyVoice()" id="replyVoiceBtn" style="font-size:.75rem;padding:.2rem .5rem;" title="Record voice">
                            <i class="bi bi-mic-fill"></i>
                        </button>
                        <label for="replyVoiceFallback" class="btn btn-outline-secondary btn-sm" style="cursor:pointer;font-size:.75rem;padding:.2rem .5rem;" title="Upload audio">
                            <i class="bi bi-upload"></i>
                        </label>
                        <span id="replyTimer" class="text-muted" style="font-size:.7rem;font-family:monospace;display:none;">00:00</span>
                        <span id="replyVoiceStatus" class="text-muted" style="font-size:.7rem;"></span>
                        <div class="ms-auto">
                            <button type="submit" class="btn btn-success btn-sm" style="font-size:.78rem;padding:.3rem .8rem;border-radius:10px;"><i class="bi bi-send-fill"></i></button>
                        </div>
                    </div>
                    <input type="file" name="attachments[]" id="replyFileInput" multiple class="d-none" accept=".jpg,.jpeg,.png,.gif,.webp,.pdf,.doc,.docx,.xls,.xlsx,.txt,.csv,.webm,.ogg,.mp3,.wav" onchange="handleReplyFiles(this.files)">
                    <input type="file" name="voice_file" id="replyVoiceFallback" class="d-none" accept="audio/*" onchange="handleReplyVoiceFile(this.files[0])">
                    <input type="hidden" name="voice_duration" id="replyVoiceDuration" value="">
                    <div id="replyFileList" class="mt-1"></div>
                    <div id="replyVoicePreview" class="mt-1 d-none">
                        <div class="d-flex align-items-center gap-2 bg-light rounded-2 px-2 py-1" style="font-size:.8rem;">
                            <i class="bi bi-mic text-danger"></i>
                            <audio id="replyVoiceAudio" controls style="height:28px;flex-grow:1;"></audio>
                            <button type="button" class="btn btn-link btn-sm text-danger p-0" onclick="removeReplyVoice()" style="font-size:.7rem;text-decoration:none;">&times;</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById("chatContainer").scrollTop = document.getElementById("chatContainer").scrollHeight;
let replyFiles=[],replyMediaRecorder=null,replyAudioChunks=[],replyRecStart=null,replyTimerInt=null;

function handleReplyFiles(files){for(const f of files){if(replyFiles.length>=5)break;if(f.size>15*1048576)continue;replyFiles.push(f);}renderReplyFiles();document.getElementById("replyFileInput").value="";}

function renderReplyFiles(){const el=document.getElementById("replyFileList");if(!replyFiles.length){el.innerHTML="";return;}el.innerHTML=replyFiles.map((f,i)=>{const sz=f.size<1024?f.size+" B":f.size<1048576?(f.size/1024).toFixed(1)+" KB":(f.size/1048576).toFixed(1)+" MB";return '<div class="d-flex align-items-center gap-1 text-muted" style="font-size:.78rem;"><i class="bi bi-file-earmark"></i><span class="text-truncate" style="max-width:180px;">'+f.name+'</span><span>'+sz+'</span><button type="button" class="btn btn-link p-0 text-danger" onclick="replyFiles.splice('+i+',1);renderReplyFiles()" style="font-size:.65rem;text-decoration:none;">&times;</button></div>';}).join("");}

async function startReplyVoice(){const btn=document.getElementById("replyVoiceBtn"),txt=document.getElementById("replyVoiceText"),timer=document.getElementById("replyTimer"),status=document.getElementById("replyVoiceStatus");if(replyMediaRecorder&&replyMediaRecorder.state==="recording"){replyMediaRecorder.stop();txt.textContent="Voice";btn.classList.remove("btn-danger");btn.classList.add("btn-outline-danger");clearInterval(replyTimerInt);timer.style.display="none";status.textContent="";return;}status.textContent="Requesting mic...";status.className="text-muted";try{const stream=await navigator.mediaDevices.getUserMedia({audio:true});replyAudioChunks=[];replyMediaRecorder=new MediaRecorder(stream,{mimeType:MediaRecorder.isTypeSupported("audio/webm;codecs=opus")?"audio/webm;codecs=opus":undefined});replyMediaRecorder.ondataavailable=e=>{if(e.data.size>0)replyAudioChunks.push(e.data);};replyMediaRecorder.onstop=()=>{stream.getTracks().forEach(t=>t.stop());const blob=new Blob(replyAudioChunks,{type:replyMediaRecorder.mimeType||"audio/webm"});document.getElementById("replyVoiceAudio").src=URL.createObjectURL(blob);document.getElementById("replyVoicePreview").classList.remove("d-none");const dur=Math.round((Date.now()-replyRecStart)/1000);document.getElementById("replyVoiceDuration").value=dur;const ext=(replyMediaRecorder.mimeType||"").includes("ogg")?"ogg":"webm";const file=new File([blob],"voice_"+Date.now()+"."+ext,{type:replyMediaRecorder.mimeType||"audio/webm"});const dt=new DataTransfer();dt.items.add(file);document.getElementById("replyVoiceFallback").files=dt.files;status.textContent="Recorded "+dur+"s";status.className="text-success";};replyMediaRecorder.start();replyRecStart=Date.now();txt.textContent="Stop";btn.classList.remove("btn-outline-danger");btn.classList.add("btn-danger");timer.style.display="";status.textContent="Recording...";status.className="text-success";let s=0;replyTimerInt=setInterval(()=>{s++;timer.textContent=String(Math.floor(s/60)).padStart(2,"0")+":"+String(s%60).padStart(2,"0");},1000);}catch(e){status.innerHTML='<i class="bi bi-exclamation-triangle me-1"></i>Mic unavailable \u2014 use <strong>upload</strong>';status.className="text-danger";}}

function handleReplyVoiceFile(file){if(!file)return;if(file.size>15*1048576){alert("Audio too large (max 15MB)");return;}const dt=new DataTransfer();dt.items.add(file);document.getElementById("replyVoiceFallback").files=dt.files;document.getElementById("replyVoiceDuration").value="?";document.getElementById("replyVoicePreview").classList.remove("d-none");document.getElementById("replyVoiceAudio").src=URL.createObjectURL(file);document.getElementById("replyVoiceStatus").textContent=file.name;document.getElementById("replyVoiceStatus").className="text-success";}

function removeReplyVoice(){document.getElementById("replyVoicePreview").classList.add("d-none");document.getElementById("replyVoiceAudio").src="";document.getElementById("replyVoiceFallback").value="";document.getElementById("replyVoiceDuration").value="";document.getElementById("replyVoiceStatus").textContent="";}
</script>