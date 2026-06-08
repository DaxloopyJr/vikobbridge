@extends('layouts.app')

@section('title', 'Add Member')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container--default .select2-selection--single {
        border: 1px solid #ced4da;
        border-radius: 6px;
        height: 38px;
        padding: 4px 8px;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 38px;
        right: 8px;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 28px;
        padding-left: 4px;
    }
    .select2-dropdown {
        border-radius: 8px;
        border: 1px solid #ced4da;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #1a5f2a;
    }
    .select2-container { width: 100% !important; }
    .location-label { font-weight: 500; margin-bottom: 4px; font-size: 0.85rem; }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0">Add New Member</h4>
            <p class="text-muted mb-0">Register a new member to your VICOBA group</p>
        </div>
        <a href="{{ route('members.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Back to Members
        </a>
    </div>

    <form method="POST" action="{{ route('members.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-bold"><i class="bi bi-person me-2 text-primary"></i>Personal Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">First Name *</label>
                                <input type="text" name="first_name" class="form-control @error('first_name') is-invalid @enderror" value="{{ old('first_name') }}" required>
                                @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Middle Name</label>
                                <input type="text" name="middle_name" class="form-control" value="{{ old('middle_name') }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Last Name *</label>
                                <input type="text" name="last_name" class="form-control @error('last_name') is-invalid @enderror" value="{{ old('last_name') }}" required>
                                @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Gender *</label>
                                <select name="gender" class="form-select @error('gender') is-invalid @enderror" required>
                                    <option value="">Select</option>
                                    <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                                    <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('gender')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Phone Number *</label>
                                <input type="text" name="phone_number" class="form-control @error('phone_number') is-invalid @enderror" value="{{ old('phone_number') }}" required>
                                @error('phone_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}">
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Profile Picture</label>
                                <input type="file" name="profile_picture" class="form-control" accept="image/*">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Join Date *</label>
                                <input type="date" name="join_date" class="form-control @error('join_date') is-invalid @enderror" value="{{ old('join_date', date('Y-m-d')) }}" required>
                                @error('join_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Location with Select2 Cascading Dropdowns --}}
                <div class="card mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-bold"><i class="bi bi-geo-alt me-2 text-primary"></i>Location Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            {{-- Region --}}
                            <div class="col-md-3 mb-3">
                                <label class="location-label">Region</label>
                                <select name="region_id" id="region" class="form-select location-select">
                                    <option value="">Select Region</option>
                                </select>
                                <input type="hidden" name="region" id="region_name" value="{{ old('region') }}">
                                @error('region_id')<div class="text-danger small">{{ $message }}</div>@enderror
                            </div>
                            {{-- District --}}
                            <div class="col-md-3 mb-3">
                                <label class="location-label">District</label>
                                <select name="district_id" id="district" class="form-select location-select" disabled>
                                    <option value="">Select District</option>
                                </select>
                                <input type="hidden" name="district" id="district_name" value="{{ old('district') }}">
                                @error('district_id')<div class="text-danger small">{{ $message }}</div>@enderror
                            </div>
                            {{-- Ward --}}
                            <div class="col-md-3 mb-3">
                                <label class="location-label">Ward</label>
                                <select name="ward_id" id="ward" class="form-select location-select" disabled>
                                    <option value="">Select Ward</option>
                                </select>
                                <input type="hidden" name="ward" id="ward_name" value="{{ old('ward') }}">
                                @error('ward_id')<div class="text-danger small">{{ $message }}</div>@enderror
                            </div>
                            {{-- Village --}}
                            <div class="col-md-3 mb-3">
                                <label class="location-label">Village</label>
                                <select name="village_id" id="village" class="form-select location-select" disabled>
                                    <option value="">Select Village</option>
                                </select>
                                <input type="hidden" name="village" id="village_name" value="{{ old('village') }}">
                                @error('village_id')<div class="text-danger small">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Street</label>
                                <input type="text" name="street" class="form-control" value="{{ old('street') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Cell Leader Name</label>
                                <input type="text" name="cell_leader_name" class="form-control" value="{{ old('cell_leader_name') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Cell Leader Phone</label>
                                <input type="text" name="cell_leader_phone" class="form-control" value="{{ old('cell_leader_phone') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">LG Chairperson Name</label>
                                <input type="text" name="lg_chairperson_name" class="form-control" value="{{ old('lg_chairperson_name') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">LG Chairperson Phone</label>
                                <input type="text" name="lg_chairperson_phone" class="form-control" value="{{ old('lg_chairperson_phone') }}">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-bold"><i class="bi bi-shield-check me-2 text-primary"></i>Guarantor Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Guarantor Name</label>
                                <input type="text" name="guarantor_name" class="form-control" value="{{ old('guarantor_name') }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Guarantor Phone</label>
                                <input type="text" name="guarantor_phone" class="form-control" value="{{ old('guarantor_phone') }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Relationship</label>
                                <select name="guarantor_relationship" class="form-select">
                                    <option value="">Select</option>
                                    <option value="spouse" {{ old('guarantor_relationship') == 'spouse' ? 'selected' : '' }}>Spouse</option>
                                    <option value="parent" {{ old('guarantor_relationship') == 'parent' ? 'selected' : '' }}>Parent</option>
                                    <option value="sibling" {{ old('guarantor_relationship') == 'sibling' ? 'selected' : '' }}>Sibling</option>
                                    <option value="relative" {{ old('guarantor_relationship') == 'relative' ? 'selected' : '' }}>Relative</option>
                                    <option value="friend" {{ old('guarantor_relationship') == 'friend' ? 'selected' : '' }}>Friend</option>
                                    <option value="colleague" {{ old('guarantor_relationship') == 'colleague' ? 'selected' : '' }}>Colleague</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-bold"><i class="bi bi-heart me-2 text-primary"></i>Family Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Marital Status</label>
                                <select name="marital_status" class="form-select">
                                    <option value="">Select</option>
                                    <option value="single" {{ old('marital_status') == 'single' ? 'selected' : '' }}>Single</option>
                                    <option value="married" {{ old('marital_status') == 'married' ? 'selected' : '' }}>Married</option>
                                    <option value="divorced" {{ old('marital_status') == 'divorced' ? 'selected' : '' }}>Divorced</option>
                                    <option value="widowed" {{ old('marital_status') == 'widowed' ? 'selected' : '' }}>Widowed</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Spouse Name</label>
                                <input type="text" name="spouse_name" class="form-control" value="{{ old('spouse_name') }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Spouse Phone</label>
                                <input type="text" name="spouse_phone" class="form-control" value="{{ old('spouse_phone') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Spouse Occupation</label>
                                <input type="text" name="spouse_occupation" class="form-control" value="{{ old('spouse_occupation') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Status *</label>
                                <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                    <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    <option value="suspended" {{ old('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                                </select>
                                @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-bold"><i class="bi bi-people-fill me-2 text-primary"></i>Dependents</h5>
                    </div>
                    <div class="card-body">
                        <div id="dependentsContainer">
                            @if(old('dependents'))
                                @foreach(old('dependents') as $i => $dep)
                                    <div class="row dependent-row mb-2">
                                        <div class="col-12 mb-2"><input type="text" name="dependents[{{ $i }}][full_name]" class="form-control form-control-sm" placeholder="Full Name" value="{{ $dep['full_name'] ?? '' }}"></div>
                                        <div class="col-6"><input type="text" name="dependents[{{ $i }}][phone]" class="form-control form-control-sm" placeholder="Phone" value="{{ $dep['phone'] ?? '' }}"></div>
                                        <div class="col-6"><input type="text" name="dependents[{{ $i }}][relationship]" class="form-control form-control-sm" placeholder="Relationship" value="{{ $dep['relationship'] ?? '' }}"></div>
                                        <div class="col-12 mt-1"><button type="button" class="btn btn-outline-danger btn-sm w-100" onclick="this.closest('.dependent-row').remove()">Remove</button></div>
                                    </div>
                                    <hr class="my-2">
                                @endforeach
                            @endif
                        </div>
                        <button type="button" class="btn btn-outline-primary btn-sm w-100" onclick="addDependent()">
                            <i class="bi bi-plus me-1"></i>Add Dependent
                        </button>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-bold"><i class="bi bi-person-badge me-2 text-primary"></i>Inheritors</h5>
                    </div>
                    <div class="card-body">
                        <div id="inheritorsContainer">
                            @if(old('inheritors'))
                                @foreach(old('inheritors') as $i => $inh)
                                    <div class="row inheritor-row mb-2">
                                        <div class="col-12 mb-2"><input type="text" name="inheritors[{{ $i }}][full_name]" class="form-control form-control-sm" placeholder="Full Name" value="{{ $inh['full_name'] ?? '' }}"></div>
                                        <div class="col-6"><input type="text" name="inheritors[{{ $i }}][phone]" class="form-control form-control-sm" placeholder="Phone" value="{{ $inh['phone'] ?? '' }}"></div>
                                        <div class="col-6"><input type="text" name="inheritors[{{ $i }}][relationship]" class="form-control form-control-sm" placeholder="Relationship" value="{{ $inh['relationship'] ?? '' }}"></div>
                                        <div class="col-12 mt-1"><button type="button" class="btn btn-outline-danger btn-sm w-100" onclick="this.closest('.inheritor-row').remove()">Remove</button></div>
                                    </div>
                                    <hr class="my-2">
                                @endforeach
                            @endif
                        </div>
                        <button type="button" class="btn btn-outline-primary btn-sm w-100" onclick="addInheritor()">
                            <i class="bi bi-plus me-1"></i>Add Inheritor
                        </button>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-bold"><i class="bi bi-sticky me-2 text-primary"></i>Notes</h5>
                    </div>
                    <div class="card-body">
                        <textarea name="notes" class="form-control" rows="4" placeholder="Any additional notes about this member...">{{ old('notes') }}</textarea>
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="bi bi-check-lg me-2"></i>Register Member
                    </button>
                    <a href="{{ route('members.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Load all regions first, then init Select2
    $.ajax({
        url: '{{ route("api.locations.regions") }}',
        dataType: 'json',
        success: function(data) {
            const $rs = $('#region');
            $rs.empty().append(new Option('Select Region', '', true, true));
            data.forEach(function(item) {
                $rs.append(new Option(item.region, item.id, false, false));
            });
            initMemberSelect2();
        },
        error: function() {
            initMemberSelect2();
        }
    });

    function initMemberSelect2() {
        const $region = $('#region').select2({ placeholder: 'Search region...', allowClear: false });
        const $district = $('#district').select2({
            placeholder: 'Select district...', allowClear: false,
            ajax: {
                url: '{{ route("api.locations.districts.search") }}',
                dataType: 'json', delay: 250,
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
                url: '{{ route("api.locations.wards.search") }}',
                dataType: 'json', delay: 250,
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
                url: '{{ route("api.locations.villages.search") }}',
                dataType: 'json', delay: 250,
                data: function(params) {
                    return { q: params.term || '', ward_id: $ward.val() };
                },
                processResults: function(data) { return { results: data.results }; },
                cache: true
            }
        });

        // Region change -> load districts
        $region.on('change', function() {
            var regionText = $(this).select2('data')[0]?.text || '';
            $('#region_name').val(regionText);
            $district.val(null).trigger('change');
            $ward.val(null).trigger('change');
            $village.val(null).trigger('change');
            $district.prop('disabled', !$(this).val());
            $ward.prop('disabled', true);
            $village.prop('disabled', true);
        });

        // District change -> load wards
        $district.on('change', function() {
            var districtText = $(this).select2('data')[0]?.text || '';
            $('#district_name').val(districtText);
            $ward.val(null).trigger('change');
            $village.val(null).trigger('change');
            $ward.prop('disabled', !$(this).val());
            $village.prop('disabled', true);
        });

        // Ward change -> load villages
        $ward.on('change', function() {
            var wardText = $(this).select2('data')[0]?.text || '';
            $('#ward_name').val(wardText);
            $village.val(null).trigger('change');
            $village.prop('disabled', !$(this).val());
        });

        // Village change
        $village.on('change', function() {
            var villageText = $(this).select2('data')[0]?.text || '';
            $('#village_name').val(villageText);
        });

        // Restore old values after validation errors
        @if(old('region_id'))
            $region.val('{{ old("region_id") }}').trigger('change');
            $district.prop('disabled', false);
        @endif
        @if(old('district_id'))
            $district.append(new Option('{{ old("district") }}', '{{ old("district_id") }}', true, true)).trigger('change');
            $district.prop('disabled', false);
        @endif
        @if(old('ward_id'))
            $ward.append(new Option('{{ old("ward") }}', '{{ old("ward_id") }}', true, true)).trigger('change');
            $ward.prop('disabled', false);
        @endif
        @if(old('village_id'))
            $village.append(new Option('{{ old("village") }}', '{{ old("village_id") }}', true, true));
            $village.prop('disabled', false);
        @endif
    }
});

let depCount = {{ count(old('dependents', [])) }};
function addDependent() {
    const container = document.getElementById('dependentsContainer');
    const div = document.createElement('div');
    div.innerHTML = `
        <div class="row dependent-row mb-2">
            <div class="col-12 mb-2"><input type="text" name="dependents[${depCount}][full_name]" class="form-control form-control-sm" placeholder="Full Name"></div>
            <div class="col-6"><input type="text" name="dependents[${depCount}][phone]" class="form-control form-control-sm" placeholder="Phone"></div>
            <div class="col-6"><input type="text" name="dependents[${depCount}][relationship]" class="form-control form-control-sm" placeholder="Relationship"></div>
            <div class="col-12 mt-1"><button type="button" class="btn btn-outline-danger btn-sm w-100" onclick="this.closest('.dependent-row').remove()">Remove</button></div>
        </div>
        <hr class="my-2">
    `;
    container.appendChild(div);
    depCount++;
}

let inhCount = {{ count(old('inheritors', [])) }};
function addInheritor() {
    const container = document.getElementById('inheritorsContainer');
    const div = document.createElement('div');
    div.innerHTML = `
        <div class="row inheritor-row mb-2">
            <div class="col-12 mb-2"><input type="text" name="inheritors[${inhCount}][full_name]" class="form-control form-control-sm" placeholder="Full Name"></div>
            <div class="col-6"><input type="text" name="inheritors[${inhCount}][phone]" class="form-control form-control-sm" placeholder="Phone"></div>
            <div class="col-6"><input type="text" name="inheritors[${inhCount}][relationship]" class="form-control form-control-sm" placeholder="Relationship"></div>
            <div class="col-12 mt-1"><button type="button" class="btn btn-outline-danger btn-sm w-100" onclick="this.closest('.inheritor-row').remove()">Remove</button></div>
        </div>
        <hr class="my-2">
    `;
    container.appendChild(div);
    inhCount++;
}
</script>
@endpush
