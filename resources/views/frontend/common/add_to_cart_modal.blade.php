<!-- Quick view -->
<div class="modal fade custom-modal" id="quickViewModal" tabindex="-1" aria-labelledby="quickViewModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="closeModel"></button>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 col-sm-12 col-xs-12 mb-md-0 mb-sm-5">
                        <div class="detail-gallery">
                            <div class="product-image-slider">
                                <figure class="border-radius-10">
                                    <img id="pimage" src="" class="img-fluid" width="375" alt="product image" />
                                </figure>
                            </div>
                            <!-- THUMBNAILS -->
                        </div>
                        <!-- End Gallery -->
                    </div>
                    <div class="col-md-6 col-sm-12 col-xs-12">
                        <div class="detail-info pr-30 pl-30">
                            <h3 class="title-detail"><a id="product_name" href="#" class="text-heading"></a></h3>
                            <div class="clearfix product-price-cover">
                                <div class="product-price primary-color float-left">
                                    <span class="current-price mb-2 d-flex text-brand">৳ <span id="pprice"></span></span>
                                    <span>
                                        <span id="oldprice" class="old-price font-md ml-15 mb-2">৳</span>
                                    </span>
                                </div>
                            </div>
                            <form id="choice_form">
                                <div class="row " id="attributes">
                                    <div class="form-group col-lg-6" id="colorArea">
                                    </div>
                                </div>

                                <div class="row" id="attribute_alert"></div>
                            </form>
                            <div class="font-xs">
                                <ul>
                                    <li class="mb-5">Category:
                                        <span class="text-brand" id="pcategory">

                                        </span>
                                    </li>
                                    <li class="mb-5">Brand:
                                        <span class="text-brand" id="pbrand">

                                        </span>
                                    </li>
                                    <li class="mb-5">Product Code:
                                        <span class="text-brand" id="pcode">
                                        </span>
                                    </li>
                                    <li class="mb-5">Stock:
                                        <span class="badge badge-pill badge-success" id="pavailable" style="background: green; color: white;">Available</span>
                                        <span class="badge badge-pill badge-danger" id="pstockout" style="background: red; color: white;">Stock Out</span>
                                    </li>
                                </ul>
                            </div>
                            <div class="detail-extralink align-items-baseline d-flex border-0" style="margin-top: 30px;">
                                <div class="mr-10">
                                    <span class="">Quantity:</span>
                                </div>
                                <div class="detail-qty border radius">
                                    <a href="#" class="qty-down"><i class="fi-rs-angle-small-down"></i></a>
                                    <input type="text" name="quantity" class="qty-val" value="{{ $product->minimum_buy_qty ?? '1' }}" min="1" id="qty">
                                    <a href="#" class="qty-up"><i class="fi-rs-angle-small-up"></i></a>
                                </div>
                            </div>
                            <div class="d-block" id="qty_alert"></div>
                            <div class="detail-extralink d-flex mb-30 border-0" style="margin-top: 10px;">
                                <!-- <div class="detail-qty border radius">
                                    <a href="#" class="qty-down"><i class="fi-rs-angle-small-down"></i></a>

                                    <input type="text" name="quantity" class="qty-val" id="qty" value="1" min="1">

                                    <a href="#" class="qty-up"><i class="fi-rs-angle-small-up"></i></a>
                                </div> -->
                                <div class="product-extra-link2">
                                    <input type="hidden" id="product_id">
                                    <input type="hidden" id="pname">
                                    <input type="hidden" id="product_price">
                                    <input type="hidden" id="discount_amount">
                                    <input type="hidden" id="pfrom" value="modal">
                                    <input type="hidden" id="pvarient" value="">

                                    <input type="hidden" id="minimum_buy_qty" value="">
                                    <input type="hidden" id="stock_qty" value="">

                                    <input type="hidden" id="buyNowCheck" value="0">
                                      @php
                                        $maintenance = getMaintenance();
                                      @endphp
                                    @if($maintenance==1)
                                        <button type="button" class="button button-add-to-cart text-white" data-bs-toggle="modal" data-bs-target="#maintenance"><i class="fi-rs-shoppi ng-cart"></i>Add to cart</button>
                                    @else
                                     <button class="button button-add-to-cart" onclick="addToCart()" id="closeModel"><i class="fi-rs-shopping-cart"></i>Add to cart</button>
                                    @endif
                                    @if($maintenance==1)
                                        <button type="button" class="button button-add-to-cart  ml-5" data-bs-toggle="modal" data-bs-target="#maintenance"><i class="fi-rs-shopping-cart"></i>Buy Now</button>
                                    @else
                                      <button class="button button-add-to-cart  ml-5" onclick="buyNow()" id="closeModel"><i class="fi-rs-shopping-cart"></i>Buy Now</button>
                                    @endif
                                </div>
                            </div>
                            <div class="row mb-3" id="stock_alert"></div>
                        </div>
                        <!-- Detail Info -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>