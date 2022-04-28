@extends('layouts.home')
@section('content')
<section class="heading-page header-text" id="top">
    <div class="container">
        <main class="authentication-content ">
            <div class="container-fluid" style="width: 60%">
                <div class="authentication-card">
                    <div class="card shadow rounded-0 overflow-hidden">
                        <div class=" g-0">

                            <div class="" style="height: 50vh">
                                <div class="card-body p-4 p-sm-5">
                                    <h5 class="card-title">Sign In</h5>
                                    <form class="form-body" method="POST">
                                        @csrf
                                        <div class="row g-3">
                                            <div class="col-12">
                                                <label for="inputEmailAddress" class="form-label">Email Address</label>
                                                <div class="ms-auto position-relative">
                                                    <div
                                                        class="position-absolute top-50 translate-middle-y search-icon px-3">
                                                        <i class="bi bi-envelope-fill"></i></div>
                                                    <input type="email" name="email" id="email" class="form-control radius-30 ps-5" required autofocus
                                                        id="inputEmailAddress" placeholder="Email Address">
                                                        @if ($errors->has('email'))
                                                        <p class="alert-danger p-1 mt-1">{{ __('string.email') }}</p>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <label for="inputChoosePassword" class="form-label">Enter
                                                    Password</label>
                                                <div class="ms-auto position-relative">
                                                    <div
                                                        class="position-absolute top-50 translate-middle-y search-icon px-3">
                                                        <i class="bi bi-lock-fill"></i></div>
                                                    <input type="password" class="form-control radius-30 ps-5" name="password" required data-eye
                                                        id="inputChoosePassword" placeholder="Enter Password">
                                                        @if ($errors->has('password'))
                                                        <p class="alert-danger p-1 mt-1">{{ __('string.password') }}</p>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox"
                                                        id="flexSwitchCheckChecked" checked="">
                                                    <label class="form-check-label"
                                                        for="flexSwitchCheckChecked">Nhớ mật khẩu</label>
                                                </div>
                                            </div>
                                            <div class="col-6 text-end">   
                                                 @if(session('msg'))
                                                <p class="alert-danger p-1 mt-1">{{session('msg')}}</p>
                                            @endif
                                            </div>
                                            <div class="col-12">
                                                <div class="d-grid">
                                                    <button type="submit" class="btn btn-primary radius-30">Đăng nhập</button>
                                                </div>
                                            </div>
                                           
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <div class="footer">
        <p>Copyright © 2022 Apartment.

        </p>
    </div>
</section>






@endsection