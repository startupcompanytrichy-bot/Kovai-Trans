@forelse($vehicleConfigs as $cfg)
<tr id="vrc-row-{{ $cfg->id }}">
    <td><span class="st-pill" style="background:#eef2ff;color:#4f46e5;">{{ $cfg->template->template_name ?? '-' }}</span></td>
    <td style="font-size:12px;">{{ $cfg->duration }}</td>
    <td style="font-size:12px;">{{ $cfg->time ?? '-' }}</td>
    <td style="text-align:center;">
        <div style="display:inline-flex;gap:6px;">
            <button class="st-ib edit" style="width:34px;height:34px;border-radius:8px;"
                    onclick="editVrc({{ $cfg->id }}, {{ $cfg->template_id }}, @js($cfg->duration), @js($cfg->time))"
                    title="Edit Config">
                <i class="ti-pencil"></i>
            </button>
            <button class="st-ib del" style="width:34px;height:34px;border-radius:8px;"
                    onclick="deleteVrc({{ $cfg->id }})" title="Delete Config">
                <i class="ti-trash"></i>
            </button>
        </div>
    </td>
</tr>
@empty
<tr id="vrc-empty-row">
    <td colspan="4" class="text-center py-4" style="color:#b0bac9;padding:40px 20px;">
        <i class="ti-truck" style="font-size:32px;display:block;margin-bottom:10px;opacity:.3;"></i>
        <div style="font-size:14px;font-weight:600;color:#94a3b8;margin-bottom:4px;">No configurations yet</div>
        <div style="font-size:12px;color:#b0bac9;">Add one using the form above.</div>
    </td>
</tr>
@endforelse
