@extends('layouts.app')

@section('content')
<style>
.ledger-page{background:#f4f6fb;}
.ledger-header{background:linear-gradient(135deg,#f97316 0%,#c2410c 100%);border-radius:14px;padding:20px 24px;color:#fff;margin-bottom:20px;position:relative;overflow:hidden;}
.ledger-header::before{content:'';position:absolute;top:-30px;right:-30px;width:120px;height:120px;background:rgba(255,255,255,.08);border-radius:50%;}
.ledger-header h4{font-size:18px;font-weight:800;margin:0 0 4px;}
.ledger-header .sub{font-size:12px;opacity:.85;}
.rpt-filter{background:#fff;border-radius:12px;padding:10px 16px;box-shadow:0 2px 10px rgba(0,0,0,.06);margin-bottom:16px;display:flex;align-items:center;gap:10px;flex-wrap:wrap;}
.rpt-filter .form-control{min-height:36px;font-size:12px;border-color:#e2e8f0;border-radius:8px;padding:4px 10px;border:1px solid #d1d9e6;}
.rpt-filter .btn{border-radius:8px;padding:7px 16px;font-weight:600;font-size:12px;}

/* ── Folder Grid ── */
.folder-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(190px,1fr));gap:14px;}
.folder{display:flex;flex-direction:column;text-decoration:none;color:inherit;position:relative;}
.folder-tab{display:flex;align-items:center;gap:6px;padding:8px 12px;border-radius:8px 8px 0 0;font-weight:800;font-size:12px;position:relative;z-index:2;margin-bottom:0;letter-spacing:.2px;}
.folder-tab .f-icon{width:24px;height:24px;border-radius:5px;display:flex;align-items:center;justify-content:center;font-size:11px;flex-shrink:0;background:rgba(255,255,255,.25);}
.folder-tab .f-label{white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
.folder-tab .f-badge{margin-left:auto;background:rgba(255,255,255,.25);padding:1px 6px;border-radius:999px;font-size:9px;font-weight:700;white-space:nowrap;min-width:18px;text-align:center;}
.folder-body{background:#fff;border-radius:0 0 10px 10px;padding:12px 14px 14px;border-left:1px solid #edf0f5;border-right:1px solid #edf0f5;border-bottom:1px solid #edf0f5;flex:1;transition:box-shadow .15s;}
.folder:hover .folder-body{box-shadow:0 6px 20px rgba(0,0,0,.06);}
.folder-body .f-count{font-size:10.5px;color:#94a3b8;margin-bottom:2px;letter-spacing:.1px;}
.folder-body .f-amount{font-size:17px;font-weight:800;color:#0f172a;letter-spacing:-.3px;}
.folder-body .f-amount.empty{color:#d1d5db;}
@keyframes pulseBadge{0%,100%{opacity:1;}50%{opacity:.5;}}
.folder-tab .f-badge.has-today{background:#fbbf24;color:#78350f;font-weight:800;animation:pulseBadge 1.4s ease-in-out infinite;}
@media(max-width:767.98px){.rpt-filter{flex-direction:column;align-items:stretch;}.rpt-filter .form-control{max-width:100%!important;}}
</style>

<div class="pcoded-inner-content ledger-page">
<div class="main-body"><div class="page-wrapper"><div class="page-body">

<div class="ledger-header">
    <div class="row align-items-center">
        <div class="col-md-8" style="position:relative;z-index:1;">
            <h4><i class="ti-folder mr-2"></i>Expense Ledger</h4>
            <div class="sub">Browse, search, and filter ledger folders.</div>
        </div>
        <div class="col-md-4 text-right mt-2 mt-md-0" style="position:relative;z-index:1;">
            <div style="display:inline-flex;align-items:center;gap:10px;flex-wrap:wrap;justify-content:flex-end;">
                <div style="background:rgba(255,255,255,.18);color:#fff;border:1px solid rgba(255,255,255,.3);border-radius:999px;padding:8px 14px;font-size:12px;font-weight:600;white-space:nowrap;">
                    Today: {{ $todayCount }}
                </div>
                <a href="{{ route('expense') }}" class="btn btn-sm" style="background:rgba(255,255,255,.2);color:#fff;border:1px solid rgba(255,255,255,.3);border-radius:8px;padding:7px 16px;font-weight:600;">
                    <i class="ti-receipt mr-1"></i> All Expenses
                </a>
            </div>
        </div>
    </div>
</div>

{{-- Filter bar --}}
<form method="GET" action="{{ route('expense.ledger.index') }}">
<div class="rpt-filter">
    <input type="date" name="date_from" class="form-control" style="max-width:140px;" value="{{ request('date_from') }}">
    <input type="date" name="date_to" class="form-control" style="max-width:140px;" value="{{ request('date_to') }}">
    <input type="text" name="search" class="form-control" style="max-width:180px;" value="{{ request('search') }}" placeholder="Search folder...">
    <button type="submit" class="btn btn-danger btn-sm">
        <i class="ti-search mr-1"></i> Filter
    </button>
    @if(request()->hasAny(['date_from','date_to','search']))
    <a href="{{ route('expense.ledger.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="ti-close mr-1"></i> Clear
    </a>
    @endif
</div>
</form>

<div class="folder-grid" id="folderGrid">
    @forelse($categories as $key => $cat)
    @php
        $count = $summary[$key]['count'] ?? 0;
        $total = $summary[$key]['total'] ?? 0;
        $todayVal = $todayCountByCategory[$key] ?? 0;
    @endphp
    <a href="{{ route('expense.ledger.category', array_filter(['category' => $key, 'date_from' => request('date_from'), 'date_to' => request('date_to')])) }}" class="folder" data-label="{{ strtolower($cat['label']) }}">
        <div class="folder-tab" style="background:{{ $cat['color'] }};color:#fff;">
            <div class="f-icon"><i class="{{ $cat['icon'] }}"></i></div>
            <span class="f-label">{{ $cat['label'] }}</span>
            <span class="f-badge{{ $todayVal > 0 ? ' has-today' : '' }}">{{ $todayVal }}</span>
        </div>
        <div class="folder-body">
            <div class="f-count">{{ $count }} entries</div>
            <div class="f-amount{{ $total ? '' : ' empty' }}">₹{{ $total ? number_format($total,0) : '0' }}</div>
        </div>
    </a>
    @empty
    <div style="grid-column:1/-1;text-align:center;padding:40px;color:#94a3b8;">
        <i class="ti-folder" style="font-size:36px;display:block;margin-bottom:10px;opacity:.5;"></i>
        <div style="font-size:14px;font-weight:600;">No folders found</div>
    </div>
    @endforelse
</div>

</div></div></div></div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var searchInput = document.querySelector('input[name="search"]');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            var q = this.value.toLowerCase().trim();
            document.querySelectorAll('.folder').forEach(function(card) {
                var label = card.getAttribute('data-label') || '';
                card.style.display = (!q || label.indexOf(q) !== -1) ? '' : 'none';
            });
        });
    }
});
</script>
@endpush
@endsection
