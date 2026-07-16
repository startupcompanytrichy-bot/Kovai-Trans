@forelse($messageTemplates as $tmpl)
<tr id="mt-row-{{ $tmpl->id }}" style="transition:background .15s;">
    <td style="font-size:12px;color:#94a3b8;font-weight:600;">{{ $loop->iteration }}</td>
    <td>
        <strong style="font-size:13px;color:#0f172a;">{{ $tmpl->template_name }}</strong>
    </td>
    <td>
        <span style="font-size:12px;color:#475569;max-width:400px;display:inline-block;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ Str::limit($tmpl->message, 100) }}</span>
    </td>
    <td style="text-align:center;">
        <button class="st-ib toggle-status" style="width:44px;height:28px;border-radius:14px;font-size:11px;font-weight:700;cursor:pointer;border:none;{{ $tmpl->status ? 'background:#dcfce7;color:#16a34a;' : 'background:#fee2e2;color:#ef4444;' }}"
                onclick="toggleMsgTemplate({{ $tmpl->id }})" title="{{ $tmpl->status ? 'Active' : 'Inactive' }}">
            {{ $tmpl->status ? 'ON' : 'OFF' }}
        </button>
    </td>
    <td style="text-align:center;">
        <div style="display:inline-flex;gap:6px;">
            <button class="st-ib edit" style="width:34px;height:34px;border-radius:8px;"
                    onclick="openMsgTemplateEdit({{ $tmpl->id }}, @js($tmpl->template_name), @js($tmpl->message), {{ $tmpl->status ? 'true' : 'false' }})"
                    title="Edit Template">
                <i class="ti-pencil"></i>
            </button>
            <button class="st-ib del" style="width:34px;height:34px;border-radius:8px;"
                    onclick="deleteMsgTemplate({{ $tmpl->id }})" title="Delete Template">
                <i class="ti-trash"></i>
            </button>
        </div>
    </td>
</tr>
@empty
<tr id="mt-empty-row">
    <td colspan="5" class="text-center py-4" style="color:#b0bac9;padding:40px 20px;">
        <i class="ti-comment" style="font-size:32px;display:block;margin-bottom:10px;opacity:.3;"></i>
        <div style="font-size:14px;font-weight:600;color:#94a3b8;margin-bottom:4px;">No templates yet</div>
        <div style="font-size:12px;color:#b0bac9;">Add a message template using the form above.</div>
    </td>
</tr>
@endforelse
