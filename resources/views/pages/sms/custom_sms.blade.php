@extends('layouts.master')
@section('content')
@php
  $lang=App\MultiSetting::select('value')->where('name','language')->first();
  App::setLocale(isset($lang->value) ? $lang->value : '' );
@endphp
<section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>@lang('key.sms.custom_sms.title')</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ URL::to('/home') }}">Home</a></li>
              <li class="breadcrumb-item">SMS</li>
              <li class="breadcrumb-item active">Custom SMS</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>
<div class="container">
	<div class="card m-0">
    <div class="card-header pt-3  flex-row align-items-center justify-content-between">
      <h5 class="m-0 font-weight-bold">@lang('key.sms.custom_sms.title')</h5>
     </div>
    <div class="card-body px-3 px-md-5">
        <form id="myForm">
        <input type="hidden" id="id">
        <div class="form-group">
          <label class="font-weight-bold">@lang('key.sms.custom_sms.sms_type'):</label>
          <select class="form-control form-control-sm" onchange="SmsType(this.value)" id="sms_type">
            <option value="0">@lang('key.sms.custom_sms.invidual')</option>
            <option value="1">@lang('key.sms.custom_sms.all')</option>
          </select>
          <div id="name_msg" class="invalid-feedback">
          </div>
        </div>
        <div class="form-group">
          <label class="font-weight-bold">@lang('key.sms.custom_sms.to'):</label>
          <input type="text" class="form-control-sm form-control" id="numbers" placeholder="018xxxxxxxxx,017xxxxxxxxx,019xxxxxxxxx">
          <div id="name_msg" class="invalid-feedback">
          </div>
        </div>
        <div class="form-group d-none">
          <label class="font-weight-bold">@lang('key.sms.custom_sms.to'):</label>
          <select class="form-control form-control-sm" id="account_type">
            <option value="0">@lang('key.sms.custom_sms.customer')</option>
            <option value="1">@lang('key.sms.custom_sms.supplier')</option>
            <option value="2">@lang('key.sms.custom_sms.employee')</option>
          </select>
          <div id="number_msg" class="invalid-feedback">
          </div>
        </div>
        <div class="form-group">
          <label class="font-weight-bold">@lang('key.sms.custom_sms.write_sms'):</label>
          <textarea class="form-control form-control-sm" rows="3" placeholder="@lang('key.sms.custom_sms.write_placeholder')" id="message"></textarea>
          <div id="branch_msg" class="invalid-feedback">
          </div>
        </div>
       <!--end 2nd column -->
       </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" onclick="ModalClose()" data-dismiss="modal">@lang('key.buttons.close')</button>
        <button type="button" class="btn btn-primary submit" onclick="getPhoneNumber()">@lang('key.sms.custom_sms.send')</button>
      </div>
                 
    </div>
  </div>
</div>
@endsection
@section('script')
<script type="text/javascript">
function SmsType(val){
  if(val==0){
    $('#numbers').parent().removeClass('d-none');
    $('#account_type').parent().addClass('d-none')
  }else if(val==1){
    $('#numbers').parent().addClass('d-none');
    $('#account_type').parent().removeClass('d-none')
  }
}
function getPhoneNumber(){
      sms_type=$('#sms_type').val();
      account_type=$('#account_type').val();
      if(sms_type==1){
      axios.get('admin/get_numbers/'+account_type)
        .then((response)=>{
          ajaxRequest(response.data);
        })
      }else if(sms_type==0){
          ajaxRequest($('#numbers').val());
      }
}
function ajaxRequest(number){
    $('.submit').attr('disabled',true);
    $('.invalid-feedback').hide();
    $('input').css('border','1px solid rgb(209,211,226)');
    $('select').css('border','1px solid rgb(209,211,226)');
    msg=$('#message').val();
      $.post('https://api.greenweb.com.bd/api.php?json',{to:number,token:'{{$data->sms_sender}}',message:msg})
      .done(function (response,status){
        if(response.status && response.status=='FAILED'){
            toastr.error(response.statusmsg)
        }
        for (var i = 0; i < response.length; i++) {
          if(response[i].status!='FAILED'){
            toastr.success(response[i].statusmsg)
          }else{
            toastr.error(response[i].statusmsg)
          }
          
        }
        $('.submit').attr('disabled',false);
        return false;
        var keys=Object.keys(response.data[0]);
        for(var i=0; i<keys.length;i++){
            $('#'+keys[i]+'_msg').html(response.data[0][keys[i]][0]);
            $('#'+keys[i]).css('border','1px solid red');
            $('#'+keys[i]+'_msg').show();
            $('.submit').attr('disabled',false);
          }
      })
       .catch(function (error) {
        $('.submit').attr('disabled',false);
        alert((JSON.parse(error.request.response)).message);
      });
 }
 function ModalClose(){
  $('input').val('');
  $("select option[value='']").attr('selected',true);
  $('.invalid-feedback').hide();
  $('input').css('border','1px solid rgb(209,211,226)');
  $('select').css('border','1px solid rgb(209,211,226)');
 }
 </script>
@endsection
