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
                                <button type="button" class="btn btn-warning btn-xs" data-toggle="modal" data-target="#editTagModal-{{ $tag->id }}">Edit</button>
                                <form action="{{ route('timerboard.settings.tags.destroy', $tag->id) }}" method="POST" onsubmit="return confirm('Are you sure?');" style="display:inline-block;">
                                    {{ csrf_field() }}
                                    {{ method_field('DELETE') }}
                                    <button type="submit" class="btn btn-danger btn-xs">Delete</button>
                                </form>

                                <!-- Edit Tag Modal -->
                                <div class="modal fade" id="editTagModal-{{ $tag->id }}" tabindex="-1" role="dialog" aria-labelledby="editTagModalLabel-{{ $tag->id }}" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="editTagModalLabel-{{ $tag->id }}">Edit Tag</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <form action="{{ route('timerboard.settings.tags.update', $tag->id) }}" method="POST">
                                                {{ csrf_field() }}
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <label for="name">Tag Name</label>
                                                        <input type="text" class="form-control" name="name" id="name" value="{{ $tag->name }}" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="color">Color</label>
                                                        <input type="color" class="form-control" name="color" id="color" value="{{ $tag->color }}" required style="width: 50px;">
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                    <button type="submit" class="btn btn-primary">Save changes</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
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
