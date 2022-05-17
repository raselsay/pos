<!DOCTYPE html>
<html>
<head>
  @php
  $lang=App\MultiSetting::select('value')->where('name','language')->first();
  @endphp
  {{App::setLocale(isset($lang->value) ? $lang->value : '' )}}
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  @php
  $info=DB::table('information')->select('company_name','logo','adress','phone')->get()->first();
  $path = asset('storage/logo/'.$info->logo);
  $type = pathinfo($path, PATHINFO_EXTENSION);
  $data = file_get_contents($path);
  $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
  @endphp
  <title>{{$info->company_name}}</title>
  <!-- Tell the browser to be responsive to screen width -->
  <!-- Font Awesome -->
  <link rel="stylesheet" type="text/css" href="{{ asset(mix('css/app.css')) }}">
  <link rel="shortcut icon" href="{{asset('storage/logo/'.$info->logo)}}" type="image/ico">
  <!-- <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css"> -->
  <!-- Ionicons -->
  @yield('link')
  <style>
    .delete{
      color:red;
    }
    .receive{
      background-color:#F8A300;
      margin-right: 5px;
    }
    .invoice{
      background-color:#8DC78A;
      margin-right: 5px;
    }
    .input-group{
      margin-top:5px;
    }
    .nav-user-dropdown{
      min-width: 230px;
    }
    .nav-user-info{
      line-height: 1.4;
      padding: 12px;
      color: #fff;
      font-size: 13px;
      border-radius: 2px 2px 0 0;
    }
    .preloader {
       position: absolute;
       top: 0;
       left: 0;
       width: 100%;
       height: 100%;
       z-index: 9999;
       background-image: url('{{asset('storage/admin-lte/dist/img/loader2.gif')}}');
       background-repeat: no-repeat; 
       background-color: #FFF;
       background-position: center;
       /*background-size: 400px,400px;*/
    }
  </style>
</head>

<body class="hold-transition sidebar-mini layout-fixed">
  {{-- loader here --}}
    <div class="preloader"></div>
  {{-- loader end --}}
<div class="wrapper">
    <input type="hidden" value="{{csrf_token()}}">
  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-purple navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
    </ul>
    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      <li>
        <button class="nav-link receive font-weight-bold btn btn-sm" onclick='MasterModal()'><span class='d-none d-md-block'>@lang('key.master.new_voucer')</span><i class="fas fa-file-invoice-dollar d-block d-md-none"></i></button>
      </li>
      <li>
        <a class="nav-link invoice font-weight-bold btn btn-sm" href='{{URL::to('admin/invoice')}}'><span class='d-none d-md-block'>@lang('key.master.new_invoice')</span><i class="fas fa-shopping-cart d-block d-md-none"></i></a>
      </li>
      <!-- user Dropdown -->
      <li class="nav-item dropdown nav-user">
                            <a class="nav-link nav-user-img" href="#" id="navbarDropdownMenuLink2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-user-circle"></i></a>
                            <div class="dropdown-menu dropdown-menu-right nav-user-dropdown" aria-labelledby="navbarDropdownMenuLink2">
                                <div class="nav-user-info">
                                    <h5 class="mb-0 text-dark nav-user-name">{{Auth::user()->name}}</h5>
                                    <span class="status"></span><span class="ml-2 text-success">@lang('key.master.available')</span>
                                </div>
                                <a class="dropdown-item" href="#"><i class="fas fa-user mr-2"></i>@lang('key.master.account')</a>
                                @role('Super-Admin')
                                <a class="dropdown-item" href="{{URL::to('register')}}"><i class="fas fa-user mr-2"></i>@lang('key.master.register')</a>
                                @endrole
                                <a class="dropdown-item" href="{{URL::to('admin/change_password')}}"><i class="fas fa-cog mr-2"></i>@lang('key.master.change_password')</a>
                                <a class="dropdown-item" href="{{ route('logout') }}" 

                                    onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
          <i class="fa fa-power-off mr-2" aria-hidden="true"></i>
          <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
              @csrf
          </form>
           @lang('key.master.logout')
        </a>
        </div>
    </li>
     
      <!-- Notifications Dropdown Menu -->
      <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
          <i class="fas fa-bell"></i>
           @php 
            $notification=DB::select('select details,action,created_at from notifications limit 10');
            $counter=count($notification);
            @endphp
          <span class="badge badge-warning navbar-badge">{{$counter}}</span>
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right" id='messageBox'>
          <span class="dropdown-item dropdown-header"> </span>
          @foreach($notification as $notice)
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item ">
            <i class="fas fa-bell mr-2 {{$notice->action}}"></i> 
            <p class="d-inline">
              @php
               echo htmlspecialchars_decode($notice->details);
              @endphp 
            </p>
            <span class="float-right text-muted text-sm">@php
              $dt = Carbon\Carbon::parse($notice->created_at);
              echo $dt->diffForHumans();
              @endphp</span>
          </a>
          <div class="dropdown-divider"></div>
          @endforeach
          <a href="{{URL::to('admin/notification')}}" class="dropdown-item dropdown-footer">@lang('key.master.see_all_notification')</a>
        </div>
      </li>
      {{-- <li class="nav-item">
        <a class="nav-link" data-widget="control-sidebar" data-slide="true" href="#" role="button">
          <i class="fas fa-th-large"></i>
        </a>
      </li> --}}
    </ul>
  </nav>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary  elevation-4">
    <!-- Brand Logo -->
    <a href="{{URL::to('/home')}}" class="brand-link">
      <img src="{{asset('storage/logo/'.$info->logo)}}" alt="Company Logo" class="brand-image img-circle elevation-3"
           style="opacity: .8">
      <span class="brand-text font-weight-light">{{$info->company_name}}</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        
        <div class="info">
          <a href="#" class="d-block"><i class="fas fa-circle text-success"></i> {{Auth::user()->name}}</a>
        </div>
      </div>

      <!-- Sidebar Menu -->
      <nav class="mt-2 pb-5">
        <ul class="nav nav-pills nav-sidebar flex-column nav-child-indent" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
         
          <li class="nav-item">
            <a href="{{ URL::to('/home') }}" class="nav-link">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>
                {{__('key.sidebar.dashboard')}}
              </p>
            </a>
          </li>
         <li class="nav-item">
            <a href="{{ URL::to('/admin/banks') }}" class="nav-link">
              <i class="nav-icon fas fa-donate"></i>
              <p>
                {{__('key.sidebar.banks')}}
              </p>
            </a>
          </li>
          <li class="nav-item">
              <a href="{{ URL::to('/admin/fund_transfer') }}" class="nav-link">
                <i class="nav-icon fas fa-exchange-alt "></i>
                <p>                
                  {{__('key.sidebar.fund_transfer')}}
                </p>
              </a>
            </li>
          <li class="nav-item">
            <a href="{{ URL::to('/admin/all_invoice') }}" class="nav-link">
              <i class="nav-icon fas fa-file-invoice"></i>
              <p>                
                {{__('key.sidebar.invoice')}}
              </p>
            </a>
          </li>
          <li class="nav-item has-treeview">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-user-alt"></i>
              <p>
                Order
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{ URL::to('/admin/create-order') }}" class="nav-link">
                  <i class="fas fa-circle nav-icon"></i>
                  <p>Order</p>
                </a>
              </li>
            </ul>
          </li>
          {{-- employee start --}}
          <li class="nav-item has-treeview">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-user-alt"></i>
              <p>
                {{__('key.sidebar.employee.employee')}}
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{ URL::to('/admin/employee') }}" class="nav-link">
                  <i class="fas fa-circle nav-icon"></i>
                  <p>{{__('key.sidebar.employee.employee')}}</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ URL::to('/admin/employee_salary') }}" class="nav-link">
                  <i class="fas fa-circle nav-icon"></i>
                  <p>{{__('key.sidebar.employee.employee_salary')}}</p>
                </a>
              </li>
            </ul>
          </li>
          {{-- employee end --}}
          <li class="nav-item">
            <a href="{{ URL::to('/admin/supplier') }}" class="nav-link">
              <i class="nav-icon fas fa-users"></i>
              <p>                
                {{__('key.sidebar.supplier')}}
              </p>
            </a>
          </li>
          {{-- start customer --}}
           <li class="nav-item has-treeview">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-user-alt"></i>
              <p>
                {{__('key.sidebar.customer')}}
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{ URL::to('/admin/all-customer') }}" class="nav-link">
                  <i class="fas fa-circle nav-icon"></i>
                  <p>{{__('key.sidebar.customer')}}</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ URL::to('/admin/group') }}" class="nav-link">
                  <i class="fas fa-circle nav-icon"></i>
                  <p>Group</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ URL::to('/admin/spo') }}" class="nav-link">
                  <i class="fas fa-circle nav-icon"></i>
                  <p>                
                    Spo
                  </p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ URL::to('/admin/customer_site') }}" class="nav-link">
                  <i class="fas fa-circle nav-icon"></i>
                  <p>                
                    Customer Site
                  </p>
                </a>
              </li>
            </ul>
          </li>
          <li class="nav-item">
            <a href="{{ URL::to('/admin/transport') }}" class="nav-link">
              <i class="fas fa-truck nav-icon"></i>
              <p>                
                {{__('key.sidebar.transport')}}
              </p>
            </a>
          </li>
          <li class="nav-item has-treeview">
            <a href="#" class="nav-link">
              <i class="nav-icon fab fa-product-hunt"></i>
              <p>
                {{__('key.sidebar.product.product')}}
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{ URL::to('/admin/product') }}" class="nav-link">
                  <i class="fas fa-circle nav-icon"></i>
                  <p>{{__('key.sidebar.product.product')}}</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ URL::to('/admin/product_type') }}" class="nav-link">
                  <i class="fas fa-circle nav-icon"></i>
                  <p>{{__('key.sidebar.product.unit')}}</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ URL::to('/admin/category') }}" class="nav-link">
                  <i class="fas fa-circle nav-icon"></i>
                  <p>{{__('key.sidebar.product.category')}}</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ URL::to('/admin/child_category') }}" class="nav-link">
                  <i class="fas fa-circle nav-icon"></i>
                  <p>{{__('key.sidebar.product.child_category')}}</p>
                </a>
              </li>
            </ul>
          </li>
          <li class="nav-item has-treeview">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-shopping-cart"></i>
              <p>
                {{__('key.sidebar.purchase.purchase')}}
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{ URL::to('/admin/purchase') }}" class="nav-link">
                  <i class="fas fa-circle nav-icon"></i>
                  <p>                
                    {{__('key.sidebar.purchase.purchase')}}
                  </p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ URL::to('/admin/opening_stock') }}" class="nav-link">
                  <i class="fas fa-circle nav-icon"></i>
                  <p>{{__('key.sidebar.purchase.opening_stock')}}</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ URL::to('/admin/all-purchase') }}" class="nav-link">
                  <i class="fas fa-circle nav-icon"></i>
                  <p>{{__('key.sidebar.purchase.purchase_list')}}</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ URL::to('/admin/stock_transfer') }}" class="nav-link">
                  <i class="fas fa-circle nav-icon"></i>
                  <p>Stock Transfer</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ URL::to('/admin/all_stock_transfer') }}" class="nav-link">
                  <i class="fas fa-circle nav-icon"></i>
                  <p>Stock Transfer List</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ URL::to('/admin/damage_out') }}" class="nav-link">
                  <i class="fas fa-circle nav-icon"></i>
                  <p>Damage Out</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ URL::to('/admin/all_damage_out') }}" class="nav-link">
                  <i class="fas fa-circle nav-icon"></i>
                  <p>Damage Out List</p>
                </a>
              </li>
            </ul>
          </li>
         <li class="nav-item">
            <a href="{{ URL::to('/admin/stock') }}" class="nav-link">
              <i class="fas fa-layer-group nav-icon"></i>
              <p>                
                {{__('key.sidebar.stock')}}
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="{{ URL::to('/admin/store') }}" class="nav-link">
              <i class="fas fa-layer-group nav-icon"></i>
              <p>                
                Store
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="{{ URL::to('/admin/voucer') }}" class="nav-link">
              <i class="nav-icon fas fa-money-check-alt"></i>
              <p>                
                {{__('key.sidebar.voucer')}}
              </p>
            </a>
          </li>
          <li class="nav-item has-treeview">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-file-alt"></i>
              <p>
                {{__('key.sidebar.custom_report.custom_report')}}
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{ URL::to('/admin/name') }}" class="nav-link">
                  <i class="fas fa-circle nav-icon"></i>
                  <p>{{__('key.sidebar.custom_report.manage_account')}}</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ URL::to('/admin/name_relation') }}" class="nav-link">
                  <i class="fas fa-circle nav-icon"></i>
                  <p>{{__('key.sidebar.custom_report.manage_account_head')}}</p>
                </a>
              </li>
            </ul>
          </li>
          <li class="nav-item has-treeview">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-poll"></i>
              <p>
                {{__('key.sidebar.reports.reports')}}
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{ URL::to('/admin/running-total') }}" class="nav-link">
                  <i class="fas fa-circle nav-icon"></i>
                  <p>{{__('key.sidebar.reports.ledger_sheet')}}</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ URL::to('/admin/bank_ledger') }}" class="nav-link">
                  <i class="fas fa-circle nav-icon"></i>
                  <p>{{__('key.sidebar.reports.bank_ledger')}}</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ URL::to('/admin/stock_ledger') }}" class="nav-link">
                  <i class="fas fa-circle nav-icon"></i>
                  <p>Stock Ledger</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ URL::to('/admin/stock_summery') }}" class="nav-link">
                  <i class="fas fa-circle nav-icon"></i>
                  <p>Stock Summery</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ URL::to('/admin/stock_summery') }}" class="nav-link">
                  <i class="fas fa-circle nav-icon"></i>
                  <p>Product W. Stock</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ URL::to('/admin/invoice_summery') }}" class="nav-link">
                  <i class="fas fa-circle nav-icon"></i>
                  <p>{{__('key.sidebar.reports.invoice_summery')}}</p>
                </a>
              </li>

                <!-- sales report -->
                <li class="nav-item has-treeview">
                  <a href="#" class="nav-link">
                    <i class="nav-icon fas fa-poll"></i>
                    <p>
                        {{__('key.sidebar.reports.sale_reports.sale_reports')}}
                      <i class="right fas fa-angle-left"></i>
                    </p>
                  </a>
                  <ul class="nav nav-treeview">
                    <li class="nav-item">
                      <a href="{{ URL::to('/admin/sales_summery') }}" class="nav-link">
                        <i class="fas fa-circle nav-icon"></i>
                        <p>{{__('key.sidebar.reports.sale_reports.sale_summery')}}</p>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a href="{{ URL::to('/admin/customer_wise_sales') }}" class="nav-link">
                        <i class="fas fa-circle nav-icon"></i>
                        <p>{{__('key.sidebar.reports.sale_reports.customer_wise_sale')}}</p>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a href="{{ URL::to('/admin/product_wise_sales') }}" class="nav-link">
                        <i class="fas fa-circle nav-icon"></i>
                        <p>{{__('key.sidebar.reports.sale_reports.product_wise_sale')}}</p>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a href="{{ URL::to('/admin/user_wise_sales') }}" class="nav-link">
                        <i class="fas fa-circle nav-icon"></i>
                        <p>{{__('key.sidebar.reports.sale_reports.user_wise_sale')}}</p>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a href="{{ URL::to('/admin/customer_wise_total_sale') }}" class="nav-link">
                        <i class="fas fa-circle nav-icon"></i>
                        <p>Customer W.T. Sale</p>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a href="{{ URL::to('/admin/customer_wise_payment') }}" class="nav-link">
                        <i class="fas fa-circle nav-icon"></i>
                        <p>Customer W.T. Pay</p>
                      </a>
                    </li>
                  </ul>
                </li>
                <!-- end sales -->
                <!-- sales report -->
                <li class="nav-item has-treeview">
                  <a href="#" class="nav-link">
                    <i class="nav-icon fas fa-poll"></i>
                    <p>
                        {{__('key.sidebar.reports.purchase_reports.purchase_reports')}}
                      <i class="right fas fa-angle-left"></i>
                    </p>
                  </a>
                  <ul class="nav nav-treeview">
                    <li class="nav-item">
                      <a href="{{ URL::to('/admin/purchase_summery') }}" class="nav-link">
                        <i class="fas fa-circle nav-icon"></i>
                        <p>{{__('key.sidebar.reports.purchase_reports.purchase_summery')}}</p>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a href="{{ URL::to('/admin/supplier_wise_purchase') }}" class="nav-link">
                        <i class="fas fa-circle nav-icon"></i>
                        <p>{{__('key.sidebar.reports.purchase_reports.supplier_wise_purchase')}}</p>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a href="{{ URL::to('/admin/product_wise_purchase') }}" class="nav-link">
                        <i class="fas fa-circle nav-icon"></i>
                        <p>{{__('key.sidebar.reports.purchase_reports.product_wise_purchase')}}</p>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a href="{{ URL::to('/admin/user_wise_purchase') }}" class="nav-link">
                        <i class="fas fa-circle nav-icon"></i>
                        <p>{{__('key.sidebar.reports.purchase_reports.user_wise_purchase')}}</p>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a href="{{ URL::to('/admin/supplier_wise_total_purchase') }}" class="nav-link">
                        <i class="fas fa-circle nav-icon"></i>
                        <p>Supplier W.T. Purchase</p>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a href="{{ URL::to('/admin/supplier_wise_payment') }}" class="nav-link">
                        <i class="fas fa-circle nav-icon"></i>
                        <p>Supplier W.T. Pay</p>
                      </a>
                    </li>
                  </ul>
                </li>
                <!-- end Purchase -->
              <li class="nav-item">
                <a href="{{ URL::to('/admin/daily_statement') }}" class="nav-link">
                  <i class="fas fa-circle nav-icon"></i>
                  <p>{{__('key.sidebar.reports.daily_statement')}}</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ URL::to('/admin/earn_statement') }}" class="nav-link">
                  <i class="fas fa-circle nav-icon"></i>
                  <p>Earn Statement</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ URL::to('/admin/earn_pay_statement') }}" class="nav-link">
                  <i class="fas fa-circle nav-icon"></i>
                  <p>Earn Pay Report</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ URL::to('/admin/head_wise_summation') }}" class="nav-link">
                  <i class="fas fa-circle nav-icon"></i>
                  <p>Head W.T. Sum</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ URL::to('/admin/total_expence_summation') }}" class="nav-link">
                  <i class="fas fa-circle nav-icon"></i>
                  <p>Total Exp. Sum</p>
                </a>
              </li>
              
              <li class="nav-item">
                <a href="{{ URL::to('/admin/buyerlistform') }}" class="nav-link">
                  <i class="fas fa-circle nav-icon"></i>
                  <p>{{__('key.sidebar.reports.buyer_list')}}</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ URL::to('/admin/getbuyerbalanceform') }}" class="nav-link">
                  <i class="fas fa-circle nav-icon"></i>
                  <p>{{__('key.sidebar.reports.buyer_balance')}}</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ URL::to('/admin/cash_details_form') }}" class="nav-link">
                  <i class="fas fa-circle nav-icon"></i>
                  <p>{{__('key.sidebar.reports.cash_details')}}</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ URL::to('/admin/custom_report') }}" class="nav-link">
                  <i class="fas fa-circle nav-icon"></i>
                  <p>{{__('key.sidebar.reports.custom_report')}}</p>
                </a>
              </li>
              {{-- <li class="nav-item">
                <a href="{{ URL::to('/admin/installment_report') }}" class="nav-link">
                  <i class="fas fa-circle nav-icon"></i>
                  <p>{{__('key.sidebar.reports.installment')}}</p>
                </a>
              </li> --}}
              <li class="nav-item">
                <a href="{{ URL::to('/admin/profit_loss') }}" class="nav-link">
                  <i class="fas fa-circle nav-icon"></i>
                  <p>{{__('key.sidebar.reports.profit_loss')}}</p>
                </a>
              </li>
            </ul>
          </li>
         <li class="nav-item">
            <a href="{{ URL::to('/admin/barcode') }}" class="nav-link">
              <i class="nav-icon fas fa-barcode"></i>
              <p>
                {{__('key.sidebar.barcode')}}
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="{{ URL::to('/admin/events') }}" class="nav-link">
              <i class="nav-icon fas fa-handshake"></i>
              <p>
                Commitment
              </p>
            </a>
          </li>
          <li class="nav-item has-treeview">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-cog"></i>
              <p>
                {{__('key.sidebar.sms.sms')}}
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{ URL::to('/admin/sms_dashboard') }}" class="nav-link">
                  <i class="fas fa-circle nav-icon"></i>
                  <p>{{__('key.sidebar.sms.sms_dashboard')}}</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ URL::to('/admin/custom_sms') }}" class="nav-link">
                  <i class="fas fa-circle nav-icon"></i>
                  <p>{{__('key.sidebar.sms.custom_sms')}}</p>
                </a>
              </li>
            </ul>
          </li>
          <li class="nav-item has-treeview">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-cog"></i>
              <p>
                {{__('key.sidebar.setting.setting')}}
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{ URL::to('/admin/info_form') }}" class="nav-link">
                  <i class="fas fa-circle nav-icon"></i>
                  <p>@lang('key.sidebar.setting.add_info')</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ URL::to('/admin/setting/make-setting') }}" class="nav-link">
                  <i class="fas fa-circle nav-icon"></i>
                  <p>@lang('key.sidebar.setting.setting')</p>
                </a>
              </li>
            </ul>
          </li>
          @role('Super-Admin')
          <li class="nav-item has-treeview">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-american-sign-language-interpreting"></i>
              <p>
                {{__('key.sidebar.permission.permission')}}
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{ URL::to('/admin/manage_role') }}" class="nav-link">
                  <i class="fas fa-circle nav-icon"></i>
                  <p>@lang('key.sidebar.permission.manage_role')</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ URL::to('/admin/manage_permission') }}" class="nav-link">
                  <i class="fas fa-circle nav-icon"></i>
                  <p>@lang('key.sidebar.permission.manage_permission')</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ URL::to('/admin/set_role_has_permission') }}" class="nav-link">
                  <i class="fas fa-circle nav-icon"></i>
                  <p>@lang('key.sidebar.permission.apply_permission')</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ URL::to('/admin/user_wise_role') }}" class="nav-link">
                  <i class="fas fa-circle nav-icon"></i>
                  <p>@lang('key.sidebar.permission.apply_role')</p>
                </a>
              </li>
            </ul>
          </li>
          <li class="nav-item">
            <a href="{{ URL::to('/admin/warehouse-permission') }}" class="nav-link">
              <i class="nav-icon fas fa-american-sign-language-interpreting"></i>
              <p>                
                {{__('key.sidebar.warehouse_permission')}}
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="{{ URL::to('/admin/user') }}" class="nav-link">
              <i class="nav-icon fas fa-user-alt"></i>
              <p>                
                {{__('key.sidebar.user')}}
              </p>
            </a>
          </li>
          @endrole
          <li class="nav-item has-treeview">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-database"></i>
              <p>
                {{__('key.sidebar.backup')}}
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{ URL::to('/admin/backup') }}" class="nav-link">
                  <i class="fas fa-circle nav-icon"></i>
                  <p>@lang('key.sidebar.db_backup')</p>
                </a>
              </li>
            </ul>
          </li>
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
  <div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" id="modal-voucer">
<div class="modal-dialog modal-lg">
  <div class="modal-content">
    <div class="modal-header">
      <h5 class="modal-title" id="VModalLabel"></h5>
      <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="MasterModalClose()">
        <span aria-hidden="true">&times;</span>
      </button>
    </div>
    <!--modal body-->
    <div class="modal-body ml-3 mr-3" id="forms">
      <form id="myMasterForm">
        <div class="row mb-2">
            <div class="col-12 col-md-3">
               <div class="form-group">
                  <label class="control-label" for="master-date">@lang('key.voucer.voucer-master.date'):</label>
                  <!-- <div class="col-8"> -->
                    <input type="text" id="master-date" class="master-date is-invalid form-control form-control-sm">
                  <!-- </div> -->
               </div>
           </div>
           <div class="col-12 col-md-4">
                <div class="form-group">
                    <label class="control-label" for="master-category">@lang('key.voucer.voucer-master.category'):</label>
                      <select  id="master-category" class="form-control is-invalid form-control-sm" onchange="getMasterCat(this)">
                        <option value="">--SELECT--</option>
                      </select>
                      <div id="master_category_msg" class="invalid-feedback">
                      </div>
                </div>
           </div>
           <div class="col-12 col-md-3">
              <div class="form-group">
                <label class="control-label" id='data-label' for="childCategory"></label>
                  <select type="text" id="master-data" class="form-control form-control-sm">
                    <option value="">--SELECT--</option>
                  </select>
                  <div id="master_data_msg" class="invalid-feedback">
                  </div>
              </div>
           </div>
           <div class="col-12 col-md-2">
            <br>
              <div class="form-group float-right">
                 <input type="checkbox" class="form-check-input form-control-sm" name="v_type" id="v_type" onchange="VoucerTypeChange()">
                 <label class="form-check-label  mt-2" for="v_type">
                    @lang('key.voucer.voucer-master.bill')
                 </label>
              </div>
           </div>
        </div>
        
        <table class="table table-sm table-bordered text-center">
          <thead>
            <tr>
              <th width="35%">@lang('key.voucer.voucer-master.details')</th>
              <th width="15%" id="th-qantity">@lang('key.voucer.voucer-master.qantity')</th>
              <th width="15%">@lang('key.voucer.voucer-master.ammount')</th>
              <th width="20%" id="th-total">@lang('key.voucer.voucer-master.total')</th>
              <th width="15%">@lang('key.voucer.voucer-master.action')</th>
            </tr>
          </thead>
          <tbody>
          </tbody>
        </table>
        <button type="button" onclick="AddVoucerDetails()" class="btn btn-sm btn-primary float-right mb-1">+</button>
      <div class="input-group">
        <label class="control-label col-sm-8 text-lg-right" for="payment">@lang('key.voucer.voucer-master.payment_type'):</label>
        <div class="col-md-3">
          <select type="text" id="master-payment_type" class="form-control form-control-sm">
            <option value="">Select</option>
            <option value="Deposit">@lang('key.voucer.voucer-master.deposit')</option>
            <option value="Expence">@lang('key.voucer.voucer-master.expence')</option>
          </select>
          <div id="master_payment_type_msg" class="invalid-feedback">
          </div>
        </div>
      </div>
      <div class="input-group">
        <label class="control-label col-sm-8 text-lg-right" for="bank">@lang('key.voucer.voucer-master.payment_method'):</label>
        <div class="col-md-3">
          <select type="text" id="master-bank" class="form-control form-control-sm">
            <option value="">--SELECT--</option>
          </select>
          <div id="master_bank_msg" class="invalid-feedback">
          </div>
        </div>
      </div>
      <div class="input-group">
        <label class="control-label col-sm-8 text-lg-right" for="product">@lang('key.voucer.voucer-master.ammount') $:</label>
        <div class="col-md-3">
          <input disabled="" type="text" id="master-ammount" class="form-control form-control-sm" placeholder="@lang('key.voucer.voucer-master.ammount_placeholder')">
          <div id="master_ammount_msg" class="invalid-feedback">
          </div>
        </div>
      </div>
     </form>
    </div>
    <div class="modal-footer">
      <button type="button" onclick="MasterModalClose()" class="btn btn-secondary" data-dismiss="modal">@lang('key.buttons.close')</button>
      <button type="button" class="btn btn-primary master-submit" onclick="MasterAjaxRequest()">@lang('key.buttons.save')</button>
      <button type="button" class="btn btn-warning master-submit" onclick="MasterAjaxRequest(1)">@lang('key.buttons.save_and_print')</button>
    </div>
  </div>
</div>
</div>
    @yield('content')
  </div>
  <!-- /.content-wrapper -->
  <footer class="main-footer">
    <strong>Copyright &copy; 2021 <a href="http://softimpire.com">SOFTiMPIRE</a>.</strong>
    All rights reserved.
    <div class="float-right d-none d-sm-inline-block">
      <b>Version</b>1.0.0
    </div>
  </footer>

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
  </aside>
</div>
{{-- voucer modal --}}
<script src="{{asset(mix('js/app.js'))}}"></script>
@yield('script')
<script>
  $("body").css("overflow", "hidden");
  window.onload=function(){
     $('.preloader').fadeOut('slow');
     $("body").css("overflow", "initial");
  }
  // alert($("[name='v_type'] option:selected").val())
$(document).ready(function(){
    const url = window.location;
    $('ul.nav-sidebar a').filter(function() {
        return this.href == url;
    }).parent().addClass('active');
    $('ul.nav-treeview a').filter(function() {
        return this.href == url;
    }).parentsUntil(".sidebar-menu > .nav-treeview").addClass('menu-open');
    $('ul.nav-treeview a').filter(function() {
        return this.href == url;
    }).addClass('active');
    $('li.has-treeview a').filter(function() {
        return this.href == url;
    }).addClass('active');
    $('ul.nav-treeview a').filter(function() {
        return this.href == url;
    }).parentsUntil(".sidebar-menu > .nav-treeview").children(0).addClass('active');
    $("li a").filter(function(){
        return this.href == url;
    }).addClass('active')
    AddVoucerDetails();
    
});

  let add_no=0;
  function AddVoucerDetails(){
    add_no=add_no+1;
    options="<tr>";
    options+="<td><input class='form-control form-control-sm' type='text' placeholder='@lang('key.voucer.voucer-master.details_placeholder')' name='Mdetails[]'></td>";
    options+="<td><input class='form-control form-control-sm' type='number'  name='Mqantity[]' value='1'></td>";
    options+="<td><input class='form-control form-control-sm' type='number' placeholder='@lang('key.voucer.voucer-master.ammount_placeholder')' name='Mammount[]'></td>";
    options+="<td><input class='form-control form-control-sm' type='number'  name='Mtotal[]'></td>";
    options+="<td><button type='button' onclick='recordMasterRemove(this)' class='btn btn-sm btn-danger remove'>X</button></td>"
    options+="</tr>";
    $('#myMasterForm tbody').append(options);
    Mcalculation();
    VoucerTypeChange()
  }
  function MasterRemoveAll(){
    add_no=0;
    $('#myMasterForm tbody').empty();
  }
  function recordMasterRemove(this_val){
    if (add_no<=1){
      alert('you can,t remove this item');
        return false;
      }else{
        $(this_val).parent().parent().remove();
        add_no=add_no-1;
      }
  }

function Mcalculation(){
  FinalTotal=0;
  $("#myMasterForm input[name='Mqantity[]']").map(function(){
    qantity=$(this).val();
    ammount=$(this).parent().next().children("input[name='Mammount[]']").val();
    total=qantity*ammount;
    FinalTotal+=qantity*ammount
    $(this).parent().next().next().children("input[name='Mtotal[]']").val(total)
  }).get();
  $('#master-ammount').val(FinalTotal);
}
$('#myMasterForm').on('keyup change','input',function(){
  Mcalculation();
})
function MasterModal(){
  $('#modal-voucer').modal('show');
  $('#VModalLabel').text("@lang('key.voucer.voucer-master.title_modal')");
  MasterModalClose();
  $('#master-bank').select2({
    theme:'bootstrap4',
    placeholder:'Select',
    allowClear:true,
    ajax:{
      url:"{{URL::to('admin/get_banks')}}",
      type:'post',
      dataType:'json',
      delay:20,
      data:function(params){
        return {
          searchTerm:params.term,
          _token:"{{csrf_token()}}",
          }
      },
      processResults:function(response){
        return {
          results:response,
        }
      },
      cache:true,
    }
  })
  $('#master-category').select2({
    theme:'bootstrap4',
    placeholder:'Select',
    allowClear:true,
    ajax:{
      url:'{{URL::to('admin/search_name')}}',
      type:'post',
      dataType:'json',
      delay:20,
      data:function(params){
        return {
          searchTerm:params.term,
          _token:"{{csrf_token()}}",
          }
      },
      processResults:function(response){
        return {
          results:response,
        }
      },
      cache:true,
    }
  })
}
function getMasterCat(data){
  if (Number.isInteger(parseInt(data.value))) {
    category=data.options[data.selectedIndex].text;
  }else{
    category='';
  }
  $('#data-label').text(category+':');
  $('#master-data').html('');
  $('.master-data').removeClass('d-none');
   $('#master-data').select2({
      theme:'bootstrap4',
      placeholder:'Select '+category,
      allowClear:true,
      ajax:{
        url:"{{URL::to('admin/relation_search')}}/"+data.value,
        type:"post",
        dataType:'json',
        delay:20,
        data:function(params){
          return {
            searchTerm:params.term,
            _token:"{{csrf_token()}}",
            }
        },
        processResults:function(response){
          return {
            results:response,
          }
        },
        cache:true,
      }
    })
  }
function MasterValid(){
  let isValid=true;
$('#master-category').removeClass('is-invalid');
  if($('#master-category').val()==''){
    $('#master-category').addClass('is-invalid');
  }
$("input[name='Mqantity[]']").each(function(){
  $(this).removeClass('is-invalid');
  if ($(this).val()=='' || $(this).val()<0) {
    isValid=false;
    $(this).addClass('is-invalid');
  }
})
  $("input[name='Mammount[]']").each(function(){
    $(this).removeClass('is-invalid');
    if ($(this).val()==''){
      isValid=false;
      $(this).addClass('is-invalid');
    }
  })
}
function MasterAjaxRequest(print=null){
    valid=MasterValid();
    // MasterPdf();
    if(valid==false){
      return false;
    }
    $('.master-submit').attr('disabled',true);
    $('.invalid-feedback').hide();
    $('select,input').removeClass('is-invalid');
    $('select,input').css('border','1px solid rgb(209,211,226)');
    let Mdetails=$("#myMasterForm input[name='Mdetails[]']").map(function(){return $(this).val();}).get();
    let Mqantity=$("#myMasterForm input[name='Mqantity[]']").map(function(){return $(this).val();}).get();
    let Mammount=$("#myMasterForm input[name='Mammount[]']").map(function(){return $(this).val();}).get();
    let main_date=$('#master-date').val();
    let main_category=$('#master-category option:selected').val();
    let main_data=$('#master-data').val();
    let main_payment_type=$('#master-payment_type').val();
    let main_bank=$('#master-bank').val();
    let main_ammount=$('#master-ammount').val();
    let formData= new FormData();
    let details=$("#myMasterForm input[name='Mqantity[]']").map(function(){return $(this).val();}).get();
    formData.append('details[]',Mdetails);
    formData.append('qantity[]',Mqantity);
    formData.append('ammount[]',Mammount);
    formData.append('date',main_date);
    formData.append('category',main_category);
    formData.append('data',main_data);
    formData.append('payment_type',main_payment_type);
    formData.append('bank',main_bank);
    formData.append('total_ammount',main_ammount);
    //axios post request
  axios.post('/admin/voucer',formData)
  .then(function (response){
    if (response.data.message){
      window.toastr.success(response.data.message);
      $('.data-table').DataTable().ajax.reload();
      if(print==1){
        MasterPdf(response.data['v_id']);
      }else{
          MasterModalClose();
          $('.master-submit').attr('disabled',false);
      }
      $('#modal-voucer').modal('hide');
      return false;
    }
    var keys=Object.keys(response.data);
    for(var i=0; i<keys.length;i++){
        $('#master_'+keys[i]+'_msg').html(response.data[keys[i]][0]);
        $('#master-'+keys[i]).addClass('is-invalid');
        $('#master-'+keys[i]).css('border','1px solid red');
        $('#master_'+keys[i]+'_msg').show();
        $('.master-submit').attr('disabled',false);
      }
  })
   .catch(function (error) {
    console.log(error);
  });
 }
function MasterPdf(v_id=null){
      details = $("input[name='Mdetails[]']")
                  .map(function(){return $(this).val();}).get();
      qantity = $("input[name='Mqantity[]']")
                  .map(function(){return $(this).val();}).get();
      ammount = $("input[name='Mammount[]']")
                  .map(function(){return $(this).val();}).get();
      date=parseFloat($('#master-date').val());
      category=parseFloat($('#master-category').val());
      payment_type=parseFloat($('#master-payment_type').val());
      data=parseFloat($('#master-data').val());
      total_ammount=parseFloat($('#master-ammount').val());
      v_type=($("[name='v_type']").prop('checked')==true) ? '' : 'display:none;';
      x=[{details,qantity,ammount}];
      html=`<div id="invoice">
            <div style='background-color:#007BFF;padding:50px;color:white;'>
              <table width="100%" style="border:none;">
                <tr>
                  <td>
                    <img height="80px" width="100px" src="{{$base64}}"><br>
                    <span style="font-size:25px;">`+((payment_type=='Deposit') ? "@lang('key.voucer.voucer.debit_voucer')" : "@lang('key.voucer.voucer.credit_voucer')")+`</span>
                  </td>
                  <td style="float:right;"><span style="font-weight:bold;">{{$info->company_name}}</span><br>{{$info->adress}}<br>{{$info->phone}}</td>
                </tr>
              </table>
            </div>
            <div style="margin-right:50px;margin-left:50px;margin-top:30px;margin-bottom:30px;">
              <table width="100%" style="border:none;font-weight:bold;">
                <tr>
                  <td>
                     @lang('key.voucer.voucer.date')
                  </td>
                  <td style="float:right;">`+$('#master-date').val()+`</td>
                </tr>
                <tr>
                  <td>
                     @lang('key.voucer.voucer-master.bill_no').
                  </td>
                  <td style="float:right;">`+'1'+String(v_id).padStart(9,'0')+`</td>
                </tr>
                <tr>
                  <td>
                     @lang('key.voucer.voucer.category'):
                  </td>
                  <td style="float:right;">`+$('#master-category option:selected').text()+`</td>
                </tr>
                <tr>
                  <td>
                     @lang('key.voucer.voucer.name'):
                  </td>
                  <td style="float:right;">`+$('#master-data option:selected').text()+`</td>
                </tr>
              </table>
            </div>
            <div id="tables" style='margin-right:50px;margin-left:50px;'>
              <table width="100%" style="text-align:center;border:1px solid grey;">
                <tr>
                  <th style='border:1px solid grey;'>@lang('key.voucer.voucer-master.details')</th>
                  <th style='`+v_type+`border:1px solid grey;'>@lang('key.voucer.voucer-master.qantity')</th>
                  <th style='border:1px solid grey;'>@lang('key.voucer.voucer-master.ammount')</th>
                  <th style="`+v_type+`border:1px solid grey;">@lang('key.voucer.voucer-master.total')</th>
                </tr>
      `;
      for (var i=0;i<details.length; i++) {
        html+="";
        html+="<td style='border:1px solid grey;'>"+x[0]['details'][i]+"</td>";
        html+="<td style='"+v_type+"border:1px solid grey;'>"+(parseFloat(x[0]['qantity'][i])).toFixed(2)+"</td>";
        html+="<td style='border:1px solid grey;'>"+(parseFloat(x[0]['ammount'][i])).toFixed(2)+"</td>";
        html+="<td style='"+v_type+"border:1px solid grey;'>"+(parseFloat(x[0]['qantity'][i])*parseFloat(x[0]['ammount'][i])).toFixed(2)+"</td>";
        html+="</tr>";
      }
      html+=`</table>
       </div>
       <div style='margin-right:50px;margin-left:50px;margin-top:30px;margin-bottom:30px;'>
    <table width="100%" style="color:black;font-weight:bold">
      <!-- total -->
      <tr style='background-color:#F1F1F1'>
        <td>
           @lang('key.voucer.voucer-master.total_ammount')৳
        </td>
        <td style="text-align:right;">`+total_ammount.toFixed(2)+`</td>
      </tr>
    </table>
    <br>
    <h2>@lang('key.voucer.voucer-master.note').</h2>
    <br>
    <br>
     </div>`
     $(html).printThis({
        importCSS:true,
        printDelay: 333,
        header: "",
        footer:`<p style="text-align:center;">
                  Software Developed By <strong>SOFTiMPIRE</strong>
                  <br>Adress:Barisal Bottola,Barisal<br>
                  Mobile:01873072253,01310588563
                </p>`,
        base: "noman"
      });
    MasterModalClose();
    $('.master-submit').attr('disabled',false);
}
 $('#master-date').daterangepicker({
 showDropdowns:true,
 singleDatePicker: true,
 locale: {
    format: 'DD-MM-YYYY',
  },
  minDate: '01-01-1950',
  maxDate: '01-01-2050'
});
function MasterModalClose(){
  MasterRemoveAll();
  // $('#myMasterForm input').val('');
  $('#myMasterForm select').val(null || 'bill').change(); 
  document.getElementById("myMasterForm").reset();
  // $("#myMasterForm select option[value='']").attr('selected',true);
  $('#myMasterForm .invalid-feedback').hide();
  $('#myMasterForm select,input').removeClass('is-invalid');
  $('#myMasterForm select,input').css('border','1px solid rgb(209,211,226)');
  AddVoucerDetails();
  $('#master-date').daterangepicker({
 showDropdowns:true,
 singleDatePicker: true,
 locale: {
    format: 'DD-MM-YYYY',
  },
  minDate: '01-01-1950',
  maxDate: '01-01-2050'
});
  VoucerTypeChange();
}
function dateFormat(date){
let date_ob = date;
let dates = ("0" + date_ob.getDate()).slice(-2);
let month = ("0" + (date_ob.getMonth() + 1)).slice(-2);
let year = date_ob.getFullYear();
return(dates + "-" + month + "-" + year);
}

function VoucerTypeChange(){
  if($("#v_type").prop('checked')==true){
    check=true
  }else{
    check=false
  }
   if (check==false){
    $('#th-qantity,#th-total').addClass('d-none');
    $("input[name='Mqantity[]'],input[name='Mtotal[]']").map(function(){
       $(this).parent().addClass('d-none');
    });
    
   }else{
    $('#th-qantity,#th-total').removeClass('d-none');
     $("input[name='Mqantity[]'],input[name='Mtotal[]']").map(function(){
       $(this).parent().removeClass('d-none');
    });
   }
}
</script>
</body>
</html>