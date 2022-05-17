@php
  $lang=App\MultiSetting::select('value')->where('name','language')->first();
  App::setLocale(isset($lang->value) ? $lang->value : '' );
@endphp
@extends('layouts.master')
@section('content')
@section('link')
<style>
 .ml{
  margin-left: 1px;
  margin-right: 1px;
 }
 .submit{
  margin:0 auto;
 }
 .buffer{
  height: 30px;
  width: 30px;
 }
 #blnc{
  text-align:right;
 }
</style>
@endsection
<div class="container">
	<div class="card m-0">
    <div class="card-header pt-3  flex-row align-items-center justify-content-between">
      <h5 class="m-0 font-weight-bold">@lang('key.ledger_sheet.title')<img class="float-right buffer d-none" src="{{asset('storage/admin-lte/dist/img/buffer.gif')}}" alt=""></h5>
      
     </div>
    <div class="card-body px-3 px-md-5">
      <form id="myForm">
        <div class="input-group">
         <label class="control-label col-sm-2 text-lg-right" for="category">@lang('key.ledger_sheet.category') :</label>
         <div class="col-sm-9">
            <select class="form-control form-control-sm" onchange="getName(this)" name="category" id="category">
            <option value="">--select--</option>
              @foreach($categories as $category)
            <option value="{{$category->id}}">{{$category->name}}</option>
            @endforeach
          </select>
             <div id="category_msg" class="invalid-feedback">
             </div>
          </div>
       </div>
        <div class="input-group d-none mt-2 name">
         <label class="control-label col-sm-2 text-lg-right" for="name" id="name_text"></label>
         <div class="col-sm-9">
             <select type="text" class="form-control form-control-sm mr-lg-2" id="name" placeholder="Enter Product Name....">
             </select>
             <div id="name_msg" class="invalid-feedback">
             </div>
          </div>
       </div>
       <div class="row ml mt-1">       
        <label class="col-sm-2 text-lg-right">@lang('key.ledger_sheet.date') :</label>
        <div class="input-group input-group-sm col-sm-9" id="date">
            <div class="input-group-prepend">
              <span class="input-group-text" id="inputGroup-sizing-sm">@lang('key.ledger_sheet.from') :</span>
            </div>
            <input class="form-control form-control-sm" name="fromDate" id="fromDate">
            <div class="input-group-prepend">
              <span class="input-group-text" id="inputGroup-sizing-sm">@lang('key.ledger_sheet.to') :</span>
            </div>
            <input class="form-control form-control-sm" name="toDate" id="toDate">
        </div>
      </div>
    </form>
      <div class=" text-center mt-2">
          <button class="btn btn-sm btn-primary submit"  onclick="ajaxRequest()">@lang('key.ledger_sheet.create_report')</button>
      </div>
    </div>
  </div>
</div>
@endsection
@section('script')
<script src="{{asset('js/pdf.js')}}"></script>
<script>

function getName(data){
  selected=data.options[data.selectedIndex].text;
  $('#name option').remove();
  $('.name').removeClass('d-none');
  if (data.value==''){
  $('#name option').remove();
  return false;
  }
  $('#name_text').text(selected+' :');
    dataURL="{{URL::to('admin/relation_search')}}/"+data.value;
  $('#name').select2({
    theme:'bootstrap4',
    placeholder:'select',
    allowClear:true,
    ajax:{
      url:dataURL,
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
// request for data
function ajaxRequest(){
  $('.buffer').removeClass('d-none');
  $('.submit').attr('disabled',true);
  let category_name=$('#category option:selected').text();
  let category=$('#category').val();
  let id=$('#name').val();
  let name=$('#name option:selected').text();
  let fromDate=$('#fromDate').val();
  let toDate=$('#toDate').val();
  let formData=new FormData();
  formData.append('category',category);
  formData.append('id',id);
  formData.append('fromDate',fromDate);
  formData.append('toDate',toDate);
  axios.post('admin/running-total',formData)
        .then(function(res){
        if (res.data.errors) {
          var keys=Object.keys(res.data.errors);
          for(var i=0; i<keys.length;i++){
              $('#'+keys[i]+'_msg').html(res.data.errors[keys[i]][0]);
              $('#'+keys[i]).addClass('is-invalid');
              $('#'+keys[i]).css('border','1px solid red');
              $('#'+keys[i]+'_msg').show();
              $('.submit').attr('disabled',false);
              $('.buffer').addClass('d-none');
            }
            return false;
        }
       data=res.data.get;
   let html='';
       html+='<table>'
       html+="<thead>"
       html+="<tr style='text-align:center;font-size:12px;height:12px;'>"
       html+="<th width='7%'>@lang('key.report-common.no')</th>"
       html+="<th width='10%'>@lang('key.report-common.date')</th>"
       html+="<th width='15%'>@lang('key.report-common.details')</th>"
       html+="<th width='10%'>@lang('key.report-common.id')</th>"
       html+="<th width='10%'>@lang('key.report-common.qantity')</th>"
       html+="<th width='10%'>@lang('key.report-common.price')</th>"
       html+="<th width='10%'>@lang('key.report-common.debit')</th>"
       html+="<th width='10%'>@lang('key.report-common.credit')</th>"
       html+="<th width='18%'>@lang('key.report-common.balance')</th>"
       html+='</tr>'
       html+="</thead>"
       html+="<tbody style='font-size:12px;text-align:center;'>"
       let balance=0;
      for (var i = 0; i < data.length; i++){
        // $('#test').text(i+=i);
        html+=`<tr style='height:12px;'>
                <td>`+(i+1)+`</td>
                <td>`+((data[i]['dates']!='') ? dateFormat(new Date(data[i]['dates']*1000)) : '')+`</td>
                <td>`+data[i]['product_name']+`</td>
                <td>`+data[i]['id']+`</td>
                <td>`+data[i]['deb_qantity']+`</td>
                <td>`+data[i]['price']+`</td>
                <td>`+data[i]['debit']+`</td>
                <td>`+data[i]['credit']+`</td>
                <td>`+((balance+=(data[i]['debit']-data[i]['credit'])).toFixed(2))+`</td>
               </tr>`
      }
       html+="</tbody>"
       html+=`<tfoot>
              <tr>
                <th colspan="6"></th>
                <th colspan="3" style='text-align:right;'>Balance: `+(balance).toFixed(2)+`<span id='curr_blnc'></span></th>
              </tr>
            </tfoot>`;
      header=`<h4 style='text-align:center;margin-top:25px;line-height:0.6;'>@lang('key.ledger_sheet.title')</h4>
              <h6 style='text-align:center;line-height:0.3;'>`+'{{$info->company_name}}'+`</h6>
              <p style='text-align:center;line-height:0.1;'>`+'{{$info->adress}}'+`</p>
              <p style='text-align:center;line-height:0.1;'><strong>@lang('key.report-common.phone'):</strong>`+'{{$info->phone}}'+`</p>
                <p style='text-align:center;font-weight:bold;line-height:0.1;'>`+capitalize(category_name)+` : `+name+`</p>
                <p style='font-size:10px;text-align:center;'>`+dateFormat(new Date(res.data.fromDate*1000))+` @lang('key.report-common.to') `+dateFormat(new Date(res.data.toDate*1000))+`</p>
                <div style='text-align:right;margin-right:30px;font-size:12px;'>@lang('key.report-common.print_date') : `+dateFormat(new Date())+` </div>`;
      footer=`<div style='margin-top:50px;'><p style='text-align:center;font-size:10px;color:#808080;'>Developed by SOFTiIMPIRE || 01715279498</p></div>`

    var head = HtmlToPdfMake(header);
    var val = HtmlToPdfMake(html,{
              tableAutoSize:true
            });
    var footer = HtmlToPdfMake(footer);
        var dd = {info:{title:res.data.name+(new Date()).getTime()},pageMargins:[20,150,20,40],content:val,header:head,footer:footer};
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
    // endpdf
  })
  .catch(function(error){
    console.log(error)
  })
}
 $('#fromDate').daterangepicker({
        showDropdowns: true,
        singleDatePicker: true,
        locale: {
            format: 'DD-MM-YYYY',
            separator:' to ',
            customRangeLabel: "Custom",

        },
        minDate: '01-01-1970',
        maxDate: '01/01/2050'
        
  })
 $('#toDate').daterangepicker({
        showDropdowns: true,
        singleDatePicker: true,
        locale: {
            format: 'DD-MM-YYYY',
            separator:' to ',
            customRangeLabel: "Custom",

        },
        minDate: '01-01-1970',
        maxDate: '01/01/2050'
        
  })
 $('#category').select2({
  theme:"bootstrap4",
  placeholder:"select",
  allowClear:true,
 })
 function capitalize(s){
    return s[0].toUpperCase() + s.slice(1);
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
