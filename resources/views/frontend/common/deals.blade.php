<div class="col-xl-3 col-lg-4 col-md-6 col-6">
    <div class="product-cart-wrap style-2">
        <div class="product-img-action-wrap">
            <div class="product-img">
                <a href="{{ route('product.details',$product->slug) }}">
                    @if($product->product_thumbnail && $product->product_thumbnail != '' && $product->product_thumbnail != 'Null')
                        <img class="default-img" data-original="{{ asset($product->product_thumbnail) }}" alt="" />
                    @else
                        <img class="img-lg mb-3" data-original="{{ asset('upload/no_image.jpg') }}" alt="" />
                    @endif
                </a>
            </div>
        </div>
        <div class="product-content-wrap">
            <div class="deals-content">
                <h2>
                	<a href="{{ route('product.details',$product->slug) }}">
                		@if(session()->get('language') == 'bangla')
                        	<?php $p_name_bn =  strip_tags(html_entity_decode($product->name_bn))?>
                        	{{ Str::limit($p_name_bn, $limit = 30, $end = '. . .') }}
	                    @else
	                        <?php $p_name_en =  strip_tags(html_entity_decode($product->name_en))?>
	                        {{ Str::limit($p_name_en, $limit = 30, $end = '. . .') }}
	                    @endif
                	</a>
                </h2>

                <div>
                    <span class="font-small text-muted">By
                    	<a href="{{ route('product.category', $product->category->slug) }}">
                    		@if(session()->get('language') == 'bangla')
		                        {{ $product->category->name_bn }}
		                    @else
		                        {{ $product->category->name_en }}
		                    @endif
                    	</a>
                	</span>
                </div>
	            @php
                    if ($product->discount_type == 1) {
                        $price_after_discount = $product->regular_price - $product->discount_price;
                    } elseif ($product->discount_type == 2) {
                        $price_after_discount =
                        $product->regular_price - ($product->regular_price * $product->discount_price) / 100;
                    }
                @endphp
                <div class="product-card-bottom">
                    @if ($product->discount_price > 0)
                        @if (auth()->check() && auth()->user()->role == 7)
                            <div class="special__price">৳{{ $product->reseller_price }}</div>
                        @else
                            <div class="special__price">৳{{ $price_after_discount }}</div>
                            <div class="old__price">
                                <del>৳{{ $product->regular_price }}</del>
                            </div>
                        @endif
                    @else
                        @if (auth()->check() && auth()->user()->role == 7)
                            <div class="special__price">৳{{ $product->reseller_price }}</div>
                        @else
                            <div class="special__price">৳{{ $product->regular_price }}</div>
                        @endif
                    @endif


                    @if (auth()->check() && auth()->user()->role == 7)
                        <div>
                            <span>Regular Price: <span class="text-info">৳ {{ $product->regular_price }}</span></span>
                            <input type="hidden" id="regular_price" name="regular_price" value="{{ $product->regular_price }}"
                                min="1">
                        </div>
                    @endif

                    <div class="add-cart">
                        @if($product->is_varient == 1)
	                        <a class="add" id="{{ $product->id }}" onclick="productView(this.id)" data-bs-toggle="modal" data-bs-target="#quickViewModal"><i class="fi-rs-shopping-cart mr-5"></i>Add </a>
	                    @else
	                        <input type="hidden" id="pfrom" value="direct">
	                        <input type="hidden" id="product_product_id" value="{{ $product->id }}"  min="1">
	                        <input type="hidden" id="{{ $product->id }}-product_pname" value="{{ $product->name_en }}">
	                        <a class="add" onclick="addToCartDirect({{ $product->id }})" ><i class="fi-rs-shopping-cart mr-5"></i>Add </a>
	                    @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>