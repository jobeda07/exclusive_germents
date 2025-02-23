@extends('admin.admin_master')
@section('admin')
<section class="content-main">
    <div class="content-header">
        <h2 class="content-title">Reseller List <span class="badge rounded-pill alert-success"> {{ count($resellers) }} </span></h2>
        <div>
            <a href="{{ route('reseller.create') }}" class="btn btn-primary"><i class="material-icons md-plus"></i> Create Reseller</a>
        </div>
    </div>
    </div>
    <div class="card mb-4">
        <div class="card-body">
            <div class="table-responsive-sm">
               <table id="example" class="table table-bordered table-striped" width="100%">
                    <thead>
                        <tr>
                            <th scope="col">Sl</th>
                            <th scope="col">Name</th>
                            <th scope="col">Phone</th>
                            <th scope="col">Email</th>
                            <th scope="col">Pending Wallet</th>
                            <th scope="col">Wallet Balance</th>
                            <th scope="col">Withdraw Request</th>
                            <th scope="col">Withdraw</th>
                            <th scope="col">Status</th>
                            <th scope="col" class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($resellers as $key => $reseller)
                        <tr>
                            @php
                               $due_withdraw = App\Models\Withdraw::where('user_id',$reseller->id)->where('status',0)->sum('amount');
                               $withdraw = App\Models\Withdraw::where('user_id',$reseller->id)->where('status',1)->sum('amount');
                          @endphp
                          {{-- @dd($due_withdraw) --}}
                            <td> {{ $key+1}} </td>
                            <td> {{ $reseller->name ?? '' }} </td>
                            <td> {{ $reseller->phone ?? '' }} </td>
                            <td> {{ $reseller->email ?? '' }} </td>
                            <td> {{ $reseller->pending_wallet_balance ?? '' }} </td>
                            <td> {{ $reseller->wallet_balance ?? '' }} </td>
                            <td> {{ $due_withdraw  }} </td>
                            <td> {{ $withdraw }} </td>
                            <td>
                                @if($reseller->status == 1)
                                  <a href="{{ route('reseller.changeStatus',['id'=>$reseller->id]) }}">
                                    <span class="badge rounded-pill alert-success">Active</span>
                                  </a>
                                @else
                                  <a href="{{ route('reseller.changeStatus',['id'=>$reseller->id]) }}" > <span class="badge rounded-pill alert-danger">Inactive</span></a>
                                @endif
                            </td>
                            <td class="text-end">
                                <a href="{{ route('reseller.show',['id'=>$reseller->id]) }}" class="btn btn-md rounded font-sm">Detail</a>
                                <div class="dropdown">
                                    <a href="#" data-bs-toggle="dropdown" class="btn btn-light rounded btn-sm font-sm"> <i class="material-icons md-more_horiz"></i> </a>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="{{ route('reseller.edit',['id'=>$reseller->id]) }}">Edit info</a>
                                        <a class="dropdown-item text-danger" href="{{ route('reseller.delete',['id'=>$reseller->id]) }}" id="delete">Delete</a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <!-- table-responsive //end -->
        </div>
        <!-- card-body end// -->
    </div>
</section>
@endsection
