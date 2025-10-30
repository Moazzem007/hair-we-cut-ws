@extends('layouts.adminapp')




@section('Main-content')
    <div class="container">
        <div class="row m-b-lg m-t-lg">

            <div class="col-md-4">
                <form action="{{ route('updateprofile', $user->id) }}" method="POST" enctype="multipart/form-data">
                    {{ method_field('PATCH') }}
                    {{ csrf_field() }}

                    <div class="profile-image">
                        @if ($user->img == null)
                            <img src="{{ asset('avatar.png') }}" class="img-circle circle-border m-b-md" alt="profile">
                        @else
                            <img src="{{ asset('barberDoc/' . $user->img) }}" class="img-circle circle-border m-b-md"
                                alt="profile">
                        @endif
                    </div>
                    <div class="profile-info">
                        <h2 class="no-margins">
                            {{ $user->name }}
                        </h2>

                        <table class="table small m-b-xs m-t-sm">
                            <tbody>
                                <tr>
                                    <td>
                                        <strong>Email : </strong>
                                    </td>
                                    <td>{{ $user->email }}</td>
                                </tr>
                                <tr>
                                    <td> <strong>Contact # </strong></td>
                                    <td>{{ $user->contact }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Business : </strong></td>
                                    <td>{{ $user->salon }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Service : </strong></td>
                                    <td>{{ $user->barber_type }}</td>


                                </tr>
                                <tr>
                                    <td><strong>Account Title : </strong></td>
                                    <td>{{ $user->account_title }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Account #: </strong></td>
                                    <td>{{ $user->account_no }}</td>

                                </tr>
                                <tr>
                                    <td><strong>Sort Code: </strong></td>
                                    <td>{{ $user->credit_card }}</td>
                                </tr>

                            </tbody>
                        </table>
                    </div>
                </form>
            </div>


            <div class="col-md-5">

                <table class="table small m-t-lg ">
                    <tbody>
                        <tr>
                            <th>
                                Total Appointments
                            </th>

                            <td>
                                <strong class="text-success">{{ $totalapp }}</strong>
                            </td>

                        </tr>
                        <tr>
                            <th>
                                Canceled Appointments
                            </th>
                            <td>
                                <strong class="text-danger">{{ $canceled }}</strong>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                Completed Appointments
                            </th>
                            <td>
                                <strong class="text-primary">{{ $completed }}</strong>
                            </td>
                        </tr>

                    </tbody>
                </table>
                <div class="row">
                    <div class="col-md-6">
                        <div class="ibox">
                            <div class="ibox-content">
                                <h5>Barber's Payment</h5>
                                <h4 class="no-margins"> <i class="fa fa-gbp"></i>
                                    {{ $wallet->total == null ? 0 : number_format($wallet->total) }}
                                </h4>
                                @if ($wallet->total !== null)
                                    <div class="stat-percent font-bold text-navy">

                                        <a href="{{ route('paybarberAmount', $user->user_id) }}"
                                            onclick="return confirm('Are you sure you want to Clear Payment ?');"
                                            style="color:#50af53;">
                                            {{ $wallet->total == null ? 0 : number_format($wallet->total) }}

                                            <i class="fa fa-send"></i>
                                        </a>
                                    </div>
                                    <small>Clear Payment</small>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="ibox">
                            <div class="ibox-content">
                                <h5>Payments So Far</h5>
                                <h4 class="no-margins"><i class="fa fa-gbp"></i>
                                    {{ $wallet_total->totalamount == null ? 0 : number_format($wallet_total->totalamount) }}
                                </h4>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">

                    <div class="col-md-6">
                        <div class="ibox">
                            <div class="ibox-content">
                                <h5>Commission</h5>
                                <h4 class="no-margins"><i class="fa fa-gbp"></i>
                                    {{ $comm->current_com == null ? 0 : number_format($comm->current_com, 2) }}
                                </h4>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="ibox">
                            <div class="ibox-content">
                                <h5>Commission So Far</h5>
                                <h4 class="no-margins"><i class="fa fa-gbp"></i>
                                    {{ $comm_tootal->totalcom == null ? 0 : number_format($comm_tootal->totalcom, 2) }}
                                </h4>
                            </div>
                        </div>
                    </div>
                </div>



                <div class="row">
                    <div class="col-md-6">
                        <div class="ibox">
                            <div class="ibox-content">
                                <h5>Total Product Sale</h5>
                                <h4 class="no-margins"> <i class="fa fa-gbp"></i>
                                    {{ $product_sale->totalPro == null ? 0 : number_format($product_sale->totalPro) }}
                                </h4>

                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="ibox">
                            <div class="ibox-content">
                                <h5>Commission On Product</h5>
                                <h4 class="no-margins"><i class="fa fa-gbp"></i>
                                    {{ $product_com->current_com == null ? 0 : number_format($product_com->current_com, 2) }}
                                </h4>
                                @if ($product_com->current_com !== null)
                                    <div class="stat-percent font-bold text-navy">

                                        <a href="{{ route('barbercommitionpayment', $user->user_id) }}"
                                            onclick="return confirm('Are you sure you want to Clear Commission Payment ?');"
                                            style="color:#50af53;">
                                            {{ $product_com->current_com == null ? 0 : number_format($product_com->current_com, 2) }}

                                            <i class="fa fa-send"></i>
                                        </a>
                                    </div>
                                    <small>Clear commission Pay</small>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div class="row m-b-lg m-t-lg">

            <div class="col-lg-6">
                <div class="ibox">
                    <div class="ibox-content">
                        <h3>Salon Barbers</h3>
                        <table class="table small m-b-xs table-striped">
                            <tbody>
                                <tr>
                                    <th>#</th>
                                    <th>Image</th>
                                    <th>Name</th>
                                    <th>Contact #</th>
                                    <th>Account #</th>
                                    <th>Account Title</th>
                                    <th>Credit Card</th>
                                </tr>
                                @foreach ($salonbarber as $key => $barber)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td><img src="{{ asset('barberDoc/' . $barber->img) }}" alt=""
                                                width="60"></td>
                                        <td>{{ $barber->name }}</td>
                                        <td>{{ $barber->contact }}</td>
                                        <td>{{ $barber->account_no }}</td>
                                        <td>{{ $barber->account_title }}</td>
                                        <td>{{ $barber->credit_card }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">

                <div class="ibox">
                    <div class="ibox-content">
                        <h3>Slot Details</h3>
                        <table class="table small m-b-xs table-striped">
                            <tbody>
                                <tr>
                                    <th>#</th>
                                    <th>Barber #</th>
                                    <th>From Time</th>
                                    <th>To Time</th>
                                    <th>Status</th>
                                </tr>
                                @foreach ($slots as $key => $slot)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $slot->barber ? $slot->barber->name : '' }}</td>
                                        <td>{{ $slot->from_time }}</td>
                                        <td>{{ $slot->to_time }}</td>
                                        <td>{{ $slot->status }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>


                    </div>
                </div>
                @if ($user->status !== 'Active')
                    <div class="row">
                        <div class="col-md-12">

                            <a href="{{ route('barberactivestatus', $user->id) }}"
                                style='background:#50af53;color:#fff;border:none'
                                class="btn btn-sm btn-outline btn-primary btn-block"
                                onclick="return confirm('Are you sure you want to  Approve ?');"><i class="fa fa-">Approve
                                    Account</i></a>
                        </div>
                    </div>
                @endif
            </div>

            <div class="col-lg-6">

                <div class="ibox">
                    <div class="ibox-content">
                        <h3>Service</h3>
                        <table class="table small m-b-xs table-striped">
                            <tbody>
                                <tr>
                                    <th>#</th>
                                    <th>Title</th>
                                    <th>Price</th>
                                </tr>
                                @foreach ($services as $key => $service)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $service->title }}</td>
                                        <td>{{ $service->price }}</td>

                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>



            <div class="col-lg-6">

                <div class="ibox">
                    <div class="ibox-content ">
                        <h3 class="">Barber Certificates</h3>
                        <div class="row">
                            @foreach ($docs as $doc)
                                <div class="col-md-6 m-b-xs">
                                    <h5 class="text-uppercase bg-info text-center" style="line-height:20px; padding:5px;">
                                        {{ $doc->title }}</h5>
                                    @if ($doc->type == 'PDF')
                                        <img src="{{ asset('PDF_file_icon.svg') }}" alt="" width="80">
                                        <br><br>
                                        <a href="{{ asset('barberDoc/' . $doc->image) }}" download> <i
                                                class="fa fa-download"></i>
                                            Download</a>
                                    @else
                                        <img alt="image" class="img-responsive"
                                            src="{{ asset('barberDoc/' . $doc->image) }}">
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>
@endsection
