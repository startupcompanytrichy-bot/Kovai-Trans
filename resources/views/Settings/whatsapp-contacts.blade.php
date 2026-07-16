<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>WhatsApp Reminder Contacts</title>
    <link rel="icon" href="{{ asset('assets/images/favicon.ico') }}" type="image/x-icon">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/icon/themify-icons/themify-icons.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/toastr/toastr.min.css') }}">
    <style>
        * { box-sizing: border-box; }
        body { background: #f0f2f8; font-family: 'Open Sans', sans-serif; margin: 0; padding: 0; }
        .wac-wrap { min-height: 100vh; padding: 24px 20px; }
        .wac-page { max-width: 720px; margin: 0 auto; }

        .wac-hdr {
            background: linear-gradient(135deg, #075e54 0%, #128c7e 50%, #25d366 100%);
            border-radius: 12px; padding: 20px 24px; color: #fff; margin-bottom: 20px;
            display: flex; align-items: center; gap: 14px; position: relative; overflow: hidden;
        }
        .wac-hdr::after {
            content:''; position:absolute; top:-40px; right:-40px; width:160px; height:160px;
            background:rgba(255,255,255,.06); border-radius:50%;
        }
        .wac-hdr h5 { margin:0; font-size:16px; font-weight:700; position:relative; z-index:1; }
        .wac-hdr small { opacity:.8; font-size:12px; position:relative; z-index:1; }

        .wac-card {
            background: #fff; border-radius: 10px; box-shadow: 0 2px 12px rgba(0,0,0,.06);
            margin-bottom: 18px; overflow: hidden;
        }
        .wac-card-hd {
            padding: 14px 20px; border-bottom: 1px solid #f1f5f9;
            display: flex; align-items: center; justify-content: space-between;
        }
        .wac-card-hd h6 { margin:0; font-size:14px; font-weight:700; color:#1e293b; display:flex; align-items:center; gap:8px; }
        .wac-card-bd { padding: 20px; }

        .wac-badge {
            display:inline-flex; align-items:center; justify-content:center;
            background:#eef2ff; color:#4f46e5; font-size:10px; font-weight:700;
            padding:2px 10px; border-radius:20px; min-width:22px;
        }

        .wac-form .form-row { display:flex; gap:12px; flex-wrap:wrap; }
        .wac-form .form-group { flex:1; min-width:180px; }
        .wac-form label { font-size:12px; font-weight:600; color:#475569; margin-bottom:4px; display:block; }
        .wac-form input {
            width:100%; padding:9px 12px; border:1px solid #d1d5db; border-radius:6px;
            font-size:13px; color:#1e293b; outline:none; transition:border .2s;
        }
        .wac-form input:focus { border-color:#25d366; box-shadow:0 0 0 3px rgba(37,211,102,.12); }
        .wac-form input.is-invalid { border-color:#ef4444; }
        .wac-form .invalid-feedback { color:#ef4444; font-size:11px; margin-top:3px; display:none; }
        .wac-form input.is-invalid ~ .invalid-feedback { display:block; }

        .wac-btn {
            display:inline-flex; align-items:center; gap:4px; padding:9px 18px;
            border:none; border-radius:6px; font-size:12px; font-weight:600; cursor:pointer;
            transition:all .2s; white-space:nowrap;
        }
        .wac-btn-primary { background:#25d366; color:#fff; }
        .wac-btn-primary:hover { background:#1fb85a; }
        .wac-btn-danger { background:#fee2e2; color:#ef4444; border:1px solid #fca5a5; }
        .wac-btn-danger:hover { background:#fecaca; }
        .wac-btn-sm { padding:5px 10px; font-size:11px; }
        .wac-btn-ghost { background:transparent; color:#64748b; border:1px solid #e2e8f0; }
        .wac-btn-ghost:hover { background:#f8fafc; }

        .wac-tbl { width:100%; border-collapse:collapse; }
        .wac-tbl th {
            background:#f8fafc; padding:10px 14px; font-size:11px; font-weight:700;
            color:#64748b; text-transform:uppercase; letter-spacing:.5px;
            border-bottom:2px solid #e2e8f0; text-align:left;
        }
        .wac-tbl td { padding:12px 14px; font-size:13px; color:#334155; border-bottom:1px solid #f1f5f9; vertical-align:middle; }
        .wac-tbl tr:last-child td { border-bottom:none; }
        .wac-tbl tr:hover td { background:#f8fafc; }

        .wac-status {
            display:inline-flex; align-items:center; gap:5px; padding:3px 10px;
            border-radius:20px; font-size:11px; font-weight:600;
        }
        .wac-status-active { background:#dcfce7; color:#16a34a; }
        .wac-status-inactive { background:#fee2e2; color:#ef4444; }

        .wac-empty { text-align:center; padding:40px 20px; color:#94a3b8; }
        .wac-empty i { font-size:40px; display:block; margin-bottom:10px; opacity:.3; }

        .wac-actions { display:flex; gap:6px; align-items:center; }

        .wac-back {
            display:inline-flex; align-items:center; gap:4px; font-size:12px; color:#64748b;
            text-decoration:none; margin-bottom:14px; transition:color .2s;
        }
        .wac-back:hover { color:#25d366; text-decoration:none; }
    </style>
</head>
<body>

<div class="wac-wrap">
    <div class="wac-page">
        <a href="{{ route('settings') }}#reminder-contacts" class="wac-back">
            <i class="ti-arrow-left"></i> Back to Settings
        </a>

        <div class="wac-hdr">
            <i class="ti-comment-alt" style="font-size:24px;"></i>
            <div>
                <h5>WhatsApp Reminder Contacts</h5>
                <small>Manage contacts for EMI &amp; expiry reminders</small>
            </div>
        </div>

        {{-- Add / Edit Form Card --}}
        <div class="wac-card" id="formCard">
            <div class="wac-card-hd">
                <h6>
                    <span id="formIcon" style="display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;border-radius:8px;background:#dcfce7;color:#16a34a;">
                        <i class="ti-plus" style="font-size:13px;"></i>
                    </span>
                    <span id="formTitle">Add Contact</span>
                </h6>
            </div>
            <div class="wac-card-bd">
                <form id="contactForm" class="wac-form">
                    @csrf
                    <input type="hidden" name="_method" id="formMethod" value="POST">
                    <input type="hidden" name="contact_id" id="contactId" value="">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="contactName">Name <span style="color:#ef4444;">*</span></label>
                            <input type="text" name="name" id="contactName" placeholder="e.g. Ramesh Kumar" required maxlength="100">
                            <div class="invalid-feedback">Please enter a valid name (max 100 characters).</div>
                        </div>
                        <div class="form-group">
                            <label for="contactMobile">Mobile Number <span style="color:#ef4444;">*</span></label>
                            <input type="text" name="mobile" id="contactMobile" placeholder="e.g. 9876543210" required maxlength="10" inputmode="numeric">
                            <div class="invalid-feedback">Enter a valid 10-digit Indian mobile number starting with 6-9.</div>
                        </div>
                    </div>
                    <div style="display:flex;gap:8px;margin-top:14px;">
                        <button type="submit" class="wac-btn wac-btn-primary" id="submitBtn">
                            <i class="ti-plus"></i> Add Contact
                        </button>
                        <button type="button" class="wac-btn wac-btn-ghost" id="cancelBtn" style="display:none;" onclick="resetForm()">
                            <i class="ti-close"></i> Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Contacts List --}}
        <div class="wac-card">
            <div class="wac-card-hd">
                <h6>
                    <i class="ti-user" style="font-size:13px;color:#25d366;"></i>
                    Contacts
                </h6>
                <span class="wac-badge" id="contactCount">{{ $contacts->count() }}</span>
            </div>
            <div class="wac-card-bd" style="padding:0;">
                @if($contacts->isEmpty())
                    <div class="wac-empty" id="emptyState">
                        <i class="ti-user"></i>
                        <div style="font-size:14px;font-weight:600;margin-bottom:4px;">No contacts yet</div>
                        <div style="font-size:12px;">Add contacts above to receive WhatsApp reminders</div>
                    </div>
                @endif
                <div style="overflow-x:auto;" id="tableWrap" {{ $contacts->isEmpty() ? 'style="display:none;"' : '' }}>
                    <table class="wac-tbl">
                        <thead>
                            <tr>
                                <th style="width:5%;">#</th>
                                <th style="width:30%;">Name</th>
                                <th style="width:25%;">Mobile</th>
                                <th style="width:15%;text-align:center;">Status</th>
                                <th style="width:25%;text-align:center;">Action</th>
                            </tr>
                        </thead>
                        <tbody id="contactTableBody">
                            @foreach($contacts as $idx => $contact)
                            <tr id="row-{{ $contact->id }}">
                                <td style="color:#94a3b8;font-size:12px;">{{ $idx + 1 }}</td>
                                <td style="font-weight:600;">{{ $contact->name }}</td>
                                <td>
                                    <span style="font-family:monospace;font-size:13px;color:#475569;">+91 {{ $contact->mobile }}</span>
                                </td>
                                <td style="text-align:center;">
                                    <span class="wac-status {{ $contact->is_active ? 'wac-status-active' : 'wac-status-inactive' }}" id="status-{{ $contact->id }}">
                                        {{ $contact->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="wac-actions" style="justify-content:center;">
                                        <button type="button" class="wac-btn wac-btn-ghost wac-btn-sm" onclick="editContact('{{ $contact->id }}', '{!! addslashes($contact->name) !!}', '{{ $contact->mobile }}')" title="Edit">
                                            <i class="ti-pencil"></i>
                                        </button>
                                        <button type="button" class="wac-btn wac-btn-sm {{ $contact->is_active ? 'wac-btn-ghost' : 'wac-btn-primary' }}" id="toggle-{{ $contact->id }}" onclick="toggleContact('{{ $contact->id }}')" title="{{ $contact->is_active ? 'Deactivate' : 'Activate' }}">
                                            <i class="ti-{{ $contact->is_active ? 'power-off' : 'check' }}"></i>
                                        </button>
                                        <button type="button" class="wac-btn wac-btn-danger wac-btn-sm" onclick="deleteContact('{{ $contact->id }}')" title="Delete">
                                            <i class="ti-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('assets/js/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('assets/js/bootstrap/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('assets/js/toastr/toastr.min.js') }}"></script>
<script>
var BASE = '{{ url("/whatsapp-contacts") }}';
var TOKEN = '{{ csrf_token() }}';
var count = {{ $contacts->count() }};

toastr.options = {
    closeButton: true, progressBar: true, positionClass: 'toast-top-right',
    timeOut: 3000, showMethod: 'slideDown', hideMethod: 'slideUp'
};

function updateCount() {
    document.getElementById('contactCount').textContent = count;
}

function toggleEmpty() {
    var empty = document.getElementById('emptyState');
    var table = document.getElementById('tableWrap');
    if (count === 0) {
        if (table) table.style.display = 'none';
        if (empty) empty.style.display = '';
    } else {
        if (empty) empty.style.display = 'none';
        if (table) table.style.display = '';
    }
}

function editContact(id, name, mobile) {
    document.getElementById('formMethod').value = 'PUT';
    document.getElementById('contactId').value = id;
    document.getElementById('contactName').value = name;
    document.getElementById('contactMobile').value = mobile;
    document.getElementById('formTitle').textContent = 'Edit Contact';
    document.getElementById('formIcon').innerHTML = '<i class="ti-pencil" style="font-size:13px;"></i>';
    document.getElementById('formIcon').style.background = '#eef2ff';
    document.getElementById('formIcon').style.color = '#4f46e5';
    document.getElementById('submitBtn').innerHTML = '<i class="ti-check"></i> Update Contact';
    document.getElementById('cancelBtn').style.display = '';
    document.getElementById('formCard').scrollIntoView({ behavior: 'smooth' });
}

function resetForm() {
    var form = document.getElementById('contactForm');
    form.reset();
    document.getElementById('formMethod').value = 'POST';
    document.getElementById('contactId').value = '';
    document.getElementById('formTitle').textContent = 'Add Contact';
    document.getElementById('formIcon').innerHTML = '<i class="ti-plus" style="font-size:13px;"></i>';
    document.getElementById('formIcon').style.background = '#dcfce7';
    document.getElementById('formIcon').style.color = '#16a34a';
    document.getElementById('submitBtn').innerHTML = '<i class="ti-plus"></i> Add Contact';
    document.getElementById('cancelBtn').style.display = 'none';
    form.querySelectorAll('input').forEach(function(el) { el.classList.remove('is-invalid'); });
}

document.getElementById('contactForm').addEventListener('submit', function(e) {
    e.preventDefault();
    var name = document.getElementById('contactName');
    var mobile = document.getElementById('contactMobile');
    var valid = true;

    name.classList.remove('is-invalid');
    mobile.classList.remove('is-invalid');

    if (!name.value.trim() || name.value.trim().length > 100) {
        name.classList.add('is-invalid');
        valid = false;
    }
    if (!/^[6-9][0-9]{9}$/.test(mobile.value.trim())) {
        mobile.classList.add('is-invalid');
        valid = false;
    }
    if (!valid) return;

    var id = document.getElementById('contactId').value;
    var method = document.getElementById('formMethod').value;
    var url = id ? BASE + '/' + id : BASE;

    var fd = new FormData(this);
    if (method === 'PUT') fd.append('_method', 'PUT');

    $.ajax({
        url: url, type: 'POST', data: fd, processData: false, contentType: false,
        headers: { 'X-CSRF-TOKEN': TOKEN, 'X-Requested-With': 'XMLHttpRequest' },
        dataType: 'json',
        success: function(r) {
            toastr.success(r.success || 'Done');
            resetForm();
            setTimeout(function() { location.reload(); }, 600);
        },
        error: function(xhr) {
            if (xhr.status === 422) {
                var errs = xhr.responseJSON.errors || {};
                if (errs.name) name.classList.add('is-invalid');
                if (errs.mobile) mobile.classList.add('is-invalid');
                toastr.error('Please fix the validation errors.');
            } else {
                toastr.error('Something went wrong.');
            }
        }
    });
});

document.getElementById('contactMobile').addEventListener('input', function() {
    this.value = this.value.replace(/[^0-9]/g, '').substring(0, 10);
});

function toggleContact(id) {
    $.ajax({
        url: BASE + '/' + id + '/toggle',
        type: 'POST',
        data: { _token: TOKEN },
        headers: { 'X-CSRF-TOKEN': TOKEN, 'X-Requested-With': 'XMLHttpRequest' },
        dataType: 'json',
        success: function(r) {
            toastr.success(r.success || 'Done');
            setTimeout(function() { location.reload(); }, 600);
        },
        error: function() { toastr.error('Failed to update status.'); }
    });
}

function deleteContact(id) {
    if (!confirm('Delete this contact?')) return;
    $.ajax({
        url: BASE + '/' + id,
        type: 'POST',
        data: { _token: TOKEN, _method: 'DELETE' },
        headers: { 'X-CSRF-TOKEN': TOKEN, 'X-Requested-With': 'XMLHttpRequest' },
        dataType: 'json',
        success: function(r) {
            toastr.success(r.success || 'Deleted');
            var row = document.getElementById('row-' + id);
            if (row) row.remove();
            count--;
            updateCount();
            toggleEmpty();
        },
        error: function() { toastr.error('Failed to delete contact.'); }
    });
}
</script>
</body>
</html>
