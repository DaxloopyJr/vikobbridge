@extends('layouts.app')

@section('title', 'Edit Profile')

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
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 28px;
        padding-left: 4px;
    }
    .select2-dropdown { border-radius: 6px; }
    .select2-container--default .select2-results__option--highlighted[aria-selected] { background-color: #1a5f2a; }
    .select2-container { width: 100% !important; }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">Edit Profile</h4>
        <a href="{{ route('profile.show') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-2"></i>Back</a>
    </div>

    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
        @csrf @method('PUT')
        <div class="row">
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header bg-white py-3"><h5 class="mb-0 fw-bold">Personal Information</h5></div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3"><label class="form-label">First Name *</label><input type="text" name="first_name" class="form-control" value="{{ old('first_name', $user->first_name) }}" required></div>
                            <div class="col-md-4 mb-3"><label class="form-label">Middle Name</label><input type="text" name="middle_name" class="form-control" value="{{ old('middle_name', $user->middle_name) }}"></div>
                            <div class="col-md-4 mb-3"><label class="form-label">Last Name *</label><input type="text" name="last_name" class="form-control" value="{{ old('last_name', $user->last_name) }}" required></div>
                            <div class="col-md-4 mb-3"><label class="form-label">Gender *</label><select name="gender" class="form-select" required><option value="male" {{ old('gender', $user->gender) == 'male' ? 'selected' : '' }}>Male</option><option value="female" {{ old('gender', $user->gender) == 'female' ? 'selected' : '' }}>Female</option><option value="other" {{ old('gender', $user->gender) == 'other' ? 'selected' : '' }}>Other</option></select></div>
                            <div class="col-md-4 mb-3"><label class="form-label">Phone *</label><input type="text" name="phone_number" class="form-control" value="{{ old('phone_number', $user->phone_number) }}" required></div>
                            <div class="col-md-4 mb-3"><label class="form-label">Email *</label><input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required></div>
                            <div class="col-md-6 mb-3"><label class="form-label">Profile Picture</label><input type="file" name="profile_picture" class="form-control" accept="image/*"></div>
                            <div class="col-md-6 mb-3"><label class="form-label">Marital Status</label><select name="marital_status" class="form-select"><option value="">Select</option><option value="single" {{ old('marital_status', $user->marital_status) == 'single' ? 'selected' : '' }}>Single</option><option value="married" {{ old('marital_status', $user->marital_status) == 'married' ? 'selected' : '' }}>Married</option><option value="divorced" {{ old('marital_status', $user->marital_status) == 'divorced' ? 'selected' : '' }}>Divorced</option><option value="widowed" {{ old('marital_status', $user->marital_status) == 'widowed' ? 'selected' : '' }}>Widowed</option></select></div>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header bg-white py-3"><h5 class="mb-0 fw-bold">Location Details</h5></div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Region</label>
                                <select name="region_id" id="region" class="form-select location-select">
                                    <option value="">Select Region</option>
                                </select>
                                <input type="hidden" name="region" id="region_name" value="{{ old('region', $user->region) }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">District</label>
                                <select name="district_id" id="district" class="form-select location-select" disabled>
                                    <option value="">Select District</option>
                                </select>
                                <input type="hidden" name="district" id="district_name" value="{{ old('district', $user->district) }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Ward</label>
                                <select name="ward_id" id="ward" class="form-select location-select" disabled>
                                    <option value="">Select Ward</option>
                                </select>
                                <input type="hidden" name="ward" id="ward_name" value="{{ old('ward', $user->ward) }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Village</label>
                                <select name="village_id" id="village" class="form-select location-select" disabled>
                                    <option value="">Select Village</option>
                                </select>
                                <input type="hidden" name="village" id="village_name" value="{{ old('village', $user->village) }}">
                            </div>
                            <div class="col-md-4 mb-3"><label class="form-label">Street</label><input type="text" name="street" class="form-control" value="{{ old('street', $user->street) }}"></div>
                            <div class="col-md-4 mb-3"><label class="form-label">Cell Leader</label><input type="text" name="cell_leader_name" class="form-control" value="{{ old('cell_leader_name', $user->cell_leader_name) }}"></div>
                            <div class="col-md-4 mb-3"><label class="form-label">LG Chairperson</label><input type="text" name="lg_chairperson_name" class="form-control" value="{{ old('lg_chairperson_name', $user->lg_chairperson_name) }}"></div>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header bg-white py-3"><h5 class="mb-0 fw-bold">Guarantor Details</h5></div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3"><label class="form-label">Guarantor Name</label><input type="text" name="guarantor_name" class="form-control" value="{{ old('guarantor_name', $user->guarantor_name) }}"></div>
                            <div class="col-md-4 mb-3"><label class="form-label">Guarantor Phone</label><input type="text" name="guarantor_phone" class="form-control" value="{{ old('guarantor_phone', $user->guarantor_phone) }}"></div>
                            <div class="col-md-4 mb-3"><label class="form-label">Relationship</label><input type="text" name="guarantor_relationship" class="form-control" value="{{ old('guarantor_relationship', $user->guarantor_relationship) }}"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-lg"><i class="bi bi-check-lg me-2"></i>Save Changes</button>
                    <a href="{{ route('profile.show') }}" class="btn btn-outline-secondary">Cancel</a>
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
            initSelect2();
        },
        error: function() { initSelect2(); }
    });

    function initSelect2() {
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
            $('#region_name').val($(this).select2('data')[0]?.text || '');
            $district.val(null).trigger('change');
            $ward.val(null).trigger('change');
            $village.val(null).trigger('change');
            $district.prop('disabled', !$(this).val());
            $ward.prop('disabled', true);
            $village.prop('disabled', true);
        });
        $district.on('change', function() {
            $('#district_name').val($(this).select2('data')[0]?.text || '');
            $ward.val(null).trigger('change');
            $village.val(null).trigger('change');
            $ward.prop('disabled', !$(this).val());
            $village.prop('disabled', true);
        });
        $ward.on('change', function() {
            $('#ward_name').val($(this).select2('data')[0]?.text || '');
            $village.val(null).trigger('change');
            $village.prop('disabled', !$(this).val());
        });
        $village.on('change', function() {
            $('#village_name').val($(this).select2('data')[0]?.text || '');
        });

        // Restore user's saved location values
        @if($user->region_id)
            $region.val('{{ $user->region_id }}').trigger('change');
            $district.prop('disabled', false);
        @endif
        @if($user->district_id)
            $district.append(new Option('{{ $user->district }}', '{{ $user->district_id }}', true, true)).trigger('change');
            $district.prop('disabled', false);
        @endif
        @if($user->ward_id)
            $ward.append(new Option('{{ $user->ward }}', '{{ $user->ward_id }}', true, true)).trigger('change');
            $ward.prop('disabled', false);
        @endif
        @if($user->village_id)
            $village.append(new Option('{{ $user->village }}', '{{ $user->village_id }}', true, true));
            $village.prop('disabled', false);
        @endif
    }
});
</script>
@endpush
