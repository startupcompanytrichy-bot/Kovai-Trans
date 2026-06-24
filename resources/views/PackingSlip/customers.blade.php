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
#csTable { min-width:1000px; margin-bottom:0; }
#csTable th, #csTable td { height:44px; padding:6px 12px; vertical-align:middle; border-color:#f0f2f7; font-size:12px; }
#csTable th { background:#f8fafc; color:#14213d; font-weight:800; font-size:11px; text-transform:uppercase; letter-spacing:.4px; white-space:nowrap; }
.btn-cs { border-radius:8px; padding:7px 16px; font-weight:600; font-size:12px; border:none; }
.modal-cs .modal-content { border-radius:12px; border:none; box-shadow:0 10px 40px rgba(0,0,0,.12); }
.modal-cs .modal-header { background:linear-gradient(135deg,#9333ea 0%,#7c3aed 100%); color:#fff; border-radius:12px 12px 0 0; padding:16px 20px; border:none; }
.modal-cs .modal-header .close { color:#fff; opacity:.8; }
.modal-cs .modal-body { padding:20px; }
.modal-cs .form-control { border-radius:8px; border-color:#e2e8f0; min-height:42px; font-size:13px; }
.modal-cs .form-control:focus { border-color:#9333ea; box-shadow:0 0 0 2px rgba(147,51,234,.15); }
.modal-cs .control-label { font-size:12px; font-weight:700; color:#374151; margin-bottom:4px; }
.cs-stat { display:grid; grid-template-columns:repeat(2,1fr); gap:12px; margin-bottom:16px; }
.cs-stat-card { background:#fff; border-radius:10px; padding:14px 16px; box-shadow:0 2px 8px rgba(0,0,0,.05); text-align:center; }
.cs-stat-card .lbl { font-size:10px; font-weight:700; color:#8a94a6; text-transform:uppercase; letter-spacing:.4px; }
.cs-stat-card .val { font-size:20px; font-weight:800; color:#1a2340; margin-top:2px; }
.action-btn { width:30px; height:30px; border-radius:8px; display:inline-flex; align-items:center; justify-content:center; border:none; font-size:13px; cursor:pointer; }
</style>

<div class="pcoded-inner-content cs-bg">
<div class="main-body"><div class="page-wrapper"><div class="page-body">

@if(session('success'))
<div class="alert alert-success" style="border-radius:10px;font-size:13px;padding:12px 16px;">{{ session('success') }}</div>
@endif

<div class="cs-hero">
    <div class="row align-items-center">
        <div class="col-md-6" style="position:relative;z-index:1;">
            <h4><i class="ti-layers mr-2"></i>Customers</h4>
            <div class="sub">Manage all customers and their details</div>
        </div>
        <div class="col-md-6 text-right" style="position:relative;z-index:1;">
            <button onclick="$('#addCustomerModal').modal('show')" class="btn btn-sm" style="border-radius:8px;background:#fff;color:#9333ea;border:none;padding:7px 18px;font-weight:700;">
                <i class="ti-plus mr-1"></i> Add Customer
            </button>
        </div>
    </div>
</div>

<div class="cs-stat">
    <div class="cs-stat-card">
        <div class="lbl">Total Customers</div>
        <div class="val" style="color:#9333ea;">{{ $customers->count() }}</div>
    </div>
    <div class="cs-stat-card">
        <div class="lbl">With GSTIN</div>
        <div class="val" style="color:#16a34a;">{{ $customers->whereNotNull('gstin')->count() }}</div>
    </div>
</div>

<div class="cs-card">
    <div class="cs-card-header">
        <h6><i class="ti-layers mr-2" style="color:#9333ea;"></i>Customers List</h6>
        <div><input type="text" id="csSearch" class="form-control" style="min-height:36px;font-size:12px;border-radius:8px;border-color:#e2e8f0;width:220px;" placeholder="Search by name, phone, email..." onkeyup="filterCustomers()"></div>
    </div>
    <div class="cs-table-wrap">
        <table class="table table-bordered" id="csTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>GSTIN</th>
                    <th>Address</th>
                    <th style="text-align:center;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($customers as $i => $c)
                <tr>
                    <td>{{ $i+1 }}</td>
                    <td><strong>{{ $c->name }}</strong></td>
                    <td>{{ $c->phone ?? '—' }}</td>
                    <td>{{ $c->email ?? '—' }}</td>
                    <td>{{ $c->gstin ?? '—' }}</td>
                    <td style="font-size:11px;max-width:180px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $c->address ?? '—' }}</td>
                    <td style="text-align:center;white-space:nowrap;">
                        <button onclick="editCustomer({{ $c->id }})" class="action-btn" style="background:#eef2ff;color:#4338ca;" title="Edit"><i class="ti-pencil"></i></button>
                        <button onclick="deleteCustomer({{ $c->id }}, '{{ addslashes($c->name) }}')" class="action-btn" style="background:#fef2f2;color:#dc2626;margin-left:4px;" title="Delete"><i class="ti-trash"></i></button>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center py-4" style="color:#b0bac9;">No customers found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

</div></div></div></div>

{{-- Add Customer Modal --}}
<div class="modal fade modal-cs" id="addCustomerModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <form method="POST" action="{{ route('packing-slip.customers.store') }}" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title"><i class="ti-plus mr-2"></i>Add Customer</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12 form-group">
                        <label class="control-label">Name <span style="color:#dc2626;">*</span></label>
                        <input type="text" name="name" class="form-control" required placeholder="Customer name">
                    </div>
                    <div class="col-md-6 form-group">
                        <label class="control-label">Phone</label>
                        <input type="text" name="phone" class="form-control" placeholder="Phone number">
                    </div>
                    <div class="col-md-6 form-group">
                        <label class="control-label">Email</label>
                        <input type="email" name="email" class="form-control" placeholder="Email address">
                    </div>
                    <div class="col-md-6 form-group">
                        <label class="control-label">GSTIN</label>
                        <input type="text" name="gstin" class="form-control" placeholder="GSTIN">
                    </div>
                    <div class="col-md-6 form-group">
                        <label class="control-label">Address</label>
                        <input type="text" name="address" class="form-control" placeholder="Address">
                    </div>
                    <div class="col-md-12 form-group">
                        <label class="control-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="Notes..."></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="border-top:1px solid #f0f2f7;padding:12px 20px;">
                <button type="button" class="btn btn-light" data-dismiss="modal" style="border-radius:8px;padding:7px 18px;font-weight:600;">Cancel</button>
                <button type="submit" class="btn btn-cs" style="background:#9333ea;color:#fff;"><i class="ti-save mr-1"></i> Save</button>
            </div>
        </form>
    </div>
</div>

{{-- Edit Customer Modal --}}
<div class="modal fade modal-cs" id="editCustomerModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <form method="POST" action="" class="modal-content" id="editCustomerForm">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title"><i class="ti-pencil mr-2"></i>Edit Customer</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12 form-group">
                        <label class="control-label">Name <span style="color:#dc2626;">*</span></label>
                        <input type="text" name="name" id="edit_name" class="form-control" required placeholder="Customer name">
                    </div>
                    <div class="col-md-6 form-group">
                        <label class="control-label">Phone</label>
                        <input type="text" name="phone" id="edit_phone" class="form-control" placeholder="Phone number">
                    </div>
                    <div class="col-md-6 form-group">
                        <label class="control-label">Email</label>
                        <input type="email" name="email" id="edit_email" class="form-control" placeholder="Email address">
                    </div>
                    <div class="col-md-6 form-group">
                        <label class="control-label">GSTIN</label>
                        <input type="text" name="gstin" id="edit_gstin" class="form-control" placeholder="GSTIN">
                    </div>
                    <div class="col-md-6 form-group">
                        <label class="control-label">Address</label>
                        <input type="text" name="address" id="edit_address" class="form-control" placeholder="Address">
                    </div>
                    <div class="col-md-12 form-group">
                        <label class="control-label">Notes</label>
                        <textarea name="notes" id="edit_notes" class="form-control" rows="2" placeholder="Notes..."></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="border-top:1px solid #f0f2f7;padding:12px 20px;">
                <button type="button" class="btn btn-light" data-dismiss="modal" style="border-radius:8px;padding:7px 18px;font-weight:600;">Cancel</button>
                <button type="submit" class="btn btn-cs" style="background:#9333ea;color:#fff;"><i class="ti-save mr-1"></i> Update</button>
            </div>
        </form>
    </div>
</div>

{{-- Delete Confirmation Modal --}}
<div class="modal fade modal-cs" id="deleteCustomerModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="ti-trash mr-2"></i>Delete Record?</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body text-center py-3">
                <p style="font-size:14px;color:#374151;margin:0;">You're about to permanently delete <strong id="deleteCustomerName"></strong></p>
                <p style="font-size:12px;color:#dc2626;margin-top:8px;">This action is <strong>permanent</strong> and cannot be undone.</p>
            </div>
            <div class="modal-footer" style="border-top:1px solid #f0f2f7;padding:12px 20px;justify-content:center;">
                <button type="button" class="btn btn-light" data-dismiss="modal" style="border-radius:8px;padding:7px 18px;font-weight:600;">Cancel</button>
                <form method="POST" action="" id="deleteCustomerForm" style="display:inline;">
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
function filterCustomers() {
    var q = document.getElementById('csSearch').value.toLowerCase();
    document.querySelectorAll('#csTable tbody tr').forEach(function(r) {
        r.style.display = r.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
}

function editCustomer(id) {
    fetch('/packing-slip/customers/edit/' + id)
        .then(function(r) { return r.json(); })
        .then(function(d) {
            document.getElementById('edit_name').value = d.name || '';
            document.getElementById('edit_phone').value = d.phone || '';
            document.getElementById('edit_email').value = d.email || '';
            document.getElementById('edit_gstin').value = d.gstin || '';
            document.getElementById('edit_address').value = d.address || '';
            document.getElementById('edit_notes').value = d.notes || '';
            document.getElementById('editCustomerForm').action = '/packing-slip/customers/update/' + id;
            $('#editCustomerModal').modal('show');
        });
}

function deleteCustomer(id, name) {
    document.getElementById('deleteCustomerName').textContent = name;
    document.getElementById('deleteCustomerForm').action = '/packing-slip/customers/delete/' + id;
    $('#deleteCustomerModal').modal('show');
}
</script>
@endpush