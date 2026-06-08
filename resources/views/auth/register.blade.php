@extends('layouts.app')

@section('title', 'Register')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .register-page { min-height: 100vh; display: flex; align-items: center; background: linear-gradient(135deg, #f8f9fa 0%, #e8f5e9 100%); padding: 40px 0; }
    .register-card { background: #fff; border-radius: 20px; box-shadow: 0 8px 40px rgba(0,0,0,0.1); }
    .register-header { background: linear-gradient(135deg, #1a5f2a 0%, #0d3320 100%); color: #fff; padding: 40px; border-radius: 20px 20px 0 0; text-align: center; }
    .register-body { padding: 40px; }
    .form-control { border-radius: 10px; padding: 12px 16px; border: 1px solid #e0e0e0; }
    .form-control:focus { border-color: #1a5f2a; box-shadow: 0 0 0 0.2rem rgba(26, 95, 42, 0.25); }
    .form-select { border-radius: 10px; padding: 12px 16px; }
    .btn-register { background: #1a5f2a; border: none; border-radius: 10px; padding: 14px; font-weight: 600; }
    .btn-register:hover { background: #124620; }
    .plan-option { border: 2px solid #e0e0e0; border-radius: 12px; padding: 16px; cursor: pointer; transition: all 0.2s; }
    .plan-option:hover, .plan-option.selected { border-color: #1a5f2a; background: #e8f5e9; }
    .plan-option input { display: none; }
    .back-link { color: #1a5f2a; text-decoration: none; }
    .back-link:hover { text-decoration: underline; }

    /* Select2 Custom Styling */
    .select2-container--default .select2-selection--single {
        border: 1px solid #e0e0e0;
        border-radius: 10px;
        height: 48px;
        padding: 8px 12px;
        font-size: 1rem;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 48px;
        right: 10px;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 30px;
        padding-left: 4px;
    }
    .select2-container--default .select2-selection--single:focus {
        border-color: #1a5f2a;
        outline: 0;
        box-shadow: 0 0 0 0.2rem rgba(26, 95, 42, 0.25);
    }
    .select2-dropdown {
        border-radius: 10px;
        border: 1px solid #e0e0e0;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #1a5f2a;
    }
    .select2-container { width: 100% !important; }
    .location-label { font-weight: 500; margin-bottom: 6px; display: block; }
</style>
@endpush

@section('content')
<div class="register-page">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="register-card">
                    <div class="register-header">
                        <a href="{{ route('landing') }}" class="text-white text-decoration-none mb-3 d-inline-block">
                            <i class="bi bi-arrow-left me-2"></i>Back to Home
                        </a>
                        <h2 class="fw-bold mb-2"><i class="bi bi-bank2 me-2"></i>Register Your VICOBA Group</h2>
                        <p class="opacity-75 mb-0">Start your 14-day free trial today. No credit card required.</p>
                    </div>
                    <div class="register-body">
                        <form method="POST" action="{{ route('register.submit') }}" id="registrationForm">
                            @csrf

                            <div class="row">
                                <div class="col-12">
                                    <h5 class="fw-bold mb-3"><i class="bi bi-person-badge me-2 text-primary"></i>Chairman Details</h5>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">First Name *</label>
                                    <input type="text" name="chairman_first_name" class="form-control @error('chairman_first_name') is-invalid @enderror" value="{{ old('chairman_first_name') }}" required>
                                    @error('chairman_first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Middle Name</label>
                                    <input type="text" name="chairman_middle_name" class="form-control @error('chairman_middle_name') is-invalid @enderror" value="{{ old('chairman_middle_name') }}">
                                    @error('chairman_middle_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Last Name *</label>
                                    <input type="text" name="chairman_last_name" class="form-control @error('chairman_last_name') is-invalid @enderror" value="{{ old('chairman_last_name') }}" required>
                                    @error('chairman_last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Phone Number *</label>
                                    <input type="text" name="chairman_phone" class="form-control @error('chairman_phone') is-invalid @enderror" value="{{ old('chairman_phone') }}" placeholder="+255..." required>
                                    @error('chairman_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Email Address *</label>
                                    <input type="email" name="chairman_email" class="form-control @error('chairman_email') is-invalid @enderror" value="{{ old('chairman_email') }}" required>
                                    @error('chairman_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            <hr class="my-4">

                            <div class="row">
                                <div class="col-12">
                                    <h5 class="fw-bold mb-3"><i class="bi bi-building me-2 text-primary"></i>Group Information</h5>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Group (VICOBA) Name *</label>
                                    <input type="text" name="group_name" class="form-control @error('group_name') is-invalid @enderror" value="{{ old('group_name') }}" required>
                                    @error('group_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Registration Number *</label>
                                    <input type="text" name="group_registration_number" class="form-control @error('group_registration_number') is-invalid @enderror" value="{{ old('group_registration_number') }}" required>
                                    @error('group_registration_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            {{-- Location Selection with Select2 Cascading --}}
                            <div class="row">
                                <div class="col-12 mb-2">
                                    <h6 class="fw-bold text-muted"><i class="bi bi-geo-alt me-2"></i>Group Location</h6>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="location-label">Region *</label>
                                    <select name="region_id" id="region" class="form-select location-select @error('region_id') is-invalid @enderror" required>
                                        <option value="">Select Region</option>
                                    </select>
                                    <input type="hidden" name="region" id="region_name" value="{{ old('region') }}">
                                    @error('region_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="location-label">District *</label>
                                    <select name="district_id" id="district" class="form-select location-select @error('district_id') is-invalid @enderror" required disabled>
                                        <option value="">Select District</option>
                                    </select>
                                    <input type="hidden" name="district" id="district_name" value="{{ old('district') }}">
                                    @error('district_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="location-label">Ward *</label>
                                    <select name="ward_id" id="ward" class="form-select location-select @error('ward_id') is-invalid @enderror" required disabled>
                                        <option value="">Select Ward</option>
                                    </select>
                                    <input type="hidden" name="ward" id="ward_name" value="{{ old('ward') }}">
                                    @error('ward_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="location-label">Village *</label>
                                    <select name="village_id" id="village" class="form-select location-select @error('village_id') is-invalid @enderror" required disabled>
                                        <option value="">Select Village</option>
                                    </select>
                                    <input type="hidden" name="village" id="village_name" value="{{ old('village') }}">
                                    @error('village_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            <hr class="my-4">

                            <div class="row">
                                <div class="col-12">
                                    <h5 class="fw-bold mb-3"><i class="bi bi-credit-card me-2 text-primary"></i>Subscription Plan</h5>
                                </div>
                                <div class="col-12 mb-3">
                                    @error('subscription_plan')<div class="alert alert-danger">{{ $message }}</div>@enderror
                                    <div class="row g-3">
                                        @foreach($plans as $plan)
                                            <div class="col-md-4">
                                                <label class="plan-option w-100 {{ old('subscription_plan') == $plan->slug ? 'selected' : '' }}">
                                                    <input type="radio" name="subscription_plan" value="{{ $plan->slug }}" {{ old('subscription_plan') == $plan->slug ? 'checked' : '' }} required>
                                                    <div class="fw-bold">{{ $plan->name }}</div>
                                                    <div class="text-primary fw-bold">{{ number_format($plan->price) }} TZS</div>
                                                    <small class="text-muted">/{{ $plan->billing_cycle }}</small>
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4">

                            <div class="row">
                                <div class="col-12">
                                    <h5 class="fw-bold mb-3"><i class="bi bi-shield-lock me-2 text-primary"></i>Account Security</h5>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Password *</label>
                                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Min 8 characters" required>
                                    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Confirm Password *</label>
                                    <input type="password" name="password_confirmation" class="form-control" required>
                                </div>
                            </div>

                            <div class="mb-4">
                                <div class="form-check">
                                    <input type="checkbox" name="terms" class="form-check-input @error('terms') is-invalid @enderror" id="terms" required>
                                    <label class="form-check-label" for="terms">
                                        I agree to the <a href="#" class="back-link">Terms and Conditions</a> and <a href="#" class="back-link">Privacy Policy</a> *
                                    </label>
                                    @error('terms')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <button type="submit" class="btn btn-register btn-primary w-100">
                                        <i class="bi bi-rocket-takeoff me-2"></i>Register & Start Trial
                                    </button>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <button type="submit" name="pay_now" value="1" class="btn btn-warning w-100 fw-semibold">
                                        <i class="bi bi-credit-card me-2"></i>Register & Pay Now
                                    </button>
                                </div>
                            </div>
                        </form>

                        <div class="text-center mt-4">
                            <p class="text-muted">Already have an account? <a href="{{ route('login') }}" class="back-link fw-semibold">Sign in</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Load all regions first, then initialize Select2
        $.ajax({
            url: '{{ route("api.locations.regions") }}',
            dataType: 'json',
            success: function(data) {
                const $regionSelect = $('#region');
                $regionSelect.empty().append(new Option('Select Region', '', true, true));
                data.forEach(function(item) {
                    $regionSelect.append(new Option(item.region, item.id, false, false));
                });

                // Handle old values from validation errors
                @if(old('region_id'))
                    $regionSelect.val('{{ old("region_id") }}');
                @endif

                // Now initialize Select2 for region (regular mode, not AJAX)
                initSelect2();
            },
            error: function() {
                // Even if load fails, init Select2 so form still works
                initSelect2();
            }
        });

        function initSelect2() {
            // Region Select2 - regular dropdown (all options already loaded)
            const $region = $('#region').select2({
                placeholder: 'Select region...',
                allowClear: false
            });

            // District Select2 - AJAX filtered by region
            const $district = $('#district').select2({
                placeholder: 'Select district...',
                allowClear: false,
                ajax: {
                    url: '{{ route("api.locations.districts.search") }}',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term || '',
                            region_id: $region.val()
                        };
                    },
                    processResults: function(data) {
                        return { results: data.results };
                    },
                    cache: true
                }
            });

            // Ward Select2 - AJAX filtered by district
            const $ward = $('#ward').select2({
                placeholder: 'Select ward...',
                allowClear: false,
                ajax: {
                    url: '{{ route("api.locations.wards.search") }}',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term || '',
                            district_id: $district.val()
                        };
                    },
                    processResults: function(data) {
                        return { results: data.results };
                    },
                    cache: true
                }
            });

            // Village Select2 - AJAX filtered by ward
            const $village = $('#village').select2({
                placeholder: 'Select village...',
                allowClear: false,
                ajax: {
                    url: '{{ route("api.locations.villages.search") }}',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term || '',
                            ward_id: $ward.val()
                        };
                    },
                    processResults: function(data) {
                        return { results: data.results };
                    },
                    cache: true
                }
            });

            // Region Change -> Reset and Enable District
            $region.on('change', function() {
                const regionId = $(this).val();
                const regionText = $(this).select2('data')[0]?.text || '';
                $('#region_name').val(regionText);

                $district.val(null).trigger('change');
                $ward.val(null).trigger('change');
                $village.val(null).trigger('change');

                if (regionId) {
                    $district.prop('disabled', false);
                } else {
                    $district.prop('disabled', true);
                    $ward.prop('disabled', true);
                    $village.prop('disabled', true);
                }
            });

            // District Change -> Reset and Enable Ward
            $district.on('change', function() {
                const districtId = $(this).val();
                const districtText = $(this).select2('data')[0]?.text || '';
                $('#district_name').val(districtText);

                $ward.val(null).trigger('change');
                $village.val(null).trigger('change');

                if (districtId) {
                    $ward.prop('disabled', false);
                } else {
                    $ward.prop('disabled', true);
                    $village.prop('disabled', true);
                }
            });

            // Ward Change -> Reset and Enable Village
            $ward.on('change', function() {
                const wardId = $(this).val();
                const wardText = $(this).select2('data')[0]?.text || '';
                $('#ward_name').val(wardText);

                $village.val(null).trigger('change');

                if (wardId) {
                    $village.prop('disabled', false);
                } else {
                    $village.prop('disabled', true);
                }
            });

            // Village Change -> Update hidden name
            $village.on('change', function() {
                const villageText = $(this).select2('data')[0]?.text || '';
                $('#village_name').val(villageText);
            });

            // Restore old values from validation errors
            restoreOldValues($region, $district, $ward, $village);

            // Plan selection
            document.querySelectorAll('.plan-option').forEach(el => {
                el.addEventListener('click', function() {
                    document.querySelectorAll('.plan-option').forEach(p => p.classList.remove('selected'));
                    this.classList.add('selected');
                    this.querySelector('input').checked = true;
                });
            });
        }

        function restoreOldValues($region, $district, $ward, $village) {
            @if(old('region_id') && old('district_id'))
                const oldDistrictId = '{{ old("district_id") }}';
                const oldDistrictText = '{{ old("district") }}';
                if (oldDistrictId && oldDistrictText) {
                    $district.append(new Option(oldDistrictText, oldDistrictId, true, true)).trigger('change');
                    $district.prop('disabled', false);
                }
            @endif

            @if(old('district_id') && old('ward_id'))
                const oldWardId = '{{ old("ward_id") }}';
                const oldWardText = '{{ old("ward") }}';
                if (oldWardId && oldWardText) {
                    $ward.append(new Option(oldWardText, oldWardId, true, true)).trigger('change');
                    $ward.prop('disabled', false);
                }
            @endif

            @if(old('ward_id') && old('village_id'))
                const oldVillageId = '{{ old("village_id") }}';
                const oldVillageText = '{{ old("village") }}';
                if (oldVillageId && oldVillageText) {
                    $village.append(new Option(oldVillageText, oldVillageId, true, true));
                    $village.prop('disabled', false);
                }
            @endif
        }
    });
</script>
@endpush
