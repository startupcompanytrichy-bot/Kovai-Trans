@extends('layouts.app')

@section('content')
<style>
.cs-bg { background:#f4f6fb; }
.cs-hero { background:linear-gradient(135deg,#9333ea 0%,#7c3aed 100%); border-radius:14px; padding:20px 24px; color:#fff; margin-bottom:20px; position:relative; overflow:hidden; }
.cs-hero::before { content:''; position:absolute; top:-30px; right:-30px; width:120px; height:120px; background:rgba(255,255,255,.07); border-radius:50%; }
.cs-hero h4 { font-size:18px; font-weight:800; margin:0 0 4px; }
.cs-hero .sub { font-size:12px; opacity:.8; }
.cs-card { background:#fff; border-radius:12px; box-shadow:0 2px 10px rgba(0,0,0,.06); overflow:hidden; }
.cs-card-header { display:flex; align-items:center; justify-content:space-between; padding:14px 20px; border-bottom:1px solid #f0f2f7; background:#fafbff; flex-wrap:wrap; gap:8px; }
.cs-card-header h6 { margin:0; font-size:14px; font-weight:700; color:#1a2340; }
.cs-table-wrap { overflow-x:auto; }
#qTable { min-width:500px; margin-bottom:0; }
#qTable th, #qTable td { height:44px; padding:6px 12px; vertical-align:middle; border-color:#f0f2f7; font-size:12px; }
#qTable th { background:#f8fafc; color:#14213d; font-weight:800; font-size:11px; text-transform:uppercase; letter-spacing:.4px; white-space:nowrap; }
.modal-cs .modal-content { border-radius:12px; border:none; box-shadow:0 10px 40px rgba(0,0,0,.12); }
.modal-cs .modal-header { background:linear-gradient(135deg,#9333ea 0%,#7c3aed 100%); color:#fff; border-radius:12px 12px 0 0; padding:16px 20px; border:none; }
.modal-cs .modal-header .close { color:#fff; opacity:.8; }
.modal-cs .modal-body { padding:20px; }
.modal-cs .form-control { border-radius:8px; border-color:#e2e8f0; min-height:42px; font-size:13px; }
.modal-cs .form-control:focus { border-color:#9333ea; box-shadow:0 0 0 2px rgba(147,51,234,.15); }
.modal-cs .control-label { font-size:12px; font-weight:700; color:#374151; margin-bottom:4px; }
.action-btn { width:30px; height:30px; border-radius:8px; display:inline-flex; align-items:center; justify-content:center; border:none; font-size:13px; cursor:pointer; }
</style>

<div class="pcoded-inner-content cs-bg">
<div class="main-body"><div class="page-wrapper"><div class="page-body">

@if(session('success'))
<div class="alert alert-success" style="border-radius:10px;font-size:13px;padding:12px 16px;">{{ session('success') }}</div>
@endif

@if(session('error'))
<div class="alert alert-danger" style="border-radius:10px;font-size:13px;padding:12px 16px;">{{ session('error') }}</div>
@endif

<div class="cs-hero">
    <div class="row align-items-center">
        <div class="col-md-6" style="position:relative;z-index:1;">
            <h4><i class="ti-star mr-2"></i>Qualities</h4>
            <div class="sub">Manage packing slip quality masters</div>
        </div>
        <div class="col-md-6 text-right" style="position:relative;z-index:1;">
            <button onclick="$('#addQualityModal').modal('show')" class="btn btn-sm" style="border-radius:8px;background:#fff;color:#9333ea;border:none;padding:7px 18px;font-weight:700;">
                <i class="ti-plus mr-1"></i> Add Quality
            </button>
        </div>
    </div>
</div>

<div class="cs-card">
    <div class="cs-card-header">
        <h6><i class="ti-star mr-2" style="color:#9333ea;"></i>All Qualities</h6>
    </div>
    <div class="cs-table-wrap">
        <table class="table table-bordered" id="qTable">
            <thead>
                <tr>
                    <th style="width:50px;">#</th>
                    <th>Name</th>
                    <th style="width:100px;text-align:center;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($qualities as $i => $q)
                <tr>
                    <td>{{ $i+1 }}</td>
                    <td><strong>{{ $q->name }}</strong></td>
                    <td style="text-align:center;white-space:nowrap;">
                        <button onclick="editQuality({{ $q->id }})" class="action-btn" style="background:#eef2ff;color:#4338ca;" title="Edit"><i class="ti-pencil"></i></button>
                        <button onclick="deleteQuality({{ $q->id }}, '{{ addslashes($q->name) }}')" class="action-btn" style="background:#fef2f2;color:#dc2626;margin-left:4px;" title="Delete"><i class="ti-trash"></i></button>
                    </td>
                </tr>
                @empty
                <tr><td colspan="3" class="text-center py-4" style="color:#b0bac9;">No qualities found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

</div></div></div></div>

{{-- Add Quality Modal --}}
<div class="modal fade modal-cs" id="addQualityModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm" role="document">
        <form method="POST" action="{{ route('packing-slip.quality.store') }}" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title"><i class="ti-plus mr-2"></i>Add Quality</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="control-label">Enter Name</label>
                    <input type="text" name="name" class="form-control" required placeholder="Quality name">
                </div>
            </div>
            <div class="modal-footer" style="border-top:1px solid #f0f2f7;padding:12px 20px;">
                <button type="button" class="btn btn-light" data-dismiss="modal" style="border-radius:8px;padding:7px 18px;font-weight:600;">Cancel</button>
                <button type="submit" class="btn btn-cs" style="background:#9333ea;color:#fff;border:none;padding:7px 18px;font-weight:600;"><i class="ti-save mr-1"></i> Save</button>
            </div>
        </form>
    </div>
</div>

{{-- Edit Quality Modal --}}
<div class="modal fade modal-cs" id="editQualityModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm" role="document">
        <form method="POST" action="" class="modal-content" id="editQualityForm">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title"><i class="ti-pencil mr-2"></i>Edit Quality</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="control-label">Enter Name</label>
                    <input type="text" name="name" id="edit_quality_name" class="form-control" required placeholder="Quality name">
                </div>
            </div>
            <div class="modal-footer" style="border-top:1px solid #f0f2f7;padding:12px 20px;">
                <button type="button" class="btn btn-light" data-dismiss="modal" style="border-radius:8px;padding:7px 18px;font-weight:600;">Cancel</button>
                <button type="submit" class="btn btn-cs" style="background:#9333ea;color:#fff;border:none;padding:7px 18px;font-weight:600;"><i class="ti-save mr-1"></i> Update</button>
            </div>
        </form>
    </div>
</div>

{{-- Delete Confirmation Modal --}}
<div class="modal fade modal-cs" id="deleteQualityModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="ti-trash mr-2"></i>Delete Record?</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body text-center py-3">
                <p style="font-size:14px;color:#374151;margin:0;">You're about to permanently delete <strong id="deleteQualityName"></strong></p>
                <p style="font-size:12px;color:#dc2626;margin-top:8px;">This action is <strong>permanent</strong> and cannot be undone.</p>
            </div>
            <div class="modal-footer" style="border-top:1px solid #f0f2f7;padding:12px 20px;justify-content:center;">
                <button type="button" class="btn btn-light" data-dismiss="modal" style="border-radius:8px;padding:7px 18px;font-weight:600;">Cancel</button>
                <form method="POST" action="" id="deleteQualityForm" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn btn-cs" style="background:#dc2626;color:#fff;">Yes, Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function editQuality(id) {
    fetch('/packing-slip/quality/edit/' + id)
        .then(function(r) { return r.json(); })
        .then(function(d) {
            document.getElementById('edit_quality_name').value = d.name || '';
            document.getElementById('editQualityForm').action = '/packing-slip/quality/update/' + id;
            $('#editQualityModal').modal('show');
        });
}

function deleteQuality(id, name) {
    document.getElementById('deleteQualityName').textContent = name;
    document.getElementById('deleteQualityForm').action = '/packing-slip/quality/delete/' + id;
    $('#deleteQualityModal').modal('show');
}
</script>
@endpush