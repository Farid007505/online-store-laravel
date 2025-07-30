@extends('admin.layout.app')
@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid my-2">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Discount Coupons</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="{{ route('coupons.index') }}" class="btn btn-primary">Coupon Discount List</a>
                </div>
            </div>
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- Main content -->
    <section class="content">
        <!-- Default box -->
        <div class="container-fluid">
            <form action="" method="POST" id="discountForm" name="discountForm">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="code">Code</label>
                                    <input type="text" value="{{ $coupons->code }}" name="code" id="code"
                                        class="form-control" placeholder="Code">
                                    <p></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name">Name</label>
                                    <input type="text" value="{{ $coupons->name }}" name="name" id="name"
                                        class="form-control" placeholder="Name">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="max_uses">Max Uses</label>
                                    <input type="number" value="{{ $coupons->max_uses }}" name="max_uses" id="max_uses"
                                        class="form-control" placeholder="max Uses">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="max_uses_user">Max Uses User</label>
                                    <input type="number" value="{{ $coupons->max_uses_user }}" name="max_uses_user"
                                        id="max_uses_user" class="form-control" placeholder="max Uses User">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="type">Type</label>
                                    <select name="type" id="type" class="form-control">
                                        <option {{ !empty($coupons->type == 'percent') ? 'selected' : '' }} value="percent">
                                            Percent
                                        </option>
                                        <option {{ !empty($coupons->type == 'fixed') ? 'selected' : '' }} value="percent">
                                            Fixed
                                        </option>
                                    </select>
                                    <p></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="discount_amount">Discount Amount</label>
                                    <input type="number" value="{{ $coupons->discount_amount }}" name="discount_amount"
                                        id="discount_amount" class="form-control" placeholder="Discount Amount">
                                    <p></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="min_amount">Min Amount</label>
                                    <input type="number" value="{{ $coupons->min_amount }}" name="min_amount"
                                        id="min_amount" class="form-control" placeholder="Min Amount">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status">Status</label>
                                    <select name="status" id="status" class="form-control">
                                        <option {{ !empty($coupons->status == 1) ? 'selected' : '' }} value="1">Active
                                        </option>
                                        <option {{ !empty($coupons->status == 0) ? 'selected' : '' }} value="0">Block
                                        </option>
                                    </select>
                                    <p></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="start_at">Start At</label>
                                    <input autocomplete="off" value="{{ $coupons->start_at }}" type="text"
                                        name="start_at" id="start_at" class="form-control" placeholder="Start At">
                                    <p></p>

                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="expire_at">Expire At</label>
                                    <input autocomplete="off" value="{{ $coupons->expire_at }}" type="text"
                                        name="expire_at" id="expire_at" class="form-control" placeholder="Expire At">
                                    <p></p>

                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="description">Description</label>
                                    <textarea class="form-control" name="description" id="description" cols="30" rows="5"
                                        placeholder="Description">{{ $coupons->description }}</textarea>
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
        $("#discountForm").submit(function(event) {
            event.preventDefault();
            var element = $(this);
            $('button[type=submit]').prop('disabled', true);
            $.ajax({
                url: '{{ route('coupons.update', $coupons->id) }}',
                type: 'put',
                data: element.serializeArray(),
                dataType: 'json',
                success: function(response) {

                    $('button[type=submit]').prop('disabled', false);

                    if (response['status'] == true) {
                        window.location.href = '{{ route('coupons.index') }}';
                        $("#code").removeClass('is-invalid').siblings('p').
                        removeClass('invalid-feedback').html('');

                        $("#type").removeClass('is-invalid').siblings('p').
                        removeClass('invalid-feedback').html('');

                        $("#discount").removeClass('is-invalid').siblings('p').
                        removeClass('invalid-feedback').html('');

                        $("#status").removeClass('is-invalid').siblings('p').
                        removeClass('invalid-feedback').html('');

                        $("#start_at").removeClass('is-invalid').siblings('p').
                        removeClass('invalid-feedback').html('');

                        $("#expire_at").removeClass('is-invalid').siblings('p').
                        removeClass('invalid-feedback').html('');

                    } else {
                        var errors = response['errors'];
                        if (errors['code']) {
                            $("#code").addClass('is-invalid').
                            siblings('p').
                            addClass('invalid-feedback').html(errors['code']);
                        } else {
                            $("#code").removeClass('is-invalid').siblings('p').
                            removeClass('invalid-feedback').html('');
                        }

                        if (errors['type']) {
                            $("#type").addClass('is-invalid').siblings('p').
                            addClass('invalid-feedback').html(errors['type']);
                        } else {
                            $("#type").removeClass('is-invalid').siblings('p').
                            removeClass('invalid-feedback').html('');
                        }

                        if (errors['discount_amount']) {
                            $("#discount_amount").addClass('is-invalid').siblings('p').
                            addClass('invalid-feedback').html(errors['discount_amount']);
                        } else {
                            $("#discount_amount").removeClass('is-invalid').siblings('p').
                            removeClass('invalid-feedback').html('');
                        }

                        if (errors['status']) {
                            $("#status").addClass('is-invalid').siblings('p').
                            addClass('invalid-feedback').html(errors['status']);
                        } else {
                            $("#status").removeClass('is-invalid').siblings('p').
                            removeClass('invalid-feedback').html('');
                        }

                        if (errors['start_at']) {
                            $("#start_at").addClass('is-invalid').siblings('p').
                            addClass('invalid-feedback').html(errors['start_at']);
                        } else {
                            $("#start_at").removeClass('is-invalid').siblings('p').
                            removeClass('invalid-feedback').html('');
                        }

                        if (errors['expire_at']) {
                            $("#expire_at").addClass('is-invalid').siblings('p').
                            addClass('invalid-feedback').html(errors['expire_at']);
                        } else {
                            $("#expire_at").removeClass('is-invalid').siblings('p').
                            removeClass('invalid-feedback').html('');
                        }

                    }

                },
                error: function(jqXHR, exception) {
                    console.log("something went wrong");
                }
            })
        })

        $(document).ready(function() {
            $('#start_at').datetimepicker({
                // options here
                format: 'Y-m-d H:i:s',
            });
            $('#expire_at').datetimepicker({
                // options here
                format: 'Y-m-d H:i:s',
            });
        });
    </script>
@endsection
