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
  .buffer{
  height: 30px;
  width: 30px;
 }
</style>
@endsection

<div class="container">
	<div class="card m-0">
    <div class="card-header pt-3  flex-row align-items-center justify-content-between">
      <h5 class="m-0 font-weight-bold">Earn Statement <img class="float-right buffer d-none" src="{{asset('storage/admin-lte/dist/img/buffer.gif')}}" alt=""></h5>
     </div>
    <div class="card-body px-3 px-md-5">
      <form>
        <div class="input-group input-group-sm col-md-4 col-12" id="date">
          <div class="input-group-prepend">
            <span class="input-group-text" id="inputGroup-sizing-sm">@lang('key.daily_statement.from')</span>
          </div>
          <input class="form-control" name="fromDate" id="fromDate">
          <div class="input-group-prepend">
            <span class="input-group-text" id="inputGroup-sizing-sm">@lang('key.daily_statement.to')</span>
          </div>
          <input class="form-control" name="toDate" id="toDate">
        </div>
        </form>
        <div class="col-md-2" id="submit">
          <button class="btn btn-sm btn-primary" onclick="Request()">@lang('key.daily_statement.create_report')</button>
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
  axios.post('/admin/earn_statement',{fromDate:fromDate,toDate:toDate})
  .then((res)=>{
    data=res.data.get;
    let html='';
       html+='<table>'
       html+="<thead>"
       html+="<tr style='text-align:center;font-size:10px;height:12px;'>"
       html+="<th width='10%'>@lang('key.report-common.no')</th>"
       html+="<th width='45%'>@lang('key.report-common.category')</th>"
       html+="<th width='45%'>@lang('key.report-common.total')</th>"
       html+='</tr>'
       html+="</thead>"
       html+="<tbody style='font-size:12px;text-align:center;'>"
       total=0;
       expence=0;
      for (var i = 0; i < data.length; i++){
        t=(parseFloat(data[i]['total'])).toFixed(2)
        if(t!=0.00){
          html+=`<tr style='height:12px;'>
                <td>`+i+`</td>
                <td>`+Capitalize(data[i]['category'])+`</td>
                <td>`+(parseFloat(data[i]['total'])).toFixed(2)+`</td>
               </tr>`
          }
        total+=parseFloat(data[i]['total']);
      }
       html+="</tbody>"
       html+=`<tfoot>
              <tr>
                <th colspan="2"></th>
                <th style='text-align:right;font-size:12px;'>@lang('key.report-common.total'): `+total.toFixed(2)+`</th>
              </tr>
            </tfoot>`;
      header=`<h6 style='text-align:center;margin-top:25px;'>Earn Statement</h6>
             <strong style='font-size:10px;text-align:center'>`+dateFormat(new Date(res.data.fromDate*1000))+` @lang('key.report-common.to') `+dateFormat(new Date(res.data.toDate*1000))+`</strong>
             <div style='text-align:right;margin-right:30px;font-size:12px;'>@lang('key.report-common.print_date') : `+dateFormat(new Date())+` </div>`;
      footer=`<div><p style='text-align:center;font-size:10px;'>Developed by SOFTiIMPIRE || 01715279498</p></div>`

    var head = HtmlToPdfMake(header);
    var val = HtmlToPdfMake(html,{
              tableAutoSize:true
            });
    var footer = HtmlToPdfMake(footer);
        var dd = {info:{title:'daily_statement'+(new Date()).getTime()},pageMargins:[20,80,20,40],content:val,header:head,footer:footer};
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
function Capitalize(string) {
  str= string.split(" ");
  st='';
  str.forEach(function(e){
    x=(e.charAt(0).toUpperCase()+e.slice(1));
    st+=x+' ';
  })
  return st;
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