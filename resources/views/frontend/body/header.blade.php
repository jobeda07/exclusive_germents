<header class="header-area header-style-1 header-height-2">
    <div class="header-top header-top-ptb-1 d-none d-lg-block">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-xxl-4 col-xl-12 col-lg-12">
                    <div class="header-info">
                        <ul>
                            <li class="contact_header"><i class="fa fa-envelope ms-1"></i>
                                <span><strong> <a href="tel:{{ get_setting('email')->value ?? 'null' }}">{{ get_setting('email')->value ?? 'null' }}</a></strong></span>
                            </li>

                            <li class="contact_header"><i class="fa fa-phone ms-1"></i>
                                <span>{{ get_setting('phone')->value ?? 'null' }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="col-xxl-4 col-xl-6 col-lg-6">
                    <div class="text-center">
                        <div id="news-flash" class="d-inline-block">
                            <ul>
                                <li style="height: 20px !important">100% Secure delivery without contacting the courier</li>
                                <li style="height: 20px !important">Supper Value Deals - Save more with coupons</li>
                                <li style="height: 20px !important">Trendy 25silver jewelry, save up 35% off today</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-xxl-4 col-xl-6 col-lg-6">
                    <div class="header-info header-info-right">
                        <ul>
                            <!--<li class="contact__page"><a href="{{ route('contact.page') }}">Contact Us</a></li>-->
                            <li>
                                <div class="mobile-social-icon justify-content-center">
                                    <a target="_blank" href="{{ get_setting('facebook_url')->value ?? 'null' }}"
                                        title="Facebook"><img
                                            src="{{ asset('frontend/assets/imgs/theme/icons/icon-facebook-white.svg') }}"
                                            alt="" /></a>

                                    <a target="_blank" href="{{ get_setting('instagram_url')->value ?? 'null' }}"
                                        title="Instagram"><img
                                            src="{{ asset('frontend/assets/imgs/theme/icons/icon-instagram-white.svg') }}"
                                            alt="" /></a>

                                    <a target="_blank" href="{{ get_setting('tiktok_url')->value ?? 'null' }}" title="Tiktok"><img
                                            src="{{ asset('frontend/assets/imgs/theme/icons/tiktok.svg') }}"
                                            alt="" /></a>

                                    <a target="_blank" href="{{ get_setting('youtube_url')->value ?? 'null' }}"
                                        title="Youtube"><img
                                            src="{{ asset('frontend/assets/imgs/theme/icons/icon-youtube-white.svg') }}"
                                            alt="" /></a>
                                </div>
                            </li>
                            <li><a href="#" data-bs-toggle="modal" data-bs-target="#exampleModal" class="vendorBtn">Apply as Reseller</a></li>
                            <li><a href="{{ route('order.tracking') }}">Order Tracking</a></li>
                            {{-- <li>
                                @if (session()->get('language') == 'bangla')
                                    <a class="language-dropdown-active"
                                        href="{{ route('english.language') }}">English</a>
                                @else
                                    <a class="language-dropdown-active" href="{{ route('bangla.language') }}">বাংলা</a>
                                @endif
                            </li> --}}
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="d-lg-block sticky-bar">
        <div class="header-middle header-middle-ptb-1 ">
            <div class="container">
                @php
                    $maintenance = getMaintenance();
                @endphp

                @if ($maintenance == 1)
                    <div class="row mb-2">
                        <div class="col-7 offset-3 ">
                            <div class="maintain-sms">
                                <h4 style="color:white" class="text-center">This Website is now under Maintanence</h4>
                            </div>
                        </div>
                        <div class="col-2 text-end">
                        </div>
                    </div>
                @endif

                @php
                    $couponCode = getCoupon();
                @endphp
                @if ($couponCode)
                    <div class="maintain-sms">
                        <h6 style="color:white">Coupon Code: {{ $couponCode }}</h6>
                    </div>
                @endif

                <div class="header-wrap">
                    <div class="logo logo-width-1">
                        <a href="{{ route('home') }}">
                            @php
                                $logo = get_setting('site_logo');
                            @endphp
                            @if ($logo != null)
                                <img src="{{ asset(get_setting('site_logo')->value ?? 'null') }}"
                                    alt="{{ env('APP_NAME') }}">
                            @else
                                <img src="{{ asset('upload/no_image.jpg') }}" alt="{{ env('APP_NAME') }}"
                                    style="height: 60px !important; width: 80px !important; min-width: 80px !important;">
                            @endif
                        </a>
                    </div>


                   <div class="shopCategory">
                    <div class="shopbyLogo d-none"><i class="fa fa-bars"></i> Shop by Categories</div>

                    <div class="najmul" style="position: absolute;background:#fff;">
                        <ul class="sticky__category">
                            @foreach (get_categories() as $category)
                                <li>
                                    <a href="{{ route('product.category', $category->slug) }}">
                                        @if (session()->get('language') == 'bangla')
                                            {{ $category->name_bn }}
                                        @else
                                            {{ $category->name_en }}
                                        @endif
                                        @if ($category->sub_categories && count($category->sub_categories) > 0)
                                            <i class="fi-rs-angle-right"></i>
                                        @endif
                                    </a>
                                    @if ($category->sub_categories && count($category->sub_categories) > 0)
                                        <ul class="sticky_subcategory">
                                            @foreach ($category->sub_categories as $sub_category)
                                                <li>
                                                    <a href="{{ route('product.category', $sub_category->slug) }}">
                                                        @if (session()->get('language') == 'bangla')
                                                            {{ $sub_category->name_bn }}
                                                        @else
                                                            {{ $sub_category->name_en }}
                                                        @endif
                                                        @if ($sub_category->sub_sub_categories && count($sub_category->sub_sub_categories) > 0)
                                                            <i class="fi-rs-angle-right"></i>
                                                        @endif

                                                    </a>
                                                    @if ($sub_category->sub_sub_categories && count($sub_category->sub_sub_categories) > 0)
                                                        <ul class="sticky_child">
                                                            @foreach ($sub_category->sub_sub_categories as $sub_sub_category)
                                                                <li>
                                                                    <a href="{{ route('product.category', $sub_sub_category->slug) }}">
                                                                        @if (session()->get('language') == 'bangla')
                                                                            {{ $sub_sub_category->name_bn }}
                                                                        @else
                                                                            {{ $sub_sub_category->name_en }}
                                                                        @endif
                                                                    </a>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    @endif
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>
                   </div>

                    <div class="header-right">
                        <div class="search-style-2">
                            <div class="search-area">
                                <form action="{{ route('product.search') }}" method="post" class="mx-auto">
                                    @csrf
                                    <select class="select-active" name="searchCategory" id="searchCategory">
                                        <option value="0">All Categories</option>
                                        @foreach (get_all_categories() as $cat)
                                            <option value="{{ $cat->id }}">
                                                @if (session()->get('language') == 'bangla')
                                                    {{ $cat->name_bn }}
                                                @else
                                                    {{ $cat->name_en }}
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    <input class="search-field search" onfocus="search_result_show()"
                                        onblur="search_result_hide()" name="search" placeholder="Search here..." />
                                    <div>
                                        <button type="submit" class="btn btn-primary text-white btn-sm rounded-0 search__button"><i
                                                class="fa-solid fa-magnifying-glass"></i></button>
                                    </div>
                                </form>
                                <div class="shadow-lg searchProducts"></div>
                            </div>
                        </div>
                        <div class="header-action-right">
                            <div class="header-action-2">
                                <div class="header-action-2 cart_hidden_mobile me-2">
                                    <div class="header-action-icon-2">
                                        <a class="mini-cart-icon item_mini_cart" href="#">
                                            <img alt="Nest"
                                                src="{{ asset('frontend/assets/imgs/theme/icons/icon-cart.svg') }}" />
                                            <span class="pro-count blue cartQty"></span>
                                        </a>
                                        <a href="{{ route('cart.show') }}"><span class="lable">Cart</span></a>
                                        <div class="cart-dropdown-wrap cart-dropdown-hm2">
                                            <div id="miniCart">

                                            </div>
                                            <div class="shopping-cart-footer" id="miniCart_btn">
                                                <div class="shopping-cart-total">
                                                    <h4>Total <span id="cartSubTotal"></span></h4>
                                                </div>
                                                <div class="shopping-cart-button">
                                                    <a href="{{ route('cart.show') }}" class="outline">View cart</a>
                                                    <a href="{{ route('checkout') }}">Checkout</a>
                                                </div>
                                            </div>
                                            <div class="shopping-cart-footer" id="miniCart_empty_btn">

                                                <div class="shopping-cart-button d-flex flex-row-reverse">
                                                    <a href="{{ route('home') }}">Continue Shopping</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="header-action-icon-2">
                                    @auth
                                        <a href="#">
                                            <img class="svgInject" alt="Nest"
                                                src="{{ asset('frontend/assets/imgs/theme/icons/icon-user.svg') }}" />
                                        </a>
                                        <a href="{{ route('dashboard') }}"><span class="lable ml-0">Account</span></a>
                                        <div class="cart-dropdown-wrap cart-dropdown-hm2 account-dropdown">
                                            <ul>
                                                <li>
                                                    <a href="{{ route('dashboard') }}"><i
                                                            class="fi fi-rs-user mr-10"></i>My Account</a>
                                                </li>
                                                <li>
                                                    <a class=" mr-10" href="{{ route('logout') }}"
                                                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                                        <i class="fi-rs-sign-out mr-10"></i>
                                                        {{ __('Logout') }}
                                                    </a>
                                                    <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                                        class="d-none">
                                                        @csrf
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    @endauth
                                    @guest
                                        <a href="{{ route('login') }}"><span class="lable ml-0"><i
                                                    class="fa-solid fa-arrow-right-to-bracket mr-10"></i>Login</span></a>
                                    @endguest
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="header-bottom header-bottom-bg-color d-none">
            <div class="container">
                <div class="header-wrap header-space-between position-relative">
                    <div class="logo logo-width-1 ">

                        <a href="{{ route('home') }}">
                            @php
                                $logo = get_setting('site_logo');
                            @endphp
                            @if ($logo != null)
                                <img src="{{ asset(get_setting('site_logo')->value ?? 'null') }}"
                                    alt="{{ env('APP_NAME') }}">
                            @else
                                <img src="{{ asset('upload/no_image.jpg') }}" alt="{{ env('APP_NAME') }}"
                                    style="height: 60px !important; width: 80px !important; min-width: 80px !important;">
                            @endif
                        </a>
                    </div>
                    <div class="header-nav d-none d-lg-flex w-100">
                        <div class="row  w-100 g-0">
                            <div class="col-xl-10 col-lg-10">
                                <div class="main__menu">
                                    <ul class="d-flex position-relative">
                                        <li>
                                            <a href="{{ route('home') }}">
                                                @if (session()->get('language') == 'bangla')
                                                    হোম
                                                @else
                                                    Home
                                                @endif
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ route('product.show') }}">
                                                @if (session()->get('language') == 'bangla')
                                                    দোকান
                                                @else
                                                    Shop
                                                @endif
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ route('hot_deals.all') }}">
                                                @if (session()->get('language') == 'bangla')
                                                    হট ডিল
                                                @else
                                                    hot deals
                                                @endif
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="header-action-right d-none">
                        <div class="header-action-2">
                            <!--Mobile Header Search start-->
                            <a class="p-2 d-block text-reset active show">
                                <i class="fas fa-search la-flip-horizontal la-2x"></i>
                            </a>
                            <section class="advance-search" style="display: none;">
                                <div class="search-box">
                                    <form action="{{ route('product.search') }}" method="post">
                                        @csrf
                                        <div class="input-group py-2">
                                            <span class="back_left hide"><i
                                                    class="fas fa-long-arrow-left me-2"></i></span>
                                            <input class="header-search form-control search-field search"
                                                aria-label="Input group example" aria-describedby="btnGroupAddon"
                                                onfocus="search_result_show()" onblur="search_result_hide()"
                                                name="search" placeholder="Search here..." />
                                            <button type="submit" class="input-group-text btn btn-sm"
                                                id="btnGroupAddon"><i class="fas fa-search"></i></button>
                                        </div>
                                    </form>
                                    <div class="shadow-lg searchProducts"></div>
                                </div>
                            </section>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="header-bottom-1 header-bottom-bg-color sticky-bar d-none d-xl-block">
        <div class="container">
           <div class="row">
             <div class="col-md-2">
                <h6 class="m-0 text-white text-center">All Categories</h6>
            </div>

            <div class="col-md-6">
                <ul class="mobile-hor-swipe header-wrap header-space-between position-relative">
                <li class="mb-10">
                    <a class="p-10" href="{{ route('home') }}">
                        @if (session()->get('language') == 'bangla')
                            হোম
                        @else
                            Home
                        @endif
                    </a>
                </li>
                <li class="mb-10">
                    <a class="p-10" href="{{ route('product.show') }}">
                        @if (session()->get('language') == 'bangla')
                            দোকান
                        @else
                            Shop
                        @endif
                    </a>
                </li>

                <li class="mb-10">
                    <a class="p-10" href="{{ route('campaign.all') }}">
                        @if (session()->get('language') == 'bangla')
                            প্রচারণ
                        @else
                            Campaign
                        @endif
                    </a>
                </li>
                <li class="mb-10">
                    <a  class="p-10"  href="{{ route('hot_deals.all') }}">
                        @if (session()->get('language') == 'bangla')
                            হট ডিল
                        @else
                            hot deals
                        @endif
                    </a>
                </li>
            </ul>
            </div>
            </div>
        </div>
    </div>
</header>

<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">
                    <div class="heading_s1">
                        <h3 class="mb-5">রিসেলার আবেদন তথ্য:</h3>
                    </div>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="page-content pt-10 pb-10">
                    <div class="container">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="login_wrap widget-taber-content background-white">
                                    <div class="padding_eight_all bg-white">
                                        <form method="POST" action="{{ route('resellerApply') }}"
                                            class="needs-validation" novalidate>
                                            @csrf
                                            <div class="form-group">
                                                <label for="name" class="fw-900">রিসেলারের নাম: <span class="text-danger"> *</span></label>
                                                <input type="text" name="name" placeholder="রিসেলারের নাম:"
                                                    id="name" value="{{ old('name') }}" required />
                                                @error('name')
                                                    <div class="text-danger" style="font-weight: bold;">
                                                        {{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="form-group">
                                                <label for="phone" class="fw-900">রিসেলারের বিকাশ নম্বর: <span class="text-denger"> *</span></label>
                                                <input type="number" name="phone" placeholder="রিসেলারের বিকাশ নম্বর: "
                                                    id="phone" value="{{ old('phone') }}" required />
                                                @error('phone')
                                                    <div class="text-danger" style="font-weight: bold;">
                                                        {{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="form-group">
                                                <label for="fb_web_url" class="fw-900">ফেসবুক পেজ লিংক / ওয়েবসাইট লিংক:<span class="text-denger"> *</span></label>
                                                <input type="text" name="fb_web_url" id="fb_web_url"
                                                    placeholder="ফেসবুক পেজ লিংক / ওয়েবসাইট লিংক: " value="{{ old('fb_web_url') }}"
                                                    required />
                                                @error('fb_web_url')
                                                    <div class="text-danger" style="font-weight: bold;">
                                                        {{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="form-group">
                                                <label for="email" class="fw-900">ইমেইল ঠিকানা: <span class="class-denger"> *</span></label>
                                                <input type="email" name="email" id="email"
                                                    placeholder="ইমেইল ঠিকানা:" value="{{ old('email') }}" required />
                                                @error('email')
                                                    <div class="text-danger" style="font-weight: bold;">
                                                        {{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="form-group">
                                                <label for="password" class="fw-900">পাসওয়ার্ড: <span class="text-danger"> *</span></label>
                                                <input type="password" name="password" placeholder="পাসওয়ার্ড:"  required />
                                                <span>পাসওয়ার্ড কমপক্ষে ৮ অক্ষরের হতে হবে</span>
                                                @error('password')
                                                    <div class="text-danger" style="font-weight: bold;">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="form-group mb-30 seller__btn">
                                                <button type="submit" class="btn-primary " name="login">Submit &amp; Register</button>
                                            </div>
                                            <p class="font-xs fw-900"><strong>শর্তাবলী:</strong>
                                            আমি নিশ্চিত করি যে, আমি প্রদত্ত তথ্য সঠিক এবং পূর্ণাঙ্গ।
আবেদনটি সফলভাবে জমা দেওয়ার পর, আমাদের পক্ষ থেকে কিছুক্ষণের মধ্যে আপনাকে ফোন কল দিয়ে যোগাযোগ করা হবে।</p>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Mobile Side menu Start -->
<div class="mobile-header-active mobile-header-wrapper-style">
    <div class="mobile-header-wrapper-inner">
        <div class="mobile-header-top">
            <div class="mobile-header-logo">
                <a href="{{ route('home') }}">
                    @php
                        $logo = get_setting('site_logo');
                    @endphp
                    @if ($logo != null)
                        <img src="{{ asset(get_setting('site_logo')->value ?? 'null') }}"
                            alt="{{ env('APP_NAME') }}">
                    @else
                        <img src="{{ asset('upload/no_image.jpg') }}" alt="{{ env('APP_NAME') }}"
                            style="height: 60px !important; width: 80px !important; min-width: 80px !important;">
                    @endif
                </a>
            </div>
            <div class="mobile-menu-close close-style-wrap close-style-position-inherit">
                <button class="close-style search-close">
                    <i class="icon-top"></i>
                    <i class="icon-bottom"></i>
                </button>
            </div>
        </div>
        <div class="mobile-header-content-area">
            <div class="mobile-search search-style-3 mobile-header-border">
                <form action="{{ route('product.search') }}" method="post">
                    @csrf
                    <input class="search-field search" onfocus="search_result_show()" onblur="search_result_hide()"
                        name="search" placeholder="Search for items…" />
                    <button type="submit"><i class="fi-rs-search"></i></button>
                </form>
            </div>
            <div class="mobile-menu-wrap mobile-header-border">
                <!-- mobile menu start -->
                <nav>
                    <ul class="mobile-menu font-heading">
                        <li class="menu-item-has-children">
                            <a href="{{ route('home') }}">Home</a>
                        </li>
                        <li class="menu-item-has-children">
                            <a href="{{ route('product.show') }}">
                                @if (session()->get('language') == 'bangla')
                                    দোকান
                                @else
                                    Shop
                                @endif
                            </a>
                        </li>
                        @foreach (get_categories() as $category)
                            <li class="menu-item-has-children">
                                <a href="{{ route('product.category', $category->slug) }}">
                                    @if (session()->get('language') == 'bangla')
                                        {{ $category->name_bn }}
                                    @else
                                        {{ $category->name_en }}
                                    @endif
                                </a>
                                @if ($category->sub_categories && count($category->sub_categories) > 0)
                                    <ul class="dropdown">
                                        @foreach ($category->sub_categories as $subcategory)
                                            <li class="menu-item-has-children">
                                                <a href="{{ route('product.category', $subcategory->slug) }}">
                                                    @if (session()->get('language') == 'bangla')
                                                        {{ $subcategory->name_bn }}
                                                    @else
                                                        {{ $subcategory->name_en }}
                                                    @endif
                                                </a>
                                                @if ($subcategory->sub_sub_categories && count($subcategory->sub_sub_categories) > 0)
                                                    <ul class="dropdown">
                                                        @foreach ($subcategory->sub_sub_categories as $subsubcategory)
                                                            <li>
                                                                <a
                                                                    href="{{ route('product.category', $subsubcategory->slug) }}">
                                                                    @if (session()->get('language') == 'bangla')
                                                                        {{ $subsubcategory->name_bn }}
                                                                    @else
                                                                        {{ $subsubcategory->name_en }}
                                                                    @endif
                                                                </a>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                @endif
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </li>
                        @endforeach

                        <li class="menu-item-has-children">
                            <a href="#">Pages</a>
                            <ul class="dropdown">
                                @foreach (get_pages_both_footer()->take(4) as $page)
                                    <li>
                                        <a href="{{ route('page.about', $page->slug) }}">{{ $page->title }}</a>
                                    </li>
                                @endforeach
                            </ul>
                        </li>
                        <li class="menu-item-has-children">
                            <a href="#">Language</a>
                            <ul class="dropdown">
                                @if (session()->get('language') == 'bangla')
                                    <li>
                                        <a href="{{ route('english.language') }}">English</a>
                                    </li>
                                @else
                                    <li>
                                        <a href="{{ route('bangla.language') }}">বাংলা</a>
                                    </li>
                                @endif
                            </ul>
                        </li>
                    </ul>
                </nav>
                <!-- mobile menu end -->
            </div>
            <div class="mobile-header-info-wrap">
                <!-- <div class="single-mobile-header-info">
                    <a href="#"><i class="fi-rs-marker"></i> Our location </a>
                </div> -->
                <div class="single-mobile-header-info">
                    <a href="{{ route('login') }}"><i class="fi-rs-user"></i>Log In </a>
                </div>
                <div class="single-mobile-header-info">
                    <a href="{{ route('register') }}"><i class="fi-rs-user"></i>Sign Up </a>
                </div>
                <div class="single-mobile-header-info">
                    {{--  <?php
                    $phone = get_setting('phone')->value ?? 'null';

                    if ($phone !== 'null') {
                        $phoneArray = explode(',', $phone);
                        foreach ($phoneArray as $phoneNumber) {
                            echo "<a href='tel:$phoneNumber' title='Call $phoneNumber'>$phoneNumber</a>";
                        }
                    } else {
                        echo "<a href='tel:null'>null</a>";
                    }
                    ?>  --}}
                    <div class="contact_header">
                        <span>01676476594
                            {{--  <i class="fa-solid fa-angle-down"></i>  --}}
                            {{-- &nbsp;:&nbsp; <strong> <a href="tel:{{ get_setting('phone')->value ?? 'null' }}">
                            {{ get_setting('phone')->value ?? 'null' }}</a></strong> --}}
                        </span>

                        <div class="email__contact mt-0">
                            <?php
                            $phone = get_setting('phone')->value ?? 'null';

                            if ($phone !== 'null') {
                                $phoneArray = explode(',', $phone);
                                foreach ($phoneArray as $phoneNumber) {
                                    echo "<a href='tel:$phoneNumber' title='Call $phoneNumber'>$phoneNumber</a>";
                                }
                            } else {
                                echo "<a href='tel:null'>null</a>";
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mobile-social-icon mb-50">
                <h6 class="mb-15">Follow Us</h6>
                <a href="{{ get_setting('facebook_url')->value ?? 'null' }}"><img src="{{ asset('frontend/assets/imgs/theme/icons/icon-facebook-white.svg') }}" alt="" /></a>
                <a href="{{ get_setting('tiktok_url')->value ?? 'null' }}"><img src="{{ asset('frontend/assets/imgs/theme/icons/tiktok.svg') }}" alt="" /></a>
                <a href="{{ get_setting('instagram_url')->value ?? 'null' }}"><img src="{{ asset('frontend/assets/imgs/theme/icons/icon-instagram-white.svg') }}" alt="" /></a>
                <a href="{{ get_setting('youtube_url')->value ?? 'null' }}"><img src="{{ asset('frontend/assets/imgs/theme/icons/icon-youtube-white.svg') }}" alt="" /></a>
            </div>
            <div class="site-copyright">
                Developed by:
                <a target="_blank"
                    href="{{ get_setting('developer_link')->value ?? 'null' }}">{{ get_setting('developed_by')->value ?? 'null' }}</a>
            </div>
        </div>
    </div>
</div>
<!-- Mobile Side menu End -->
<!--End header-->