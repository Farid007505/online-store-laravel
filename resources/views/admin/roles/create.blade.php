@extends('admin.layout.app')
@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid my-2">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Create Roles</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="{{ route('roles.index') }}" class="btn btn-primary">Roles List</a>
                </div>
            </div>
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- Main content -->
    <section class="content">
        <!-- Default box -->
        <div class="container-fluid">
            <form action="" method="POST" id="roleForm" name="roleForm">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name">Name</label>
                                    <input type="text" name="name" id="name" class="form-control"
                                        placeholder="Name">
                                    <p></p>
                                </div>
                                <div class="mb-3">
                                    @if ($permissions->isNotEmpty())
                                        @foreach ($permissions as $permission)
                                            <input type="checkbox" id="permission-{{ $permission->name }}"
                                                value="{{ $permission->name }}" name="permission[]">
                                            <label class="form-check-label"
                                                for="permission-{{ $permission->name }}">{{ $permission->name }}</label>
                                        @endforeach
                                    @endif
                                    <p></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="pb-5 pt-3">
                    <button type="submit" class="btn btn-primary">Create</button>
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
        $("#roleForm").submit(function(event) {
            event.preventDefault();
            var element = $(this);
            $('button[type=submit]').prop('disabled', true);
            $.ajax({
                url: '{{ route('roles.store') }}',
                type: 'post',
                data: element.serializeArray(),
                dataType: 'json',
                success: function(response) {

                    $('button[type=submit]').prop('disabled', false);

                    if (response['status'] == true) {
                        window.location.href = '{{ route('roles.index') }}';
                        $("#name").removeClass('is-invalid').siblings('p').
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

                    }

                },
                error: function(jqXHR, exception) {
                    console.log("something went wrong");
                }
            })
        });
    </script>
@endsection
