

<div class="modal fade" id="globalDeleteModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered global-del-dialog" role="document">
        <div class="modal-content global-del-content">

            
            <div style="height:4px; background:linear-gradient(90deg,#f87171,#ef4444,#dc2626); border-radius:16px 16px 0 0;"></div>

            
            <div style="padding: 22px 22px 0; text-align:center; position:relative;">
                <button type="button" data-dismiss="modal"
                    style="position:absolute; top:12px; right:14px; background:#f8fafc; border:1px solid #e2e8f0; width:28px; height:28px; border-radius:50%; font-size:15px; color:#94a3b8; cursor:pointer; display:flex; align-items:center; justify-content:center; padding:0; line-height:1; transition:all .15s;">
                    &times;
                </button>

                
                <div class="global-del-icon-wrap">
                    <div class="global-del-icon-ring"></div>
                    <i class="ti-trash global-del-icon-i"></i>
                </div>

                <h6 style="font-size:15.5px; font-weight:700; color:#0f172a; margin:0 0 5px; letter-spacing:-0.2px;">
                    Delete <span id="globalDelType">Record</span>?
                </h6>
                <p style="font-size:12px; color:#64748b; margin:0; line-height:1.65;">
                    You're about to permanently delete<br>
                    <strong style="color:#ef4444;" id="globalDelName"></strong>
                </p>
            </div>

            
            <div style="margin: 14px 22px; border-top: 1px dashed #e2e8f0;"></div>

            
            <div style="margin: 0 22px; background:#fff7ed; border-left:3px solid #f97316; border-radius:0 6px 6px 0; padding:8px 12px; display:flex; align-items:center; gap:8px;">
                <i class="ti-alert" style="color:#f97316; font-size:13px; flex-shrink:0;"></i>
                <span style="font-size:11.5px; color:#7c2d12; line-height:1.5;">This action is <strong>permanent</strong> and cannot be undone.</span>
            </div>

            
            <div style="padding: 14px 22px 20px; display:flex; gap:10px; margin-top:4px; align-items:center;">
                
                <div id="globalDelTimerBadge" style="background:#f1f5f9; border:1px solid #e2e8f0; border-radius:20px; padding:3px 10px; display:flex; align-items:center; gap:4px; flex-shrink:0;">
                    <i class="ti-timer" style="font-size:11px; color:#94a3b8;"></i>
                    <span id="globalDelTimerText" style="font-size:11px; font-weight:700; color:#94a3b8; min-width:22px; text-align:center;">15s</span>
                </div>
                <button type="button" data-dismiss="modal" class="global-del-btn-cancel">
                    <i class="ti-close" style="font-size:10px; margin-right:4px;"></i> Cancel
                </button>
                <button type="button" onclick="globalDelConfirm()" class="global-del-btn-confirm">
                    <i class="ti-trash" style="font-size:11px; margin-right:4px;"></i> Yes, Delete
                </button>
            </div>

        </div>
    </div>
</div>

<style>
    /* ── Dialog sizing ───────────────────────────── */
    #globalDeleteModal .global-del-dialog {
        margin: auto !important;
        max-width: 380px !important;
        width: 100% !important;
        height: auto !important;
        display: flex !important;
        align-items: center !important;
        min-height: calc(100% - 1rem) !important;
    }
    #globalDeleteModal .global-del-content {
        height: auto !important;
        min-height: unset !important;
        border-radius: 16px !important;
        border: none !important;
        box-shadow: 0 24px 64px rgba(15,23,42,0.18), 0 4px 16px rgba(15,23,42,0.08) !important;
        overflow: hidden !important;
        background: #ffffff !important;
    }

    /* ── Pulsing icon ────────────────────────────── */
    .global-del-icon-wrap {
        position: relative;
        width: 62px; height: 62px;
        margin: 0 auto 12px;
        display: flex; align-items: center; justify-content: center;
    }
    .global-del-icon-ring {
        position: absolute; inset: 0;
        border-radius: 50%;
        background: linear-gradient(135deg, #fee2e2, #fecaca);
        animation: globalDelPulse 2s ease-in-out infinite;
    }
    .global-del-icon-i {
        position: relative; z-index: 1;
        font-size: 24px; color: #ef4444;
    }
    @keyframes globalDelPulse {
        0%   { box-shadow: 0 0 0 0 rgba(239,68,68,0.35); }
        60%  { box-shadow: 0 0 0 10px rgba(239,68,68,0); }
        100% { box-shadow: 0 0 0 0 rgba(239,68,68,0); }
    }

    /* ── Buttons ─────────────────────────────────── */
    .global-del-btn-cancel, .global-del-btn-confirm {
        flex: 1;
        padding: 9px 0;
        font-size: 12.5px;
        font-weight: 600;
        border-radius: 9px;
        cursor: pointer;
        transition: all .18s ease;
        letter-spacing: 0.1px;
    }
    .global-del-btn-cancel {
        border: 1.5px solid #e2e8f0;
        background: #f8fafc;
        color: #475569;
    }
    .global-del-btn-confirm {
        border: none;
        background: linear-gradient(135deg, #f87171 0%, #ef4444 50%, #dc2626 100%);
        color: #fff;
        box-shadow: 0 4px 14px rgba(239,68,68,0.38);
    }
    .global-del-btn-cancel:hover  { background:#f1f5f9; border-color:#cbd5e1; color:#1e293b; transform:translateY(-1px); }
    .global-del-btn-confirm:hover { background:linear-gradient(135deg,#ef4444 0%,#dc2626 50%,#b91c1c 100%); box-shadow:0 6px 20px rgba(239,68,68,0.52); transform:translateY(-1px); }
    .global-del-btn-cancel:active, .global-del-btn-confirm:active { transform:translateY(0); }

    /* ── Close btn hover ─────────────────────────── */
    #globalDeleteModal button[data-dismiss]:hover {
        background: #fee2e2 !important;
        border-color: #fca5a5 !important;
        color: #ef4444 !important;
    }
</style>

<script>
    var globalDelFormId   = null;
    var globalDelTimer    = null;
    var globalDelSeconds  = 15;

    /**
     * Call this from any page to show the delete modal.
     * @param {string} formId    - The hidden form id to submit (e.g. 'deleteFormCompany5')
     * @param {string} name      - The record name to display
     * @param {string} type      - Label like 'Company', 'Branch', 'Party', 'Vehicle'
     */
    function showDeleteModal(formId, name, type) {
        globalDelFormId = formId;
        document.getElementById('globalDelName').textContent = name;
        document.getElementById('globalDelType').textContent = type || 'Record';

        // Reset timer badge
        globalDelSeconds = 15;
        var badge = document.getElementById('globalDelTimerBadge');
        badge.style.background    = '#f1f5f9';
        badge.style.borderColor   = '#e2e8f0';
        document.getElementById('globalDelTimerText').style.color = '#94a3b8';
        document.getElementById('globalDelTimerText').textContent  = '15s';

        $('#globalDeleteModal').modal('show');

        // Start countdown
        globalDelTimer = setInterval(function() {
            globalDelSeconds--;
            document.getElementById('globalDelTimerText').textContent = globalDelSeconds + 's';
            if (globalDelSeconds <= 5) {
                badge.style.background  = '#fee2e2';
                badge.style.borderColor = '#fca5a5';
                document.getElementById('globalDelTimerText').style.color = '#ef4444';
            }
            if (globalDelSeconds <= 0) {
                clearInterval(globalDelTimer);
                $('#globalDeleteModal').modal('hide');
            }
        }, 1000);
    }

    function globalDelConfirm() {
        clearInterval(globalDelTimer);
        if (globalDelFormId) {
            var form = document.getElementById(globalDelFormId);
            if (form) form.submit();
        }
    }

    // Clear timer on manual close
    $('#globalDeleteModal').on('hide.bs.modal', function() {
        clearInterval(globalDelTimer);
    });
</script>
<?php /**PATH D:\laragon\www\Kovai-Trans\resources\views/partials/delete-modal.blade.php ENDPATH**/ ?>