@extends('layouts.frontend')
@push('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.1.0/magnific-popup.min.css">
    <style>
        .app-figure {
            width: 100% !important;
            margin: 0px auto;
            border: 0px solid red;
            padding: 20px;
            position: relative;
            text-align: center;
        }



        .MagicZoom {
            display: none;
        }

        .MagicZoom.Active {
            display: block;
            overflow: hidden;
        }

        .selectors {
            margin-top: 10px;
        }

        .selectors .mz-thumb img {
            max-width: 56px;
        }

        .product__item .dropdown-toggle::after {
            display: none;
        }

        @media screen and (max-width: 1023px) {
            .app-figure {
                width: 99% !important;
                margin: 20px auto;
                padding: 0;
            }
        }

        .selectors {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 10px;
        }

        .rating i {
            color: #ffb301;
        }

        .single-review-item {
            border-top: 1px solid #ffb301;
        }

        .single-review-item {
            padding: 10px 0;
        }

        .review_list {
            margin-top: 20px;
        }

        a[data-zoom-id],
        .mz-thumb,
        .mz-thumb:focus {
            margin-top: 0 !important;
        }

        .dropdown-menu.dots__dropdown.show li a {
            color: #000 !important;
            font-size: 15px;
            padding: 5px 10px;
            line-height: 1;
        }
    </style>
    <!-- Image zoom -->
    <link rel="stylesheet" href="{{ asset('frontend/magiczoomplus/magiczoomplus.css') }}" />
@endpush
@section('content-frontend')
    @include('frontend.common.maintenance')
    <main class="main">
        <div class="page-header breadcrumb-wrap">
            <div class="container">
                <div class="breadcrumb d-flex align-items-center" style="justify-content: space-between;cursor: pointer;">
                    <a href="{{ route('home') }}" rel="nofollow"><i class="fi-rs-home mr-5"></i>
                        @if (session()->get('language') == 'bangla')
                            {{ $product->category->name_bn ?? 'No Category' }}
                        @else
                            {{ $product->category->name_en ?? 'No Category' }}
                        @endif
                    </a>
                    <div class="product__item d-md-none d-sm-block">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                                aria-expanded="false">
                                <i class="fa-solid fa-ellipsis-vertical"></i>
                            </a>
                            <ul class="dropdown-menu dots__dropdown">
                                <li><a class="dropdown-item" href="{{ route('home') }}">Home</a></li>
                                <li><a class="dropdown-item" href="{{ route('category_list.index') }}">Categories</a></li>
                                <li><a class="dropdown-item" href="{{ route('product.show') }}">Shop</a></li>
                                @auth
                                    <li><a class="dropdown-item" href="{{ route('dashboard') }}">My Account</a></li>
                                @endauth
                                @guest
                                    <li><a class="dropdown-item" href="{{ route('login') }}">Login</a></li>
                                @endguest
                            </ul>
                        </li>
                    </div>
                </div>
            </div>
        </div>
        <div class="container mb-30">
            <div class="row">
                <div class="col-xl-12">
                    <div class="product-detail accordion-detail">
                        <div class="row mb-50 mt-30">
                            <div class="col-lg-6 col-sm-12 col-xs-12 mb-md-0">
                                <div class="row g-2">
                                    <div class="col-2">
                                        <div class="product__thumbnail slider-nav">
                                            @foreach ($product->multi_imgs as $img)
                                                <div class="single___thumbnail">
                                                    <img src="{{ asset($img->photo_name) }}" alt="">
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="col-10">
                                        <div class="product__gallery slider-for">
                                            @foreach ($product->multi_imgs as $img)
                                                <div class="product__thumbnail__image">
                                                    <a href="{{ asset($img->photo_name) }}">
                                                        <img src="{{ asset($img->photo_name) }}" alt="">
                                                    </a>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                <!-- Product Zoom Image -->
                            </div>
                            <div class="col-lg-6 col-sm-12 col-xs-12">
                                <div class="detail-info">
                                    @php
                                        $discount = 0;
                                        if (auth()->check() && auth()->user()->role == 7) {
                                            $amount = $product->reseller_price;
                                        } else {
                                            $amount = $product->regular_price;
                                            if ($product->discount_price > 0) {
                                                if ($product->discount_type == 1) {
                                                    $discount = $product->discount_price;
                                                    $amount = $product->regular_price - $discount;
                                                } elseif ($product->discount_type == 2) {
                                                    $discount =
                                                        ($product->regular_price * $product->discount_price) / 100;
                                                    $amount = $product->regular_price - $discount;
                                                } else {
                                                    $amount = $product->regular_price;
                                                }
                                            }
                                        }
                                    @endphp

                                    <input type="hidden" id="discount_amount" value="{{ $discount }}">
                                    <h2 class="title-detail">
                                        @if (session()->get('language') == 'bangla')
                                            {{ $product->name_bn }}
                                        @else
                                            {{ $product->name_en }}
                                        @endif
                                    </h2>

                                    @php
                                        $reviews = \App\Models\Review::where('product_id', $product->id)
                                            ->where('status', 1)
                                            ->get();
                                        $averageRating = $reviews->avg('rating');
                                        $ratingCount = $reviews->count(); // Add this line to get the rating count
                                    @endphp

                                    <div class="product__rating">
                                        @if ($reviews->isNotEmpty())
                                            @for ($i = 1; $i <= 5; $i++)
                                                @if ($i <= floor($averageRating))
                                                    <i class="fa fa-star" style="color: #FFB811;"></i>
                                                @elseif ($i == ceil($averageRating) && $averageRating - floor($averageRating) >= 0.5)
                                                    {{-- Display a half-star with gradient --}}
                                                    <i class="fa fa-star"
                                                        style="background: linear-gradient(to right, #FFB811 50%, gray 50%); -webkit-background-clip: text; color: transparent;"></i>
                                                @else
                                                    <i class="fa fa-star" style="color: #000;"></i>
                                                @endif
                                            @endfor
                                        @else
                                            @for ($i = 1; $i <= 5; $i++)
                                                <i class="fa fa-star" style="color: #000;"></i>
                                            @endfor
                                        @endif
                                        <span class="rating-count">({{ number_format($averageRating, 1) }})</span>
                                    </div>

                                    <div class="clearfix product-price-cover">
                                        <div class="product-price primary-color float-left" style="display: block">
                                            @if ($product->discount_price <= 0)
                                                @if (auth()->check() && auth()->user()->role == 7)
                                                    <span class="current-price">৳{{ number_format($product->reseller_price) }}</span>
                                                @else
                                                    <span class="current-price">৳{{ $product->regular_price }}</span>
                                                @endif
                                            @else
                                                <span class="current-price text-brand">৳{{ $amount }}</span>
                                                @if (!auth()->check() || (auth()->user()->role != 7))
                                                    @if ($product->discount_type == 1)
                                                        <span class="save-price font-md color3 ml-15">৳{{ $discount }} Off </span>
                                                    @elseif ($product->discount_type == 2)
                                                        <span class="save-price font-md color3 ml-15">{{ $product->discount_price }}% Off</span>
                                                    @endif
                                                @endif

                                                @if (!auth()->check() || (auth()->user()->role != 7))
                                                    <span class="old-price font-md ml-15">৳{{ $product->regular_price }}</span>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                    <div class="d-inline-block">
                                        @if ($product->stock_qty > 0)
                                            <div class="mb-5 hot_stock">In Stock </div>
                                        @else
                                            <div class="mb-5 hot_stock">Stock Out</div>
                                        @endif
                                    </div>

                                    <div class="row" id="attribute_alert"></div>
                                </div>
                                <div class="row" id="stock_alert"></div>

                                @if ($product->short_description_en != null)
                                    <div class="product__info__text">
                                        <p>{!! $product->short_description_en ?? '' !!}</p>
                                    </div>
                                @endif

                                <div class="detail-extralink align-items-baseline d-flex border-0">
                                    <div class="mr-10">
                                        <span class="sku_name">SKU :</span>
                                        <span class="sku__item">{{ $product->product_sku ?? 'No SKU' }}</span>
                                    </div>
                                </div>

                                <form id="choice_form">
                                    <div class="row mt-10" id="choice_attributes">
                                        @if ($product->is_varient)
                                            @php $i=0; @endphp
                                            @foreach (json_decode($product->attribute_values) as $attribute)
                                                @php
                                                    $attr = get_attribute_by_id($attribute->attribute_id);
                                                    $i++;
                                                @endphp
                                                <div class="attr-detail attr-size mb-10">
                                                    <strong class="mr-10">{{ $attr->name }}: </strong>
                                                    <input type="hidden" name="attribute_ids[]"
                                                        id="attribute_id_{{ $i }}"
                                                        value="{{ $attribute->attribute_id }}">
                                                    <input type="hidden" name="attribute_names[]"
                                                        id="attribute_name_{{ $i }}"
                                                        value="{{ $attr->name }}">
                                                    <input type="hidden" id="attribute_check_{{ $i }}"
                                                        value="0">
                                                    <input type="hidden" id="attribute_check_attr_{{ $i }}"
                                                        value="0">
                                                    <ul class="list-filter size-filter font-small">
                                                        @foreach ($attribute->values as $value)
                                                            <li>
                                                                <a href="#"
                                                                    onclick="selectAttribute('{{ $attribute->attribute_id }}{{ $attr->name ?? '' }}', '{{ $value }}', '{{ $product->id }}', '{{ $i }}')"
                                                                    style="border: 1px solid #7E7E7E;">{{ $value }}</a>
                                                            </li>
                                                        @endforeach
                                                        <input type="hidden" name="attribute_options[]"
                                                            id="{{ $attribute->attribute_id }}{{ $attr->name ?? '' }}"
                                                            class="attr_value_{{ $i }}">
                                                    </ul>
                                                </div>
                                            @endforeach
                                            <input type="hidden" id="total_attributes"
                                                value="{{ count(json_decode($product->attribute_values)) }}">
                                        @endif
                                    </div>
                                </form>
                                <div class="d-flex align-items-center mobile__block" style="gap: 30px">
                                    <div class="detail-extralink align-items-baseline d-flex border-0">
                                        <div class="quantity item__quantity">
                                            <a href="#" class="quantity__minus"><span><i
                                                        class="fa-solid fa-minus"></i></span></a>
                                            <input name="quantity" type="text" class="qty-val quantity__input"
                                                id="qty" value="1">
                                            <a href="#" class="quantity__plus"><span><i
                                                        class="fa-solid fa-plus"></i></span></a>
                                        </div>

                                        <div class="row" id="qty_alert"></div>
                                    </div>
                                    <div class="detail-extralink border-0">
                                        <div class="product-extra-link2">

                                            <input type="hidden" id="product_id" value="{{ $product->id }}">

                                            <input type="hidden" id="pname" value="{{ $product->name_en }}">

                                            <input type="hidden" id="product_price" value="{{ $amount }}">

                                            <input type="hidden" id="minimum_buy_qty"
                                                value="{{ $product->minimum_buy_qty }}">
                                            <input type="hidden" id="stock_qty" value="{{ $product->stock_qty }}">

                                            <input type="hidden" id="pvarient" value="">

                                            <input type="hidden" id="buyNowCheck" value="0">
                                            @php
                                                $maintenance = getMaintenance();
                                            @endphp
                                            @if ($maintenance == 1)
                                                <button type="button" class="button button-add-to-cart text-white"
                                                    data-bs-toggle="modal" data-bs-target="#maintenance"><i
                                                        class="fi-rs-shoppi ng-cart"></i>Add to cart</button>
                                                <button type="submit" class="button button-add-to-cart ml-5 buy_now-btn"
                                                    onclick="buyNow()"><i class="fi-rs-shoppi ng-cart"></i>BuyNow</button>
                                            @else
                                                <button type="submit" class="button button-add-to-cart text-white"
                                                    onclick="addToCart()"><i class="fi-rs-shoppi ng-cart"></i>Add
                                                    to cart</button>

                                                <button type="submit" class="button button-add-to-cart ml-5 buy_now-btn"
                                                    onclick="buyNow()"><i class="fi-rs-shoppi ng-cart"></i>Buy
                                                    Now</button>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="video_div pt-3">
                                    @if ($product->youtube_link)
                                        @php
                                            // Extract the video ID from the YouTube link
                                            if (
                                                preg_match(
                                                    '/(?:youtube\.com\/(?:shorts\/|embed\/|watch\?v=)|youtu\.be\/)([a-zA-Z0-9_-]+)/',
                                                    $product->youtube_link,
                                                    $matches,
                                                )
                                            ) {
                                                $videoId = $matches[1];
                                                $embedLink = 'https://www.youtube.com/embed/' . $videoId;
                                            } else {
                                                $embedLink = null; // Handle invalid YouTube links gracefully
                                            }
                                        @endphp

                                        @if ($embedLink)
                                            <iframe width="100%" height="350" src="{{ $embedLink }}"
                                                frameborder="0"
                                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                                allowfullscreen></iframe>
                                        @endif
                                    @endif
                                </div>
                                <!-- Detail Info -->
                            </div>
                        </div>



                        <div class="product-info">
                            <div class="tab-style3">
                                <ul class="nav nav-tabs text-uppercase">
                                    <li class="nav-item">
                                        <a class="nav-link active" id="Description-tab" data-bs-toggle="tab"
                                            href="#Description">Description</a>
                                    </li>
                                    <li class="nav-item">
                                        @php
                                            $data = \App\Models\Review::where('product_id', $product->id)
                                                ->where('status', 1)
                                                ->get();
                                        @endphp
                                        <a class="nav-link" id="Vendor-info-tab" data-bs-toggle="tab"
                                            href="#reviews">customer reviews ({{ $data->count() }})</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="Additional-info-tab" data-bs-toggle="tab"
                                            href="#Additional-info">Additional info</a>
                                    </li>
                                </ul>
                                <div class="tab-content shop_info_tab entry-main-content">
                                    <div class="tab-pane fade show active" id="Description">
                                        <div class="">
                                            <p>
                                                @if (session()->get('language') == 'bangla')
                                                    {!! $product->description_en ?? 'No Product Long Descrption' !!}
                                                @else
                                                    {!! $product->description_bn ?? 'No Product Logn Descrption' !!}
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="Additional-info">
                                        <table class="font-md">
                                            <tbody>
{{--                                                <tr class="stand-up">--}}
{{--                                                    <th>Product Code</th>--}}
{{--                                                    <td>--}}
{{--                                                        <p>{{ $product->product_code ?? 'No Product Code' }}</p>--}}
{{--                                                    </td>--}}
{{--                                                </tr>--}}
                                                <tr class="folded-wo-wheels">
                                                    <th>Product Size</th>
                                                    <td>
                                                        <p>{{ $product->product_size_en ?? 'No Product Size' }}</p>
                                                    </td>
                                                </tr>
                                                <tr class="folded-w-wheels">
                                                    <th>Product Color</th>
                                                    <td>
                                                        <p>{{ $product->product_color_en ?? 'No Product Color' }}
                                                        </p>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="tab-pane fade" id="reviews">
                                        <div class="product__review__system">
                                            <h6>Youre reviewing:</h6>
                                            <h5>
                                                @if (session()->get('language') == 'bangla')
                                                    {{ $product->name_bn }}
                                                @else
                                                    {{ $product->name_en }}
                                                @endif
                                            </h5>
                                            <form action="{{ route('review.store') }}" method="post">
                                                @csrf
                                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                                <input type="hidden" name="user_id"
                                                    value="{{ Auth::user()->id ?? 'null' }}">
                                                <div class="product__rating">
                                                    <label for="rating">Rating <span
                                                            class="text-danger">*</span></label>
                                                    <div class="rating-checked">
                                                        <input type="radio" name="rating" value="5"
                                                            style="--r: #ffb301" />
                                                        <input type="radio" name="rating" value="4"
                                                            style="--r: #ffb301" />
                                                        <input type="radio" name="rating" value="3"
                                                            style="--r: #ffb301" />
                                                        <input type="radio" name="rating" value="2"
                                                            style="--r: #ffb301" />
                                                        <input type="radio" name="rating" value="1"
                                                            style="--r: #ffb301" />
                                                    </div>
                                                    @error('rating')
                                                        <p class="text-danger">{{ $message }}</p>
                                                    @enderror
                                                </div>
                                                <div class="review__form">
                                                    <div class="row">
                                                        <div class="col-md-6 col-12">
                                                            <div class="form-group">
                                                                <label for="name">Name <span
                                                                        class="text-danger">*</span></label>
                                                                <input type="text" name="name" id="name"
                                                                    value="{{ old('name') }}">
                                                                @error('name')
                                                                    <p class="text-danger">{{ $message }}</p>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6 col-12">
                                                            <div class="form-group">
                                                                <label for="summary">Summary <span
                                                                        class="text-danger">*</span></label>
                                                                <input type="text" name="summary" id="summary"
                                                                    value="{{ old('summary') }}">
                                                                @error('summary')
                                                                    <p class="text-danger">{{ $message }}</p>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6 col-12">
                                                            <div class="form-group">
                                                                <label for="review">Review <span
                                                                        class="text-danger">*</span></label>
                                                                <input type="text" name="review" id="review"
                                                                    value="{{ old('review') }}">
                                                                @error('review')
                                                                    <p class="text-danger">{{ $message }}</p>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <button type="submit" class="btn btn-info">Submit
                                                        Review</button>
                                                </div>
                                            </form>
                                            <div class="review_list">
                                                @php
                                                    $data = \App\Models\Review::where('product_id', $product->id)
                                                        ->latest()
                                                        ->get();
                                                @endphp
                                                @foreach ($data as $value)
                                                    @if ($value->status == 1)
                                                        <div class="single-review-item">
                                                            <div class="rating">
                                                                @if ($value->rating == '1')
                                                                    <i class="fa fa-star"></i>
                                                                @elseif($value->rating == '2')
                                                                    <i class="fa fa-star"></i>
                                                                    <i class="fa fa-star"></i>
                                                                @elseif($value->rating == '3')
                                                                    <i class="fa fa-star"></i>
                                                                    <i class="fa fa-star"></i>
                                                                    <i class="fa fa-star"></i>
                                                                @elseif($value->rating == '4')
                                                                    <i class="fa fa-star"></i>
                                                                    <i class="fa fa-star"></i>
                                                                    <i class="fa fa-star"></i>
                                                                    <i class="fa fa-star"></i>
                                                                @elseif($value->rating == '5')
                                                                    <i class="fa fa-star"></i>
                                                                    <i class="fa fa-star"></i>
                                                                    <i class="fa fa-star"></i>
                                                                    <i class="fa fa-star"></i>
                                                                    <i class="fa fa-star"></i>
                                                                @endif
                                                            </div>
                                                            <h5 class="review-title">{{ $value->summary }}</h5>
                                                            <h6 class="review-user">{{ $value->name }}</h6>
                                                            <span class="review-description">{!! $value->review !!}</span>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-60">
                            <div class="border">
                                <div class="row g-0 align-items-center p-1" style="background: #f9f9f9">
                                    <div class="col-8">
                                        <div class="section__heading">
                                            <h3 class="py-3 fs-3">Related products</h3>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="related__roduct_arrow text-end"></div>
                                    </div>
                                </div>
                                <div class="related__product__active">
                                    @foreach ($relatedProduct as $product)
                                        <div class="single__product__item border">
                                            <div class="product__image position-relative">
                                                <a href="{{ route('product.details', $product->slug) }}"
                                                    class="product__item__photo">
                                                    <img src="{{ asset($product->product_thumbnail) }}" alt="">
                                                </a>
                                                <div class="product__discount__price d-flex">
                                                    @if ($product->created_at >= Carbon\Carbon::now()->subWeek())
                                                        <div class="product__labels">
                                                            <div class="product__label new__label">New</div>
                                                        </div>
                                                    @endif
                                                    @if (!auth()->check() || (auth()->user()->role != 7 && $product->discount_price > 0))
                                                        <div class="product__labels d-flex">
                                                            @if ($product->discount_type == 1)
                                                                <div class="product__label sale__label">
                                                                    ৳{{ $product->discount_price }} off</div>
                                                            @elseif($product->discount_type == 2)
                                                                <div class="product__label sale__label">
                                                                    {{ $product->discount_price }}% off</div>
                                                            @endif
                                                        </div>
                                                    @endif
                                                </div>

                                                <div class="product__item__action">
                                                </div>
                                            </div>
                                            <div class="product__details">
                                                <div class="product__details__top">
                                                    <strong class="product__name">
                                                        <a href="{{ route('product.details', $product->slug) }}"
                                                            class="product__link">
                                                            @if (session()->get('language') == 'bangla')
                                                                {{ Str::limit($product->name_bn, 30) }}
                                                            @else
                                                                {{ Str::limit($product->name_en, 30) }}
                                                            @endif
                                                        </a>
                                                    </strong>
                                                    @php
                                                        $reviews = \App\Models\Review::where('product_id', $product->id)
                                                            ->where('status', 1)
                                                            ->get();
                                                        $averageRating = $reviews->avg('rating');
                                                        $ratingCount = $reviews->count(); // Add this line to get the rating count
                                                    @endphp

                                                    <div class="product__rating">
                                                        @if ($reviews->isNotEmpty())
                                                            @for ($i = 1; $i <= 5; $i++)
                                                                @if ($i <= floor($averageRating))
                                                                    <i class="fa fa-star" style="color: #FFB811;"></i>
                                                                @elseif ($i == ceil($averageRating) && $averageRating - floor($averageRating) >= 0.5)
                                                                    {{-- Display a half-star with gradient --}}
                                                                    <i class="fa fa-star"
                                                                        style="background: linear-gradient(to right, #FFB811 50%, gray 50%); -webkit-background-clip: text; color: transparent;"></i>
                                                                @else
                                                                    <i class="fa fa-star" style="color: #000;"></i>
                                                                @endif
                                                            @endfor
                                                        @else
                                                            @for ($i = 1; $i <= 5; $i++)
                                                                <i class="fa fa-star" style="color: #000;"></i>
                                                            @endfor
                                                        @endif
                                                        <span
                                                            class="rating-count">({{ number_format($averageRating, 1) }})</span>
                                                    </div>

                                                    @php
                                                        if ($product->discount_type == 1) {
                                                            $price_after_discount =
                                                                $product->regular_price - $product->discount_price;
                                                        } elseif ($product->discount_type == 2) {
                                                            $price_after_discount =
                                                                $product->regular_price -
                                                                ($product->regular_price *
                                                                    $product->discount_price) /
                                                                    100;
                                                        }
                                                    @endphp

                                                    <div class="product__price d-flex justify-space-between">
                                                        @if ($product->discount_price > 0)
                                                            @if (auth()->check() && auth()->user()->role == 7)
                                                                <div class="special__price">৳{{ $product->reseller_price }}</div>
                                                            @else
                                                                <div class="special__price">৳{{ $price_after_discount }}
                                                                </div>
                                                                <div class="old__price">
                                                                    <del>৳{{ $product->regular_price }}</del>
                                                                </div>
                                                            @endif
                                                        @else
                                                            @if (auth()->check() && auth()->user()->role == 7)
                                                                <div class="special__price">
                                                                    ৳{{ $product->reseller_price }}</div>
                                                            @else
                                                                <div class="special__price">৳{{ $product->regular_price }}
                                                                </div>
                                                            @endif
                                                        @endif
                                                    </div>

                                                    @if (auth()->check() && auth()->user()->role == 7)
                                                        <div>
                                                            <span>Regular Price: <span class="text-info">৳
                                                                    {{ $product->regular_price }}</span></span>
                                                            <input type="hidden" id="regular_price" name="regular_price"
                                                                value="{{ $product->regular_price }}" min="1">
                                                        </div>
                                                    @endif
                                                </div>

                                                <div class="product__view__add">
                                                    <input type="hidden" id="pfrom" value="direct">
                                                    <input type="hidden" id="product_product_id"
                                                        value="{{ $product->id }}" min="1">
                                                    <input type="hidden" id="{{ $product->id }}-product_pname"
                                                        value="{{ $product->name_en }}">
                                                    <input type="hidden" id="buyNowCheck" value="0">
                                                    @if ($product->is_varient == 1)
                                                        @if ($maintenance == 1)
                                                            <a class="add" data-bs-toggle="modal"
                                                                data-bs-target="#maintenance"><i
                                                                    class="fi-rs-shopping-cart mr-5"></i>Add To Cart</a>

                                                            <a class="add" data-bs-toggle="modal"
                                                                data-bs-target="#maintenance"><i
                                                                    class="fi-rs-shopping-cart mr-5"></i>Buy Now</a>
                                                        @else
                                                            <a class="add" id="{{ $product->id }}"
                                                                onclick="productView(this.id)" data-bs-toggle="modal"
                                                                data-bs-target="#quickViewModal"><i
                                                                    class="fi-rs-shopping-cart mr-5"></i>Add To Cart</a>

                                                            <a class="add" id="{{ $product->id }}"
                                                                onclick="productView(this.id)" data-bs-toggle="modal"
                                                                data-bs-target="#quickViewModal"><i
                                                                    class="fi-rs-shopping-cart mr-5"></i>Buy Now</a>
                                                        @endif
                                                    @else
                                                        @if ($maintenance == 1)
                                                            <a class="add" data-bs-toggle="modal"
                                                                data-bs-target="#maintenance"><i
                                                                    class="fi-rs-shopping-cart mr-5"></i>Add To Cart</a>

                                                            <a class="add" data-bs-toggle="modal"
                                                                data-bs-target="#maintenance"><i
                                                                    class="fi-rs-shopping-cart mr-5"></i>Buy Now</a>
                                                        @else
                                                            <a class="add"
                                                                onclick="addToCartDirect({{ $product->id }})"><i
                                                                    class="fi-rs-shopping-cart mr-5"></i>Add To Cart</a>

                                                            <a class="add" onclick="buyNow({{ $product->id }})"><i
                                                                    class="fi-rs-shopping-cart mr-5"></i>Buy Now</a>
                                                        @endif
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection

@push('footer-script')
    {{--  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>  --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.1.0/jquery.magnific-popup.min.js"></script>
    <script>
        $('.slider-for').slick({
            slidesToShow: 1,
            slidesToScroll: 1,
            arrows: false,
            prevArrow: '<button type="button" class="slick-prev"><i class="fa-solid fa-angle-left"></i></button>',
            nextArrow: '<button type="button" class="slick-next"><i class="fa-solid fa-angle-right"></i></button>',
            fade: true,
            asNavFor: '.slider-nav'
        });

        $('.slider-nav').slick({
            slidesToShow: 5,
            slidesToScroll: 1,
            asNavFor: '.slider-for',
            dots: false,
            autoplay: true,
            margin: 10,
            arrows: false,
            centerMode: true,
            focusOnSelect: true,
            vertical: true,
            verticalSwiping: true,
            centerPadding: '0px', // Adjust centerPadding to zero
            infinite: true // Enable infinite looping
        });

        $(document).on('click', '.single___thumbnail', function () {
            const newImageUrl = $(this).find('img').attr('src');
            // Update the main image in the slider
            $('.slider-for .product__thumbnail__image img').attr('src', newImageUrl);
            $('.slider-for .product__thumbnail__image a').attr('href', newImageUrl);

            // Resume the sliders
            $('.slider-nav').slick('slickPlay');
            $('.slider-for').slick('slickPlay');
        });
    </script>
    <script>
        $(document).ready(function() {
            const minus = $('.quantity__minus');
            const plus = $('.quantity__plus');
            const input = $('.quantity__input');
            minus.click(function(e) {
                e.preventDefault();
                var value = input.val();
                if (value > 1) {
                    value--;
                }
                input.val(value);
            });

            plus.click(function(e) {
                e.preventDefault();
                var value = input.val();
                value++;
                input.val(value);
            })
        });
    </script>
    <script>
        $(document).ready(function() {
            $('.product__thumbnail__image').magnificPopup({
                delegate: 'a',
                type: 'image',
                gallery: {
                    enabled: true
                }
            });
        });
    </script> 
    <script>
        // function selectAttribute(){
        //     alert('lizaa')
        //    }
       // selectAttribute()  
        $(document).ready(function() {
           
        });
    </script>
@endpush
