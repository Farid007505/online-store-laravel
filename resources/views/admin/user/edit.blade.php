@extends('admin.layout.app')
@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid my-2">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Edit User</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="{{ route('users.index') }}" class="btn btn-primary">User List</a>
                </div>
            </div>
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- Main content -->
    <section class="content">
        <!-- Default box -->
        <div class="container-fluid">
            <form action="" method="POST" id="userForm" name="userForm">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name">Name</label>
                                    <input value="{{ $user->name }}" type="text" name="name" id="name"
                                        class="form-control" placeholder="Name">
                                    <p></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email">E-mail</label>
                                    <input value="{{ $user->email }}" type="text" name="email" id="email"
                                        class="form-control" placeholder="email">
                                    <p></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password">Password</label>
                                    <input type="text" name="password" id="password" class="form-control"
                                        placeholder="password"><span>To change Password You have to enter the
                                        Value,Otherwise Leave Blank</span>
                                    <p></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="phone">Phone</label>
                                    <input value="{{ $user->phone }}" type="text" name="phone" id="phone"
                                        class="form-control" placeholder="phone">
                                    <p></p>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status">Status</label>
                                    <select name="status" id="status" class="form-control">
                                        <option {{ !empty($user->status == 1) ? 'selected' : '' }} value="1">Active
                                        </option>
                                        <option {{ !empty($user->status == 0) ? 'selected' : '' }} value="0">Block
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    @if ($roles->isNotEmpty())
                                        @foreach ($roles as $role)
                                            <input {{ $hasRole->contains($role->id) ? 'checked' : '' }} type="checkbox"
                                                id="checkbox-{{ $role->id }}" value="{{ $role->name }}"
                                                name="role[]">
                                            <label for="checkbox-{{ $role->id }}">{{ $role->name }}</label>
                                        @endforeach
                                    @endif

                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="pb-5 pt-3">
                    <button type="submit" class="btn btn-primary">Update</button>
                    <a href="#" class="btn btn-outline-dark ml-3">Cancel</a>
                </div>
            </form>
        </div>
        <!-- /.card -->
    </section>
    <!-- /.content -->
@endsection

@section('customJs')
    <script>
        $("#userForm").submit(function(event) {
            event.preventDefault();
            var element = $(this);
            $('button[type=submit]').prop('disabled', true);
            $.ajax({
                url: '{{ route('users.update', $user->id) }}',
                type: 'put',
                data: element.serializeArray(),
                dataType: 'json',
                success: function(response) {

                    $('button[type=submit]').prop('disabled', false);

                    if (response['status'] == true) {
                        window.location.href = '{{ route('users.index') }}';
                        $("#name").removeClass('is-invalid').siblings('p').
                        removeClass('invalid-feedback').html('');

                        $("#email").removeClass('is-invalid').siblings('p').
                        removeClass('invalid-feedback').html('');
                        $("#phone").removeClass('is-invalid').siblings('p').
                        removeClass('invalid-feedback').html('');

                    } else {
                        var errors = response['errors'];
                        if (errors['name']) {
                            $("#name").addClass('is-invalid').
                            siblings('p').
                            addClass('invalid-feedback').html(errors['name']);
                        } else {
                            $("#name").removeClass('is-invalid').siblings('p').
                            removeClass('invalid-feedback').html(errors['name']);
                        }

                        if (errors['email']) {
                            $("#email").addClass('is-invalid').siblings('p').
                            addClass('invalid-feedback').html(errors['email']);
                        } else {
                            $("#email").removeClass('is-invalid').siblings('p').
                            removeClass('invalid-feedback').html(errors['email']);
                        }

                        if (errors['phone']) {
                            $("#phone").addClass('is-invalid').siblings('p').
                            addClass('invalid-feedback').html(errors['phone']);
                        } else {
                            $("#phone").removeClass('is-invalid').siblings('p').
                            removeClass('invalid-feedback').html(errors['phone']);
                        }

                    }

                },
                error: function(jqXHR, exception) {
                    console.log("something went wrong");
                }
            })
        });
    </script>
@endsection
