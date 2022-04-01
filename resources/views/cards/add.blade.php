@extends('layouts.app')
@section('content')
<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <div class="breadcrumb-title pe-3">Card</div>
    <div class="ps-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 p-0">
                <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Card management</li>
            </ol>
        </nav>
    </div>
    <div class="ms-auto">
        <div class="btn-group">
            <a href="#" class="btn btn-primary">Settings</a>
        </div>
    </div>
</div>
<!--end breadcrumb-->

<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card">
            <div class="card-header py-3 bg-transparent">
                <h5 class="mb-0">Thêm mới thẻ</h5>
            </div>
            <div class="card-body">
                <div class="border p-3 rounded">
                    <form class="row g-3" action="" method="POST">
                    @csrf
                        <div class="col-12">
                            <label class="form-label">Tên </label>
                            <input type="text" class="form-control" name="name" value="{{old('name')}}" placeholder="Thêm mới tên...">
                            @error('name')
                                <span class="text-danger">{{$message}}</span>
                              @enderror
                        </div>
                        
                        <div class="col-12 col-md-6">
                            <label class="form-label">Trạng thái</label>
                            <select class="form-select" name="status" value="{{old('status')}}">
                                
                                <option  value="1">Kích hoat</option>
                                <option value="0">Chưa kích hoạt</option>

                            </select>
                            @error('status')
                                <span class="text-danger">{{$message}}</span>
                              @enderror
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label">Căn hộ</label>
                            <select class="form-select" name="apartment_id">
                                @foreach ($apartments as $item)
                                <option value="{{$item->id}}">{{$item->apartment_id}}</option>
                                @endforeach
                            </select>
                            @error('apartment_id')
                                <span class="text-danger">{{$message}}</span>
                              @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Ngày hết hạn</label>
                            <input type="datetime-local" name="expire_time" class="form-control datepicker" value="{{old('expire_time')}}"/>
                            @error('expire_time')
                                <span class="text-danger">{{$message}}</span>
                              @enderror
                        </div>
                        <div class="col-12">
                            <button class="btn btn-primary px-4" type="submit">Add New Card </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!--end row-->
@endsection