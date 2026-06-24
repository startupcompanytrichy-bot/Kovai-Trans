@php
$trip = $trip ?? null;
$selectedBillingType = old('billing_type', $trip->billing_type ?? 'fixed');
$billingTypes = [
'fixed' => 'Fixed',
'per_tonne' => 'Per Tonne',
'per_kg' => 'Per Kg',
'per_km' => 'Per Km',
'per_trip' => 'Per Trip',
'per_day' => 'Per Day',
'per_hour' => 'Per Hour',
'per_litre' => 'Per Litre',
'per_bag' => 'Per Bag',
];
@endphp

<style>
    .trip-form {
        max-width: 1180px;
    }

    .trip-form h6 {
        display: flex;
        align-items: center;
        gap: 8px;
        margin: 20px 0 14px;
        padding: 10px 12px;
        border-left: 3px solid #667eea;
        background: #f7f8fc;
        color: #303549;
        font-size: 13px;
        font-weight: 800;
        letter-spacing: 0;
    }

    .trip-form h6:first-of-type {
        margin-top: 0;
    }

    .trip-form .form-group {
        margin-bottom: 16px;
    }

    .trip-form label {
        margin-bottom: 6px;
        color: #596579;
        font-size: 12px;
        font-weight: 700;
    }

    .trip-form .form-control {
        min-height: 45px;
        border-color: #d7dce5;
        color: #303549;
        font-size: 14px;
    }

    .trip-form .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 2px rgba(102, 126, 234, .12);
    }

    .trip-form textarea.form-control {
        min-height: 92px;
        resize: vertical;
    }

    .trip-form .select2-container {
        width: 100% !important;
    }

    .trip-form .select2-container--default .select2-selection--single {
        min-height: 45px !important;
        height: 45px !important;
        border-color: #d7dce5 !important;
    }

    .trip-form .select2-container--default.select2-container--focus .select2-selection--single,
    .trip-form .select2-container--default.select2-container--open .select2-selection--single {
        border-color: #667eea !important;
        box-shadow: 0 0 0 2px rgba(102, 126, 234, .12) !important;
    }

    .trip-form .required-star {
        color: #dc3545;
    }

    .billing-type-group {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .billing-type-option input {
        display: none;
    }

    .billing-type-option span {
        display: inline-flex;
        align-items: center;
        min-height: 32px;
        padding: 8px 13px;
        border-radius: 7px;
        background: #e9ecef;
        color: #6c7890;
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
        border: 1px solid transparent;
    }

    .billing-type-option input:checked+span {
        background: #eaf1ff;
        border-color: #0d6efd;
        color: #0d47c2;
        box-shadow: 0 2px 6px rgba(13, 110, 253, .12);
    }

    .trip-input-group {
        display: flex;
        width: 100%;
        align-items: stretch;
    }

    .trip-input-addon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 38px;
        padding: 0 10px;
        border: 1px solid #ccc;
        background: #f8f9fa;
        color: #495057;
        font-weight: 600;
        min-height: 45px;
    }

    .trip-input-addon:first-child {
        border-right: 0;
        border-radius: 4px 0 0 4px;
    }

    .trip-input-addon:last-child {
        border-left: 0;
        border-radius: 0 4px 4px 0;
    }

    .trip-input-group .form-control {
        min-height: 45px;
        flex: 1 1 auto;
        width: 1%;
    }

    .trip-input-group .trip-input-addon:first-child+.form-control {
        border-radius: 0 4px 4px 0;
    }

    .trip-input-group .form-control:not(:last-child) {
        border-radius: 4px 0 0 4px;
    }

    @media (max-width: 767.98px) {
        .trip-form h6 {
            margin-top: 14px;
        }

        .billing-type-option span {
            min-width: calc(50vw - 34px);
            justify-content: center;
        }
    }

    @keyframes plSpin { to { transform: rotate(360deg); } }
</style>

<div class="trip-form">

    <input type="hidden" name="trip_no" value="{{ old('trip_no', $trip->trip_no ?? '') }}">
    {{-- status is injected by the parent view --}}

    <h6><i class="ti-clipboard"></i> Trip Details</h6>
    <div class="row align-items-end">
        <div class="col-md-6">
            <div class="form-group">
                <label>Select Party <span class="required-star">*</span></label>
                <select name="party_id" class="form-control select2-trip @error('party_id') is-invalid @enderror" required>
                    <option value="">Eg: Arjun Reddy</option>
                    @foreach($parties as $party)
                    <option value="{{ $party->id }}" {{ (string)old('party_id', $trip->party_id ?? '') === (string)$party->id ? 'selected' : '' }}>
                        {{ $party->company_name ?: $party->name }}
                    </option>
                    @endforeach
                </select>
                @error('party_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label>Truck Registration No. <span class="required-star">*</span></label>
                <select name="vehicle_id" id="vehicleIdSelect"
                    class="form-control @error('vehicle_id') is-invalid @enderror" required>
                    <option value="">Eg: KA 02 Q 1234</option>
                    @foreach($vehicles as $vehicle)
                    @php
                        $ts = $vehicle->trip_status ?? null;
                        $isCurrentTripVehicle = $trip && (string)$trip->vehicle_id === (string)$vehicle->id;
                        $shouldDisable = $ts === 'running' && !$isCurrentTripVehicle;
                    @endphp
                    <option value="{{ $vehicle->id }}"
                        data-vtype="{{ strtolower($vehicle->owner_type ?? 'own') }}"
                        data-vstatus="{{ strtolower($vehicle->status ?? 'available') }}"
                        data-trip-status="{{ $ts ?? '' }}"
                        {{ $shouldDisable ? 'disabled' : '' }}
                        {{ (string)old('vehicle_id', $trip->vehicle_id ?? '') === (string)$vehicle->id ? 'selected' : '' }}>
                        {{ $vehicle->vehicle_number }}{{ $vehicle->vehicle_name ? ' — '.$vehicle->vehicle_name : '' }}
                    </option>
                    @endforeach
                </select>
                @error('vehicle_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
    </div>

    <h6><i class="ti-map-alt"></i> Route</h6>

                            @php
                                $savedFrom = old('from_location', $trip->from_location ?? '');
                                $savedTo   = old('to_location',   $trip->to_location   ?? '');
                            @endphp
                            <input type="hidden" name="from_location" id="fromLocationVal" value="{{ $savedFrom }}">
                            <input type="hidden" name="to_location"   id="toLocationVal"   value="{{ $savedTo }}">

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>From Place <span class="required-star">*</span></label>
                                        <select id="fromPlaceSelect" class="form-control" style="width:100%;" required>
                                            <option value="">Search state, district or city…</option>
                                            @if($savedFrom)
                                                <option value="{{ $savedFrom }}" selected>{{ $savedFrom }}</option>
                                            @endif
                                        </select>
                                        @error('from_location')<div class="text-danger" style="font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>To Place <span class="required-star">*</span></label>
                                        <select id="toPlaceSelect" class="form-control" style="width:100%;" required>
                                            <option value="">Search state, district or city…</option>
                                            @if($savedTo)
                                                <option value="{{ $savedTo }}" selected>{{ $savedTo }}</option>
                                            @endif
                                        </select>
                                        @error('to_location')<div class="text-danger" style="font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>
                                            Distance (KM)
                                            <span id="distAutoTag" style="display:none;background:#eef2ff;color:#667eea;font-size:9px;padding:1px 6px;border-radius:10px;font-weight:700;margin-left:4px;">AUTO</span>
                                        </label>
                                        <div class="trip-input-group">
                                            <input type="number" step="0.1" min="0" name="distance_km" id="distKmInput"
                                                class="form-control @error('distance_km') is-invalid @enderror"
                                                value="{{ old('distance_km', $trip->distance_km ?? '') }}" placeholder="0">
                                            <button type="button" id="recalcDistBtn" class="trip-input-addon"
                                                style="cursor:pointer;background:#667eea;color:#fff;border-color:#667eea;"
                                                title="Recalculate distance">
                                                <i class="ti-reload" id="recalcDistIcon" style="font-size:12px;"></i>
                                            </button>
                                        </div>
                                        <small id="distNote" style="font-size:10px;color:#8a94a6;margin-top:3px;display:block;"></small>
                                        @error('distance_km')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                            </div>

    


    <h6><i class="ti-wallet"></i> Billing Information</h6>
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label>Party Billing Type <span class="required-star">*</span></label>
                <div class="billing-type-group">
                    @foreach($billingTypes as $value => $label)
                    <label class="billing-type-option">
                        <input type="radio" name="billing_type" value="{{ $value }}" {{ $selectedBillingType === $value ? 'checked' : '' }}>
                        <span>{{ $label }}</span>
                    </label>
                    @endforeach
                </div>
                @error('billing_type')<div class="text-danger" style="font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label>Party Freight Amount <span class="required-star">*</span></label>
                <div class="trip-input-group">
                    <span class="trip-input-addon">₹</span>
                    <input type="number" step="0.01" min="0" name="freight_amount" id="freightAmount"
                        class="form-control @error('freight_amount') is-invalid @enderror"
                        value="{{ old('freight_amount', $trip->freight_amount ?? '') }}" placeholder="Eg: 45,000" required>
                </div>
                @error('freight_amount')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label>Start Date <span class="required-star">*</span></label>
                <input type="date" name="trip_date" class="form-control @error('trip_date') is-invalid @enderror"
                    value="{{ old('trip_date', $trip && $trip->trip_date ? $trip->trip_date->format('Y-m-d') : date('Y-m-d')) }}" required>
                @error('trip_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label>Start Kms Reading</label>
                <div class="trip-input-group">
                    <input type="number" step="0.01" min="0" name="start_kms_reading"
                        class="form-control @error('start_kms_reading') is-invalid @enderror"
                        value="{{ old('start_kms_reading', $trip->start_kms_reading ?? '') }}" placeholder="Start readings">
                    <span class="trip-input-addon">KMs</span>
                </div>
                @error('start_kms_reading')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            </div>
        </div>
    </div>

    <h6><i class="ti-more-alt"></i> More Details</h6>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label>LR No</label>
                @if($trip && $trip->id)
                {{-- Edit mode: LR number is read-only --}}
                <input type="text" name="lr_no" class="form-control @error('lr_no') is-invalid @enderror"
                    value="{{ old('lr_no', $trip->lr_no ?? '') }}" placeholder="N/A" style="min-height:45px;background:#f8f9fa;" readonly>
                @else
                {{-- Create mode: LR number with generate button --}}
                <div class="trip-input-group" style="border-radius:4px;">
                    <input type="text" name="lr_no" class="form-control lr-number-field @error('lr_no') is-invalid @enderror"
                        value="{{ old('lr_no', '') }}" placeholder="Auto-generated" style="min-height:45px;">
                    <button type="button" class="trip-input-addon lr-generate-btn" style="background:#667eea;color:#fff;border-color:#667eea;cursor:pointer;" title="Generate new LR Number">
                        <i class="ti-reload" style="font-size:13px;"></i>
                    </button>
                </div>
                @endif
                @error('lr_no')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label>Material</label>
                <input type="text" name="material" class="form-control @error('material') is-invalid @enderror"
                    value="{{ old('material', $trip->material ?? '') }}" placeholder="Enter Material Name">
                @error('material')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label>Assign Driver</label>
                <select name="driver_id" id="driverIdSelect"
                    class="form-control @error('driver_id') is-invalid @enderror">
                    <option value="">— No Driver —</option>
                    @foreach($drivers as $driver)
                    <option value="{{ $driver->id }}"
                        data-type="{{ $driver->driver_type ?? 'own' }}"
                        data-mobile="{{ $driver->mobile }}"
                        data-license="{{ $driver->license_number }}"
                        data-city="{{ $driver->city }}"
                        data-state="{{ $driver->state }}"
                        {{ (string)old('driver_id', $trip->driver_id ?? '') === (string)$driver->id ? 'selected' : '' }}>
                        {{ $driver->name }}{{ $driver->mobile ? ' — '.$driver->mobile : '' }}
                    </option>
                    @endforeach
                </select>
                @error('driver_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label>Assign Supplier</label>
                <select name="supplier_id" class="form-control select2-trip @error('supplier_id') is-invalid @enderror">
                    <option value="">— No Supplier —</option>
                    @foreach($suppliers as $supplier)
                    <option value="{{ $supplier->id }}" {{ (string)old('supplier_id', $trip->supplier_id ?? '') === (string)$supplier->id ? 'selected' : '' }}>
                        {{ $supplier->name }}
                    </option>
                    @endforeach
                </select>
                @error('supplier_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label>Notes</label>
                <textarea name="remarks" rows="3" class="form-control @error('remarks') is-invalid @enderror"
                    placeholder="Notes">{{ old('remarks', $trip->remarks ?? '') }}</textarea>
                @error('remarks')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
    $(document).ready(function() {

        /* ── Standard Select2 (party, supplier) ── */
        if ($.fn.select2) {
            $('.select2-trip').each(function() {
                $(this).select2({
                    width: '100%',
                    allowClear: true,
                    placeholder: $(this).find('option:first').text()
                });
            });
        }

        /* ── From / To Place — Select2 with live Nominatim search ─────────
         * Same implementation as New Trip.
         * Stored value (hidden input): "Locality, District, State"
         * ─────────────────────────────────────────────────────────────── */
        (function () {
            var NOM_URL = 'https://nominatim.openstreetmap.org/search';

            function parseAddr(r) {
                var addr = r.address || {};
                var state    = addr.state || '';
                var district = addr.state_district || addr.county || addr.city || addr.town || '';
                var locality = addr.suburb || addr.neighbourhood || addr.quarter ||
                               addr.village || addr.town || addr.city_district || '';
                if (locality && locality.toLowerCase() === district.toLowerCase()) locality = '';
                if (!locality) {
                    locality = addr.city || addr.town || (r.display_name || '').split(',')[0].trim();
                    district = addr.state_district || addr.county || '';
                }
                if (!locality) locality = (r.display_name || '').split(',')[0].trim();
                return { locality: locality, district: district, state: state };
            }

            function fmtResult(item) {
                if (item.loading) {
                    return $('<div style="padding:6px 4px;color:#8a94a6;font-size:12px;"><i class="ti-reload" style="margin-right:6px;"></i>Searching…</div>');
                }
                if (!item.id) {
                    return $('<div style="padding:6px 4px;color:#b0bac9;font-size:12px;">' + item.text + '</div>');
                }
                var loc  = item.locality  || '';
                var dist = item.district  || '';
                var st   = item.state     || '';
                var crumbs = [];
                if (st)                       crumbs.push(st);
                if (dist && dist !== st)       crumbs.push(dist);
                if (loc  && loc  !== dist)     crumbs.push(loc);
                var breadcrumb = crumbs.join('<i class="ti-angle-right" style="font-size:8px;color:#b0bac9;margin:0 4px;vertical-align:middle;"></i>');
                var typeLabel = (item.osm_type || '').replace(/_/g,' ');
                var typeColor = '#8a94a6';
                if (typeLabel === 'suburb' || typeLabel === 'neighbourhood') typeColor = '#667eea';
                else if (typeLabel === 'city' || typeLabel === 'town')       typeColor = '#38a169';
                else if (typeLabel === 'village')                            typeColor = '#d97706';
                return $(
                    '<div style="padding:6px 2px;">' +
                        '<div style="display:flex;align-items:center;justify-content:space-between;gap:8px;">' +
                            '<div style="font-size:13px;font-weight:700;color:#1a2340;line-height:1.2;">' + loc + '</div>' +
                            (typeLabel ? '<span style="font-size:9px;font-weight:700;color:' + typeColor + ';background:rgba(0,0,0,.04);padding:2px 7px;border-radius:8px;white-space:nowrap;text-transform:capitalize;">' + typeLabel + '</span>' : '') +
                        '</div>' +
                        '<div style="font-size:11px;color:#8a94a6;margin-top:3px;line-height:1.3;">' + breadcrumb + '</div>' +
                    '</div>'
                );
            }

            function fmtSelection(item) {
                if (!item.id) return item.text;
                var parts = [];
                if (item.locality)                                    parts.push(item.locality);
                if (item.district && item.district !== item.locality) parts.push(item.district);
                if (item.state    && item.state    !== item.district) parts.push(item.state);
                return parts.length ? parts.join(', ') : item.text;
            }

            function initPlaceSelect(selector, hiddenId, placeholder) {
                var $sel     = $(selector);
                if (!$sel.length) return;
                var savedVal = $('#' + hiddenId).val() || '';

                $sel.select2({
                    width:              '100%',
                    placeholder:        placeholder,
                    allowClear:         true,
                    minimumInputLength: 2,
                    templateResult:     fmtResult,
                    templateSelection:  fmtSelection,
                    language: {
                        inputTooShort: function () { return 'Type at least 2 characters…'; },
                        noResults:     function () { return 'No places found. Try a different spelling.'; },
                        searching:     function () { return 'Searching India locations…'; }
                    },
                    ajax: {
                        url:      NOM_URL,
                        dataType: 'json',
                        delay:    350,
                        headers:  { 'Accept-Language': 'en' },
                        data: function (params) {
                            return {
                                q:              (params.term || '') + ', India',
                                format:         'json',
                                limit:          25,
                                countrycodes:   'in',
                                addressdetails: 1,
                                featuretype:    'settlement'
                            };
                        },
                        processResults: function (data) {
                            var seen  = {};
                            var items = [];
                            (data || []).forEach(function (r) {
                                var p = parseAddr(r);
                                if (!p.locality) return;
                                var key = p.locality + '||' + p.district + '||' + p.state;
                                if (seen[key]) return;
                                seen[key] = 1;
                                var fullValue = [p.locality, p.district, p.state].filter(Boolean).join(', ');
                                items.push({
                                    id: key, text: fullValue,
                                    locality: p.locality, district: p.district,
                                    state: p.state, osm_type: r.type || r.addresstype || ''
                                });
                            });
                            return { results: items };
                        },
                        cache: true
                    }
                });

                /* Restore saved value (edit mode) */
                if (savedVal) {
                    var sp      = savedVal.split(', ');
                    var loc     = sp[0] || savedVal;
                    var dist    = sp[1] || '';
                    var st      = sp.slice(2).join(', ') || '';
                    var optKey  = loc + '||' + dist + '||' + st;
                    var opt     = new Option(savedVal, optKey, true, true);
                    var optData = { id: optKey, text: savedVal, locality: loc, district: dist, state: st, osm_type: '' };
                    $sel.append(opt).trigger('change.select2');
                    $sel.data('select2').selection.update([optData]);
                }

                $sel.on('select2:select', function (e) {
                    var d = e.params.data;
                    var parts = [];
                    if (d.locality)                                    parts.push(d.locality);
                    if (d.district && d.district !== d.locality)       parts.push(d.district);
                    if (d.state    && d.state    !== d.district)       parts.push(d.state);
                    $('#' + hiddenId).val(parts.join(', '));
                    scheduleDistCalc();
                });
                $sel.on('select2:clear', function () {
                    $('#' + hiddenId).val('');
                });
            }

            /* ── Distance calculation via Laravel backend ── */
            var _distTimer = null;

            function scheduleDistCalc() {
                clearTimeout(_distTimer);
                if ($('#fromLocationVal').val() && $('#toLocationVal').val()) {
                    _distTimer = setTimeout(calcDist, 800);
                }
            }

            function calcDist() {
                var from = $('#fromLocationVal').val();
                var to   = $('#toLocationVal').val();
                if (!from || !to) return;

                var $btn  = $('#recalcDistBtn');
                var $icon = $('#recalcDistIcon');
                $btn.prop('disabled', true);
                $icon.css({ animation: 'plSpin .7s linear infinite', display: 'inline-block' });
                $('#distNote').show().text('Calculating distance…');

                $.ajax({
                    url:      '{{ route("api.general.distance") }}',
                    method:   'GET',
                    dataType: 'json',
                    data:     { from: from, to: to },
                    timeout:  15000,
                    success:  function (r) {
                        $btn.prop('disabled', false);
                        $icon.css('animation', '');
                        if (r && r.km) {
                            $('#distKmInput').val(r.km);
                            $('#distAutoTag').show();
                            var src = r.source === 'google' ? 'Google Maps' : (r.source === 'geoapify' ? 'Geoapify' : r.source === 'ors' ? 'OpenRouteService' : 'OSRM routing');
                            $('#distNote').show().text('Road distance via ' + src + '. You can edit.');
                        } else {
                            $('#distNote').text('Could not calculate — enter distance manually.');
                        }
                    },
                    error: function () {
                        $btn.prop('disabled', false);
                        $icon.css('animation', '');
                        $('#distNote').text('Could not calculate — enter distance manually.');
                    }
                });
            }

            $('#recalcDistBtn').on('click', calcDist);

            function bootLocationSelects() {
                if (typeof $.fn.select2 === 'undefined') {
                    setTimeout(bootLocationSelects, 100);
                    return;
                }
                initPlaceSelect('#fromPlaceSelect', 'fromLocationVal', 'Type to search origin (e.g. Chennai)');
                initPlaceSelect('#toPlaceSelect',   'toLocationVal',   'Type to search destination (e.g. Delhi)');
            }
            bootLocationSelects();

        }()); // end place-select IIFE

        /* ═══════════════════════════════════════════════════════════════
           VEHICLE — rich Select2 with color-safe CSS classes
        ═══════════════════════════════════════════════════════════════ */
        // Inject CSS so badge colors survive Select2 highlight override
        $('<style>')
            .text(
                '.veh-opt-own   { background:#dcfce7 !important; color:#16a34a !important; border:1px solid #16a34a !important; }' +
                '.veh-opt-rental{ background:#fee2e2 !important; color:#dc2626 !important; border:1px solid #dc2626 !important; }' +
                '.veh-opt-run   { background:#dbeafe !important; color:#1e40af !important; }' +
                '.veh-opt-plan  { background:#fef9c3 !important; color:#854d0e !important; }' +
                '.veh-opt-avail { background:#dcfce7 !important; color:#166534 !important; }' +
                '.veh-opt-badge { display:inline-flex;align-items:center;gap:4px;padding:2px 9px;border-radius:20px;font-size:10px;font-weight:700;white-space:nowrap; }' +
                '.veh-opt-dot   { width:6px;height:6px;border-radius:50%;display:inline-block; }'
            ).appendTo('head');

        function formatVehicleOption(option) {
            if (!option.id) return $('<span style="color:#b0bac9;font-size:13px;">Eg: KA 02 Q 1234</span>');
            var el         = option.element;
            var vtype      = ($(el).data('vtype')       || 'own').toLowerCase();
            var tripStatus = ($(el).data('trip-status') || '').toLowerCase();
            var isOwn      = vtype !== 'rental' && vtype !== 'market';
            var isRunning  = tripStatus === 'running';
            var isPlanned  = tripStatus === 'planned';

            var ownerClass = isOwn ? 'veh-opt-own' : 'veh-opt-rental';
            var tLabel     = isOwn ? 'Own' : 'Rental';

            var statusClass, sDotColor, sLabel;
            if (isRunning) {
                statusClass = 'veh-opt-run';   sDotColor = '#2563eb'; sLabel = 'Running';
            } else if (isPlanned) {
                statusClass = 'veh-opt-plan';  sDotColor = '#ca8a04'; sLabel = 'Planned';
            } else {
                statusClass = 'veh-opt-avail'; sDotColor = '#16a34a'; sLabel = 'Available';
            }

            var rawReg   = (option.text || '').split(' — ')[0].trim();
            var namePart = option.text.indexOf(' — ') !== -1 ? option.text.split(' — ').slice(1).join(' — ') : '';
            var opacity  = isRunning ? 'opacity:0.6;' : '';
            var note     = isRunning
                ? '<div style="font-size:10px;color:#1e40af;margin-top:2px;"><i class="ti-info-alt" style="margin-right:3px;"></i>On a running trip — cannot select</div>'
                : (isPlanned ? '<div style="font-size:10px;color:#854d0e;margin-top:2px;"><i class="ti-info-alt" style="margin-right:3px;"></i>Assigned to a planned trip</div>' : '');

            return $(
                '<div style="display:flex;align-items:center;gap:10px;padding:8px 4px;' + opacity + '">' +
                    '<div style="width:38px;height:38px;border-radius:9px;display:flex;align-items:center;justify-content:center;flex-shrink:0;" class="' + ownerClass + '">' +
                        '<i class="ti-truck" style="font-size:15px;"></i>' +
                    '</div>' +
                    '<div style="flex:1;min-width:0;">' +
                        '<div style="font-size:13px;font-weight:700;color:#1a2340;">' + rawReg + '</div>' +
                        (namePart ? '<div style="font-size:11px;color:#8a94a6;margin-top:1px;">' + namePart + '</div>' : '') +
                        note +
                    '</div>' +
                    '<div style="display:flex;flex-direction:column;align-items:flex-end;gap:4px;flex-shrink:0;">' +
                        '<span class="veh-opt-badge ' + ownerClass + '">' + tLabel + '</span>' +
                        '<span class="veh-opt-badge ' + statusClass + '">' +
                            '<span class="veh-opt-dot" style="background:' + sDotColor + ';"></span>' + sLabel +
                        '</span>' +
                    '</div>' +
                '</div>'
            );
        }

        function formatVehicleSelection(option) {
            if (!option.id) return $('<span style="color:#b0bac9;">Eg: KA 02 Q 1234</span>');
            var rawReg = (option.text || '').split(' — ')[0].trim();
            return $('<span style="font-size:13px;font-weight:700;color:#1a2340;">' + rawReg + '</span>');
        }
        $('#vehicleIdSelect').select2({
            width:             '100%',
            allowClear:        true,
            placeholder:       'Eg: KA 02 Q 1234',
            templateResult:    formatVehicleOption,
            templateSelection: formatVehicleSelection,
            escapeMarkup:      function(m) { return m; },
        });

        /* ═══════════════════════════════════════════════════════════════
           DRIVER — rich Select2 (initials avatar · name+mobile · Own/Rental badge)
        ═══════════════════════════════════════════════════════════════ */
        function formatDriverOption(option) {
            if (!option.id) return $('<span style="color:#b0bac9;font-size:13px;">— No Driver —</span>');
            var type = $(option.element).data('type') || 'own';
            var mobile = $(option.element).data('mobile') || '';
            var isOwn = type !== 'rental';
            var dColor = isOwn ? '#16a34a' : '#d97706';
            var dBg = isOwn ? '#f0fdf4' : '#fff8ed';
            var dLabel = isOwn ? 'Own' : 'Rental';
            var name = option.text.split(' — ')[0].trim();
            var initials = name.split(' ').slice(0, 2).map(function(w) {
                return w.charAt(0);
            }).join('').toUpperCase();
            return $(
                '<div style="display:flex;align-items:center;gap:10px;padding:7px 4px;">' +
                '<div style="width:38px;height:38px;border-radius:50%;background:' + dBg + ';border:2px solid ' + dColor + ';display:flex;align-items:center;justify-content:center;flex-shrink:0;">' +
                '<span style="font-size:13px;font-weight:800;color:' + dColor + ';line-height:1;">' + initials + '</span>' +
                '</div>' +
                '<div style="flex:1;min-width:0;">' +
                '<div style="font-size:13px;font-weight:700;color:#1a2340;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">' + name + '</div>' +
                (mobile ? '<div style="font-size:11px;color:#8a94a6;margin-top:1px;">' + mobile + '</div>' : '') +
                '</div>' +
                '<span style="padding:3px 10px;border-radius:20px;font-size:10px;font-weight:700;background:' + dBg + ';color:' + dColor + ';border:1px solid ' + dColor + ';flex-shrink:0;">' + dLabel + '</span>' +
                '</div>'
            );
        }

        function formatDriverSelection(option) {
            if (!option.id) return $('<span style="color:#b0bac9;">— No Driver —</span>');
            var name = (option.text || '').split(' — ')[0].trim();
            return $('<span style="font-size:13px;font-weight:700;color:#1a2340;">' + name + '</span>');
        }
        $('#driverIdSelect').select2({
            width: '100%',
            allowClear: true,
            placeholder: '— No Driver —',
            templateResult: formatDriverOption,
            templateSelection: formatDriverSelection,
            escapeMarkup: function(m) { return m; },
        });

        /* ── LR Number Auto-Generation (for edit form) ──────────────────── */
        function generateLRNumber() {
            var lrNo = '';
            $.ajax({
                url: '{{ route("trip.generate-lr") }}',
                method: 'GET',
                async: false,
                success: function(res) { lrNo = res.lr_no; },
                error: function() {
                    var year = new Date().getFullYear();
                    lrNo = 'LRN' + year + String(Math.floor(Math.random() * 900) + 100);
                }
            });
            return lrNo;
        }

        // Generate new LR Number on button click
        $('.lr-generate-btn').on('click', function(e) {
            e.preventDefault();
            var $btn = $(this);
            var $input = $btn.siblings('.lr-number-field');
            var originalHtml = $btn.html();
            $btn.html('<i class="ti-reload" style="font-size:13px;animation:sw-spin .6s linear infinite;display:inline-block;"></i>');
            $.ajax({
                url: '{{ route("trip.generate-lr") }}',
                method: 'GET',
                success: function(res) {
                    $input.val(res.lr_no).focus();
                    $btn.html('<i class="ti-check" style="font-size:13px;"></i>');
                    setTimeout(function() { $btn.html(originalHtml); }, 800);
                },
                error: function() { $btn.html(originalHtml); }
            });
        });

    });
</script>
@endpush