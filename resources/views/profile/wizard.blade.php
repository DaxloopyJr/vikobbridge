@extends('layouts.app')

@section('title', 'Complete Your Profile')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
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
    .select2-dropdown {
        border-radius: 10px;
        border: 1px solid #e0e0e0;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #1a5f2a;
    }
    .select2-container { width: 100% !important; }
</style>
@endpush

@section('content')
<style>
    .wizard-container { max-width: 800px; margin: 0 auto; }
    .wizard-card { background: #fff; border-radius: 20px; box-shadow: 0 8px 40px rgba(0,0,0,0.1); overflow: hidden; }
    .wizard-header { background: linear-gradient(135deg, #1a5f2a 0%, #0d3320 100%); color: #fff; padding: 30px; text-align: center; }
    .wizard-body { padding: 40px; }
    .step-indicator { display: flex; justify-content: center; margin-bottom: 30px; }
    .step-dot { width: 40px; height: 40px; border-radius: 50%; background: #e0e0e0; display: flex; align-items: center; justify-content: center; font-weight: 600; color: #666; position: relative; z-index: 1; }
    .step-dot.active { background: #1a5f2a; color: #fff; }
    .step-dot.completed { background: #2e7d32; color: #fff; }
    .step-line { width: 60px; height: 3px; background: #e0e0e0; margin: 18px 0; }
    .step-line.completed { background: #2e7d32; }
</style>

<div class="container py-5">
    <div class="wizard-container">
        <div class="wizard-card">
            <div class="wizard-header">
                <h3 class="fw-bold mb-2"><i class="bi bi-person-check me-2"></i>Complete Your Profile</h3>
                <p class="mb-0 opacity-75">Please provide the following information to complete your registration</p>
            </div>

            @php
                $currentStep = request('step', 1);
                if($user->profile_completed) $currentStep = 7;
            @endphp

            <div class="wizard-body">
                <div class="step-indicator">
                    @for($i = 1; $i <= 6; $i++)
                        <div class="step-dot {{ $i == $currentStep ? 'active' : ($i < $currentStep ? 'completed' : '') }}">
                            @if($i < $currentStep)
                                <i class="bi bi-check-lg"></i>
                            @else
                                {{ $i }}
                            @endif
                        </div>
                        @if($i < 6)
                            <div class="step-line {{ $i < $currentStep ? 'completed' : '' }}"></div>
                        @endif
                    @endfor
                </div>

                <form method="POST" action="{{ route('profile.wizard.store') }}" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="step" value="{{ $currentStep }}">

                    @if($currentStep == 1)
                        <h5 class="fw-bold mb-4"><i class="bi bi-person me-2 text-primary"></i>Step 1: Personal Details</h5>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">First Name *</label>
                                <input type="text" name="first_name" class="form-control" value="{{ old('first_name', $user->first_name) }}" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Middle Name</label>
                                <input type="text" name="middle_name" class="form-control" value="{{ old('middle_name', $user->middle_name) }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Last Name *</label>
                                <input type="text" name="last_name" class="form-control" value="{{ old('last_name', $user->last_name) }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Gender *</label>
                                <select name="gender" class="form-select" required>
                                    <option value="">Select Gender</option>
                                    <option value="male" {{ old('gender', $user->gender) == 'male' ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ old('gender', $user->gender) == 'female' ? 'selected' : '' }}>Female</option>
                                    <option value="other" {{ old('gender', $user->gender) == 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Phone Number *</label>
                                <input type="text" name="phone_number" class="form-control" value="{{ old('phone_number', $user->phone_number) }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email Address *</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Profile Picture</label>
                                <input type="file" name="profile_picture" class="form-control" accept="image/*">
                            </div>
                        </div>

                    @elseif($currentStep == 2)
                        <h5 class="fw-bold mb-4"><i class="bi bi-geo-alt me-2 text-primary"></i>Step 2: Location Details</h5>
                        <div class="row">
                            {{-- Region --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Region *</label>
                                <select name="region_id" id="region" class="form-select location-select" required>
                                    <option value="">Select Region</option>
                                </select>
                                <input type="hidden" name="region" id="region_name" value="{{ old('region', $user->region) }}">
                                @error('region_id')<div class="text-danger small">{{ $message }}</div>@enderror
                            </div>
                            {{-- District --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label">District *</label>
                                <select name="district_id" id="district" class="form-select location-select" required disabled>
                                    <option value="">Select District</option>
                                </select>
                                <input type="hidden" name="district" id="district_name" value="{{ old('district', $user->district) }}">
                                @error('district_id')<div class="text-danger small">{{ $message }}</div>@enderror
                            </div>
                            {{-- Ward --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Ward *</label>
                                <select name="ward_id" id="ward" class="form-select location-select" required disabled>
                                    <option value="">Select Ward</option>
                                </select>
                                <input type="hidden" name="ward" id="ward_name" value="{{ old('ward', $user->ward) }}">
                                @error('ward_id')<div class="text-danger small">{{ $message }}</div>@enderror
                            </div>
                            {{-- Village --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Village *</label>
                                <select name="village_id" id="village" class="form-select location-select" required disabled>
                                    <option value="">Select Village</option>
                                </select>
                                <input type="hidden" name="village" id="village_name" value="{{ old('village', $user->village) }}">
                                @error('village_id')<div class="text-danger small">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Street</label>
                                <input type="text" name="street" class="form-control" value="{{ old('street', $user->street) }}">
                            </div>
                            <div class="col-12"><hr class="my-3"></div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Cell Leader Name</label>
                                <input type="text" name="cell_leader_name" class="form-control" value="{{ old('cell_leader_name', $user->cell_leader_name) }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Cell Leader Phone</label>
                                <input type="text" name="cell_leader_phone" class="form-control" value="{{ old('cell_leader_phone', $user->cell_leader_phone) }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">LG Chairperson Name</label>
                                <input type="text" name="lg_chairperson_name" class="form-control" value="{{ old('lg_chairperson_name', $user->lg_chairperson_name) }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">LG Chairperson Phone</label>
                                <input type="text" name="lg_chairperson_phone" class="form-control" value="{{ old('lg_chairperson_phone', $user->lg_chairperson_phone) }}">
                            </div>
                        </div>

                    @elseif($currentStep == 3)
                        <h5 class="fw-bold mb-4"><i class="bi bi-shield-check me-2 text-primary"></i>Step 3: Guarantor Details</h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Guarantor Full Name *</label>
                                <input type="text" name="guarantor_name" class="form-control" value="{{ old('guarantor_name', $user->guarantor_name) }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Guarantor Phone *</label>
                                <input type="text" name="guarantor_phone" class="form-control" value="{{ old('guarantor_phone', $user->guarantor_phone) }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Relationship *</label>
                                <select name="guarantor_relationship" class="form-select" required>
                                    <option value="">Select Relationship</option>
                                    <option value="spouse" {{ old('guarantor_relationship', $user->guarantor_relationship) == 'spouse' ? 'selected' : '' }}>Spouse</option>
                                    <option value="parent" {{ old('guarantor_relationship', $user->guarantor_relationship) == 'parent' ? 'selected' : '' }}>Parent</option>
                                    <option value="sibling" {{ old('guarantor_relationship', $user->guarantor_relationship) == 'sibling' ? 'selected' : '' }}>Sibling</option>
                                    <option value="relative" {{ old('guarantor_relationship', $user->guarantor_relationship) == 'relative' ? 'selected' : '' }}>Relative</option>
                                    <option value="friend" {{ old('guarantor_relationship', $user->guarantor_relationship) == 'friend' ? 'selected' : '' }}>Friend</option>
                                    <option value="colleague" {{ old('guarantor_relationship', $user->guarantor_relationship) == 'colleague' ? 'selected' : '' }}>Colleague</option>
                                    <option value="other" {{ old('guarantor_relationship', $user->guarantor_relationship) == 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                            </div>
                        </div>

                    @elseif($currentStep == 4)
                        <h5 class="fw-bold mb-4"><i class="bi bi-heart me-2 text-primary"></i>Step 4: Marital Status & Family</h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Marital Status *</label>
                                <select name="marital_status" class="form-select" id="maritalStatus" required onchange="toggleSpouseFields()">
                                    <option value="">Select Status</option>
                                    <option value="single" {{ old('marital_status', $user->marital_status) == 'single' ? 'selected' : '' }}>Single</option>
                                    <option value="married" {{ old('marital_status', $user->marital_status) == 'married' ? 'selected' : '' }}>Married</option>
                                    <option value="divorced" {{ old('marital_status', $user->marital_status) == 'divorced' ? 'selected' : '' }}>Divorced</option>
                                    <option value="widowed" {{ old('marital_status', $user->marital_status) == 'widowed' ? 'selected' : '' }}>Widowed</option>
                                </select>
                            </div>
                        </div>
                        <div id="spouseFields" style="display: {{ old('marital_status', $user->marital_status) == 'married' ? 'block' : 'none' }};">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Spouse Name</label>
                                    <input type="text" name="spouse_name" class="form-control" value="{{ old('spouse_name', $user->spouse_name) }}">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Spouse Phone</label>
                                    <input type="text" name="spouse_phone" class="form-control" value="{{ old('spouse_phone', $user->spouse_phone) }}">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Spouse Occupation</label>
                                    <input type="text" name="spouse_occupation" class="form-control" value="{{ old('spouse_occupation', $user->spouse_occupation) }}">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <label class="form-label">Dependents</label>
                                <div id="dependentsContainer">
                                    @php $deps = old('dependents', $user->dependents ?? []); @endphp
                                    @if(is_array($deps) && count($deps) > 0)
                                        @foreach($deps as $i => $dep)
                                            <div class="row dependent-row mb-2">
                                                <div class="col-md-4"><input type="text" name="dependents[{{ $i }}][full_name]" class="form-control" placeholder="Full Name" value="{{ $dep['full_name'] ?? '' }}"></div>
                                                <div class="col-md-3"><input type="text" name="dependents[{{ $i }}][phone]" class="form-control" placeholder="Phone" value="{{ $dep['phone'] ?? '' }}"></div>
                                                <div class="col-md-3"><input type="text" name="dependents[{{ $i }}][relationship]" class="form-control" placeholder="Relationship" value="{{ $dep['relationship'] ?? '' }}"></div>
                                                <div class="col-md-2"><button type="button" class="btn btn-outline-danger btn-sm" onclick="this.closest('.dependent-row').remove()">Remove</button></div>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                                <button type="button" class="btn btn-outline-primary btn-sm mt-2" onclick="addDependent()">
                                    <i class="bi bi-plus me-1"></i>Add Dependent
                                </button>
                            </div>
                        </div>

                    @elseif($currentStep == 5)
                        <h5 class="fw-bold mb-4"><i class="bi bi-people-fill me-2 text-primary"></i>Step 5: Inheritor Details</h5>
                        <div class="row">
                            <div class="col-12">
                                <label class="form-label">Inheritors</label>
                                <p class="text-muted small">People who will inherit your assets in case of death</p>
                                <div id="inheritorsContainer">
                                    @php $inhs = old('inheritors', $user->inheritors ?? []); @endphp
                                    @if(is_array($inhs) && count($inhs) > 0)
                                        @foreach($inhs as $i => $inh)
                                            <div class="row inheritor-row mb-2">
                                                <div class="col-md-4"><input type="text" name="inheritors[{{ $i }}][full_name]" class="form-control" placeholder="Full Name" value="{{ $inh['full_name'] ?? '' }}"></div>
                                                <div class="col-md-3"><input type="text" name="inheritors[{{ $i }}][phone]" class="form-control" placeholder="Phone" value="{{ $inh['phone'] ?? '' }}"></div>
                                                <div class="col-md-3"><input type="text" name="inheritors[{{ $i }}][relationship]" class="form-control" placeholder="Relationship" value="{{ $inh['relationship'] ?? '' }}"></div>
                                                <div class="col-md-2"><button type="button" class="btn btn-outline-danger btn-sm" onclick="this.closest('.inheritor-row').remove()">Remove</button></div>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                                <button type="button" class="btn btn-outline-primary btn-sm mt-2" onclick="addInheritor()">
                                    <i class="bi bi-plus me-1"></i>Add Inheritor
                                </button>
                            </div>
                        </div>

                    @elseif($currentStep == 6)
                        <h5 class="fw-bold mb-4"><i class="bi bi-check-circle me-2 text-primary"></i>Step 6: Verification</h5>
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>Please review all the information you have provided and certify that it is correct.
                        </div>
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="fw-bold">Summary</h6>
                                <div class="row small">
                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>Name:</strong> {{ $user->full_name }}</p>
                                        <p class="mb-1"><strong>Phone:</strong> {{ $user->phone_number }}</p>
                                        <p class="mb-1"><strong>Email:</strong> {{ $user->email }}</p>
                                        <p class="mb-1"><strong>Gender:</strong> {{ ucfirst($user->gender) }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>Location:</strong> {{ $user->village }}, {{ $user->ward }}</p>
                                        <p class="mb-1"><strong>Marital Status:</strong> {{ ucfirst($user->marital_status) }}</p>
                                        <p class="mb-1"><strong>Guarantor:</strong> {{ $user->guarantor_name }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="terms_accepted" id="terms_accepted" value="1" required>
                                <label class="form-check-label" for="terms_accepted">
                                    I certify that all the information provided is true and correct. I have read and agree to the <a href="#">group terms and conditions</a>.
                                </label>
                            </div>
                        </div>
                    @endif

                    <div class="d-flex justify-content-between mt-4">
                        @if($currentStep > 1)
                            <a href="?step={{ $currentStep - 1 }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-2"></i>Previous
                            </a>
                        @else
                            <div></div>
                        @endif
                        <button type="submit" class="btn btn-primary">
                            @if($currentStep < 6)
                                Next<i class="bi bi-arrow-right ms-2"></i>
                            @else
                                <i class="bi bi-check-lg me-2"></i>Complete Registration
                            @endif
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // ===== LOCATION SELECT2 INITIALIZATION (Step 2) =====
    if (document.getElementById('region')) {
        // Load regions first, then init Select2
        $.ajax({
            url: '{{ route("api.locations.regions") }}',
            dataType: 'json',
            success: function(data) {
                const $rs = $('#region');
                $rs.empty().append(new Option('Select Region', '', true, true));
                data.forEach(function(item) {
                    $rs.append(new Option(item.region, item.id, false, false));
                });
                initWizardSelect2();
            },
            error: function() { initWizardSelect2(); }
        });
    }

    function initWizardSelect2() {
        const $region = $('#region').select2({ placeholder: 'Select region...', allowClear: false });
        const $district = $('#district').select2({
            placeholder: 'Select district...', allowClear: false,
            ajax: {
                url: '{{ route("api.locations.districts.search") }}', dataType: 'json', delay: 250,
                data: function(params) {
                    return { q: params.term || '', region_id: $region.val() };
                },
                processResults: function(data) { return { results: data.results }; },
                cache: true
            }
        });
        const $ward = $('#ward').select2({
            placeholder: 'Select ward...', allowClear: false,
            ajax: {
                url: '{{ route("api.locations.wards.search") }}', dataType: 'json', delay: 250,
                data: function(params) {
                    return { q: params.term || '', district_id: $district.val() };
                },
                processResults: function(data) { return { results: data.results }; },
                cache: true
            }
        });
        const $village = $('#village').select2({
            placeholder: 'Select village...', allowClear: false,
            ajax: {
                url: '{{ route("api.locations.villages.search") }}', dataType: 'json', delay: 250,
                data: function(params) {
                    return { q: params.term || '', ward_id: $ward.val() };
                },
                processResults: function(data) { return { results: data.results }; },
                cache: true
            }
        });

        $region.on('change', function() {
            const regionText = $(this).select2('data')[0]?.text || '';
            $('#region_name').val(regionText);
            $district.val(null).trigger('change');
            $ward.val(null).trigger('change');
            $village.val(null).trigger('change');
            $district.prop('disabled', !$(this).val());
            $ward.prop('disabled', true);
            $village.prop('disabled', true);
        });
        $district.on('change', function() {
            const districtText = $(this).select2('data')[0]?.text || '';
            $('#district_name').val(districtText);
            $ward.val(null).trigger('change');
            $village.val(null).trigger('change');
            $ward.prop('disabled', !$(this).val());
            $village.prop('disabled', true);
        });
        $ward.on('change', function() {
            const wardText = $(this).select2('data')[0]?.text || '';
            $('#ward_name').val(wardText);
            $village.val(null).trigger('change');
            $village.prop('disabled', !$(this).val());
        });
        $village.on('change', function() {
            const villageText = $(this).select2('data')[0]?.text || '';
            $('#village_name').val(villageText);
        });

        // Restore old values after validation errors
        @if(old('region_id'))
            $region.val('{{ old("region_id") }}').trigger('change');
            $district.prop('disabled', false);
        @elseif($user->region_id)
            $region.val('{{ $user->region_id }}').trigger('change');
            $district.prop('disabled', false);
        @endif

        @if(old('district_id'))
            const oldDistrictId = '{{ old("district_id") }}';
            const oldDistrictText = '{{ old("district") }}';
            if (oldDistrictId && oldDistrictText) {
                $district.append(new Option(oldDistrictText, oldDistrictId, true, true)).trigger('change');
            }
        @elseif($user->district_id)
            const userDistrictId = '{{ $user->district_id }}';
            const userDistrictText = '{{ $user->district }}';
            if (userDistrictId && userDistrictText) {
                $district.append(new Option(userDistrictText, userDistrictId, true, true)).trigger('change');
            }
        @endif

        @if(old('ward_id'))
            const oldWardId = '{{ old("ward_id") }}';
            const oldWardText = '{{ old("ward") }}';
            if (oldWardId && oldWardText) {
                $ward.append(new Option(oldWardText, oldWardId, true, true)).trigger('change');
            }
        @elseif($user->ward_id)
            const userWardId = '{{ $user->ward_id }}';
            const userWardText = '{{ $user->ward }}';
            if (userWardId && userWardText) {
                $ward.append(new Option(userWardText, userWardId, true, true)).trigger('change');
            }
        @endif

        @if(old('village_id'))
            const oldVillageId = '{{ old("village_id") }}';
            const oldVillageText = '{{ old("village") }}';
            if (oldVillageId && oldVillageText) {
                $village.append(new Option(oldVillageText, oldVillageId, true, true));
            }
        @elseif($user->village_id)
            const userVillageId = '{{ $user->village_id }}';
            const userVillageText = '{{ $user->village }}';
            if (userVillageId && userVillageText) {
                $village.append(new Option(userVillageText, userVillageId, true, true));
            }
        @endif

        // Enable/disable based on what's set
        @if(old('district_id') || $user->district_id)
            $district.prop('disabled', false);
        @endif
        @if(old('ward_id') || $user->ward_id)
            $ward.prop('disabled', false);
        @endif
        @if(old('village_id') || $user->village_id)
            $village.prop('disabled', false);
        @endif
    }
});

// ===== EXISTING WIZARD FUNCTIONS =====
function toggleSpouseFields() {
    const status = document.getElementById('maritalStatus').value;
    document.getElementById('spouseFields').style.display = status === 'married' ? 'block' : 'none';
}

let depCount = {{ count(old('dependents', $user->dependents ?? [])) }};
function addDependent() {
    const container = document.getElementById('dependentsContainer');
    const row = document.createElement('div');
    row.className = 'row dependent-row mb-2';
    row.innerHTML = `
        <div class="col-md-4"><input type="text" name="dependents[${depCount}][full_name]" class="form-control" placeholder="Full Name" required></div>
        <div class="col-md-3"><input type="text" name="dependents[${depCount}][phone]" class="form-control" placeholder="Phone"></div>
        <div class="col-md-3"><input type="text" name="dependents[${depCount}][relationship]" class="form-control" placeholder="Relationship" required></div>
        <div class="col-md-2"><button type="button" class="btn btn-outline-danger btn-sm" onclick="this.closest('.dependent-row').remove()">Remove</button></div>
    `;
    container.appendChild(row);
    depCount++;
}

let inhCount = {{ count(old('inheritors', $user->inheritors ?? [])) }};
function addInheritor() {
    const container = document.getElementById('inheritorsContainer');
    const row = document.createElement('div');
    row.className = 'row inheritor-row mb-2';
    row.innerHTML = `
        <div class="col-md-4"><input type="text" name="inheritors[${inhCount}][full_name]" class="form-control" placeholder="Full Name" required></div>
        <div class="col-md-3"><input type="text" name="inheritors[${inhCount}][phone]" class="form-control" placeholder="Phone"></div>
        <div class="col-md-3"><input type="text" name="inheritors[${inhCount}][relationship]" class="form-control" placeholder="Relationship" required></div>
        <div class="col-md-2"><button type="button" class="btn btn-outline-danger btn-sm" onclick="this.closest('.inheritor-row').remove()">Remove</button></div>
    `;
    container.appendChild(row);
    inhCount++;
}
</script>
@endpush
@endsection
