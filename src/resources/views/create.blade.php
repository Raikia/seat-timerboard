@extends('web::layouts.grids.12')

@section('title', 'Create Timer')
@section('page_header', 'Create Timer')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">New Structure Timer</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('timerboard.store') }}" method="POST">
                {{ csrf_field() }}

                <div class="form-group">
                    <label for="system">System / Location</label>
                    <select name="system" class="form-control select2-system" id="system" required style="width: 100%;">
                        @if(old('system'))
                            <option value="{{ old('system') }}" selected>{{ old('system') }}</option>
                        @endif
                    </select>
                    <small class="text-muted">Search for a solar system or celestial (e.g. Moon, Planet)</small>
                </div>

                <div class="form-group">
                    <label for="structure_type">Structure Type</label>
                    <select name="structure_type" class="form-control" id="structure_type" required>
                        <option value="">Select Type</option>
                        <option value="Astrahus">Astrahus</option>
                        <option value="Fortizar">Fortizar</option>
                        <option value="Keepstar">Keepstar</option>
                        <option value="Raitaru">Raitaru</option>
                        <option value="Azbel">Azbel</option>
                        <option value="Sotiyo">Sotiyo</option>
                        <option value="Athanor">Athanor</option>
                        <option value="Tatara">Tatara</option>
                        <option value="Skyhook">Skyhook</option>
                        <option value="POCO">Customs Office</option>
                        <option value="POS">POS (Starbase)</option>
                        <option value="Ansiblex">Ansiblex Jump Gate</option>
                        <option value="Pharolux">Pharolux Cyno Beacon</option>
                        <option value="Tenebrex">Tenebrex Jammer</option>
                        <option value="Metenox">Metenox Moon Drill</option>
                        <option value="Other">Other</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="structure_name">Structure Name</label>
                    <input type="text" name="structure_name" class="form-control" id="structure_name" placeholder="Structure Name" required value="{{ old('structure_name') }}">
                </div>

                <div class="form-group">
                    <label for="owner_corporation">Owner Corporation</label>
                    <select name="owner_corporation" class="form-control select2-corporation" id="owner_corporation" required style="width: 100%;">
                         @if(old('owner_corporation'))
                            <option value="{{ old('owner_corporation') }}" selected>{{ old('owner_corporation') }}</option>
                        @endif
                    </select>
                </div>

                <div class="form-group">
                    <label for="time_input">Time</label>
                    <input type="text" name="time_input" class="form-control" id="time_input" placeholder="YYYY.MM.DD HH:MM:SS or '2 days 4 hours'" required value="{{ old('time_input') }}">
                    <small class="form-text text-muted">Enter absolute EVE time (UTC) or relative time like '1d 4h 30m'.</small>
                </div>

                <div class="form-group">
                    <label>Tags</label>
                    @foreach($tags as $tag)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="tags[]" value="{{ $tag->id }}" id="tag_{{ $tag->id }}">
                            <label class="form-check-label" for="tag_{{ $tag->id }}" style="color: {{ $tag->color }}">
                                {{ $tag->name }}
                            </label>
                        </div>
                    @endforeach
                </div>

                <button type="submit" class="btn btn-primary">Create Timer</button>
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
            placeholder: 'Search for a corporation...',
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
    });
</script>
@endpush
