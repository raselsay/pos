@extends('layouts.master')
@section('content')
@php
  $lang=App\MultiSetting::select('value')->where('name','language')->first();
  App::setLocale(isset($lang->value) ? $lang->value : '' );
@endphp
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">@lang('key.sms.sms_dashboard.title') <button class="btn btn-sm btn-info" onclick="loadData()">Refresh</button></h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">SMS Dashboard</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <section class="content">
      <div class="container-fluid">
        <!-- Small boxes (Stat box) -->
        <div class="row">
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-info">
              <div class="inner">
                <h3 id="token_sms"></h3>
                <div class="spinner-border text-primary" role="status">
                  <span class="sr-only">@lang('key.sms.sms_dashboard.loading').</span>
                </div>
                <p>@lang('key.sms.sms_dashboard.total_send_sms')</p>
              </div>
              <div class="icon">
                <i class="nav-icon fas fa-donate"></i>
              </div>
              <a href="#" class="small-box-footer">@lang('key.sms.sms_dashboard.more_info') <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-success">
              <div class="inner">
                <h3 id="balance"></h3>
                <div class="spinner-border text-primary" role="status">
                  <span class="sr-only">@lang('key.sms.sms_dashboard.loading')</span>
                </div>
                <p>@lang('key.sms.sms_dashboard.total_balance')</p>
              </div>
              <div class="icon">
                <i class="nav-icon fas fa-donate"></i>
              </div>
              <a href="#" class="small-box-footer">@lang('key.sms.sms_dashboard.more_info') <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-warning">
              <div class="inner">
                <h3 id='expiry_date'></h3>
                <div class="spinner-border text-primary" role="status">
                  <span class="sr-only">@lang('key.sms.sms_dashboard.loading')</span>
                </div>
                <p>@lang('key.sms.sms_dashboard.expire_date')</p>
              </div>
              <div class="icon">
                <i class="nav-icon fas fa-donate"></i>
              </div>
              <a href="#" class="small-box-footer">@lang('key.sms.sms_dashboard.more_info') <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-warning">
              <div class="inner">
                <h3 id='sms_rate'></h3>
                <div class="spinner-border text-primary" role="status">
                  <span class="sr-only">@lang('key.sms.sms_dashboard.loading')</span>
                </div>
                <p>@lang('key.sms.sms_dashboard.sms_rate')</p>
              </div>
              <div class="icon">
                <i class="nav-icon fas fa-donate"></i>
              </div>
              <a href="#" class="small-box-footer">@lang('key.sms.sms_dashboard.more_info') <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
        </div> <!-- ./row -->
    </div>
    </section>
@endsection

@section('script')
<script src="{{asset('js/Chart.js')}}"></script>
<script>
$(document).ready(function(){
   loadData();
})
window.onload=function(){
    $('.spinner-border').addClass('d-none');
}
function loadData(){
  $('.spinner-border').removeClass('d-none');
  $('.spinner-border').prev().addClass('d-none');
  $.get('{{$data->sms_api}}'+'?'+'token={{$data->sms_sender}}'+'&expiry&rate&tokensms&totalsms', function(data, status){
    data=data.split('</br>')
    $('#expiry_date').text((data[0]=='' ? 'Unlimited' : data[0]))
    $('#sms_rate').text(data[1])
    $('#token_sms').text(data[2])
    $('.spinner-border').prev().removeClass('d-none');
    $('.spinner-border').addClass('d-none');
  });
  $.get('{{$data->sms_api}}'+'?'+'token={{$data->sms_sender}}'+'&balance', function(data, status){
    data=data.split('</br>')
    $('#balance').text((data[0]=='' ? '0.00' : (parseFloat(data[0]).toFixed(2))))
    // $('.spinner-border').addClass('d-none');
  });
}
 
</script>
@endsection