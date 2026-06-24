<?php $__env->startSection('content'); ?>

<style>
.st-page { background: #f4f6fb; }

/* ── Page header ─────────────────────────────────────────────────── */
.st-page-header {
    background: linear-gradient(135deg, #1a2340 0%, #2d3a5e 60%, #667eea 100%);
    border-radius: 10px; padding: 14px 20px; color: #fff;
    margin-bottom: 16px; position: relative; overflow: hidden;
}
.st-page-header::before {
    content:''; position:absolute; top:-40px; right:-40px;
    width:140px; height:140px; background:rgba(255,255,255,.05); border-radius:50%;
}
.st-page-header h4 { font-size:16px; font-weight:800; margin:0 0 2px; }
.st-page-header .sub { font-size:12px; opacity:.75; }

/* ── Settings sidebar nav ────────────────────────────────────────── */
.st-nav {
    background: #fff; border-radius: 10px;
    box-shadow: 0 1px 6px rgba(0,0,0,.06); overflow: hidden;
    position: sticky; top: 16px;
}
.st-nav-item {
    display: flex; align-items: center; gap: 10px;
    padding: 12px 16px; cursor: pointer;
    border-left: 3px solid transparent;
    transition: all .15s; font-size: 13px; font-weight: 600; color: #596579;
}
.st-nav-item:hover { background: #f5f7ff; color: #1a2340; }
.st-nav-item.active { background: #eef2ff; border-left-color: #667eea; color: #4f46e5; }
.st-nav-item .nav-icon {
    width: 32px; height: 32px; border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    font-size: 14px; flex-shrink: 0;
    background: #f4f6fb; color: #8a94a6;
}
.st-nav-item.active .nav-icon { background: #667eea; color: #fff; }
.st-nav-item .nav-count {
    margin-left: auto;
    background: #f4f6fb; color: #8a94a6;
    font-size: 10px; font-weight: 700; padding: 2px 8px; border-radius: 12px;
}
.st-nav-item.active .nav-count { background: #c7d2fe; color: #4f46e5; }

/* ── Tab panes ────────────────────────────────────────────────────── */
.st-tab-pane { display: none; }
.st-tab-pane.active { display: block; }

/* ── Section label ────────────────────────────────────────────────── */
.st-section-label {
    display: flex; align-items: center; gap: 8px;
    font-size: 11px; font-weight: 800; text-transform: uppercase;
    letter-spacing: .7px; color: #8a94a6;
    margin: 0 0 10px;
}
.st-section-label::after {
    content:''; flex:1; height:1px; background:#e9ecef;
}

/* ── Card ─────────────────────────────────────────────────────────── */
.st-card {
    background: #fff; border-radius: 10px;
    box-shadow: 0 1px 6px rgba(0,0,0,.06); overflow: hidden;
    margin-bottom: 14px;
}
.st-card-head {
    display: flex; align-items: center; justify-content: space-between;
    padding: 12px 16px; border-bottom: 1px solid #f0f2f7; background: #fafbff;
    flex-wrap: wrap; gap: 8px;
}
.st-card-head h6 {
    margin: 0; font-size: 13px; font-weight: 800; color: #1a2340;
    display: flex; align-items: center; gap: 6px;
}
.st-card-body { padding: 16px; }

/* ── Active FY banner ─────────────────────────────────────────────── */
.st-fy-active-banner {
    display: flex; align-items: center; gap: 10px;
    padding: 10px 14px; border-radius: 8px;
    background: #f0fff4; border: 1px solid #9ae6b4;
    margin-bottom: 14px;
}
.st-fy-active-banner .af-icon {
    width: 32px; height: 32px; border-radius: 8px;
    background: #c6f6d5; color: #276749;
    display: flex; align-items: center; justify-content: center;
    font-size: 14px; flex-shrink: 0;
}
.st-fy-active-banner .af-label { font-size: 10px; font-weight: 700; color: #276749; text-transform: uppercase; letter-spacing: .3px; }
.st-fy-active-banner .af-value { font-size: 15px; font-weight: 800; color: #276749; line-height: 1.1; }
.st-fy-active-banner .af-range { font-size: 11px; color: #48bb78; }

.st-no-fy-banner {
    display: flex; align-items: center; gap: 10px;
    padding: 10px 14px; border-radius: 8px;
    background: #fffbeb; border: 1px solid #fde68a;
    margin-bottom: 14px;
}

/* ── FY Table ─────────────────────────────────────────────────────── */
#fyTable { min-width: 500px; margin-bottom: 0; }
#fyTable th, #fyTable td { height: 44px; padding: 7px 12px; vertical-align: middle; border-color: #f0f2f7; font-size: 13px; }
#fyTable th { background: #f8fafc; color: #14213d; font-weight: 800; font-size: 11px; text-transform: uppercase; letter-spacing: .4px; }
#fyTable tr.fy-active td { background: #f0fff4 !important; }
#fyTable tr.fy-active td:first-child { border-left: 3px solid #48bb78; }

/* default: only first 3 rows visible */
#fyTable tbody tr.fy-hidden { display: none; }

/* scrollable expanded area */
.fy-table-wrap {
    max-height: 320px;
    overflow-y: auto;
    overflow-x: auto;
}
.fy-table-wrap::-webkit-scrollbar { width: 4px; height: 4px; }
.fy-table-wrap::-webkit-scrollbar-track { background: #f4f6fb; }
.fy-table-wrap::-webkit-scrollbar-thumb { background: #c7d2fe; border-radius: 4px; }

/* show-more toggle */
.fy-show-more {
    display: flex; align-items: center; justify-content: center; gap: 6px;
    padding: 8px; border-top: 1px solid #f0f2f7;
    font-size: 12px; font-weight: 700; color: #667eea;
    cursor: pointer; background: #fafbff;
    border-radius: 0 0 8px 8px;
    transition: background .15s;
}
.fy-show-more:hover { background: #eef2ff; }

/* ── Form ─────────────────────────────────────────────────────────── */
.st-form-label { font-size: 12px; font-weight: 700; color: #596579; margin-bottom: 4px; }
.st-form-label span.req { color: #e53e3e; }
.st-input { height: 40px; font-size: 13px; border: 1.5px solid #e2e8f0; border-radius: 8px; padding: 0 12px; width: 100%; color: #1a2340; background: #fff; }
.st-input:focus { border-color: #667eea; outline: none; box-shadow: 0 0 0 3px rgba(102,126,234,.15); }
select.st-input { padding: 0 10px; }

/* ── Info box ─────────────────────────────────────────────────────── */
.st-info-box {
    padding: 12px 14px; border-radius: 8px;
    background: #f5f7ff; border-left: 3px solid #667eea;
    font-size: 12px; color: #596579; line-height: 1.8;
}
.st-info-box .ib-title {
    font-size: 10px; font-weight: 800; color: #667eea;
    text-transform: uppercase; letter-spacing: .5px; margin-bottom: 6px;
}
.st-info-box ul { margin: 0; padding-left: 16px; }

/* ── Action buttons ───────────────────────────────────────────────── */
.st-icon-btn {
    width: 30px; height: 30px; border-radius: 7px; border: none;
    display: inline-flex; align-items: center; justify-content: center;
    font-size: 13px; cursor: pointer; transition: all .15s;
}
.st-icon-btn.del   { background: #fff5f5; color: #e53e3e; }
.st-icon-btn.del:hover   { background: #e53e3e; color: #fff; }
.st-badge { display: inline-flex; align-items: center; gap: 4px; padding: 3px 8px; border-radius: 20px; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .3px; }
</style>

<div class="pcoded-inner-content st-page">
<div class="main-body"><div class="page-wrapper"><div class="page-body">


<div class="st-page-header">
    <div class="row align-items-center">
        <div class="col-md-8" style="position:relative;z-index:1;">
            <div style="display:inline-flex;align-items:center;gap:5px;background:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.25);border-radius:20px;padding:3px 12px;font-size:11px;font-weight:700;letter-spacing:.5px;margin-bottom:6px;">
                <i class="ti-settings"></i> Settings
            </div>
            <h4>Application Settings</h4>
            <div class="sub">Configure financial years and application preferences.</div>
        </div>
        <div class="col-md-4 text-right mt-2 mt-md-0" style="position:relative;z-index:1;">
            <a href="<?php echo e(route('dashboard')); ?>" class="btn btn-sm" style="background:rgba(255,255,255,.15);color:#fff;border:1px solid rgba(255,255,255,.3);font-weight:600;border-radius:8px;padding:6px 16px;">
                <i class="ti-home mr-1"></i> Dashboard
            </a>
        </div>
    </div>
</div>

<?php echo $__env->make('partials.flash', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>


<?php if(session('success')): ?>
<div class="alert alert-success alert-dismissible fade show" style="border-radius:8px;font-size:13px;" role="alert">
    <i class="ti-check-box mr-2"></i><?php echo e(session('success')); ?>

    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
</div>
<?php endif; ?>
<?php if($errors->any()): ?>
<div class="alert alert-danger alert-dismissible fade show" style="border-radius:8px;font-size:13px;" role="alert">
    <i class="ti-alert mr-2"></i><?php echo e($errors->first()); ?>

    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
</div>
<?php endif; ?>


<div class="row">

    
    <div class="col-md-3 mb-3 mb-md-0">
        <div class="st-nav" id="settingsNav">
            <div class="st-nav-item active" data-tab="tab-all-settings">
                <div class="nav-icon"><i class="ti-server"></i></div>
                All Settings
                <span class="nav-count"><?php echo e($allSettings->count()); ?></span>
            </div>
            <?php if(userCan('view_settings_financial_year')): ?>
            <div class="st-nav-item" data-tab="tab-financial-year">
                <div class="nav-icon"><i class="ti-calendar"></i></div>
                Financial Year
                <span class="nav-count"><?php echo e($financialYears->count()); ?></span>
            </div>
            <?php endif; ?>
            <?php if(userCan('view_settings_branch_default')): ?>
            <div class="st-nav-item" data-tab="tab-branch">
                <div class="nav-icon"><i class="ti-layers"></i></div>
                Branch Settings
            </div>
            <?php endif; ?>
            <?php if(showAllMenu()): ?>
            <div class="st-nav-item" data-tab="tab-limits">
                <div class="nav-icon"><i class="ti-lock"></i></div>
                Account Limits
            </div>
            <?php endif; ?>
        </div>
    </div>

    
    <div class="col-md-9">

        
        <div class="st-tab-pane active" id="tab-all-settings">
            <div class="st-section-label">
                <i class="ti-server" style="color:#667eea;font-size:12px;"></i>
                All Settings
            </div>
            <div class="st-card">
                <div class="st-card-head">
                    <h6>
                        <i class="ti-server" style="color:#667eea;"></i>
                        Settings Index
                        <span style="background:#eef2ff;color:#667eea;font-size:10px;font-weight:700;padding:2px 8px;border-radius:12px;">
                            <?php echo e($allSettings->count()); ?>

                        </span>
                    </h6>
                </div>
                <div class="st-card-body" style="padding:0;">
                    <table class="table table-hover" style="margin-bottom:0;min-width:500px;">
                        <thead>
                            <tr>
                                <th style="width:35%;border-top:0;">Setting</th>
                                <th style="width:18%;border-top:0;">Key</th>
                                <th style="width:32%;border-top:0;">Value</th>
                                <th style="width:15%;border-top:0;text-align:center;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $allSettings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $setting): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr id="setting-row-<?php echo e($setting->id); ?>">
                                <td style="vertical-align:middle;">
                                    <strong style="font-size:13px;"><?php echo e($setting->label ?? $setting->key); ?></strong>
                                    <div style="font-size:10px;color:#8a94a6;font-weight:600;text-transform:uppercase;letter-spacing:.3px;">
                                        <?php echo e($setting->group); ?>

                                    </div>
                                </td>
                                <td style="vertical-align:middle;">
                                    <code style="font-size:11px;background:#f4f6fb;padding:2px 6px;border-radius:4px;"><?php echo e($setting->key); ?></code>
                                </td>
                                <td style="vertical-align:middle;">
                                    <span class="setting-value" id="sv-<?php echo e($setting->id); ?>"
                                          style="font-size:13px;color:#596579;max-width:200px;display:inline-block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                                        <?php echo e($setting->value ?? '<empty>'); ?>

                                    </span>
                                    <form method="POST" action="<?php echo e(route('settings.update')); ?>"
                                          id="sef-<?php echo e($setting->id); ?>" style="display:none;" class="setting-edit-form">
                                        <?php echo csrf_field(); ?>
                                        <input type="hidden" name="key" value="<?php echo e($setting->key); ?>">
                                        <div class="input-group input-group-sm" style="flex-wrap:nowrap;">
                                            <input type="text" name="value" class="form-control"
                                                   value="<?php echo e($setting->value); ?>"
                                                   style="height:32px;font-size:12px;border-radius:6px 0 0 6px;border:1.5px solid #667eea;">
                                            <div class="input-group-append">
                                                <button type="submit" class="btn btn-sm"
                                                        style="background:#667eea;color:#fff;border-radius:0 6px 6px 0;border:none;height:32px;padding:0 10px;font-size:12px;font-weight:700;">
                                                    Save
                                                </button>
                                                <button type="button" class="btn btn-sm"
                                                        style="background:#f4f6fb;color:#596579;border:1.5px solid #e2e8f0;border-radius:6px;height:32px;padding:0 8px;font-size:12px;font-weight:600;margin-left:4px;"
                                                        onclick="cancelEdit(<?php echo e($setting->id); ?>)">
                                                    Cancel
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </td>
                                <td style="vertical-align:middle;text-align:center;">
                                    <button class="st-icon-btn" style="background:#eef2ff;color:#667eea;"
                                            onclick="toggleEdit(<?php echo e($setting->id); ?>)"
                                            title="Edit this setting">
                                        <i class="ti-pencil"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="4" class="text-center py-4" style="color:#b0bac9;">
                                    <i class="ti-server" style="font-size:28px;display:block;margin-bottom:8px;opacity:.4;"></i>
                                    <div style="font-size:13px;font-weight:600;">No settings found</div>
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <?php if(userCan('view_settings_financial_year')): ?>
        <div class="st-tab-pane" id="tab-financial-year">

            <div class="st-section-label">
                <i class="ti-calendar" style="color:#667eea;font-size:12px;"></i>
                Financial Year
            </div>

            <div class="row">

                
                <div class="col-lg-8 col-md-12">
                    <div class="st-card">
                        <div class="st-card-head">
                            <h6>
                                <i class="ti-calendar" style="color:#667eea;"></i>
                                Financial Years
                                <span style="background:#eef2ff;color:#667eea;font-size:10px;font-weight:700;padding:2px 8px;border-radius:12px;">
                                    <?php echo e($financialYears->count()); ?>

                                </span>
                            </h6>
                        </div>
                        <div class="st-card-body">

                            
                            <?php if($currentFY): ?>
                            <div class="st-fy-active-banner">
                                <div class="af-icon"><i class="ti-check"></i></div>
                                <div>
                                    <div class="af-label">Active Financial Year</div>
                                    <div class="af-value">FY <?php echo e($currentFY->label); ?></div>
                                    <div class="af-range">
                                        <?php echo e($currentFY->start_date->format('d M Y')); ?> &mdash; <?php echo e($currentFY->end_date->format('d M Y')); ?>

                                    </div>
                                </div>
                            </div>
                            <?php else: ?>
                            <div class="st-no-fy-banner">
                                <div style="width:32px;height:32px;border-radius:8px;background:#fde68a;color:#92400e;display:flex;align-items:center;justify-content:center;font-size:14px;flex-shrink:0;">
                                    <i class="ti-alert"></i>
                                </div>
                                <div>
                                    <div style="font-size:12px;font-weight:700;color:#92400e;">No Active Financial Year</div>
                                    <div style="font-size:11px;color:#b45309;">Data from all years is shown. Create and activate a year below.</div>
                                </div>
                            </div>
                            <?php endif; ?>

                            
                            <div class="fy-table-wrap">
                                <table class="table table-hover" id="fyTable">
                                    <thead>
                                        <tr>
                                            <th>Financial Year</th>
                                            <th>Start Date</th>
                                            <th>End Date</th>
                                            <th class="text-center">Status</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__empty_1 = true; $__currentLoopData = $financialYears; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $fy): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <tr class="<?php echo e($fy->is_default ? 'fy-active' : ''); ?> <?php echo e($idx >= 3 ? 'fy-hidden' : ''); ?>">
                                            <td>
                                                <strong style="color:<?php echo e($fy->is_default ? '#276749' : '#1a2340'); ?>;">
                                                    FY <?php echo e($fy->label); ?>

                                                </strong>
                                                <?php if($fy->is_default): ?>
                                                    <span class="st-badge ml-1" style="background:#c6f6d5;color:#276749;">
                                                        <i class="ti-check" style="font-size:8px;"></i> Active
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                            <td style="color:#596579;"><?php echo e($fy->start_date->format('d M Y')); ?></td>
                                            <td style="color:#596579;"><?php echo e($fy->end_date->format('d M Y')); ?></td>
                                            <td class="text-center">
                                                <?php if($fy->is_default): ?>
                                                    <span class="st-badge" style="background:#c6f6d5;color:#276749;">Current</span>
                                                <?php else: ?>
                                                    <span class="st-badge" style="background:#f4f6fb;color:#8a94a6;">Inactive</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <div style="display:inline-flex;gap:4px;align-items:center;">
                                                    <?php if(!$fy->is_default): ?>
                                                    <form method="POST" action="<?php echo e(route('settings.fy.setDefault', $fy->id)); ?>" class="d-inline">
                                                        <?php echo csrf_field(); ?>
                                                        <button type="submit" class="btn btn-sm"
                                                            style="height:30px;padding:0 10px;border-radius:7px;background:#eef2ff;color:#667eea;font-size:11px;font-weight:700;border:none;"
                                                            title="Set as Active Year"
                                                            onclick="return confirm('Set FY <?php echo e($fy->label); ?> as active?\n\nAll modules will filter data for this year only.')">
                                                            <i class="ti-check mr-1"></i> Set Active
                                                        </button>
                                                    </form>
                                                    <form method="POST" action="<?php echo e(route('settings.fy.destroy', $fy->id)); ?>" class="d-inline">
                                                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                                        <button type="submit" class="st-icon-btn del" title="Delete"
                                                            onclick="return confirm('Delete FY <?php echo e($fy->label); ?>? This cannot be undone.')">
                                                            <i class="ti-trash"></i>
                                                        </button>
                                                    </form>
                                                    <?php else: ?>
                                                    <span style="font-size:11px;color:#8a94a6;">Active year</span>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <tr>
                                            <td colspan="5" class="text-center py-4" style="color:#b0bac9;">
                                                <i class="ti-calendar" style="font-size:28px;display:block;margin-bottom:8px;opacity:.4;"></i>
                                                <div style="font-size:13px;font-weight:600;margin-bottom:4px;">No financial years yet</div>
                                                <div style="font-size:11px;">Use the form on the right to add one.</div>
                                            </td>
                                        </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php if($financialYears->count() > 3): ?>
                            <div class="fy-show-more" id="fyShowMore" onclick="toggleFYRows(this)">
                                <i class="ti-angle-down" id="fyToggleIcon"></i>
                                <span id="fyToggleText">Show <?php echo e($financialYears->count() - 3); ?> more</span>
                            </div>
                            <?php endif; ?>

                        </div>
                    </div>
                </div>

                
                <div class="col-lg-4 col-md-12">
                    <div class="st-card">
                        <div class="st-card-head">
                            <h6><i class="ti-plus" style="color:#48bb78;"></i> Add Financial Year</h6>
                        </div>
                        <div class="st-card-body">
                            <?php
                                $lastFY       = $financialYears->sortByDesc('start_date')->first();
                                $nextStartYear = $lastFY
                                    ? $lastFY->start_date->year + 1
                                    : (now()->month >= 4 ? now()->year : now()->year - 1);
                                $nextLabel     = \App\Models\FinancialYear::generateLabel($nextStartYear);
                                $nextStart     = $nextStartYear . '-04-01';
                                $nextEnd       = ($nextStartYear + 1) . '-03-31';
                            ?>

                            <div style="background:#f5f7ff;border:1.5px dashed #c7d2fe;border-radius:8px;padding:14px;margin-bottom:14px;text-align:center;">
                                <div style="font-size:10px;font-weight:700;color:#8a94a6;text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px;">
                                    Next Financial Year to Create
                                </div>
                                <div style="font-size:22px;font-weight:800;color:#4f46e5;letter-spacing:.5px;margin-bottom:4px;">
                                    FY <?php echo e($nextLabel); ?>

                                </div>
                                <div style="font-size:12px;color:#6b7280;">
                                    01 Apr <?php echo e($nextStartYear); ?> &mdash; 31 Mar <?php echo e($nextStartYear + 1); ?>

                                </div>
                            </div>

                            <form method="POST" action="<?php echo e(route('settings.fy.store')); ?>">
                                <?php echo csrf_field(); ?>
                                <input type="hidden" name="start_year" value="<?php echo e($nextStartYear); ?>">
                                <?php $__errorArgs = ['start_year'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div style="color:#e53e3e;font-size:11px;margin-bottom:8px;"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                <button type="submit" class="btn btn-primary btn-block" style="border-radius:8px;font-weight:700;height:42px;font-size:14px;">
                                    <i class="ti-plus mr-1"></i> Create FY <?php echo e($nextLabel); ?>

                                </button>
                            </form>

                            <div class="st-info-box mt-3">
                                <div class="ib-title"><i class="ti-info-alt mr-1"></i> How it works</div>
                                <ul>
                                    <li>Create financial years as needed</li>
                                    <li>Click <strong>Set Active</strong> to activate a year</li>
                                    <li>Trips, Expenses &amp; Reports filter automatically</li>
                                    <li>Switch years anytime — no data is lost</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <?php endif; ?>

        <?php if(userCan('view_settings_branch_default')): ?>
        <div class="st-tab-pane" id="tab-branch">

            <div class="st-section-label">
                <i class="ti-layers" style="color:#667eea;font-size:12px;"></i>
                Branch Settings
            </div>

            <div class="st-card">
                <div class="st-card-head">
                    <h6><i class="ti-layers" style="color:#667eea;"></i> Default Branch &amp; Preferences</h6>
                </div>
                <div class="st-card-body">
                    <form method="POST" action="<?php echo e(route('settings.branch.update')); ?>">
                        <?php echo csrf_field(); ?>

                        <div class="row">
                            <div class="col-md-6">
                                <label class="st-form-label">Default Branch</label>
                                <select name="default_branch" class="select2 form-control st-input">
                                    <option value="">— Select Default Branch —</option>
                                    <?php $__currentLoopData = $branches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $branch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($branch->id); ?>" <?php echo e(($branchSettings['default_branch']->value ?? '') == $branch->id ? 'selected' : ''); ?>>
                                            <?php echo e($branch->branch_name); ?> (<?php echo e(optional($branch->company)->company_name ?? 'N/A'); ?>)
                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <small style="color:#8a94a6;font-size:11px;">
                                    This branch will be pre-selected when creating new records.
                                </small>
                            </div>
                        </div>

                        <div style="margin-top:18px;">
                            <button type="submit" class="btn btn-primary" style="border-radius:8px;font-weight:700;height:40px;padding:0 24px;font-size:13px;">
                                <i class="ti-check mr-1"></i> Save Branch Settings
                            </button>
                        </div>
                    </form>

                    <div class="st-info-box mt-3">
                        <div class="ib-title"><i class="ti-info-alt mr-1"></i> How it works</div>
                        <ul>
                            <li>Set a <strong>Default Branch</strong> to auto-select it on all forms</li>
                            <li>Branch settings are used globally across all modules</li>
                            <li>Change anytime — existing data is not affected</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if(showAllMenu()): ?>
        <div class="st-tab-pane" id="tab-limits">

            <div class="st-section-label">
                <i class="ti-lock" style="color:#667eea;font-size:12px;"></i>
                Account Limits
            </div>

            <div class="st-card">
                <div class="st-card-head">
                    <h6><i class="ti-lock" style="color:#667eea;"></i> Company &amp; Branch Add Limits</h6>
                </div>
                <div class="st-card-body">
                    <form method="POST" action="<?php echo e(route('settings.limits.update')); ?>">
                        <?php echo csrf_field(); ?>

                        <div class="row">
                            <div class="col-md-4">
                                <label class="st-form-label">Company Add Limit</label>
                                <input type="number" name="company_limit" class="form-control st-input"
                                       value="<?php echo e($companyLimit); ?>" placeholder="e.g. 1" min="0">
                                <small style="color:#8a94a6;font-size:11px;">
                                    Maximum companies allowed to add. Leave empty for unlimited.
                                </small>
                            </div>
                            <div class="col-md-4">
                                <label class="st-form-label">Branch Add Limit</label>
                                <input type="number" name="branch_limit" class="form-control st-input"
                                       value="<?php echo e($branchLimit); ?>" placeholder="e.g. 5" min="0">
                                <small style="color:#8a94a6;font-size:11px;">
                                    Maximum branches allowed to add. Leave empty for unlimited.
                                </small>
                            </div>
                        </div>

                        <div style="margin-top:18px;">
                            <button type="submit" class="btn btn-primary" style="border-radius:8px;font-weight:700;height:40px;padding:0 24px;font-size:13px;">
                                <i class="ti-check mr-1"></i> Save Limits
                            </button>
                        </div>
                    </form>

                    <div class="st-info-box mt-3">
                        <div class="ib-title"><i class="ti-info-alt mr-1"></i> How it works</div>
                        <ul>
                            <li>Set a <strong>Company Add Limit</strong> to restrict how many companies can be created</li>
                            <li>Set a <strong>Branch Add Limit</strong> to restrict how many branches can be created</li>
                            <li>When limit is reached, the "Add" button will be hidden and users will see a contact-support message</li>
                            <li>Leave a field empty for <strong>unlimited</strong> additions</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

    </div>
</div>

</div></div></div></div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
function toggleEdit(id) {
    var valSpan = document.getElementById('sv-' + id);
    var form    = document.getElementById('sef-' + id);
    if (form.style.display === 'none') {
        form.style.display = 'block';
        valSpan.style.display = 'none';
    } else {
        form.style.display = 'none';
        valSpan.style.display = 'inline-block';
    }
}
function cancelEdit(id) {
    var valSpan = document.getElementById('sv-' + id);
    var form    = document.getElementById('sef-' + id);
    form.style.display = 'none';
    valSpan.style.display = 'inline-block';
}

function toggleFYRows(btn) {
    var hidden  = document.querySelectorAll('#fyTable tbody tr.fy-hidden');
    var visible = document.querySelectorAll('#fyTable tbody tr:not(.fy-hidden)');
    var icon    = document.getElementById('fyToggleIcon');
    var text    = document.getElementById('fyToggleText');
    var wrap    = document.querySelector('.fy-table-wrap');

    if (hidden.length > 0) {
        hidden.forEach(function(r){ r.classList.remove('fy-hidden'); });
        wrap.style.maxHeight = '320px';
        icon.className = 'ti-angle-up';
        text.textContent = 'Show less';
    } else {
        var rows = document.querySelectorAll('#fyTable tbody tr');
        rows.forEach(function(r, i){ if(i >= 3) r.classList.add('fy-hidden'); });
        wrap.style.maxHeight = 'none';
        wrap.style.overflowY = 'visible';
        icon.className = 'ti-angle-down';
        text.textContent = 'Show ' + (rows.length - 3) + ' more';
    }
}

// ── Tab switching ──────────────────────────────────────────────────
(function() {
    var navItems = document.querySelectorAll('#settingsNav .st-nav-item');

    function switchTab(tabId) {
        if (!tabId) return;
        navItems.forEach(function(n) {
            n.classList.toggle('active', n.getAttribute('data-tab') === tabId);
        });
        var panes = document.querySelectorAll('.st-tab-pane');
        panes.forEach(function(p) { p.classList.remove('active'); });
        var target = document.getElementById(tabId);
        if (target) target.classList.add('active');
    }

    navItems.forEach(function(item) {
        item.addEventListener('click', function() {
            var tabId = this.getAttribute('data-tab');
            switchTab(tabId);
            if (history.pushState) {
                history.pushState(null, null, '#' + tabId.replace('tab-', ''));
            }
        });
    });

    // Check URL hash on load
    var hash = window.location.hash.replace('#', '');
    if (hash) {
        var matchedTab = 'tab-' + hash;
        if (document.getElementById(matchedTab)) {
            switchTab(matchedTab);
        }
    }
})();
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\laragon\www\Kovai-Trans\resources\views/Settings/Settings.blade.php ENDPATH**/ ?>