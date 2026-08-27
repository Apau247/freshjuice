# Opens FreshJuice in the default web browser, fullscreen when supported.
param(
    [string]$Url = 'http://localhost/freshjuice/public/'
)

$cmd = $null
try {
    # Which browser owns the http protocol for this user?
    $userChoice = 'HKCU:\SOFTWARE\Microsoft\Windows\Shell\Associations\UrlAssociations\http\UserChoice'
    $progId = (Get-ItemProperty -Path $userChoice -ErrorAction Stop).ProgId
    $cmd = (Get-ItemProperty -Path "Registry::HKEY_CLASSES_ROOT\$progId\shell\open\command" -ErrorAction Stop).'(default)'
} catch { $cmd = $null }

# Extract the executable path from e.g.  "C:\...\msedge.exe" --single-instance %1
$exe = $null
if ($cmd) {
    if ($cmd.StartsWith('"')) {
        $m = [regex]::Match($cmd, '^"([^"]+)"')
        if ($m.Success) { $exe = $m.Groups[1].Value }
    } elseif ($cmd.Contains(' ')) {
        $exe = ($cmd -split '\s+', 2)[0]
    } else {
        $exe = $cmd
    }
}

$name = ''
if ($exe) {
    try { $name = [System.IO.Path]::GetFileNameWithoutExtension($exe).ToLowerInvariant() } catch { $name = '' }
}

# Chromium browsers honour --start-fullscreen (true F11-style kiosk view).
$chromium = @('chrome', 'msedge', 'brave', 'opera', 'vivaldi', 'chromium')
if ($name -and ($chromium -contains $name) -and (Test-Path $exe)) {
    Start-Process -FilePath $exe -ArgumentList @('--start-fullscreen', $Url)
} else {
    # Unknown/Firefox-like default: just hand off the URL to the shell.
    Start-Process $Url
}
