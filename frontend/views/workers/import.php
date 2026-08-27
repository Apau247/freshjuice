<?php $pageTitle = 'Import Workers from Excel'; ?>
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <h5 class="fw-bold mb-0"><i class="bi bi-file-earmark-excel me-2"></i>Import Workers from Excel</h5>
    <a href="?route=workers" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
</div>

<div class="card border-0 shadow-sm mb-3">
    <div class="card-body">
        <h6 class="fw-bold mb-2"><i class="bi bi-info-circle me-1"></i>How it works</h6>
        <ol class="mb-2" style="font-size:.88rem; color: var(--bs-body-color);">
            <li>Prepare an Excel (.xlsx) or CSV file. <strong>Any column names work</strong> — the system matches common variations (FirstName/First Name/Name, LastName/Last Name/Surname, MonthlyPay/Monthly Pay/Salary, etc.).</li>
            <li>Only a <strong>name column</strong> is required. Missing fields default to sensible values.</li>
            <li>Choose your file below — the data is parsed in your browser (nothing uploaded yet).</li>
            <li>Review the preview, then click <strong>Import</strong> to save all rows to the system.</li>
        </ol>
        <a href="?route=workers/template" class="btn btn-sm btn-outline-success"><i class="bi bi-download me-1"></i>Download Template (.xlsx)</a>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <div class="row g-3 align-items-end">
            <div class="col-md-6">
                <label class="form-label fw-semibold">Select Excel or CSV file</label>
                <input type="file" id="excelFile" class="form-control" accept=".xlsx,.xls,.csv">
            </div>
            <div class="col-md-3">
                <button class="btn btn-outline-primary" id="parseBtn" disabled>
                    <i class="bi bi-eye me-1"></i>Preview
                </button>
            </div>
        </div>
        <div id="previewArea" class="mt-3" style="display:none;">
            <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
                <h6 class="fw-bold mb-0">Preview — <span id="rowCount">0</span> rows found</h6>
                <button class="btn btn-success btn-sm" id="importBtn" onclick="doImport()">
                    <i class="bi bi-cloud-upload me-1"></i>Import All
                </button>
            </div>
            <div class="table-responsive" style="max-height:400px; overflow-y:auto;">
                <table class="table table-sm table-bordered align-middle mb-0" id="previewTable">
                    <thead class="table-light" id="previewHead"></thead>
                    <tbody id="previewBody"></tbody>
                </table>
            </div>
        </div>
        <div id="importResult" class="mt-3" style="display:none;"></div>
    </div>
</div>

<script src="<?= appBaseUrl() ?>/frontend/assets/js/xlsx.full.min.js?v=1"></script>
<script>
if (typeof XLSX === 'undefined') {
    var s = document.createElement('script');
    s.src = 'https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js';
    s.onload = function() { console.log('XLSX loaded from CDN fallback'); };
    s.onerror = function() {
        console.error('XLSX library could not be loaded from either source');
        document.getElementById('parseBtn').disabled = true;
        document.getElementById('parseBtn').innerHTML = '<i class=\"bi bi-exclamation-triangle me-1\"></i>XLSX library unavailable — use CSV files';
    };
    document.head.appendChild(s);
}
</script>
<script>
let parsedRows = [];

function parseFile() {
    const file = document.getElementById('excelFile').files[0];
    if (!file) return;

    if (file.name.endsWith('.csv') || file.name.endsWith('.txt')) {
        parseCSV(file);
    } else if (typeof XLSX !== 'undefined') {
        parseExcel(file);
    } else {
        alert('The Excel parsing library is still loading or unavailable.\n\nPlease:\n1. Wait a moment and try again, OR\n2. Convert your file to CSV format and re-upload.');
    }
}

function parseCSV(file) {
    const reader = new FileReader();
    reader.onload = function(e) {
        const text = e.target.result;
        const lines = text.split(/\r?\n/).filter(l => l.trim());
        if (lines.length < 2) { alert('No data rows found.'); return; }
        const headers = parseCSVLine(lines[0]);
        parsedRows = [];
        for (let i = 1; i < lines.length; i++) {
            const vals = parseCSVLine(lines[i]);
            const row = {};
            headers.forEach((h, idx) => {
                const key = h.trim().replace(/\s+/g, '_').toLowerCase();
                row[key] = (vals[idx] || '').trim();
            });
            parsedRows.push(row);
        }
        renderPreview();
    };
    reader.readAsText(file);
}

function parseCSVLine(line) {
    const result = [];
    let current = '';
    let inQuotes = false;
    for (let i = 0; i < line.length; i++) {
        const ch = line[i];
        if (inQuotes) {
            if (ch === '"' && line[i+1] === '"') { current += '"'; i++; }
            else if (ch === '"') { inQuotes = false; }
            else { current += ch; }
        } else {
            if (ch === '"') { inQuotes = true; }
            else if (ch === ',') { result.push(current); current = ''; }
            else { current += ch; }
        }
    }
    result.push(current);
    return result;
}

function parseExcel(file) {
    const reader = new FileReader();
    reader.onload = function(e) {
        const wb = XLSX.read(e.target.result, { type: 'array' });
        const ws = wb.Sheets[wb.SheetNames[0]];
        const json = XLSX.utils.sheet_to_json(ws, { defval: '' });
        parsedRows = json.map(r => {
            const row = {};
            Object.keys(r).forEach(k => {
                const key = k.trim().replace(/\s+/g, '_').toLowerCase();
                row[key] = r[k];
            });
            return row;
        });
        renderPreview();
    };
    reader.readAsArrayBuffer(file);
}

function renderPreview() {
    if (!parsedRows.length) { alert('No rows found in file.'); return; }
    document.getElementById('previewArea').style.display = '';
    document.getElementById('rowCount').textContent = parsedRows.length;

    const keys = Object.keys(parsedRows[0]);
    document.getElementById('previewHead').innerHTML = '<tr>' + keys.map(k => '<th>' + escHtml(k) + '</th>').join('') + '</tr>';
    document.getElementById('previewBody').innerHTML = parsedRows.slice(0, 200).map(r =>
        '<tr>' + keys.map(k => '<td>' + escHtml(String(r[k] ?? '')) + '</td>').join('') + '</tr>'
    ).join('');
    document.getElementById('importBtn').disabled = false;
}

function doImport() {
    if (!parsedRows.length) return;
    document.getElementById('importBtn').disabled = true;
    document.getElementById('importBtn').innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Importing…';

    fetch('?route=workers/import-store', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').content },
        body: JSON.stringify(parsedRows)
    }).then(r => r.json()).then(data => {
        if (data.ok) {
            document.getElementById('importResult').style.display = '';
            document.getElementById('importResult').innerHTML =
                '<div class="alert alert-success"><i class="bi bi-check-circle me-1"></i><strong>' + data.imported + ' worker(s) imported successfully!</strong> <a href="?route=workers">View workers &rarr;</a></div>';
            document.getElementById('previewArea').style.display = 'none';
        } else {
            alert('Import failed: ' + (data.error || 'Unknown error'));
            document.getElementById('importBtn').disabled = false;
            document.getElementById('importBtn').innerHTML = '<i class="bi bi-cloud-upload me-1"></i>Import All';
        }
    }).catch(err => {
        alert('Network error: ' + err.message);
        document.getElementById('importBtn').disabled = false;
        document.getElementById('importBtn').innerHTML = '<i class="bi bi-cloud-upload me-1"></i>Import All';
    });
}

function escHtml(s) {
    const d = document.createElement('div');
    d.appendChild(document.createTextNode(s));
    return d.innerHTML;
}

document.getElementById('parseBtn').addEventListener('click', parseFile);
document.getElementById('excelFile').addEventListener('change', function() {
    document.getElementById('parseBtn').disabled = !this.files.length;
    document.getElementById('previewArea').style.display = 'none';
    document.getElementById('importResult').style.display = 'none';
});
</script>
