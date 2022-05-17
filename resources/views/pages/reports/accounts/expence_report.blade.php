@extends('layouts.master')
@section('content')
@section('link')
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
      <h5 class="m-0 font-weight-bold">Daily Statement <img class="float-right buffer d-none" src="{{asset('storage/admin-lte/dist/img/buffer.gif')}}" alt=""></h5>
     </div>
    <div class="card-body px-3 px-md-5">
      <form>
        <div class="input-group input-group-sm col-md-4 col-12" id="date">
          <div class="input-group-prepend">
            <span class="input-group-text" id="inputGroup-sizing-sm">from</span>
          </div>
          <input class="form-control" name="fromDate" id="fromDate">
          <div class="input-group-prepend">
            <span class="input-group-text" id="inputGroup-sizing-sm">To</span>
          </div>
          <input class="form-control" name="toDate" id="toDate">
        </div>
        </form>
        <div class="col-md-2" id="submit">
          <button class="btn btn-sm btn-primary" onclick="Request()">Create Report</button>
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
  axios.post('/admin/expence_report',{fromDate:fromDate,toDate:toDate})
  .then((res)=>{
    data=res.data.get;
    let html='';
       html+='<table>'
       html+="<thead>"
       html+="<tr style='text-align:center;font-size:10px;height:12px;'>"
       html+="<th width='10%'>ID</th>"
       html+="<th width='10%'>Date</th>"
       html+="<th width='10%'>Bank Name</th>"
       html+="<th width='15%'>Category</th>"
       html+="<th width='25%'>Name</th>"
       html+="<th width='15%'>Debit</th>"
       html+="<th width='15%'>Credit</th>"
       html+='</tr>'
       html+="</thead>"
       html+="<tbody style='font-size:12px;text-align:center;'>"
       deposit=0;
       expence=0;
      for (var i = 0; i < data.length; i++){
        html+=`<tr style='height:12px;'>
                <td>`+i+`</td>
                <td>`+dateFormat(new Date(data[i]['dates']*1000))+`</td>
                <td>`+data[i]['bank_name']+`</td>
                <td>`+data[i]['category']+`</td>
                <td>`+data[i]['name']+`</td>
                <td>`+(parseFloat(data[i]['Deposit'])).toFixed(2)+`</td>
                <td>`+(parseFloat(data[i]['Expence'])).toFixed(2)+`</td>
               </tr>`
        deposit+=parseFloat(data[i]['Deposit']);
        expence+=parseFloat(data[i]['Expence']);
      }
       html+="</tbody>"
       html+=`<tfoot>
              <tr>
                <th colspan="5"></th>
                <th style='text-align:right;font-size:12px;'>Total: `+deposit.toFixed(2)+`</th>
                <th style='text-align:right;font-size:12px;'>Total: `+expence.toFixed(2)+`</th>
              </tr>
                <th colspan='7' style='text-align:right;font-size:12px;'>Grand Total: `+(parseFloat(res.data.total)).toFixed(2)+`</th>
              <tr>
              </tr>
            </tfoot>`;
      header=`<h6 style='text-align:center;margin-top:25px;'>Company Daily Statement</h6>
             <strong style='font-size:10px;text-align:center'>`+dateFormat(new Date(res.data.fromDate*1000))+` to `+dateFormat(new Date(res.data.toDate*1000))+`</strong>
             <div style='text-align:right;margin-right:30px;font-size:12px;'>Print Date : `+dateFormat(new Date())+` </div>`;
      footer=`<div><p style='text-align:center;font-size:10px;'>DevTunes Technology || 01731186740</p></div>`

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
function dateFormat(date){
let date_ob = date;
let dates = ("0" + date_ob.getDate()).slice(-2);
let month = ("0" + (date_ob.getMonth() + 1)).slice(-2);
let year = date_ob.getFullYear();
return(dates + "-" + month + "-" + year);
}
</script>
@endsection
