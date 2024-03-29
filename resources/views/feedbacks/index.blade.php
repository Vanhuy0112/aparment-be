@extends('layouts.app')
@section('content')
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Card</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Feedbacks management</li>
                </ol>
            </nav>
        </div>
        
    </div>
    <!--end breadcrumb-->

    <div class="card">
        <div class="card-body">
            <div class="d-flex align-items-center">
                <h5 class="mb-0">User Card</h5>
                <form method="GET"  action="{{\Illuminate\Support\Facades\URL::current()}}" class="ms-auto position-relative d-flex">
                    <input class="form-control ps-5" type="text" name="keyword"  placeholder="search"> &nbsp;
                    <input type="submit" class="btn btn-primary"> &nbsp;
                </form>
            </div>
            <div class="table-responsive mt-3">
                <table class="table align-middle">
                    <thead class="table-secondary">
                    <tr>
                        <th>#</th>
                        <th>Tiêu đề</th>
                        
                        <th></th>
                        
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($feedbacks as $key =>$feedback)
                        <tr>
                            <td>{{$key++}}</td>
                            
                            <td>{{$feedback->subject}}</td>
                            
                            <td>
                                <div class="table-actions d-flex align-items-center gap-3 fs-6">
                                     <a href="{{route('feedback.view',['id' => $feedback->id])}}" class="text-primary" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Views"><i class="bi bi-eye-fill"></i></a> 
                                    
                                    
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                <div class="paginate">
                  
                </div>
            </div>
        </div>
    </div>
@endsection

