@extends('web::layouts.grids.12')

@section('title', 'Edit Timer')
@section('page_header', 'Edit Timer')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Edit Structure Timer</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('timerboard.update', $timer->id) }}" method="POST">
                {{ csrf_field() }}

                <div class="form-group">
                    <label for="system">System / Location</label>
                    <select name="system" class="form-control select2-system" id="system" required style="width: 100%;">
                        <option value="{{ old('system', $timer->system) }}" selected>{{ old('system', $timer->system) }}</option>
                    </select>
                    <small class="text-muted">Search for a solar system or celestial (e.g. Moon, Planet)</small>
                </div>

                <div class="form-group">
                    <label for="structure_type">Structure Type</label>
                    <select name="structure_type" class="form-control select2-structure-type" id="structure_type" required>
                        <option value="{{ old('structure_type', $timer->structure_type) }}" selected>{{ old('structure_type', $timer->structure_type) }}</option>
                        <option value="Ansiblex">Ansiblex Jump Gate</option>
                        <option value="Astrahus">Astrahus</option>
                        <option value="Athanor">Athanor</option>
                        <option value="Azbel">Azbel</option>
                        <option value="POCO">Customs Office</option>
                        <option value="Fortizar">Fortizar</option>
                        <option value="Keepstar">Keepstar</option>
                        <option value="Metenox">Metenox Moon Drill</option>                        
                        <option value="Pharolux">Pharolux Cyno Beacon</option>
                        <option value="POS">POS</option>
                        <option value="Raitaru">Raitaru</option>
                        <option value="Skyhook">Skyhook</option>
                        <option value="Sotiyo">Sotiyo</option>
                        <option value="Tatara">Tatara</option>
                        <option value="Tenebrex">Tenebrex Jammer</option>
                        <option value="Other">Other</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="structure_name">Structure Name</label>
                    <input type="text" name="structure_name" class="form-control" id="structure_name" placeholder="Structure Name" required value="{{ old('structure_name', $timer->structure_name) }}">
                </div>

                <div class="form-group">
                    <label for="owner_corporation">Owner Corporation</label>
                    <select name="owner_corporation" class="form-control select2-corporation" id="owner_corporation" required style="width: 100%;">
                        <option value="{{ old('owner_corporation', $timer->owner_corporation) }}" selected>{{ old('owner_corporation', $timer->owner_corporation) }}</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="attacker_corporation">Attacker Corporation (Optional)</label>
                    <select name="attacker_corporation" class="form-control select2-attacker-corporation" id="attacker_corporation" style="width: 100%;">
                         @if(old('attacker_corporation', $timer->attacker_corporation))
                            <option value="{{ old('attacker_corporation', $timer->attacker_corporation) }}" selected>{{ old('attacker_corporation', $timer->attacker_corporation) }}</option>
                        @endif
                    </select>
                </div>

                <div class="form-group">
                    <label for="time_input">Time</label>
                    <input type="text" name="time_input" class="form-control" id="time_input" placeholder="YYYY.MM.DD HH:MM:SS or '2 days 4 hours'" required value="{{ old('time_input', $timer->eve_time->format('Y.m.d H:i:s')) }}">
                    <small class="form-text text-muted">Enter absolute EVE time (UTC) or relative time like '1d 4h 30m'.</small>
                </div>

                <div class="form-group">
                    <label>Tags</label>
                    @foreach($tags as $tag)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="tags[]" value="{{ $tag->id }}" id="tag_{{ $tag->id }}" {{ $timer->tags->contains($tag->id) ? 'checked' : '' }}>
                            <label class="form-check-label" for="tag_{{ $tag->id }}" style="color: {{ $tag->color }}">
                                {{ $tag->name }}
                            </label>
                        </div>
                    @endforeach
                </div>

                <button type="submit" class="btn btn-primary">Update Timer</button>
            </form>
        </div>
    </div>
@endsection

@push('javascript')
<script>
    $(document).ready(function() {
        $('.select2-system').select2({
            theme: 'bootstrap4',
            placeholder: 'Search for a system or celestial...',
            minimumInputLength: 3,
            ajax: {
                url: '{{ route("timerboard.search.systems") }}',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        q: params.term
                    };
                },
                processResults: function (data) {
                    return {
                        results: data.results
                    };
                },
                cache: true
            }
        });

        $('.select2-corporation').select2({
            theme: 'bootstrap4',
            placeholder: 'Search for corporation or alliance...',
            minimumInputLength: 3,
            ajax: {
                url: '{{ route("timerboard.search.corporations") }}',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        q: params.term
                    };
                },
                processResults: function (data) {
                    return {
                        results: data.results
                    };
                },
                cache: true
            }
        });

        $('.select2-attacker-corporation').select2({
            theme: 'bootstrap4',
            placeholder: 'Search for attacker (corp/alliance)...',
            minimumInputLength: 3,
            allowClear: true,
            ajax: {
                url: '{{ route("timerboard.search.corporations") }}',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        q: params.term
                    };
                },
                processResults: function (data) {
                    return {
                        results: data.results
                    };
                },
                cache: true
            }
        });

        $('.select2-structure-type').select2({
            theme: 'bootstrap4',
            placeholder: 'Select Structure Type',
            allowClear: true
        });
    });
</script>
@endpush
