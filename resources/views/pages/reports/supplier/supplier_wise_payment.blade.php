@extends('layouts.master')
@section('content')
@section('link')
@php
  $lang=App\MultiSetting::select('value')->where('name','language')->first();
  App::setLocale(isset($lang->value) ? $lang->value : '' );
@endphp
<style>
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
      <h5 class="m-0 font-weight-bold">Supplier Wise Payment</h5>
      </div>
    <div class="card-body px-3 px-md-5">
      <form>
       <div class="row ml mt-1">       
        <label class="col-sm-2 text-lg-right">@lang('key.supplier_wise_purchase.date') :</label>
        <div class="input-group input-group-sm col-sm-9" id="date">
            <div class="input-group-prepend">
              <span class="input-group-text" id="inputGroup-sizing-sm">@lang('key.supplier_wise_purchase.from') :</span>
            </div>
            <input class="form-control form-control-sm" name="fromDate" id="fromDate">
            <div id="fromDate_msg" class="invalid-feedback">
             </div>
            <div class="input-group-prepend">
              <span class="input-group-text" id="inputGroup-sizing-sm">@lang('key.supplier_wise_purchase.to') :</span>
            </div>
            <input class="form-control form-control-sm" name="toDate" id="toDate">
            <div id="toDate_msg" class="invalid-feedback">
             </div>
        </div>
      </div>
      </form>
        <div class="text-center" id="submit">
          <button class="btn btn-sm btn-primary" onclick="Request()">@lang('key.supplier_wise_purchase.create_report')</button>
          <button class="btn btn-sm btn-warning" onclick="Request(1)">Download CSV</button>
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
 $('#product').select2({
    theme:'bootstrap4',
    placeholder:'select',
    allowClear:true,
    ajax:{
      url:"{{URL::to('admin/select2')}}",
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
function Request(cond=null){
  $('#fromDate,#toDate,#type').removeClass('is-invalid');
  fromDate=$('#fromDate').val()
  toDate=$('#toDate').val()

  axios.post('/admin/supplier_wise_payment',{fromDate:fromDate,toDate:toDate})
  .then((res)=>{
    if (res.data.errors) {
      var keys=Object.keys(res.data.errors);
      for(var i=0; i<keys.length;i++){
          $('#'+keys[i]+'_msg').html(res.data.errors[keys[i]][0]);
          $('#'+keys[i]).addClass('is-invalid');
          // $('#'+keys[i]).css('border','1px solid red');
          $('#'+keys[i]+'_msg').show();
          $('.submit').attr('disabled',false);
          $('.buffer').addClass('d-none');
        }
        return false;
    }
    data=res.data.get;
    if(cond==1){
      let datax = [
                {
                  sheet: 'customer_wise_total_sale',
                  columns: [
                    { label: 'Name', value: 'name' },
                    { label: 'Phone', value: 'phone'},
                    { label: 'Adress', value:'adress'},
                    { label: 'Total', value:'total'},
                  ],
                  content: data,
                },
              ]
            let settings = {
            fileName: 'supplier_wise_payment'+fromDate+'_to_'+toDate,
            extraLength: 3, 
            writeOptions: {} 
          }
          xlsx(datax, settings)
          return false;
    }
    let html='';
       html+='<table>'
       html+="<thead>"
       html+="<tr style='text-align:center;font-size:12px;height:12px;'>"
       html+="<th width='10%'>@lang('key.report-common.no')</th>"
       html+="<th width='35%'>Supplier Name</th>"
       html+="<th width='15%'>Phone</th>"
       html+="<th width='25%'>Adress</th>"
       html+="<th width='15%'>Total</th>"
       html+='</tr>'
       html+="</thead>"
       html+="<tbody style='font-size:12px;text-align:center;'>"
       price=0;
       qantity=0;
       total=0;
      for (var i = 0; i < data.length; i++){
        html+=`<tr style='height:12px;'>
                <td>`+(i+1)+`</td>
                <td>`+data[i]['name']+`</td>
                <td>`+data[i]['phone']+`</td>
                <td>`+data[i]['adress']+`</td>
                <td>`+data[i]['total']+`</td>
               </tr>`
        qantity+=parseFloat(data[i]['qantity']);
        price+=parseFloat(data[i]['total']);
      }
       html+="</tbody>"
       html+=`<tfoot>
                <tr>
                  <th colspan="4"></th>
                  <th style='text-align:right'>`+price.toFixed(2)+`</th>
                </tr>
              </tfoot>`;
      header=`<h5 style='text-align:center;margin-top:30px;line-height:0.6'>Supplier Wise Payment</h5>
              <strong style='font-size:10px;text-align:center'>`+dateFormat(new Date(res.data.fromDate*1000))+` @lang('key.report-common.to') `+dateFormat(new Date(res.data.toDate*1000))+`</strong>
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