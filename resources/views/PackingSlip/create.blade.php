@extends('layouts.fullscreen')

@section('content')
<style>
    .cs-hero {
        background: linear-gradient(135deg, #9333ea 0%, #7c3aed 100%);
        border-radius: 14px;
        padding: 20px 24px;
        color: #fff;
        margin-bottom: 20px;
        position: relative;
        overflow: hidden;
    }

    .cs-hero::before {
        content: '';
        position: absolute;
        top: -30px;
        right: -30px;
        width: 120px;
        height: 120px;
        background: rgba(255, 255, 255, .07);
        border-radius: 50%;
    }

    .cs-hero h4 {
        font-size: 18px;
        font-weight: 800;
        margin: 0 0 4px;
    }

    .cs-hero .sub {
        font-size: 12px;
        opacity: .8;
    }

    .frm-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, .06);
        overflow: hidden;
    }

    .frm-card-body {
        padding: 24px;
    }

    .frm-card-body .control-label {
        font-size: 12px;
        font-weight: 700;
        color: #374151;
        margin-bottom: 4px;
    }

    .frm-card-body .form-control {
        border-radius: 8px;
        border-color: #e2e8f0;
        min-height: 42px;
        font-size: 13px;
    }

    .frm-card-body .form-control:focus {
        border-color: #9333ea;
        box-shadow: 0 0 0 2px rgba(147, 51, 234, .15);
    }

    .entry-section {
        background: #fafbff;
        border: 1px solid #e8ecf4;
        border-radius: 10px;
        overflow: hidden;
        transition: box-shadow .3s, border-color .3s;
    }

    .entry-section.highlight-new {
        box-shadow: 0 0 0 3px #9333ea, 0 0 20px rgba(147, 51, 234, .25);
        border-color: #9333ea;
    }

    .entry-section-header {
        background: linear-gradient(135deg, #9333ea 0%, #7c3aed 100%);
        color: #fff;
        padding: 6px 10px;
        font-size: 11px;
        font-weight: 900;
        text-align: center;
    }

    .entry-table {
        width: 100%;
        border-collapse: collapse;
    }

    .entry-table th {
        background: #f0f2f7;
        color: #14213d;
        font-weight: 800;
        font-size: 10px;
        text-transform: uppercase;
        letter-spacing: .3px;
        padding: 4px 6px;
        border-bottom: 2px solid #dde1ea;
        text-align: center;
    }

    .entry-table td {
        padding: 2px;
        border-bottom: 1px solid #f0f2f7;
    }

    .entry-table .sno-cell {
        text-align: center;
        font-weight: 700;
        color: #374151;
        font-size: 15px;
        width: 30px;
    }

    .entry-table input[type="number"] {
        width: 90%;
        border: 1px solid #e2e8f0;
        border-radius: 4px;
        padding: 1px 2px;
        font-size: 15px;
        min-height: 18px;
        text-align: center;
        outline: none;
        transition: border-color .15s;
        -moz-appearance: textfield;
    }

    .entry-table input[type="number"]::-webkit-outer-spin-button,
    .entry-table input[type="number"]::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    .entry-table input[type="number"]:focus {
        border-color: #9333ea;
        box-shadow: 0 0 0 2px rgba(147, 51, 234, .12);
    }

    .total-row td {
        font-weight: 900;
        font-size: 11px;
        background: #f8fafc;
        border-top: 2px solid #9333ea;
        padding: 4px 6px;
    }

    .total-row .total-val {
        color: #9333ea;
        text-align: right;
        padding-right: 6px;
        font-weight: 900;
    }

    .entry-section-footer {
        padding: 4px 10px;
        background: #f8fafc;
        border-top: 1px solid #e8ecf4;
        font-size: 12px;
        color: #6b7280;
    }

    .bale-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 12px;
        margin-bottom: 16px;
    }
</style>

<div style="padding:24px;">

    <div class="cs-hero">
        <div class="row align-items-center">
            <div class="col-md-6" style="position:relative;z-index:1;">
                <h4><i class="ti-plus mr-2"></i>{{ isset($slip) ? 'Edit' : 'Add' }} Packing Slip</h4>
                <div class="sub">{{ isset($slip) ? 'Update' : 'Create a new' }} packing slip with bale entry</div>
            </div>
            <div class="col-md-6 text-right" style="position:relative;z-index:1;">
                <a href="{{ route('packing-slip.index') }}" class="btn btn-sm" style="border-radius:8px;background:rgba(255,255,255,.2);color:#fff;border:1px solid rgba(255,255,255,.3);padding:7px 16px;font-weight:600;">
                    <i class="ti-arrow-left mr-1"></i> Back
                </a>
            </div>
        </div>
    </div>

    <div class="frm-card">
        <div class="frm-card-body">
            <form method="POST" action="{{ route('packing-slip.store') }}" id="slipForm" onsubmit="return false;">
                @csrf
                <input type="hidden" name="id" id="slipId" value="{{ isset($slip) ? $slip->id : '' }}">

                <div class="row" style="margin-bottom:4px;">
                    <div class="col-md-3 form-group">
                        <label class="control-label">Bill No</label>
                        <input type="text" name="bill_no" class="form-control" placeholder="Bill number" value="{{ isset($slip) ? $slip->bill_no : '' }}">
                    </div>
                    <div class="col-md-3 form-group">
                        <label class="control-label">Lot No</label>
                        <input type="text" name="lot_no" class="form-control" placeholder="Lot number" value="{{ isset($slip) ? $slip->lot_no : '' }}">
                    </div>
                    <div class="col-md-3 form-group">
                        <label class="control-label">Date <span style="color:#dc2626;">*</span></label>
                        <input type="date" name="slip_date" class="form-control" required value="{{ isset($slip) ? $slip->slip_date?->format('Y-m-d') : date('Y-m-d') }}">
                    </div>
                    <div class="col-md-3 form-group">
                        <label class="control-label">Customer (TO) <span style="color:#dc2626;">*</span></label>
                        <select name="pack_customer_id" class="form-control select2" required>
                            <option value="">Select Customer</option>
                            @foreach($customers as $c)
                            <option value="{{ $c->id }}" {{ isset($slip) && $slip->pack_customer_id == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row" style="margin-bottom:4px;">
                    <div class="col-md-3 form-group">
                        <label class="control-label">Quality</label>
                        <select name="quality" class="form-control select2">
                            <option value="">Select Quality</option>
                            @foreach($qualities as $q)
                            <option value="{{ $q->name }}" {{ isset($slip) && $slip->quality == $q->name ? 'selected' : '' }}>{{ $q->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 form-group">
                        <label class="control-label">BALE NO'S</label>
                        <input type="text" id="summaryBaleNos" class="form-control" readonly style="background:#f0f2f7;font-weight:700;color:#9333ea;cursor:default;">
                    </div>
                    <div class="col-md-3 form-group">
                        <label class="control-label">NO OF BALE</label>
                        <input type="text" id="summaryNoOfBale" class="form-control" readonly style="background:#f0f2f7;font-weight:700;color:#1a2340;cursor:default;">
                    </div>
                    <div class="col-md-3 form-group">
                        <label class="control-label">TOTAL METER</label>
                        <input type="text" id="summaryTotalMeter" class="form-control" readonly style="background:#f0f2f7;font-weight:700;color:#16a34a;cursor:default;">
                    </div>
                </div>

                <hr style="border-color:#e8ecf4;margin:16px 0 20px;">

                <h6 style="font-weight:800;color:#1a2340;margin:0 0 12px;"><i class="ti-layout-grid2-alt mr-2" style="color:#9333ea;"></i>Bale Entry</h6>

                <div id="baleEntryContainer" class="bale-grid">
                </div>

                <div class="entry-section-footer" style="margin-bottom:8px;text-align:center;"><kbd>Enter</kbd> save &middot; <kbd>Ctrl</kbd> new section</div>

                <div style="margin-bottom:16px;">
                    <button type="button" id="addSectionBtn" class="btn btn-sm" style="border-radius:8px;background:#eef2ff;color:#4338ca;border:none;padding:7px 16px;font-weight:700;font-size:12px;">
                        <i class="ti-plus mr-1"></i> Add Section (Ctrl)
                    </button>
                </div>

                <div class="row">
                    <div class="col-md-12 form-group">
                        <label class="control-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="Optional notes..."></textarea>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-12">
                        <button type="button" id="closeBtn" class="btn btn-light" style="border-radius:8px;padding:8px 20px;font-weight:600;">Close</button>
                        <button type="submit" class="btn" style="border-radius:8px;background:#9333ea;color:#fff;border:none;padding:8px 20px;font-weight:600;margin-left:8px;">
                            <i class="ti-save mr-1"></i> Save Slip
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    var ROWS_PER_SECTION = 10;
    var sectionCount = 0;
    var saved = false;

    function createSection(baleNo) {
        sectionCount++;
        var secId = 'section-' + sectionCount;
        var html = '<div class="entry-section" id="' + secId + '" data-section="' + sectionCount + '" data-bale-no="' + baleNo + '">';
        html += '<div class="entry-section-header">';
        html += '<span>BALE NO : ' + baleNo + '</span>';
        html += '</div>';
        html += '<table class="entry-table">';
        html += '<thead><tr><th style="width:40px;">S.No</th><th>Meter</th><th>Weight</th></tr></thead><tbody>';

        for (var i = 0; i < ROWS_PER_SECTION; i++) {
            var sNo = i + 1;
            html += '<tr>';
            html += '<td class="sno-cell">' + sNo + '</td>';
            html += '<td><input type="hidden" name="items[' + (sectionCount * ROWS_PER_SECTION + i) + '][bale_no]" value="' + baleNo + '">';
            html += '<input type="hidden" name="items[' + (sectionCount * ROWS_PER_SECTION + i) + '][s_no]" value="' + sNo + '">';
            html += '<input type="number" step="0.01" name="items[' + (sectionCount * ROWS_PER_SECTION + i) + '][meter]" class="meter-input form-control" placeholder="0.00" oninput="calcTotals()" onfocus="this.select()" onblur="formatMeter(this)"></td>';
            html += '<td><input type="number" step="0.01" name="items[' + (sectionCount * ROWS_PER_SECTION + i) + '][weight]" class="weight-input form-control" placeholder="0.00" oninput="calcTotals()" onfocus="this.select()"></td>';
            html += '</tr>';
        }

        html += '</tbody><tfoot>';
        html += '<tr class="total-row"><td style="text-align:center;">TOTAL</td><td class="total-val sec-meter-total">0.00</td><td class="total-val sec-weight-total">0.00</td></tr>';
        html += '</tfoot></table>';
        html += '</div>';

        document.getElementById('baleEntryContainer').insertAdjacentHTML('beforeend', html);
        calcTotals();

        setTimeout(function() {
            var sec = document.getElementById(secId);
            if (!sec) return;
            var firstMeter = sec.querySelector('.meter-input');
            if (firstMeter) firstMeter.focus();
            sec.classList.add('highlight-new');
            setTimeout(function() {
                sec.classList.remove('highlight-new');
            }, 1200);
            var rect = sec.getBoundingClientRect();
            if (rect.bottom > window.innerHeight) {
                sec.scrollIntoView({
                    behavior: 'smooth',
                    block: 'nearest'
                });
            }
        }, 100);
    }

    function formatMeter(el) {
        var val = parseFloat(el.value);
        if (!isNaN(val)) {
            el.value = val.toFixed(2);
        } else {
            el.value = '';
        }
    }

    function calcTotals() {
        var sections = document.querySelectorAll('.entry-section');
        var allMeters = 0,
            firstBale = null,
            lastBale = null;
        sections.forEach(function(sec) {
            var meters = sec.querySelectorAll('.meter-input');
            var mTotal = 0,
                wTotal = 0;
            meters.forEach(function(inp) {
                mTotal += parseFloat(inp.value) || 0;
            });
            sec.querySelectorAll('.weight-input').forEach(function(inp) {
                wTotal += parseFloat(inp.value) || 0;
            });
            sec.querySelector('.sec-meter-total').textContent = mTotal.toFixed(2);
            sec.querySelector('.sec-weight-total').textContent = wTotal.toFixed(2);
            allMeters += mTotal;
            var secBaleNo = parseInt(sec.dataset.baleNo);
            if (firstBale === null || secBaleNo < firstBale) firstBale = secBaleNo;
            if (lastBale === null || secBaleNo > lastBale) lastBale = secBaleNo;
        });
        document.getElementById('summaryBaleNos').value = (firstBale !== null && lastBale !== null) ? firstBale + ' - ' + lastBale : '';
        document.getElementById('summaryNoOfBale').value = sections.length;
        document.getElementById('summaryTotalMeter').value = allMeters.toFixed(2);
    }

    function addNewSection() {
        var maxBale = 0;
        document.querySelectorAll('.entry-section').forEach(function(sec) {
            var bn = parseInt(sec.dataset.baleNo);
            if (bn > maxBale) maxBale = bn;
        });
        createSection(maxBale + 1);
    }

    function ajaxSave(callback) {
        if (window._saving) return;
        window._saving = true;

        var form = document.getElementById('slipForm');
        if (!form) {
            console.log('slipForm not found');
            return;
        }

        var meterInputs = document.querySelectorAll('.meter-input');
        var items = [];
        meterInputs.forEach(function(inp) {
            var row = inp.closest('tr');
            var baleInput = row.querySelector('input[name*="[bale_no]"]');
            var sNoInput = row.querySelector('input[name*="[s_no]"]');
            var weightInput = row.querySelector('.weight-input');
            items.push({
                bale_no: baleInput ? parseInt(baleInput.value) : 0,
                s_no: sNoInput ? parseInt(sNoInput.value) : 0,
                meter: parseFloat(inp.value) || 0,
                weight: parseFloat(weightInput ? weightInput.value : 0) || 0
            });
        });

        var formData = new FormData(form);
        // Remove old item entries from hidden inputs to avoid duplicates
        var itemKeys = [];
        formData.forEach(function(value, key) {
            if (key.indexOf('items[') === 0) itemKeys.push(key);
        });
        itemKeys.forEach(function(k) {
            formData.delete(k);
        });
        // Append re-indexed items
        items.forEach(function(item, i) {
            formData.append('items[' + i + '][bale_no]', item.bale_no);
            formData.append('items[' + i + '][s_no]', item.s_no);
            formData.append('items[' + i + '][meter]', item.meter);
            formData.append('items[' + i + '][weight]', item.weight);
        });

        var btn = form.querySelector('button[type="submit"]');
        btn.disabled = true;
        btn.innerHTML = '<i class="ti-save mr-1"></i> Saving...';

        fetch('{{ route("packing-slip.store") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(function(r) {
                if (!r.ok) throw new Error('HTTP ' + r.status);
                return r.json();
            })
            .then(function(d) {
                window._saving = false;
                btn.disabled = false;
                btn.innerHTML = '<i class="ti-save mr-1"></i> Save Slip';
                if (d.id) {
                    document.getElementById('slipId').value = d.id;
                    saved = true;
                }
                if (typeof callback === 'function') callback(d);
            })
            .catch(function(err) {
                window._saving = false;
                btn.disabled = false;
                btn.innerHTML = '<i class="ti-save mr-1"></i> Save Slip';
                console.log('Save error:', err.message);
            });
    }

    var editBaleItems = {!! isset($slip) ? json_encode($slip->baleItems) : 'null' !!};
    var initialBaleNo = {{ $nextBaleNo ?? 1 }};

    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM ready');

        if (editBaleItems && editBaleItems.length) {
            var groups = {};
            editBaleItems.forEach(function(item) {
                if (!groups[item.bale_no]) groups[item.bale_no] = [];
                groups[item.bale_no].push(item);
            });
            var baleNos = Object.keys(groups).map(Number).sort(function(a, b) {
                return a - b;
            });
            baleNos.forEach(function(bn) {
                createSection(bn);
                var sec = document.querySelector('.entry-section:last-child');
                groups[bn].forEach(function(item) {
                    var row = sec.querySelector('tbody tr:nth-child(' + item.s_no + ')');
                    if (row) {
                        var meterInput = row.querySelector('.meter-input');
                        var weightInput = row.querySelector('.weight-input');
                        if (meterInput) {
                            meterInput.value = item.meter;
                            formatMeter(meterInput);
                        }
                        if (weightInput) weightInput.value = item.weight;
                    }
                });
            });
            calcTotals();
        } else {
            createSection(initialBaleNo);
        }

        document.getElementById('addSectionBtn').addEventListener('click', addNewSection);

        document.getElementById('closeBtn').addEventListener('click', function() {
            if (saved || document.getElementById('slipId').value) {
                toastr.success('All details saved successfully');
                setTimeout(function() {
                    window.location.href = '{{ route("packing-slip.index") }}';
                }, 500);
            } else {
                window.location.href = '{{ route("packing-slip.index") }}';
            }
        });

        document.getElementById('slipForm').addEventListener('submit', function(e) {
            console.log('submit event triggered');
            ajaxSave();
        });
    });

    // Keyboard shortcuts (at document level, outside DOMContentLoaded)
    var ctrlPressed = false;
    var ctrlUsedWithOtherKey = false;

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Control') {
            ctrlPressed = true;
            ctrlUsedWithOtherKey = false;
            setTimeout(function() {
                ctrlPressed = false;
            }, 500);
        } else if (ctrlPressed) {
            ctrlUsedWithOtherKey = true;
            ctrlPressed = false;
        }

        if (e.ctrlKey && e.key === 'Enter') {
            console.log('Ctrl+Enter');
            e.preventDefault();
            ajaxSave(function() {
                toastr.success('Packing slip saved successfully.', 'Success');
            });
            return;
        }

        // Enter in bale grid → next input
        if (e.key === 'Enter' && (e.target.closest('.meter-input') || e.target.closest('.weight-input'))) {
            console.log('Enter in bale grid - next input');
            e.preventDefault();
            var inputs = document.querySelectorAll('.meter-input, .weight-input');
            for (var i = 0; i < inputs.length; i++) {
                if (inputs[i] === e.target) {
                    var next = inputs[i + 1];
                    if (next) {
                        next.focus();
                        next.select();
                    }
                    break;
                }
            }
        }
    });

    document.addEventListener('keyup', function(e) {
        if (e.key === 'Control' && ctrlPressed && !ctrlUsedWithOtherKey) {
            e.preventDefault();
            addNewSection();
        }
        if (e.key === 'Control') ctrlPressed = false;
    });
</script>
@endpush