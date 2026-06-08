@extends('layouts.app')

@section('title', 'Edit Member')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">Edit Member: {{ $member->full_name }}</h4>
        <a href="{{ route('members.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-2"></i>Back</a>
    </div>

    <form method="POST" action="{{ route('members.update', $member) }}" enctype="multipart/form-data">
        @csrf @method('PUT')
        <div class="row">
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header bg-white py-3"><h5 class="mb-0 fw-bold">Personal Information</h5></div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3"><label class="form-label">First Name *</label><input type="text" name="first_name" class="form-control" value="{{ old('first_name', $member->first_name) }}" required></div>
                            <div class="col-md-4 mb-3"><label class="form-label">Middle Name</label><input type="text" name="middle_name" class="form-control" value="{{ old('middle_name', $member->middle_name) }}"></div>
                            <div class="col-md-4 mb-3"><label class="form-label">Last Name *</label><input type="text" name="last_name" class="form-control" value="{{ old('last_name', $member->last_name) }}" required></div>
                            <div class="col-md-4 mb-3"><label class="form-label">Gender *</label><select name="gender" class="form-select" required><option value="male" {{ old('gender', $member->gender) == 'male' ? 'selected' : '' }}>Male</option><option value="female" {{ old('gender', $member->gender) == 'female' ? 'selected' : '' }}>Female</option><option value="other" {{ old('gender', $member->gender) == 'other' ? 'selected' : '' }}>Other</option></select></div>
                            <div class="col-md-4 mb-3"><label class="form-label">Phone *</label><input type="text" name="phone_number" class="form-control" value="{{ old('phone_number', $member->phone_number) }}" required></div>
                            <div class="col-md-4 mb-3"><label class="form-label">Email</label><input type="email" name="email" class="form-control" value="{{ old('email', $member->email) }}"></div>
                            <div class="col-md-6 mb-3"><label class="form-label">Profile Picture</label><input type="file" name="profile_picture" class="form-control" accept="image/*"></div>
                            <div class="col-md-6 mb-3"><label class="form-label">Join Date *</label><input type="date" name="join_date" class="form-control" value="{{ old('join_date', $member->join_date->format('Y-m-d')) }}" required></div>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header bg-white py-3"><h5 class="mb-0 fw-bold"><i class="bi bi-geo-alt me-2 text-primary"></i>Location Details</h5></div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="location-label">Region</label>
                                <select name="region_id" id="region" class="form-select location-select">
                                    <option value="">Select Region</option>
                                </select>
                                <input type="hidden" name="region" id="region_name" value="{{ old('region', $member->region) }}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="location-label">District</label>
                                <select name="district_id" id="district" class="form-select location-select" disabled>
                                    <option value="">Select District</option>
                                </select>
                                <input type="hidden" name="district" id="district_name" value="{{ old('district', $member->district) }}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="location-label">Ward</label>
                                <select name="ward_id" id="ward" class="form-select location-select" disabled>
                                    <option value="">Select Ward</option>
                                </select>
                                <input type="hidden" name="ward" id="ward_name" value="{{ old('ward', $member->ward) }}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="location-label">Village</label>
                                <select name="village_id" id="village" class="form-select location-select" disabled>
                                    <option value="">Select Village</option>
                                </select>
                                <input type="hidden" name="village" id="village_name" value="{{ old('village', $member->village) }}">
                            </div>
                            <div class="col-md-6 mb-3"><label class="form-label">Street</label><input type="text" name="street" class="form-control" value="{{ old('street', $member->street) }}"></div>
                            <div class="col-md-6 mb-3"><label class="form-label">Cell Leader</label><input type="text" name="cell_leader_name" class="form-control" value="{{ old('cell_leader_name', $member->cell_leader_name) }}"></div>
                            <div class="col-md-6 mb-3"><label class="form-label">Cell Leader Phone</label><input type="text" name="cell_leader_phone" class="form-control" value="{{ old('cell_leader_phone', $member->cell_leader_phone) }}"></div>
                            <div class="col-md-6 mb-3"><label class="form-label">LG Chairperson</label><input type="text" name="lg_chairperson_name" class="form-control" value="{{ old('lg_chairperson_name', $member->lg_chairperson_name) }}"></div>
                            <div class="col-md-6 mb-3"><label class="form-label">LG Chairperson Phone</label><input type="text" name="lg_chairperson_phone" class="form-control" value="{{ old('lg_chairperson_phone', $member->lg_chairperson_phone) }}"></div>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header bg-white py-3"><h5 class="mb-0 fw-bold">Guarantor Details</h5></div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3"><label class="form-label">Guarantor Name</label><input type="text" name="guarantor_name" class="form-control" value="{{ old('guarantor_name', $member->guarantor_name) }}"></div>
                            <div class="col-md-4 mb-3"><label class="form-label">Guarantor Phone</label><input type="text" name="guarantor_phone" class="form-control" value="{{ old('guarantor_phone', $member->guarantor_phone) }}"></div>
                            <div class="col-md-4 mb-3"><label class="form-label">Relationship</label>
                                <select name="guarantor_relationship" class="form-select">
                                    <option value="">Select</option>
                                    @foreach(['spouse', 'parent', 'sibling', 'relative', 'friend', 'colleague'] as $rel)
                                        <option value="{{ $rel }}" {{ old('guarantor_relationship', $member->guarantor_relationship) == $rel ? 'selected' : '' }}>{{ ucfirst($rel) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header bg-white py-3"><h5 class="mb-0 fw-bold">Family Details</h5></div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3"><label class="form-label">Marital Status</label><select name="marital_status" class="form-select"><option value="">Select</option>@foreach(['single', 'married', 'divorced', 'widowed'] as $ms)<option value="{{ $ms }}" {{ old('marital_status', $member->marital_status) == $ms ? 'selected' : '' }}>{{ ucfirst($ms) }}</option>@endforeach</select></div>
                            <div class="col-md-4 mb-3"><label class="form-label">Spouse Name</label><input type="text" name="spouse_name" class="form-control" value="{{ old('spouse_name', $member->spouse_name) }}"></div>
                            <div class="col-md-4 mb-3"><label class="form-label">Spouse Phone</label><input type="text" name="spouse_phone" class="form-control" value="{{ old('spouse_phone', $member->spouse_phone) }}"></div>
                            <div class="col-md-6 mb-3"><label class="form-label">Spouse Occupation</label><input type="text" name="spouse_occupation" class="form-control" value="{{ old('spouse_occupation', $member->spouse_occupation) }}"></div>
                            <div class="col-md-6 mb-3"><label class="form-label">Status *</label><select name="status" class="form-select" required>@foreach(['active', 'inactive', 'suspended', 'deceased'] as $st)<option value="{{ $st }}" {{ old('status', $member->status) == $st ? 'selected' : '' }}>{{ ucfirst($st) }}</option>@endforeach</select></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-header bg-white py-3"><h5 class="mb-0 fw-bold">Notes</h5></div>
                    <div class="card-body"><textarea name="notes" class="form-control" rows="4">{{ old('notes', $member->notes) }}</textarea></div>
                </div>
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-lg"><i class="bi bi-check-lg me-2"></i>Update Member</button>
                    <a href="{{ route('members.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container--default .select2-selection--single {
        border: 1px solid #ced4da; border-radius: 6px; height: 38px; padding: 4px 8px;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow { height: 38px; right: 8px; }
    .select2-container--default .select2-selection--single .select2-selection__rendered { line-height: 28px; padding-left: 4px; }
    .select2-dropdown { border-radius: 8px; border: 1px solid #ced4da; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
    .select2-container--default .select2-results__option--highlighted[aria-selected] { background-color: #1a5f2a; }
    .select2-container { width: 100% !important; }
    .location-label { font-weight: 500; margin-bottom: 4px; font-size: 0.85rem; }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    $.ajax({
        url: '{{ route("api.locations.regions") }}',
        dataType: 'json',
        success: function(data) {
            const $rs = $('#region');
            $rs.empty().append(new Option('Select Region', '', true, true));
            data.forEach(function(item) {
                $rs.append(new Option(item.region, item.id, false, false));
            });
            initEditSelect2();
        },
        error: function() { initEditSelect2(); }
    });

    function initEditSelect2() {
        const $region = $('#region').select2({ placeholder: 'Search region...', allowClear: false });
        const $district = $('#district').select2({
            placeholder: 'Select district...', allowClear: false,
            ajax: {
                url: '{{ route("api.locations.districts.search") }}', dataType: 'json', delay: 250,
                data: function(params) { return { q: params.term || '', region_id: $region.val() }; },
                processResults: function(data) { return { results: data.results }; }, cache: true
            }
        });
        const $ward = $('#ward').select2({
            placeholder: 'Select ward...', allowClear: false,
            ajax: {
                url: '{{ route("api.locations.wards.search") }}', dataType: 'json', delay: 250,
                data: function(params) { return { q: params.term || '', district_id: $district.val() }; },
                processResults: function(data) { return { results: data.results }; }, cache: true
            }
        });
        const $village = $('#village').select2({
            placeholder: 'Select village...', allowClear: false,
            ajax: {
                url: '{{ route("api.locations.villages.search") }}', dataType: 'json', delay: 250,
                data: function(params) { return { q: params.term || '', ward_id: $ward.val() }; },
                processResults: function(data) { return { results: data.results }; }, cache: true
            }
        });

        $region.on('change', function() {
            $('#region_name').val($(this).select2('data')[0]?.text || '');
            $district.val(null).trigger('change');
            $ward.val(null).trigger('change');
            $village.val(null).trigger('change');
            $district.prop('disabled', !$(this).val());
            $ward.prop('disabled', true); $village.prop('disabled', true);
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

        // Pre-populate saved member values
        @if($member->region_id)
            $region.val('{{ $member->region_id }}').trigger('change');
            $district.prop('disabled', false);
        @endif
        @if($member->district_id)
            $district.append(new Option('{{ $member->district }}', '{{ $member->district_id }}', true, true)).trigger('change');
            $district.prop('disabled', false);
        @endif
        @if($member->ward_id)
            $ward.append(new Option('{{ $member->ward }}', '{{ $member->ward_id }}', true, true)).trigger('change');
            $ward.prop('disabled', false);
        @endif
        @if($member->village_id)
            $village.append(new Option('{{ $member->village }}', '{{ $member->village_id }}', true, true));
            $village.prop('disabled', false);
        @endif

        // Restore old values after validation errors (override pre-populated)
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
</script>
@endpush
