@extends('layouts.master')
@section('content')
@section('link')
<style>
  /*#date{
    margin:0 auto;
  }*/
  .ml{
  margin-left:1px;
  margin-right:1px;
 }
  #submit{
    margin:0 auto;
    margin-top: 20px;
  }
  #blnc{
    float:right;
  }
</style>
@endsection
@php
  $lang=App\MultiSetting::select('value')->where('name','language')->first();
  App::setLocale(isset($lang->value) ? $lang->value : '' );
@endphp
<div class="container">
	<div class="card m-0">
    <div class="card-header pt-3  flex-row align-items-center justify-content-between">
      <h5 class="m-0 font-weight-bold">@lang('key.invoice.installment_pay.title')</h5>
     </div>
    <div class="card-body px-3 px-md-5">
      <form id="myForm">
        <div class="input-group">
           <label class="control-label col-sm-2 col-md-2 text-lg-right" for="type">@lang('key.invoice.installment_pay.customer'):</label>
           <div class="col-sm-9 col-md-9">
              <select class="form-control form-control-sm" name="customer" id="customer" onchange="getInvoice()">
              </select>
               <div id="customer_msg" class="invalid-feedback">
               </div>
            </div>
        </div>
        <div class="input-group">
           <label class="control-label col-sm-2 text-lg-right" for="type">@lang('key.invoice.installment_pay.date'):</label>
           <div class="col-sm-9">
              <input class="form-control form-control-sm" name="date" id="date">
               <div id="date_msg" class="invalid-feedback">
               </div>
            </div>
        </div>
        <div class="input-group">
           <label class="control-label col-sm-2 text-lg-right" for="bank">@lang('key.invoice.installment_pay.bank'):</label>
           <div class="col-sm-9">
              <select class="form-control form-control-sm" name="bank" id="bank">
                <option value=""></option>
                @foreach($bank as $banks)
                <option value="{{$banks->id}}">{{$banks->name}}</option>
                @endforeach
              </select>
               <div id="bank_msg" class="invalid-feedback">
               </div>
            </div>
        </div>
        <div class="input-group">
           <label class="control-label col-sm-2 text-lg-right" for="type">@lang('key.invoice.installment_pay.transaction'):</label>
           <div class="col-sm-9">
              <input  class="form-control form-control-sm" name="transaction" id="transaction" placeholder="XXNS33M3565N445.....">
               <div id="transaction_msg" class="invalid-feedback">
               </div>
            </div>
        </div>
        <div class="input-group">
           <label class="control-label col-sm-2 text-lg-right" for="type">@lang('key.invoice.installment_pay.ammount') $:</label>
           <div class="col-sm-9">
              <input disabled="" class="form-control form-control-sm" name="ammount" id="ammount" placeholder="@lang('key.invoice.installment_pay.ammount_placeholder')">
               <div id="ammount_msg" class="invalid-feedback">
               </div>
            </div>
        </div>
      </form>
        <div class="col-md-2" id="submit">
          <button class="btn btn-sm btn-secondary" onclick="Reset()">@lang('key.buttons.reset')</button>
          <button class="btn btn-sm btn-primary submit" onclick="Request()">@lang('key.buttons.save')</button>
        </div>
      </div>
  </div>
</div>
@endsection
@section('script')
<script src="{{asset('js/pdf.js')}}"></script>
<script>
 $(document).ready(function(){
    $('#bank').select2({
      theme:'bootstrap4',
      placeholder:'select',
      allowClear:true,
    })
    $('#customer').select2({
      theme:'bootstrap4',
      placeholder:'select',
      allowClear:true,
      ajax:{
        url:"{{URL::to('admin/get_ins_invoice')}}",
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
 })
 function getInvoice(){
  val= $('#customer').val()
  if (val!=undefined) {
     val=val.split('|');
     id=val[0];
  }else{
    id=''
    $('#ammount').val('')
  }
  if (id!='') {
      axios.get('admin/get_ins_ammount/'+id)
      .then((res)=>{
        if (parseInt(res.data[0].total_paid)<parseInt(res.data[0].total_days)){
          $('#ammount').val(parseInt(res.data[0].total))
        }else{
          alert('This Customer already paid')
          $('#ammount').val('')
        }
      })
    }
    
 }

$('#date').daterangepicker({
 showDropdowns:true,
 singleDatePicker: true,
 locale: {
    format: 'DD-MM-YYYY',
  },
  minDate: '01-01-1950',
  maxDate: '01-01-2050'
});

function Request(){
  id_val=$('#customer option:selected').val();
  if (id_val!=undefined){
      arr=id_val.split('|');
      customer_id=arr[1];
      invoice_id=arr[0];
  }else{
    customer_id='';
    invoice_id='';
  }
  ammount=$('#ammount').val();
  date=$('#date').val();
  bank=$('#bank').val();
  transaction=$('#transaction').val();
  formData=new FormData;
  formData.append('customer',customer_id);
  formData.append('invoice',invoice_id);
  formData.append('ammount',ammount);
  formData.append('date',date);
  formData.append('bank',bank);
  formData.append('transaction',transaction);
  axios.post('/admin/installment_pay',formData)
  .then((res)=>{
    $('submit').attr('disabled',true);
    $('input').removeClass('is-invalid');
    $('select').removeClass('is-invalid');
    if (res.data.error){
      var keys=Object.keys(res.data.error);
    for(var i=0; i<keys.length;i++){
        $('#'+keys[i]+'_msg').html(res.data.error[keys[i]][0]);
        $('#'+keys[i]).addClass('is-invalid');
        $('#'+keys[i]+'_msg').show();
      }
      return false;
    }
    toastr.success(res.data.message)
    id=res.data.v_id;
       html=`
          <table style="font-size:10px">
          <tr>
            <th width='10%'>ID</th>
            <th width='60%'>Details</th>
            <th width='30%'>Total</th>
          </tr>
          <tr>
            <td>V-`+id+`</td>
            <td>Installment Ammount</td>
            <td>`+ammount+`</td>
          </tr>
          </table>
          <br>
          <strong style='text-align:center;font-size:16px'>Total:`+ammount+`(Paid)</strong>
       `;
       debit=0;
       credit=0;
      hmtl=
      header=`<strong style='font-size:20px;text-align:center;margin-top:25px;'>Installment Money Receipt</strong>
              <p style='text-align:center;font-size:10px;'><strong>Name: </strong>`+$('#customer option:selected').text()+`</p>
             <div style='text-align:right;margin-right:30px;font-size:12px;'>Print Date: `+dateFormat(new Date())+` </div>`;
      footer=`<div></div>`

    var head = HtmlToPdfMake(header);
    var val = HtmlToPdfMake(html,{
              tableAutoSize:true
            });
    var footer = HtmlToPdfMake(footer);
        var dd = {pageMargins:[20,80,20,40],pageSize:'C7',content:val,header:head,footer:footer};
    MakePdf.createPdf(dd).open();
  $('.buffer').addClass('d-none');
  // document.getElementById('myForm').reset()
  $('.submit').attr('disabled',false);
  Reset();
  })
  .catch((error)=>{
    console.log(error)
  })
}

function dateFormat(date){
let date_ob = date;
let dates = ("0" + date_ob.getDate()).slice(-2);
let month = ("0" + (date_ob.getMonth() + 1)).slice(-2);
let year = date_ob.getFullYear();
return(dates + "-" + month + "-" + year);
}
function Reset(){
   document.getElementById('myForm').reset()
   $("select").val("").change();
   $('select').removeClass('is-invalid');
   $('input').removeClass('is-invalid');
}
</script>
@endsection
