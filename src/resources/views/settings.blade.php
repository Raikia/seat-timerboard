@extends('web::layouts.grids.12')

@section('title', 'Timerboard Settings')
@section('page_header', 'Timerboard Settings')

@section('content')
    <div class="card mb-4">
        <div class="card-header">
            <h3 class="card-title">General Settings</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('timerboard.settings.default-role') }}" method="POST" class="form-inline">
                {{ csrf_field() }}
                <div class="form-group mb-2">
                    <label for="default_timer_role" class="mr-2">Default Access Role:</label>
                    <select name="default_timer_role" id="default_timer_role" class="form-control mr-2">
                        <option value="">Public (Everyone)</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" {{ $defaultRoleId == $role->id ? 'selected' : '' }}>
                                {{ $role->title }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-primary mb-2">Save</button>
            </form>
            <small class="text-muted">Timers created will default to this role restricted visibility. If "Public" is selected, timers are visible to everyone by default.</small>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Manage Tags</h3>
        </div>
        <div class="card-body">
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
                                                        <input type="color" class="form-control" name="color" id="color" value="{{ $tag->color }}" style="width: 50px;" required>
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
                    <input type="color" class="form-control" name="color" id="color" value="#007bff" style="width: 50px;" required>
                </div>
                <button type="submit" class="btn btn-primary mb-2">Add Tag</button>
            </form>
        </div>
    </div>

    @can('seat-timerboard.delete')
    <div class="card mt-4">
        <div class="card-header">
            <h3 class="card-title">Maintenance</h3>
        </div>
        <div class="card-body">
            <p>Use this button to remove all timers that have already elapsed. This action cannot be undone.</p>
            <form action="{{ route('timerboard.destroy.elapsed') }}" method="POST" onsubmit="return confirm('Are you sure you want to delete ALL elapsed timers? This action cannot be undone.');">
                {{ csrf_field() }}
                {{ method_field('DELETE') }}
                <button type="submit" class="btn btn-danger">Delete All Elapsed Timers</button>
            </form>
        </div>
    </div>
    @endcan
@endsection
