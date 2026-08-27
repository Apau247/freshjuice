<?php
declare(strict_types=1);
require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../models/MessageModel.php';
require_once __DIR__ . '/../models/MessageAttachmentModel.php';

class MessageController extends Controller
{
    private MessageModel $messages;
    private MessageAttachmentModel $attachments;

    private const UPLOAD_DIR = __DIR__ . '/../../uploads/messages';
    private const MAX_FILE_SIZE = 15 * 1024 * 1024;
    private const ALLOWED_MIME = [
        'image/jpeg','image/png','image/gif','image/webp','image/svg+xml',
        'application/pdf',
        'application/msword','application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel','application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'text/plain','text/csv',
        'audio/webm','audio/ogg','audio/mpeg','audio/wav',
        'video/webm','video/mp4',
    ];
    private const ALLOWED_EXT = [
        'jpg','jpeg','png','gif','webp','svg','pdf','doc','docx','xls','xlsx','txt','csv',
        'webm','ogg','mp3','wav','mp4',
    ];

    public function __construct()
    {
        parent::__construct();
        $this->viewPath = 'messages';
        $this->messages = new MessageModel();
        $this->attachments = new MessageAttachmentModel();
    }

    public function inbox(): void
    {
        $userId = $_SESSION['user_id'];
        $inbox  = $this->messages->getInbox($userId);
        $this->render('inbox', ['messages' => $inbox]);
    }

    public function sent(): void
    {
        $userId = $_SESSION['user_id'];
        $sent   = $this->messages->getSent($userId);
        $this->render('sent', ['messages' => $sent]);
    }

    public function compose(): void
    {
        $userId = $_SESSION['user_id'];
        $db = getDb();
        $stmt = $db->prepare("SELECT UserID, Name, RoleID FROM users WHERE Status = 'Active' AND UserID != ? ORDER BY Name");
        $stmt->execute([$userId]);
        $users = $stmt->fetchAll();

        $replyTo = $this->getInput('reply');
        $parentMsg = null;
        if ($replyTo) {
            $parentMsg = $this->messages->find($replyTo);
        }

        $this->render('compose', [
            'users'     => $users,
            'parentMsg' => $parentMsg,
            'replyTo'   => $replyTo,
            'prefillTo' => $parentMsg ? $parentMsg['SenderID'] : ($this->getInput('to') ?: ''),
            'prefillSubject' => $parentMsg ? 'Re: ' . ltrim($parentMsg['Subject'], 'Re: ') : ($this->getInput('subject') ?: ''),
        ]);
    }

    public function send(): void
    {
        $userId = $_SESSION['user_id'];
        $to      = $this->getInput('ReceiverID');
        $subject = trim($this->getInput('Subject') ?: '');
        $body    = trim($this->getInput('Body') ?: '');
        $parent  = $this->getInput('ParentMessageID') ?: null;

        if (!$to || !$subject || !$body) {
            setFlash('error', 'Recipient, subject, and message are required.');
            $this->redirect('messages/compose');
            return;
        }

        $msgId = $this->messages->create([
            'SenderID'        => $userId,
            'ReceiverID'      => $to,
            'Subject'         => $subject,
            'Body'            => $body,
            'ParentMessageID' => $parent,
        ]);

        $this->handleAttachments($msgId);
        $this->handleVoiceRecording($msgId);

        setFlash('success', 'Message sent successfully.');
        $this->redirect('messages/sent');
    }

    public function view(): void
    {
        $userId = $_SESSION['user_id'];
        $id = $this->getInput('id');
        $msg = $this->messages->find($id);

        if (!$msg) {
            setFlash('error', 'Message not found.');
            $this->redirect('messages/inbox');
            return;
        }

        if ($msg['ReceiverID'] === $userId || $msg['SenderID'] === $userId) {
            if ($msg['ReceiverID'] === $userId && !$msg['IsRead']) {
                $this->messages->markRead($id);
            }
            $thread = $this->messages->getThread($id);
        } else {
            setFlash('error', 'Access denied.');
            $this->redirect('messages/inbox');
            return;
        }

        $threadIds = array_column($thread, 'MessageID');
        $allAttachments = $this->attachments->getAttachmentsForThread($threadIds);
        $attachMap = [];
        foreach ($allAttachments as $att) {
            $attachMap[$att['MessageID']][] = $att;
        }

        $userId = $_SESSION['user_id'];
        $db = getDb();
        $stmt = $db->prepare("SELECT UserID, Name, RoleID FROM users WHERE Status = 'Active' AND UserID != ? ORDER BY Name");
        $stmt->execute([$userId]);
        $users = $stmt->fetchAll();

        $this->render('view', [
            'message' => $msg,
            'thread'  => $thread,
            'attachments' => $attachMap,
            'users'   => $users,
        ]);
    }

    public function edit(): void
    {
        $userId = $_SESSION['user_id'];
        $id = $this->getInput('id');
        $msg = $this->messages->find($id);

        if (!$msg || $msg['SenderID'] !== $userId) {
            setFlash('error', 'You can only edit your own messages.');
            $this->redirect('messages/inbox');
            return;
        }

        $this->render('edit', ['message' => $msg]);
    }

    public function update(): void
    {
        $userId = $_SESSION['user_id'];
        $id     = $this->getInput('id');
        $body   = trim($this->getInput('Body') ?: '');

        if (!$body) {
            setFlash('error', 'Message body cannot be empty.');
            $this->redirect('messages/edit?id=' . $id);
            return;
        }

        $msg = $this->messages->find($id);
        if (!$msg || $msg['SenderID'] !== $userId) {
            setFlash('error', 'You can only edit your own messages.');
            $this->redirect('messages/inbox');
            return;
        }

        $this->messages->updateMessage($id, ['Body' => $body]);

        if (!empty($_FILES['attachments']['name'][0])) {
            $this->handleAttachments($id);
        }
        if (!empty($_FILES['voice_file']['name'])) {
            $this->handleVoiceRecording($id);
        }

        setFlash('success', 'Message updated.');
        $this->redirect('messages/view?id=' . $id);
    }

    public function delete(): void
    {
        $userId = $_SESSION['user_id'];
        $id = $this->getInput('id');
        $this->messages->softDelete($id, $userId);
        setFlash('success', 'Message deleted.');
        $this->redirect('messages/inbox');
    }

    public function download(): void
    {
        $userId = $_SESSION['user_id'];
        $attId = $this->getInput('id');
        $att = $this->attachments->find($attId);

        if (!$att) {
            setFlash('error', 'Attachment not found.');
            $this->redirect('messages/inbox');
            return;
        }

        $msg = $this->messages->find($att['MessageID']);
        if (!$msg || ($msg['SenderID'] !== $userId && $msg['ReceiverID'] !== $userId)) {
            setFlash('error', 'Access denied.');
            $this->redirect('messages/inbox');
            return;
        }

        $fileName = basename($att['FilePath']);
        $fullPath = self::UPLOAD_DIR . '/' . $fileName;

        $realUpload = realpath(self::UPLOAD_DIR);
        $realFile   = realpath($fullPath);

        if (!$realFile || !$realUpload || strncmp($realFile, $realUpload, strlen($realUpload)) !== 0 || !file_exists($fullPath)) {
            setFlash('error', 'File not found on disk.');
            $this->redirect('messages/view?id=' . $att['MessageID']);
            return;
        }

        header('Content-Type: ' . ($att['MimeType'] ?: 'application/octet-stream'));
        header('Content-Disposition: attachment; filename="' . sanitize($att['FileName']) . '"');
        header('Content-Length: ' . filesize($fullPath));
        header('Cache-Control: no-cache');
        readfile($fullPath);
        exit;
    }

    public function uploadAjax(): void
    {
        header('Content-Type: application/json');
        $userId = $_SESSION['user_id'] ?? '';
        if (!$userId) { echo json_encode(['error' => 'Not authenticated']); exit; }

        if (empty($_FILES['file'])) {
            echo json_encode(['error' => 'No file uploaded']);
            exit;
        }

        $file = $_FILES['file'];
        if ($file['error'] !== UPLOAD_ERR_OK) {
            echo json_encode(['error' => 'Upload error: ' . $file['error']]);
            exit;
        }
        if ($file['size'] > self::MAX_FILE_SIZE) {
            echo json_encode(['error' => 'File too large (max 15MB)']);
            exit;
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, self::ALLOWED_EXT, true)) {
            echo json_encode(['error' => 'File type not allowed']);
            exit;
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file['tmp_name']);
        if (!in_array($mime, self::ALLOWED_MIME, true)) {
            echo json_encode(['error' => 'MIME type not allowed: ' . $mime]);
            exit;
        }

        if (!is_dir(self::UPLOAD_DIR)) {
            mkdir(self::UPLOAD_DIR, 0755, true);
        }

        $newName = generateId('ATT') . '.' . $ext;
        $dest = self::UPLOAD_DIR . '/' . $newName;
        if (!move_uploaded_file($file['tmp_name'], $dest)) {
            echo json_encode(['error' => 'Failed to save file']);
            exit;
        }

        echo json_encode([
            'ok'       => true,
            'name'     => $file['name'],
            'path'     => 'uploads/messages/' . $newName,
            'type'     => $mime,
            'size'     => $file['size'],
            'filename' => $newName,
        ]);
        exit;
    }

    private function handleAttachments(string $msgId): void
    {
        if (empty($_FILES['attachments']['name'][0])) return;

        foreach ($_FILES['attachments']['name'] as $i => $name) {
            if ($_FILES['attachments']['error'][$i] !== UPLOAD_ERR_OK) continue;
            if (empty($name)) continue;

            $size = $_FILES['attachments']['size'][$i];
            if ($size > self::MAX_FILE_SIZE) continue;

            $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
            if (!in_array($ext, self::ALLOWED_EXT, true)) continue;

            $tmpName = $_FILES['attachments']['tmp_name'][$i];
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mime = $finfo->file($tmpName);
            if (!in_array($mime, self::ALLOWED_MIME, true)) continue;

            if (!is_dir(self::UPLOAD_DIR)) {
                mkdir(self::UPLOAD_DIR, 0755, true);
            }

            $newName = generateId('ATT') . '.' . $ext;
            $dest = self::UPLOAD_DIR . '/' . $newName;
            if (move_uploaded_file($tmpName, $dest)) {
                $this->attachments->create([
                    'MessageID' => $msgId,
                    'FileName'  => $name,
                    'FilePath'  => 'uploads/messages/' . $newName,
                    'FileType'  => 'file',
                    'FileSize'  => $size,
                    'MimeType'  => $mime,
                ]);
            }
        }
    }

    private function handleVoiceRecording(string $msgId): void
    {
        if (empty($_FILES['voice_file']['name'])) return;

        $file = $_FILES['voice_file'];
        if ($file['error'] !== UPLOAD_ERR_OK) return;
        if ($file['size'] > self::MAX_FILE_SIZE) return;

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION)) ?: 'webm';
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file['tmp_name']);

        if (!is_dir(self::UPLOAD_DIR)) {
            mkdir(self::UPLOAD_DIR, 0755, true);
        }

        $newName = generateId('VOX') . '.' . $ext;
        $dest = self::UPLOAD_DIR . '/' . $newName;
        if (move_uploaded_file($file['tmp_name'], $dest)) {
            $duration = (int)($_POST['voice_duration'] ?? 0) ?: null;
            $this->attachments->create([
                'MessageID' => $msgId,
                'FileName'  => 'Voice Message.' . $ext,
                'FilePath'  => 'uploads/messages/' . $newName,
                'FileType'  => 'voice',
                'FileSize'  => $file['size'],
                'MimeType'  => $mime ?: 'audio/webm',
                'Duration'  => $duration,
            ]);
        }
    }

    public function unreadCount(): void
    {
        header('Content-Type: application/json');
        $userId = $_SESSION['user_id'] ?? '';
        $count = $userId ? $this->messages->getUnreadCount($userId) : 0;
        echo json_encode(['count' => $count]);
        exit;
    }

    public function unreadPoll(): void
    {
        $userId = $_SESSION['user_id'] ?? '';
        if (!$userId) { echo json_encode([]); exit; }
        header('Content-Type: application/json');
        echo json_encode($this->messages->getUnreadMessages($userId));
        exit;
    }
}
