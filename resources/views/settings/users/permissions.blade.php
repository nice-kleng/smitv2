@extends('layouts.app', ['title' => 'User Permissions ' . $user->name])

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('settings.users.permissions', $user->id) }}" method="post">
                        @method('put')
                        @csrf
                        <div class="form-group">
                            @foreach ($permissions as $group => $items)
                                <h3>{{ Str::ucfirst($group) }}</h3>
                                @foreach ($items->chunk(4) as $chunk)
                                    <div class="row">
                                        @foreach ($chunk as $permission)
                                            <div class="col-md-3">
                                                <label>
                                                    <input type="checkbox" name="permissions[]"
                                                        value="{{ $permission->name }}"
                                                        {{ $user->hasPermissionTo($permission->name) ? 'checked' : '' }}>
                                                    {{ $permission->name }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                @endforeach
                            @endforeach
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Simpan</button>
                            <a href="{{ route('settings.users.index') }}" class="btn btn-secondary">Kembali</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
