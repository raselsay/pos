@extends('layouts.master')
@section('content')
@section('link')
@php
  $lang=App\MultiSetting::select('value')->where('name','language')->first();
  App::setLocale(isset($lang->value) ? $lang->value : '' );
@endphp
<style>
  #date{
    margin:0 auto;
  }
  #submit{
    margin:0 auto;
    margin-top: 20px;
  }
  #blnc{
    float:right;
  }
  .img-buffer{
  height: 30px;
  width: 30px;
 }
</style>
@endsection
<div class="container">
	<div class="card m-0">
    <div class="card-header pt-3  flex-row align-items-center justify-content-between">
      <h5 class="m-0 font-weight-bold">@lang('key.cash_details.title')<img class="float-right buffer img-buffer d-none" src="{{asset('storage/admin-lte/dist/img/buffer.gif')}}" alt=""></h5>
     </div>
    <div class="card-body px-3 px-md-5">
      <h2 class="buffer d-none">@lang('key.cash_details.message')</h2>
      <div class="p-2">
      <button class="btn btn-primary" onclick="Request()">@lang('key.cash_details.create_report')</button>
      </div>
    </div>
    
  </div>
</div>
@endsection
@section('script')
<script src="{{asset('js/pdf.js')}}"></script>
<script>
  // $(document).ready(()=>{
  //   Request();
  // })
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
  $('.buffer').removeClass('d-none');
  fromDate=$('#fromDate').val()
  toDate=$('#toDate').val()
  axios.get('/admin/cash_details')
  .then((res)=>{
    data=res.data.get;
    let html='';
       html+='<table>'
       html+="<thead>"
       html+="<tr style='text-align:center;font-size:12px;height:12px;'>"
       html+="<th width='10%'>@lang('key.report-common.no')</th>"
       html+="<th width='60%'>@lang('key.report-common.name')</th>"
       html+="<th width='30%'>@lang('key.report-common.balance')</th>"
       html+='</tr>'
       html+="</thead>"
       html+="<tbody style='font-size:12px;text-align:center;'>"
       let total=0;
       x=1;
      for (var i = 0; i < data.length; i++){
        total+=parseFloat(data[i]['total'])
        html+=`<tr style='height:12px;'>
                <td>`+(x++)+`</td>
                <td>`+data[i]['name']+`</td>
                <td>`+data[i]['total']+`</td>
               </tr>`;
               
      }
       html+="</tbody>"
       html+=`<tfoot>
              <tr>
                <th></th>
                <th></th>
                <th style='text-align:right;font-size:12px;'>@lang('key.report-common.total'): `+(total.toFixed(2))+`</th>
              </tr>
            </tfoot>`;
      header=`<h4 style='text-align:center;margin-top:30px;'>@lang('key.cash_details.title')</h4>
              <h6 style='text-align:center;line-height:0.3;'>`+'{{$info->company_name}}'+`</h6>
              <p style='text-align:center;line-height:0.1;'>`+'{{$info->adress}}'+`</p>
              <p style='text-align:center;line-height:0.1;'>@lang('key.report-common.phone') :`+'{{$info->phone}}'+`</p>
             <div style='text-align:right;margin-right:30px;font-size:12px;'>@lang('key.report-common.print_date') : `+dateFormat(new Date())+` </div>`;
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
