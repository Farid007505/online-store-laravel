@extends('front.layouts.app')
@section('content')
    <section class=" section-11 ">
        <div class="container  mt-5">
            <div class="row">
                @include('front.account.common.message')
                <div class="col-md-3">
                    @include('front.account.common.sidebar')
                </div>
                <div class="col-md-9">
                    <div class="card">
                        <div class="card-header">
                            <h2 class="h5 mb-0 pt-2 pb-2">My Orders</h2>
                        </div>
                        @if ($wishlists->isNotEmpty())
                            @foreach ($wishlists as $wishlist)
                                <div class="card-body p-4">
                                    <div class="d-sm-flex justify-content-between mt-lg-4 mb-4 pb-3 pb-sm-2 border-bottom">
                                        <div class="d-block d-sm-flex align-items-start text-center text-sm-start">

                                            @php
                                                $productImage = getPorductImage($wishlist->product_id);
                                            @endphp
                                            <a class="d-block flex-shrink-0 mx-auto me-sm-4"
                                                href="{{ route('front.product', $wishlist->product->slug) }}"
                                                style="width: 10rem;">
                                                @if (!empty($productImage->image))
                                                    <img class="card-img-top"
                                                        src="{{ asset('uploads/product/small/' . $productImage->image) }}"
                                                        class="img-thumbnail">
                                                @else
                                                    <img class="card-img-top"
                                                        src="{{ asset('uploads/product/small/download.jpeg') }}"
                                                        class="img-thumbnail">
                                                @endif
                                            </a>
                                            <div class="pt-2">
                                                <h3 class="product-title fs-base mb-2"><a
                                                        href="{{ route('front.product', $wishlist->product->slug) }}">{{ $wishlist->product->title }}</a>
                                                </h3>
                                                <div class="fs-lg text-accent pt-2"> <span
                                                        class="h5"><strong>{{ $wishlist->product->price }}</strong></span>
                                                    @if ($wishlist->product->compare_price > 0)
                                                        <span
                                                            class="h6 text-underline"><del>{{ $wishlist->product->compare_price }}</del></span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="pt-2 ps-sm-3 mx-auto mx-sm-0 text-center">
                                            <button onclick="removeProduct({{ $wishlist->product_id }})"
                                                class="btn btn-outline-danger btn-sm" type="button"><i
                                                    class="fas fa-trash-alt me-2"></i>Remove</button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div>
                                <h3 class="h5">Your WishList is Empty!!</h3>
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@section('customJs')
    <script>
        function removeProduct(id) {
            $.ajax({
                url: '{{ route('account.removeProductFromWishlist') }}',
                type: 'post',
                data: {
                    id: id
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status == true) {
                        window.location.href = "{{ route('account.wishlist') }}";



                    }
                }
            });
        }
    </script>
@endsection
