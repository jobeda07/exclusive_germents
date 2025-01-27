@extends('layouts.frontend')
@section('content-frontend')
 <main class="main">
        <div class="page-header breadcrumb-wrap">
            <div class="container">
                <div class="breadcrumb">
                    <a href="{{route('home')}}" rel="nofollow"><i class="fi-rs-home mr-5"></i>Home</a>
                    <span></span> Reseller
                    <span></span> Apply Reseller
                </div>
            </div>
        </div>
        <div class="container mb-80 mt-50">
            <div class="row">
                <div class="col-lg-6 offset-lg-3 col-12 col-sm-12">
                   <div class="login_wrap widget-taber-content background-white " >
                        <h3 class="text-center mb-3">রিসেলার আবেদন তথ্য:</h3>
                        <div class="padding_eight_all bg-white card p-4" style="box-shadow: rgba(99, 99, 99, 0.2) 0px 2px 8px 0px;">

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

                                <p class="font-xs fw-900"><strong>শর্তাবলী:</strong>আমি নিশ্চিত করি যে, আমি প্রদত্ত তথ্য সঠিক এবং পূর্ণাঙ্গ।
আবেদনটি সফলভাবে জমা দেওয়ার পর, আমাদের পক্ষ থেকে কিছুক্ষণের মধ্যে আপনাকে ফোন কল দিয়ে যোগাযোগ করা হবে।</p>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection
