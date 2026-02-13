@extends('web::layouts.grids.12')

@section('title', 'Timerboard Settings')
@section('page_header', 'Timerboard Settings')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Manage Tags</h3>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Color</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tags as $tag)
                        <tr>
                            <td>
                                <span class="badge" style="background-color: {{ $tag->color }}; color: #fff;">{{ $tag->name }}</span>
                            </td>
                            <td>{{ $tag->color }}</td>
                            <td>
                                <form action="{{ route('timerboard.settings.tags.destroy', $tag->id) }}" method="POST" onsubmit="return confirm('Are you sure?');">
                                    {{ csrf_field() }}
                                    {{ method_field('DELETE') }}
                                    <button type="submit" class="btn btn-danger btn-xs">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <hr>

            <h4>Add New Tag</h4>
            <form action="{{ route('timerboard.settings.tags.store') }}" method="POST" class="form-inline">
                {{ csrf_field() }}
                <div class="form-group mb-2">
                    <label for="name" class="sr-only">Name</label>
                    <input type="text" class="form-control" name="name" id="name" placeholder="Tag Name" required>
                </div>
                <div class="form-group mx-sm-3 mb-2">
                    <label for="color" class="sr-only">Color</label>
                    <input type="color" class="form-control" name="color" id="color" value="#007bff" required>
                </div>
                <button type="submit" class="btn btn-primary mb-2">Add Tag</button>
            </form>
        </div>
    </div>
@endsection
