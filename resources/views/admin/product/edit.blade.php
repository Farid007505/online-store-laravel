@extends('admin.layout.app')
@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid my-2">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Update Product</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="{{ route('products.index') }}" class="btn btn-primary">Product List</a>
                </div>
            </div>
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- Main content -->
    <section class="content">
        <!-- Default box -->
        <form action="" name="productForm" id="productForm">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-8">
                        <div class="card mb-3">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="title">Title</label>
                                            <input type="text" name="title" id="title"
                                                value="{{ old('title', $products->title) }}" class="form-control"
                                                placeholder="Title">
                                            <p class="error"></p>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="slug">Slug</label>
                                            <input type="text" name="slug" id="slug" class="form-control"
                                                placeholder="slug" readonly value="{{ old('slug', $products->slug) }}">
                                            <p class="error"></p>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="short_description">Short Description</label>
                                            <textarea name="short_description" id="short_description" cols="30" rows="10" class="summernote"
                                                placeholder="short_description"></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="description">Description</label>
                                            <textarea name="description" id="description" cols="30" rows="10" class="summernote"
                                                placeholder="Description"></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="shipping_returns">Shipping and Returns</label>
                                            <textarea name="shipping_returns" id="shipping_returns" cols="30" rows="10" class="summernote"
                                                placeholder="shipping_returns"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card mb-3">
                            <div class="card-body">
                                <h2 class="h4 mb-3">Media</h2>
                                <div id="image" class="dropzone dz-clickable">
                                    <div class="dz-message needsclick">
                                        <br>Drop files here or click to upload.<br><br>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="row" id="product-gallery">
                            @foreach ($productImages as $image)
                                <div class="col-md-3" id="image-row-{{ $image->id }}">
                                    <div class="card">
                                        <input type="hidden" name="image_array[]" value="{{ $image->id }}">
                                        <img src="{{ asset('uploads/product/small/' . $image->image) }}"
                                            class="card-img-top" alt="">
                                        <div class="card-body">
                                            <a href="javascript:void(0)" class="btn btn-danger"
                                                onclick="deleteImage({{ $image->id }})">Delete</a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="card mb-3">
                            <div class="card-body">
                                <h2 class="h4 mb-3">Pricing</h2>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="price">Price</label>
                                            <input type="text" name="price" id="price"
                                                value="{{ old('price', $products->price) }}" class="form-control"
                                                placeholder="Price">
                                            <p class="error"></p>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="compare_price">Compare at Price</label>
                                            <input type="text" name="compare_price" id="compare_price"
                                                value="{{ old('compare_price', $products->compare_price) }}"
                                                class="form-control" placeholder="Compare Price">
                                            <p class="text-muted mt-3">
                                                To show a reduced price, move the product’s original price into Compare at
                                                price. Enter a lower value into Price.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card mb-3">
                            <div class="card-body">
                                <h2 class="h4 mb-3">Inventory</h2>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="sku">SKU (Stock Keeping Unit)</label>
                                            <input type="text" name="sku" id="sku"
                                                value="{{ old('sku', $products->sku) }}" class="form-control"
                                                placeholder="sku">
                                            <p class="error"></p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="barcode">Barcode</label>
                                            <input type="text" name="barcode" id="barcode"
                                                value="{{ old('barcode', $products->barcode) }}" class="form-control"
                                                placeholder="Barcode">
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <div class="custom-control custom-checkbox">
                                                <input type="hidden" name="track_qty" value="No">
                                                <input class="custom-control-input" type="checkbox" id="track_qty"
                                                    name="track_qty" value="Yes"
                                                    {{ old('track_qty', $products->track_qty) == 'Yes' ? 'checked' : '' }}
                                                    checked>
                                                <label for="track_qty" class="custom-control-label">Track Quantity</label>
                                                <p class="error"></p>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <input type="number" min="0" name="qty" id="qty"
                                                class="form-control" placeholder="Qty"
                                                value="{{ old('qty', $products->qty) }}">
                                            <p class="error"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card mb-3">
                            <div class="card-body">
                                <h2 class="h4 mb-3">Related product</h2>
                                <div class="mb-3">
                                    <select multiple class="related-product w-100" name="related_products[]"
                                        id="related_products">
                                        @if (!empty($relatedproducts))
                                            @foreach ($relatedproducts as $relproduct)
                                                <option selected value="{{ $relproduct->id }}">
                                                    {{ $relproduct->title }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                    <p class="error"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card mb-3">
                            <div class="card-body">
                                <h2 class="h4 mb-3">Product status</h2>
                                <div class="mb-3">
                                    <select name="status" id="status" class="form-control">
                                        <option {{ $products->status == '1' ? 'selected' : '' }} value="1">Active
                                        </option>
                                        <option {{ $products->status == '0' ? 'selected' : '' }} value="0">Block
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-body">
                                <h2 class="h4  mb-3">Product category</h2>
                                <div class="mb-3">
                                    <label for="category_id">Category</label>
                                    <select name="category" id="category" class="form-control">
                                        @if ($categories->isNotEmpty())
                                            @foreach ($categories as $cat)
                                                <option {{ $products->category_id == $cat->id ? 'selected' : '' }}
                                                    value="{{ $cat->id }}">{{ $cat->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    <p class="error"></p>
                                </div>
                                <div class="mb-3">
                                    <label for="category">Sub category</label>
                                    <select name="sub_category" id="sub_category" class="form-control">
                                        <option value="">Select SubCategory</option>
                                        @if ($subcategories->isNotEmpty())
                                            @foreach ($subcategories as $subcat)
                                                <option {{ $products->sub_category_id == $subcat->id ? 'selected' : '' }}
                                                    value="{{ $subcat->id }}">{{ $subcat->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="card mb-3">
                            <div class="card-body">
                                <h2 class="h4 mb-3">Product brand</h2>
                                <div class="mb-3">
                                    <select name="brand" id="brand" class="form-control">
                                        @if ($brands->isNotEmpty())
                                            @foreach ($brands as $brand)
                                                <option {{ $products->brand_id == $brand->id ? 'selected' : '' }}
                                                    value="{{ $brand->id }}">{{ $brand->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="card mb-3">
                            <div class="card-body">
                                <h2 class="h4 mb-3">Featured product</h2>
                                <div class="mb-3">
                                    <select name="is_featured" id="is_featured" class="form-control">
                                        <option {{ $products->is_featured == 'No' ? 'selected' : '' }} value="No">No
                                        </option>
                                        <option {{ $products->is_featured == 'Yes' ? 'selected' : '' }} value="Yes">
                                            Yes
                                        </option>
                                    </select>
                                    <p class="error"></p>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>

                <div class="pb-5 pt-3">
                    <button type="submit" class="btn btn-primary">Update</button>
                    <a href="products.html" class="btn btn-outline-dark ml-3">Cancel</a>
                </div>
            </div>
        </form>
        <!-- /.card -->
    </section>
    <!-- /.content -->
@endsection
@section('customJs')
    <script>
        $('.related-product').select2({
            ajax: {
                url: '{{ route('products.getProducts') }}',
                dataType: 'json',
                tags: true,
                multiple: true,
                minimumInputLength: 3,
                processResults: function(data) {
                    return {
                        results: data.tags
                    }
                }
            }
        });
        $('#title').change(function() {
            element = $(this);

            $("button[type='submit']").prop('disabled', true);
            $.ajax({
                url: '{{ route('getSlug') }}',
                type: 'get',
                data: {
                    title: element.val()
                },
                dataType: 'json',
                success: function(response) {
                    if (response['status'] == true) {
                        $("button[type='submit']").prop('disabled', false);
                        $('#slug').val(response['slug']);
                    }
                }
            })
        })

        $('#productForm').submit(function(event) {
            event.preventDefault();
            var formArray = $(this).serializeArray();
            $("button[type='submit']").prop('disabled', true);
            $.ajax({
                url: '{{ route('products.update', $products->id) }}',
                type: 'PUT',
                data: formArray,
                dataType: 'json',
                success: function(response) {
                    $("button[type='submit']").prop('disabled', false);
                    if (response['status'] == true) {
                        //$('.error').removeClass('invalid-feedback').html('');
                        //$("input[type='text'],input[type='numeric'],select").removeClass('is-invalid').val('');
                        window.location.href = "{{ route('products.index') }}";
                    } else {
                        var errors = response['errors'];
                        //if(errors['title']){
                        // $('#title').addClass('is-invalid')
                        //.siblings('p')
                        //.addClass('invalid-feedback')
                        //.html(errors['title']);
                        //}else{
                        //$('#title').removeClass('is-invalid')
                        //.siblings('p')
                        //.addClass('invalid-feedback')
                        //.html('');
                        //}
                        $('.error').removeClass('invalid-feedback').html('');
                        $("input[type='text'],input[type='numeric'],select").removeClass('is-invalid')
                            .val(
                                '');

                        $.each(errors, function(key, value) {
                            $(`#${key}`).addClass('is-invalid')
                                .siblings('p')
                                .addClass('invalid-feedback')
                                .html(value);
                        });

                    }

                },
                error: function() {
                    console.log('Something went wrong');
                }

            });
        });

        $('#category').change(function() {
            var category_id = $(this).val();
            $.ajax({
                url: '{{ route('product-subcategories.index') }}',
                type: 'get',
                data: {
                    category_id: category_id
                },
                dataType: 'json',
                success: function(response) {
                    $('#sub_category').find('option').not(':First').remove();
                    $.each(response['subcategories'], function(key, item) {
                        $('#sub_category').append(
                            `<option value="${item.id}">${item.name}</option>`)
                    })
                },
                error: function() {
                    console.log('Something went wrong');
                }

            })
        });

        Dropzone.autoDiscover = false;
        const dropzone = $('#image').dropzone({

            url: "{{ route('product_images.update') }}",
            maxFiles: 10,
            params: {
                'product_id': '{{ $products->id }}'
            },
            paramName: 'image',
            addRemoveLinks: true,
            acceptedFiles: "image/jpeg,image/png,image/gif,image/jpg",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(file, response) {
                //$('#image_id').val(response.image_id);
                var html = `<div class="col-md-3" id="image-row-${response.image_id}"><div class="card">
                <input type="hidden" name="image_array[]" value="${response.image_id}">
            <img src="${response.image_path}" class="card-img-top" alt="">
            <div class="card-body">
                <a href="javascript:void(0)" class="btn btn-danger" onclick="deleteImage(${response.image_id})">Delete</a>
            </div>
            </div>
            </div>`;
                $('#product-gallery').append(html);
            },
            complete: function(file) {
                this.removeFile(file);
            }
        });

        function deleteImage(id) {
            $("#image-row-" + id).remove();

            if (confirm('Are you want to delte?')) {
                $.ajax({
                    url: '{{ route('product_images.destroy') }}',
                    type: 'delete',
                    data: {
                        id: id
                    },
                    success: function(response) {
                        if (response == true) {
                            alert(response.message);
                        } else {
                            alert(response.message);
                        }
                    }
                });
            }
        }
    </script>
@endsection
