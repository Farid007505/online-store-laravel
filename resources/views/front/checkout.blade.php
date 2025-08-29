@extends('front.layouts.app')
@section('content')
    <section class="section-9 pt-4">
        <div class="container">
            <form id="orderForm" name="orderForm" action="{{ route('front.processCheckout') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-8">
                        <div class="sub-title">
                            <h2>Shipping Address</h2>
                        </div>
                        <div class="card shadow-lg border-0">
                            <div class="card-body checkout-form">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <input type="text" name="first_name" id="first_name" class="form-control"
                                                placeholder="First Name"
                                                value="{{ !empty($customerAddress) ? $customerAddress->first_name : '' }}">
                                            <p></p>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <input type="text" name="last_name" id="last_name" class="form-control"
                                                placeholder="Last Name"
                                                value="{{ !empty($customerAddress) ? $customerAddress->last_name : '' }}">
                                            <p></p>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <input type="text" name="email" id="email" class="form-control"
                                                placeholder="Email"
                                                value="{{ !empty($customerAddress) ? $customerAddress->email : '' }}">
                                            <p></p>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <select name="country" id="country" class="form-control">
                                                <option value="">Select a Country</option>
                                                @if ($countries->isNotEmpty())
                                                    @foreach ($countries as $country)
                                                        <option
                                                            {{ !empty($customerAddress) && $customerAddress->country_id == $country->id ? 'selected' : '' }}
                                                            value="{{ $country->id }}">
                                                            {{ $country->name }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                            <p></p>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <textarea name="address" id="address" cols="30" rows="3" placeholder="Address" class="form-control">{{ !empty($customerAddress) ? $customerAddress->address : '' }}</textarea>
                                            <p></p>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <input type="text" name="appartment" id="appartment" class="form-control"
                                                placeholder="Apartment, suite, unit, etc. (optional)"
                                                value="{{ !empty($customerAddress) ? $customerAddress->apartment : '' }}">
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <input type="text" name="city" id="city" class="form-control"
                                                placeholder="City"
                                                value="{{ !empty($customerAddress) ? $customerAddress->city : '' }}">
                                            <p></p>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <input type="text" name="state" id="state" class="form-control"
                                                placeholder="State"
                                                value="{{ !empty($customerAddress) ? $customerAddress->state : '' }}">
                                            <p></p>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <input type="text" name="zip" id="zip" class="form-control"
                                                placeholder="Zip"
                                                value="{{ !empty($customerAddress) ? $customerAddress->zip : '' }}">
                                            <p></p>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <input type="text" name="mobile" id="mobile" class="form-control"
                                                placeholder="Mobile No."
                                                value="{{ !empty($customerAddress) ? $customerAddress->mobile : '' }}">
                                            <p></p>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <textarea name="order_notes" id="order_notes" cols="30" rows="2" placeholder="Order Notes (optional)"
                                                class="form-control">{{ !empty($customerAddress) ? $customerAddress->notes : '' }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="sub-title">
                            <h2>Order Summary</h2>
                        </div>
                        <div class="card cart-summery">
                            <div class="card-body">
                                @foreach (Cart::content() as $item)
                                    <div class="d-flex justify-content-between pb-2">
                                        <div class="h6">{{ $item->name }} X {{ $item->qty }}</div>
                                        <div class="h6">${{ $item->price * $item->qty }}</div>
                                    </div>
                                @endforeach
                                <div class="d-flex justify-content-between summery-end">
                                    <div class="h6"><strong>Subtotal</strong></div>
                                    <div class="h6"><strong>${{ Cart::subtotal() }}</strong></div>
                                </div>
                                <div class="d-flex justify-content-between summery-end">
                                    <div class="h6"><strong>Discount</strong></div>
                                    <div class="h6"><strong id="discount_value">${{ $discount }}</strong></div>
                                </div>

                                <div class="d-flex justify-content-between mt-2">
                                    <div class="h6"><strong>Shipping</strong></div>
                                    <div class="h6"><strong
                                            id="shippingCharge">${{ number_format($totalshippingCharge, 2) }}</strong>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between mt-2 summery-end">
                                    <div class="h5"><strong>Total</strong></div>
                                    <div class="h5"><strong
                                            id="grandtotal">${{ number_format($grandtotal, 2) }}</strong></div>
                                </div>
                            </div>
                        </div>
                        <div class="input-group apply-coupan mt-4">
                            <input type="text" placeholder="Coupon Code" class="form-control" id="discount_code"
                                name="discount_code">
                            <button class="btn btn-dark" type="button" id="apply_discount">Apply Coupon</button>
                        </div>
                        <div id="discount-response-wrapper">
                            @if (Session::has('code'))
                                <div class="mt-4" id="discount_row">
                                    <strong>{{ Session::get('code')->code }}</strong>
                                    <a class="btn btn-sm btn-danger" id="remove_discount"><i class="fa fa-times"></i></a>
                                </div>
                            @endif
                        </div>

                        <div class="card payment-form">
                            <h3 class="card-title h5 mb-3">Payment Method</h3>

                            <div>
                                <input checked type="radio" name="payment_method" value="cod"
                                    id="payment_method_one">
                                <label for="payment_method_one" class="form-check-label">COD</label>
                            </div>

                            <div>
                                <input type="radio" name="payment_method" value="stripe" id="payment_method_two">
                                <label for="payment_method_two" class="form-check-label">Stripe</label>
                            </div>

                            <div class="card-body p-0 d-none mt-3" id="card-payment-form">
                                <div id="card-element" class="form-control"></div>
                                <div id="card-errors" role="alert" class="text-danger mt-2"></div>
                            </div>

                            <div class="pt-4">
                                <button type="submit" class="btn-dark btn btn-block w-100">Pay Now</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>
@endsection

@section('customJs')
    <script src="https://js.stripe.com/v3/"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const stripe = Stripe('{{ config('services.stripe.key') }}');
            const elements = stripe.elements();
            const cardElement = elements.create('card');
            cardElement.mount('#card-element');

            // Payment method toggle
            $('input[name="payment_method"]').change(function() {
                if ($(this).val() === 'stripe') {
                    $('#card-payment-form').removeClass('d-none');
                } else {
                    $('#card-payment-form').addClass('d-none');
                }
            });

            // Form submission
            $('#orderForm').submit(async function(e) {
                e.preventDefault();
                $('button[type="submit"]').prop('disabled', true);

                // Clear previous errors
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').text('');

                let paymentMethodId = null;
                const paymentMethod = $('input[name="payment_method"]:checked').val();

                if (paymentMethod === 'stripe') {
                    const {
                        paymentMethod,
                        error
                    } = await stripe.createPaymentMethod({
                        type: 'card',
                        card: cardElement,
                        billing_details: {
                            name: $('#first_name').val() + ' ' + $('#last_name').val(),
                            email: $('#email').val(),
                        },
                    });

                    if (error) {
                        showError('Payment Error: ' + error.message);
                        $('button[type="submit"]').prop('disabled', false);
                        return;
                    }
                    paymentMethodId = paymentMethod.id;
                }

                $.ajax({
                    url: '{{ route('front.processCheckout') }}',
                    type: 'post',
                    data: $(this).serialize() + (paymentMethodId ? '&payment_method_id=' +
                        paymentMethodId : ''),
                    dataType: 'json',
                    success: async function(response) {
                        $('button[type="submit"]').prop('disabled', false);

                        if (response.status === false) {
                            if (response.requires_action) {
                                // Handle 3D Secure authentication
                                const {
                                    error: stripeError,
                                    paymentIntent
                                } = await stripe.handleCardAction(
                                    response.payment_intent_client_secret
                                );

                                if (stripeError) {
                                    showError(stripeError.message);
                                    return;
                                }

                                // Confirm payment on server
                                $.ajax({
                                    url: '{{ route('front.processCheckout') }}',
                                    type: 'post',
                                    data: $('#orderForm').serialize() +
                                        '&payment_intent_id=' + paymentIntent.id,
                                    dataType: 'json',
                                    success: function(confirmResponse) {
                                        if (confirmResponse.status) {
                                            window.location.href =
                                                "{{ url('/thanks/') }}/" +
                                                confirmResponse.orderId;
                                        } else {
                                            showError(confirmResponse.message);
                                        }
                                    }
                                });
                            } else if (response.errors) {
                                // Show validation errors
                                for (const field in response.errors) {
                                    const input = $('#' + field);
                                    input.addClass('is-invalid');
                                    input.siblings('p').addClass('invalid-feedback').html(
                                        response.errors[field][0]);
                                }
                            } else {
                                showError(response.message);
                            }
                        } else {
                            // Success - redirect to thank you page
                            window.location.href = "{{ url('/thanks/') }}/" + response
                                .orderId;
                        }
                    },
                    error: function(xhr) {
                        $('button[type="submit"]').prop('disabled', false);
                        showError('An error occurred. Please try again.');
                    }
                });
            });

            function showError(message) {
                $('#card-errors').text(message);
            }

            // Country change handler
            $('#country').change(function() {
                $.ajax({
                    url: '{{ route('front.getOrderSummery') }}',
                    type: 'post',
                    data: {
                        country_id: $(this).val()
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status) {
                            $('#shippingCharge').html('$' + response.shippingCharge);
                            $('#grandtotal').html('$' + response.grandtotal);
                            $('#discount_value').html('$' + response.discount);
                            $('#discount-response-wrapper').html(response.discountString);
                        }
                    }
                });
            });

            // Apply discount
            $('#apply_discount').click(function() {
                $.ajax({
                    url: '{{ route('front.applyDiscount') }}',
                    type: 'post',
                    data: {
                        code: $('#discount_code').val(),
                        country_id: $('#country').val()
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status) {
                            $('#shippingCharge').html('$' + response.shippingCharge);
                            $('#grandtotal').html('$' + response.grandtotal);
                            $('#discount_value').html('$' + response.discount);
                            $('#discount-response-wrapper').html(response.discountString);
                        } else {
                            $('#discount-response-wrapper').html('<span class="text-danger">' +
                                response.message + '</span>');
                        }
                    }
                });
            });

            // Remove discount
            $(document).on('click', '#remove_discount', function() {
                $.ajax({
                    url: '{{ route('front.removeCoupon') }}',
                    type: 'post',
                    data: {
                        country_id: $('#country').val()
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status) {
                            $('#shippingCharge').html('$' + response.shippingCharge);
                            $('#grandtotal').html('$' + response.grandtotal);
                            $('#discount_value').html('$' + response.discount);
                            $('#discount-response-wrapper').html('');
                        }
                    }
                });
            });
        });
    </script>
@endsection
