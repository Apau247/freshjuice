<?php $pageTitle = 'Accounting Calculator'; ?>
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <h5 class="fw-bold mb-0"><i class="bi bi-calculator me-2"></i>Accounting Calculator</h5>
    <a href="?route=accounting" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back to Accounting</a>
</div>

<div class="row g-3">
    <!-- Calculator -->
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-3">
                <!-- Display -->
                <div class="bg-dark text-end rounded-3 p-3 mb-3" style="min-height:100px;">
                    <div id="calcHistory" class="text-secondary" style="font-size:.78rem; min-height:1.2em; word-break:break-all;">&nbsp;</div>
                    <div id="calcDisplay" class="text-white fw-bold" style="font-size:2rem; line-height:1.2; word-break:break-all;">0</div>
                </div>

                <!-- Memory indicator -->
                <div class="d-flex justify-content-between align-items-center mb-2" style="font-size:.75rem;">
                    <div class="text-muted">
                        <span id="memIndicator" class="badge bg-secondary d-none">M</span>
                        <span id="memValue" class="ms-1"></span>
                    </div>
                    <div class="text-muted">
                        <button class="btn btn-link btn-sm text-muted p-0 me-2" onclick="clearHistory()" title="Clear history"><i class="bi bi-clock-history"></i> Clear Log</button>
                    </div>
                </div>

                <!-- Memory & Clear row -->
                <div class="row g-1 mb-1">
                    <div class="col"><button class="btn btn-outline-secondary btn-sm w-100" onclick="memClear()">MC</button></div>
                    <div class="col"><button class="btn btn-outline-secondary btn-sm w-100" onclick="memRecall()">MR</button></div>
                    <div class="col"><button class="btn btn-outline-secondary btn-sm w-100" onclick="memAdd()">M+</button></div>
                    <div class="col"><button class="btn btn-outline-secondary btn-sm w-100" onclick="memSub()">M−</button></div>
                    <div class="col"><button class="btn btn-outline-secondary btn-sm w-100" onclick="memStore()">MS</button></div>
                </div>

                <!-- Main buttons -->
                <div class="row g-1 mb-1">
                    <div class="col"><button class="btn btn-outline-danger btn-sm w-100 py-2" onclick="calcClear()">AC</button></div>
                    <div class="col"><button class="btn btn-outline-danger btn-sm w-100 py-2" onclick="calcBackspace()">⌫</button></div>
                    <div class="col"><button class="btn btn-outline-secondary btn-sm w-100 py-2" onclick="calcInput('%')">%</button></div>
                    <div class="col"><button class="btn btn-primary btn-sm w-100 py-2" onclick="calcInput('÷')">÷</button></div>
                </div>
                <div class="row g-1 mb-1">
                    <div class="col"><button class="btn btn-outline-dark btn-sm w-100 py-2" onclick="calcInput('7')">7</button></div>
                    <div class="col"><button class="btn btn-outline-dark btn-sm w-100 py-2" onclick="calcInput('8')">8</button></div>
                    <div class="col"><button class="btn btn-outline-dark btn-sm w-100 py-2" onclick="calcInput('9')">9</button></div>
                    <div class="col"><button class="btn btn-primary btn-sm w-100 py-2" onclick="calcInput('×')">×</button></div>
                </div>
                <div class="row g-1 mb-1">
                    <div class="col"><button class="btn btn-outline-dark btn-sm w-100 py-2" onclick="calcInput('4')">4</button></div>
                    <div class="col"><button class="btn btn-outline-dark btn-sm w-100 py-2" onclick="calcInput('5')">5</button></div>
                    <div class="col"><button class="btn btn-outline-dark btn-sm w-100 py-2" onclick="calcInput('6')">6</button></div>
                    <div class="col"><button class="btn btn-primary btn-sm w-100 py-2" onclick="calcInput('−')">−</button></div>
                </div>
                <div class="row g-1 mb-1">
                    <div class="col"><button class="btn btn-outline-dark btn-sm w-100 py-2" onclick="calcInput('1')">1</button></div>
                    <div class="col"><button class="btn btn-outline-dark btn-sm w-100 py-2" onclick="calcInput('2')">2</button></div>
                    <div class="col"><button class="btn btn-outline-dark btn-sm w-100 py-2" onclick="calcInput('3')">3</button></div>
                    <div class="col"><button class="btn btn-primary btn-sm w-100 py-2" onclick="calcInput('+')">+</button></div>
                </div>
                <div class="row g-1">
                    <div class="col"><button class="btn btn-outline-dark btn-sm w-100 py-2" onclick="calcInput('±')">±</button></div>
                    <div class="col"><button class="btn btn-outline-dark btn-sm w-100 py-2" onclick="calcInput('0')">0</button></div>
                    <div class="col"><button class="btn btn-outline-dark btn-sm w-100 py-2" onclick="calcInput('.')">.</button></div>
                    <div class="col"><button class="btn btn-success btn-sm w-100 py-2 fw-bold" onclick="calcEquals()">=</button></div>
                </div>

                <!-- Accounting shortcuts -->
                <hr class="my-2">
                <div class="d-flex flex-wrap gap-1">
                    <button class="btn btn-outline-success btn-sm" onclick="quickVAT()"><i class="bi bi-percent me-1"></i>VAT 15%</button>
                    <button class="btn btn-outline-success btn-sm" onclick="quickNet()"><i class="bi bi-arrow-down me-1"></i>Net from Gross</button>
                    <button class="btn btn-outline-success btn-sm" onclick="quickMarkup()"><i class="bi bi-arrow-up me-1"></i>Markup %</button>
                    <button class="btn btn-outline-success btn-sm" onclick="quickDiscount()"><i class="bi bi-tag me-1"></i>Discount %</button>
                    <button class="btn btn-outline-success btn-sm" onclick="quickSplit()"><i class="bi bi-collection me-1"></i>Split Evenly</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Right panel: History + Quick tools -->
    <div class="col-lg-7">
        <!-- Quick VAT Tool -->
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body">
                <h6 class="fw-bold mb-3"><i class="bi bi-percent me-1"></i>VAT / Tax Quick Calculator</h6>
                <div class="row g-2 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label" style="font-size:.78rem;">Amount (GH₵)</label>
                        <input type="number" id="vatAmount" class="form-control form-control-sm" placeholder="0.00" step="0.01" oninput="calcVAT()">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" style="font-size:.78rem;">Tax Rate (%)</label>
                        <input type="number" id="vatRate" class="form-control form-control-sm" value="15" step="0.1" oninput="calcVAT()">
                    </div>
                    <div class="col-md-5">
                        <div class="table-responsive">
                            <table class="table table-sm mb-0" style="font-size:.85rem;">
                                <tr><td class="text-muted">Tax Amount</td><td class="fw-bold text-danger text-end" id="vatTax">GH₵ 0.00</td></tr>
                                <tr><td class="text-muted">Net Amount</td><td class="fw-bold text-end" id="vatNet">GH₵ 0.00</td></tr>
                                <tr><td class="text-muted">Gross (incl. tax)</td><td class="fw-bold text-success text-end" id="vatGross">GH₵ 0.00</td></tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Markup / Discount -->
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body">
                <h6 class="fw-bold mb-3"><i class="bi bi-graph-up me-1"></i>Markup & Discount</h6>
                <div class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label" style="font-size:.78rem;">Base Price (GH₵)</label>
                        <input type="number" id="basePrice" class="form-control form-control-sm" placeholder="0.00" step="0.01" oninput="calcMarkup()">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" style="font-size:.78rem;">Percentage (%)</label>
                        <input type="number" id="markupPct" class="form-control form-control-sm" value="0" step="0.1" oninput="calcMarkup()">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" style="font-size:.78rem;">Mode</label>
                        <select id="markupMode" class="form-select form-select-sm" onchange="calcMarkup()">
                            <option value="markup">Markup ↑</option>
                            <option value="discount">Discount ↓</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <div class="table-responsive">
                            <table class="table table-sm mb-0" style="font-size:.85rem;">
                                <tr><td class="text-muted">Adjustment</td><td class="fw-bold text-end" id="mdAdj">GH₵ 0.00</td></tr>
                                <tr><td class="text-muted">Final Price</td><td class="fw-bold text-success text-end" id="mdFinal">GH₵ 0.00</td></tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Split / Divide -->
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body">
                <h6 class="fw-bold mb-3"><i class="bi bi-collection me-1"></i>Split Evenly</h6>
                <div class="row g-2 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label" style="font-size:.78rem;">Total Amount (GH₵)</label>
                        <input type="number" id="splitAmount" class="form-control form-control-sm" placeholder="0.00" step="0.01" oninput="calcSplit()">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" style="font-size:.78rem;">Split Into</label>
                        <input type="number" id="splitParts" class="form-control form-control-sm" value="2" min="1" oninput="calcSplit()">
                    </div>
                    <div class="col-md-5">
                        <div class="table-responsive">
                            <table class="table table-sm mb-0" style="font-size:.85rem;">
                                <tr><td class="text-muted">Each Share</td><td class="fw-bold text-success text-end" id="splitEach">GH₵ 0.00</td></tr>
                                <tr><td class="text-muted">Remainder</td><td class="fw-bold text-end" id="splitRemain">GH₵ 0.00</td></tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Calculation History -->
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h6 class="fw-bold mb-2"><i class="bi bi-clock-history me-1"></i>Calculation History</h6>
                <div id="calcLogList" style="max-height:250px; overflow-y:auto; font-size:.85rem;">
                    <p class="text-muted mb-0">No calculations yet.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// ── State ──
let current = '0', previous = '', operator = '', newNumber = true, memory = 0, hasMemory = false;
let calcLog = JSON.parse(localStorage.getItem('acctCalcLog') || '[]');

function fmt(n) { return 'GH₵ ' + Number(n).toLocaleString('en-GH', { minimumFractionDigits: 2, maximumFractionDigits: 2 }); }

// ── Display ──
function updateDisplay() {
    document.getElementById('calcDisplay').textContent = current;
    document.getElementById('memIndicator').classList.toggle('d-none', !hasMemory);
    document.getElementById('memValue').textContent = hasMemory ? fmt(memory) : '';
}

// ── Input ──
function calcInput(val) {
    if (val === '±') { current = current.startsWith('-') ? current.slice(1) : '-' + current; updateDisplay(); return; }
    if ('0123456789.'.includes(val)) {
        if (newNumber) { current = val === '.' ? '0.' : val; newNumber = false; }
        else {
            if (val === '.' && current.includes('.')) return;
            current += val;
        }
    } else {
        calcEquals();
        previous = current;
        operator = val;
        newNumber = true;
        document.getElementById('calcHistory').textContent = previous + ' ' + val;
    }
    updateDisplay();
}

function calcEquals() {
    if (!operator || previous === '') return;
    const a = parseFloat(previous), b = parseFloat(current);
    let result;
    switch (operator) {
        case '+': result = a + b; break;
        case '−': result = a - b; break;
        case '×': result = a * b; break;
        case '÷': result = b !== 0 ? a / b : 'Error'; break;
        case '%': result = a * (b / 100); break;
        default: return;
    }
    const expr = previous + ' ' + operator + ' ' + current + ' = ' + (typeof result === 'number' ? result : result);
    document.getElementById('calcHistory').textContent = expr;
    addLog(expr);
    current = typeof result === 'number' ? String(Math.round(result * 1e10) / 1e10) : 'Error';
    previous = '';
    operator = '';
    newNumber = true;
    updateDisplay();
}

function calcClear() { current = '0'; previous = ''; operator = ''; newNumber = true; document.getElementById('calcHistory').innerHTML = '&nbsp;'; updateDisplay(); }
function calcBackspace() { if (!newNumber && current.length > 1) current = current.slice(0, -1); else current = '0'; updateDisplay(); }

// ── Memory ──
function memClear() { memory = 0; hasMemory = false; updateDisplay(); }
function memRecall() { if (hasMemory) { current = String(memory); newNumber = true; updateDisplay(); } }
function memAdd() { memory += parseFloat(current) || 0; hasMemory = true; updateDisplay(); }
function memSub() { memory -= parseFloat(current) || 0; hasMemory = true; updateDisplay(); }
function memStore() { memory = parseFloat(current) || 0; hasMemory = true; updateDisplay(); }

// ── History ──
function addLog(entry) {
    calcLog.unshift({ time: new Date().toLocaleTimeString(), expr: entry });
    if (calcLog.length > 50) calcLog.pop();
    localStorage.setItem('acctCalcLog', JSON.stringify(calcLog));
    renderLog();
}
function clearHistory() { calcLog = []; localStorage.removeItem('acctCalcLog'); renderLog(); }
function renderLog() {
    const el = document.getElementById('calcLogList');
    if (!calcLog.length) { el.innerHTML = '<p class="text-muted mb-0">No calculations yet.</p>'; return; }
    el.innerHTML = calcLog.map(h => '<div class="d-flex justify-content-between border-bottom py-1"><span class="text-muted">' + h.time + '</span><span class="fw-semibold">' + h.expr + '</span></div>').join('');
}
renderLog();

// ── VAT Calculator ──
function calcVAT() {
    const amt = parseFloat(document.getElementById('vatAmount').value) || 0;
    const rate = parseFloat(document.getElementById('vatRate').value) || 0;
    const tax = amt * (rate / 100);
    const net = amt - tax;
    document.getElementById('vatTax').textContent = fmt(tax);
    document.getElementById('vatNet').textContent = fmt(net);
    document.getElementById('vatGross').textContent = fmt(amt);
}

// ── Markup / Discount ──
function calcMarkup() {
    const base = parseFloat(document.getElementById('basePrice').value) || 0;
    const pct = parseFloat(document.getElementById('markupPct').value) || 0;
    const mode = document.getElementById('markupMode').value;
    const adj = base * (pct / 100);
    const final_ = mode === 'markup' ? base + adj : base - adj;
    document.getElementById('mdAdj').textContent = fmt(adj);
    document.getElementById('mdFinal').textContent = fmt(final_);
}

// ── Split ──
function calcSplit() {
    const total = parseFloat(document.getElementById('splitAmount').value) || 0;
    const parts = parseInt(document.getElementById('splitParts').value) || 1;
    const each = parts > 0 ? Math.floor((total / parts) * 100) / 100 : 0;
    const remain = total - (each * parts);
    document.getElementById('splitEach').textContent = fmt(each);
    document.getElementById('splitRemain').textContent = fmt(Math.round(remain * 100) / 100);
}

// ── Quick Accounting Shortcuts (use the main calculator) ──
function quickVAT() {
    const val = parseFloat(current) || 0;
    const tax = val * 0.15;
    const result = val + tax;
    document.getElementById('calcHistory').textContent = current + ' + 15% VAT';
    addLog(current + ' + 15% VAT = ' + result);
    current = String(Math.round(result * 1e10) / 1e10);
    newNumber = true;
    updateDisplay();
}
function quickNet() {
    const val = parseFloat(current) || 0;
    const net = val / 1.15;
    document.getElementById('calcHistory').textContent = current + ' net from gross (15% VAT)';
    addLog(current + ' net from gross = ' + Math.round(net * 1e10) / 1e10);
    current = String(Math.round(net * 1e10) / 1e10);
    newNumber = true;
    updateDisplay();
}
function quickMarkup() {
    const val = parseFloat(current) || 0;
    const pct = prompt('Markup percentage:', '20');
    if (pct === null) return;
    const result = val * (1 + parseFloat(pct) / 100);
    document.getElementById('calcHistory').textContent = current + ' + ' + pct + '% markup';
    addLog(current + ' + ' + pct + '% markup = ' + Math.round(result * 1e10) / 1e10);
    current = String(Math.round(result * 1e10) / 1e10);
    newNumber = true;
    updateDisplay();
}
function quickDiscount() {
    const val = parseFloat(current) || 0;
    const pct = prompt('Discount percentage:', '10');
    if (pct === null) return;
    const result = val * (1 - parseFloat(pct) / 100);
    document.getElementById('calcHistory').textContent = current + ' − ' + pct + '% discount';
    addLog(current + ' − ' + pct + '% discount = ' + Math.round(result * 1e10) / 1e10);
    current = String(Math.round(result * 1e10) / 1e10);
    newNumber = true;
    updateDisplay();
}
function quickSplit() {
    const val = parseFloat(current) || 0;
    const parts = prompt('Split into how many parts?', '2');
    if (parts === null || parseInt(parts) < 1) return;
    const each = Math.floor((val / parseInt(parts)) * 100) / 100;
    document.getElementById('calcHistory').textContent = current + ' ÷ ' + parts + ' parts';
    addLog(current + ' ÷ ' + parts + ' parts = ' + each + ' each');
    current = String(each);
    newNumber = true;
    updateDisplay();
}

// ── Keyboard support ──
document.addEventListener('keydown', function(e) {
    if (e.target.tagName === 'INPUT' || e.target.tagName === 'SELECT') return;
    if ('0123456789'.includes(e.key)) calcInput(e.key);
    else if (e.key === '.') calcInput('.');
    else if (e.key === '+') calcInput('+');
    else if (e.key === '-') calcInput('−');
    else if (e.key === '*') calcInput('×');
    else if (e.key === '/') { e.preventDefault(); calcInput('÷'); }
    else if (e.key === '%') calcInput('%');
    else if (e.key === 'Enter' || e.key === '=') calcEquals();
    else if (e.key === 'Escape') calcClear();
    else if (e.key === 'Backspace') calcBackspace();
});
</script>
