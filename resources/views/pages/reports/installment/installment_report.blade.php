@extends('layouts.master')
@section('content')
@section('link')
@php
  $lang=App\MultiSetting::select('value')->where('name','language')->first();
  App::setLocale(isset($lang->value) ? $lang->value : '' );
@endphp
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

<div class="container">
	<div class="card m-0">
    <div class="card-header pt-3  flex-row align-items-center justify-content-between">
      <h5 class="m-0 font-weight-bold">@lang('key.installment.title')</h5>
     </div>
    <div class="card-body px-3 px-md-5">
      <form>
        <div class="input-group">
           <label class="control-label col-sm-2 text-lg-right" for="type">@lang('key.installment.customer'):</label>
           <div class="col-sm-9">
              <select class="form-control form-control-sm" name="type" id="customer">
              </select>
               <div id="report_name_msg" class="invalid-feedback">
               </div>
            </div>
        </div>
      </form>
        <div class="col-md-2" id="submit">
          <button class="btn btn-sm btn-primary" onclick="Request()">@lang('key.installment.create_report')</button>
        </div>
            </div>
  </div>
</div>
@endsection
@section('script')
<script src="{{asset('js/pdf.js')}}"></script>
<script>
 // $('#date').daterangepicker({
 //        showDropdowns: true,
 //        locale: {
 //            format: 'DD-MM-YYYY',
 //            separator:' to ',
 //            customRangeLabel: "Custom",
 //        },
 //        minDate: '01-01-1970',
 //        maxDate: '01/01/2050'
 //  })
 $(document).ready(function(){
  
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
 function getInvoice(this_val){
    axios.get('admin/get_installment_report/'+this_val.value)
    .then((res)=>{
      total_ins=parseFloat(res.data.invoice[0].insmnt_total_days)
      total_payable=parseFloat(res.data.invoice[0].total_payable)
      type=parseFloat(res.data.invoice[0].insmnt_type);
      dates=res.data.invoice[0].dates;
      issue_dates=parseFloat(res.data.invoice[0].issue_dates);
      var dates=new Date(dates*1000);
      var x=0;
    })
 }
$('#fromDate').daterangepicker({
  showDropdowns:true,
 singleDatePicker: true,
 locale: {
    format: 'DD-MM-YYYY',
  },
 minDate: '01-01-1970',
 maxDate: '01-01-2050'
});
$('#toDate').daterangepicker({
 showDropdowns:true,
 singleDatePicker: true,
 locale: {
    format: 'DD-MM-YYYY',
  },
  minDate: '01-01-1950',
  maxDate: '01-01-2050'
});

function Request(){
  id=$('#customer').val()
  axios.get('/admin/get_installment_report/'+id)
  .then((res)=>{
    invoice=res.data.invoice[0];
    voucer=res.data.voucers;
    issue_dates=new Date(invoice.issue_dates*1000)
    let html='';
       html+='<table>'
       html+="<thead>"
       html+="<tr style='text-align:center;font-size:12px;height:12px;'>"
       html+="<th width='10%'>@lang('key.report-common.no')</th>"
       html+="<th width='10%'>@lang('key.report-common.date')</th>"
       html+="<th width='10%'>@lang('key.report-common.paid_date')</th>"
       html+="<th width='10%'>@lang('key.report-common.inv_id')</th>"
       html+="<th width='25%'>@lang('key.report-common.name')</th>"
       html+="<th width='10%'>@lang('key.report-common.status')</th>"
       html+="<th width='10%'>@lang('key.report-common.paid')</th>"
       html+="<th width='15%'>@lang('key.report-common.payable')</th>"
       html+='</tr>'
       html+="</thead>"
       html+="<tbody style='font-size:12px;text-align:center;'>"
       debit=0;
       credit=0;
       increment=0;
       switch(true){
        case invoice.insmnt_type==1:
          for (var i = 1; i <= invoice.insmnt_total_days; i++){
              html+=`<tr style='height:12px;'>
                      <td>`+i+`</td>
                      <td>`+dateFormat(new Date(issue_dates.setMonth((issue_dates.getMonth()+increment))))+`</td>
                      <td>`+((voucer[i-1]) ? dateFormat(new Date(voucer[i-1].dates*1000)) : 'Not Pay')+`</td>
                      <td>INV-`+invoice['id']+`</td>
                      <td>`+invoice['name']+`</td>
                      <td>`+((voucer[i-1]) ? 'Paid' : 'Not Pay')+`</td>
                      <td>`+((voucer[i-1]) ?  voucer[i-1]['debit'] : '0.00')+`</td>
                      <td>`+(parseInt((parseFloat(invoice.total_payable)-parseFloat((invoice.total_payable*invoice.insmnt_pay_percent)/100))/parseFloat(invoice.insmnt_total_days)).toFixed(2))+`</td>
                     </tr>`
                     increment=1;
            }
          break;
          case invoice.insmnt_type==0:
          for (var i = 1; i <= invoice.insmnt_total_days; i++){
              html+=`<tr style='height:12px;'>
                      <td>`+i+`</td>
                      <td>`+dateFormat(new Date(issue_dates.setDate((issue_dates.getDate()+increment))))+`</td>
                      <td>`+((voucer[i-1]) ? dateFormat(new Date(voucer[i-1].dates*1000)) : 'Not Pay')+`</td>
                      <td>INV-`+invoice['id']+`</td>
                      <td>`+invoice['name']+`</td>
                      <td>`+((voucer[i-1]) ? 'Paid' : 'Not Pay')+`</td>
                      <td>`+((voucer[i-1]) ?  voucer[i-1]['debit'] : '0.00')+`</td>
                      <td>`+(parseInt((parseFloat(invoice.total_payable)-parseFloat((invoice.total_payable*invoice.insmnt_pay_percent)/100))/parseFloat(invoice.insmnt_total_days)).toFixed(2))+`</td>
                     </tr>`
                     increment=7;
              }
              break;
       }
      
       html+="</tbody>"
      header=`<h5 style='text-align:center;margin-top:25px;line-height:0.6;'>@lang('key.installment.title')<br></h5>
              <h6 style='text-align:center;line-height:0.3;'>`+'{{$info->company_name}}'+`</h6>
              <p style='text-align:center;line-height:0.1;'>`+'{{$info->adress}}'+`</p>
              <p style='text-align:center;line-height:0.1;'>@lang('key.report-common.phone'):`+'{{$info->phone}}'+`</p>
              <p style='text-align:center;line-height:0.1;'><strong>@lang('key.invoice.invoice.customer') : </strong>`+$('#customer option:selected').text()+`</p>
             <div style='text-align:right;margin-right:30px;font-size:12px;'>@lang('key.report-common.print_date'): `+dateFormat(new Date())+` </div>`;
      footer=`<div><p style='text-align:center;font-size:10px;'>Developed by SOFTiIMPIRE || 01715279498</p></div>`
    var head = HtmlToPdfMake(header);
    var val = HtmlToPdfMake(html,{
              tableAutoSize:true
            });
    var footer = HtmlToPdfMake(footer);
        var dd = {pageMargins:[20,120,20,40],content:val,header:head,footer:footer};
        pdfMake.fonts = {
         Roboto: {
               normal: '{{asset('fonts/Baloo-Da/Bangla-Regular.ttf')}}',
               bold: '{{asset('fonts/Baloo-Da/Bangla-SemiBold.ttf')}}',
               italic: '{{asset('fonts/Baloo-Da/Bangla-Medium.ttf')}}',
                },
        };
    MakePdf.createPdf(dd).open();
  $('.buffer').addClass('d-none');
  // document.getElementById('myForm').reset()
  $('.submit').attr('disabled',false);
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

</script>
@endsection
