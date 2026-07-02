@forelse($gstSettings as $gst)
<tr id="gst-row-{{ $gst->id }}" style="transition:background .15s;">
    <td>
        <strong style="font-size:13px;color:#0f172a;">{{ $gst->name }}</strong>
    </td>
    <td>
        <span class="st-pill" style="background:#eef2ff;color:#4f46e5;border:1px solid #e0e7ff;">{{ $gst->type }}</span>
    </td>
    <td>
        <span style="display:inline-flex;align-items:center;gap:4px;background:#f0fdf4;color:#059669;font-weight:700;font-size:13px;padding:4px 12px;border-radius:8px;border:1px solid #bbf7d0;">
            {{ $gst->percentage }}%
        </span>
    </td>
    <td style="text-align:center;">
        <div style="display:inline-flex;gap:6px;">
            <button class="st-ib edit" style="width:34px;height:34px;border-radius:8px;"
                    onclick="openGstEdit({{ $gst->id }}, @js($gst->name), @js($gst->type), {{ $gst->percentage }})"
                    title="Edit GST">
                <i class="ti-pencil"></i>
            </button>
            <button class="st-ib del" style="width:34px;height:34px;border-radius:8px;"
                    onclick="deleteGst({{ $gst->id }})" title="Delete GST">
                <i class="ti-trash"></i>
            </button>
        </div>
    </td>
</tr>
@empty
<tr id="gst-empty-row">
    <td colspan="4" class="text-center py-4" style="color:#b0bac9;padding:40px 20px;">
        <i class="ti-receipt" style="font-size:32px;display:block;margin-bottom:10px;opacity:.3;"></i>
        <div style="font-size:14px;font-weight:600;color:#94a3b8;margin-bottom:4px;">No GST rates yet</div>
        <div style="font-size:12px;color:#b0bac9;">Add a GST rate using the form on the right.</div>
    </td>
</tr>
@endforelse
