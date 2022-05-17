@extends('layouts.master')
@section('content')
@section('link')
<style type="text/css">
  .file {
    border: 1px solid #ccc;
    display: inline-block;
    width: 100px;
    cursor: pointer;
    background-color:green;
    color:white;
}
.file:hover{
  background-color:#fff000;
}
.image-upload{
  margin:0 auto;
}
.input-group{
  margin-top: 5px;
}
</style>
@endsection
@php
$info=DB::table('information')->select('company_name','logo','phone','adress')->get()->first();
$path = asset('storage/logo/'.$info->logo);
$type = pathinfo($path, PATHINFO_EXTENSION);
$data = file_get_contents($path);
$base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
$setting=DB::table('multi_settings')->select('name','value')->get();
foreach($setting as $value){
$settings[$value->name]=$value->value;
}
  $lang=App\MultiSetting::select('value')->where('name','language')->first();
  App::setLocale(isset($lang->value) ? $lang->value : '' );

@endphp
<section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>@lang('key.voucer.voucer.title')</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ URL::to('/home') }}">Home</a></li>
              <li class="breadcrumb-item">Voucer</li>
              <li class="breadcrumb-item active">Manage Voucers</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>
<div class="container">
	<div class="card m-0">
    <div class="card-header pt-3  flex-row align-items-center justify-content-between">
      <h5 class="m-0 font-weight-bold">@lang('key.voucer.voucer.title')</h5>
     </div>
    <div class="card-body px-3 px-md-5">
		  	<button type="button" class="btn btn-primary" data-toggle="modal" data-target=".bd-example-modal-lg" onclick="MasterModal()">
          @lang('key.voucer.voucer.add_new') <i class="fas fa-plus"></i>
        </button>
        {{-- datatable start --}}
        {{-- <div class="container-fluid" id="container-wrapper"> --}}
            <!-- Datatables -->
                <div class="table-responsive mt-2">
                  <table class="table table-sm table-bordered table-striped align-items-center text-center display table-flush data-table">
                    <thead class="thead-dark text-light">
                     <tr>
                        <th>@lang('key.voucer.voucer.no')</th>
                        <th>@lang('key.voucer.voucer.date')</th>
                        <th>@lang('key.voucer.voucer.id')</th>
                        <th>@lang('key.voucer.voucer.category')</th>
                        <th>@lang('key.voucer.voucer.name')</th>
                        <th>@lang('key.voucer.voucer.debit')</th>
                        <th>@lang('key.voucer.voucer.credit')</th>
                        <th>@lang('key.voucer.voucer.bank_name')</th>
                        <th>@lang('key.voucer.voucer.action')</th>
                    </tr>
                    </thead>
                    <tbody class="bg-secondary">
                    </tbody>
                  </table>
                </div>
        {{-- datatable end --}}
    </div>
  </div>
</div>
@endsection
@section('script')
<script type="text/javascript">
   $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }

    });
    $('.data-table').DataTable({
        processing:true,
        serverSide:true,
        ajax:{
          url:"{{ URL::to('/admin/voucer') }}"
        },
        columns:[
          {
            data:'DT_RowIndex',
            name:'DT_RowIndex',
            orderable:false,
            searchable:false
          },
          {
            data:'dat',
            name:'dat',
          },
          {
            data:'v_id',
            name:'v_id',
          },
          {
            data:'category',
            name:'category',
          },
          {
            data:'name',
            name:'name',
          },
          {
            data:'debit',
            name:'debit',
          },
          {
            data:'credit',
            name:'credit',
          },
          {
            data:'bank_name',
            name:'bank_name',
          },
          {
            data:'action',
            name:'action',
          },
       ]
    });
// read Image 
 function readURL(input) {
      if (input.files && input.files[0]) {
          var reader = new FileReader();
          reader.onload = function (e) {
            document.getElementById('imagex').setAttribute('src', e.target.result)
          };
          reader.readAsDataURL(input.files[0]);
      }
   }
 //ajax request from employee.js
function ajaxRequest(){
    $('.invalid-feedback').hide();
    $('input').css('border','1px solid rgb(209,211,226)');
    $('select').css('border','1px solid rgb(209,211,226)');
    let date=$('#date').val();
    let category=$('#category').val();
    let data=$('#data').val();
    let payment_type=$('#payment_type').val();
    let bank=$('#bank').val();
    let ammount=$('#ammount').val();
    let formData= new FormData();
    formData.append('date',date);
    formData.append('category',category);
    formData.append('data',data);
    formData.append('payment_type',payment_type);
    formData.append('bank',bank);
    formData.append('ammount',ammount);
    //axios post request
  axios.post('/admin/voucer',formData)
  .then(function (response){
    if (response.data.message=='success') {
      window.toastr.success('Purchase Added Success');
      $('.data-table').DataTable().ajax.reload();
      document.getElementById('myForm').reset();
    }
    var keys=Object.keys(response.data[0]);
    for(var i=0; i<keys.length;i++){
        $('#'+keys[i]+'_msg').html(response.data[0][keys[i]][0]);
        $('#'+keys[i]).css('border','1px solid red');
        $('#'+keys[i]+'_msg').show();
      }
  })
   .catch(function (error) {
    console.log(error.request.response);
  });
 }

$('table').on('click','.delete',function(){
     Swal.fire({
  title: "Are you sure?",
  text: "Once deleted, you will not be able to recover this imaginary file!",
  icon: "warning",
  showCancelButton: true,
  // dangerMode: true,
  confirmButtonColor: "#DD6B55",
  cancelButtonText: "CANCEL",
  confirmButtonText: "CONFIRM",
})
.then((isConfirmed) => {
  if (isConfirmed.isConfirmed) {
  var id=$(this).data('id');
    axios.delete('/admin/voucer/'+id,{_method:'DELETE'})
      .then((res)=>{
        if (res.data.message) {
          window.toastr.success(res.data.message);
          $('.data-table').DataTable().ajax.reload();
        }
      })
      .catch((error)=>{
        alert((JSON.parse(error.request.response)).message);
      })
  }
});
 })
$('table').on('click','.print',function(){
   id=$(this).data('id');
   axios.get('admin/get_voucer_data/'+id)
   .then((res)=>{
     voucer=res.data.voucer;
     voucerDetails=res.data.detail;
      details = voucerDetails.map(function(c){return c.details; });
     Swal.fire({
  // title: "Print Chalan ?.",
  // text: "If you want to Print chalan then please Confirm",
  icon: "warning",
  showCancelButton: true,
  // dangerMode: true,
  confirmButtonColor: "#DD6B55",
  cancelButtonText: "Bill Print",
  confirmButtonText: "Chalan Print",
})
.then((isConfirmed) => {
  if (isConfirmed.isConfirmed) {
      v_type='chalan'
      MasterP(id,voucer,voucerDetails,v_type)  
    }else{
      v_type='bill'
      MasterP(id,voucer,voucerDetails,v_type) 
    }
});
     
   })
 })
 function MasterPrint(v_no=100){
      details = voucerDetails.map(function(c){return c.details; });
      qantity = voucerDetails.map(function(c){return c.qantity; });
      ammount = voucerDetails.map(function(c){return c.ammount; });
      date=parseFloat($('#master-date').val());
      category=voucer[0].category;
      deposit=parseFloat(voucer[0].Deposit);
      expence=parseFloat(voucer[0].Expence);
      if (deposit==0.00) {
        payment_type='Expence'
      }else{
        payment_type='Deposit'
      }
      data=voucer[0].name;
      x=[{details,qantity,ammount}];
      total_ammount=0;
      html=`
      <table style='font-size:10px;'>
      <tr style='text-align:center;width:`+((v_type=='chalan') ? '25%' : '50%')+`'>
        <th>Details</th>
        ${(v_type=='chalan') ? '<th>Qantity</th>': '' }
        <th>Ammount</th>
        ${(v_type=='chalan') ? '<th>Total</th>': '' }
      </tr>
      `;
      for (var i=0;i<details.length; i++) {
        html+="<tr style='text-align:center;width:"+((v_type=='chalan') ? '25%' : '50%')+"'>";
        html+="<td>"+x[0]['details'][i]+"</td>";
        html+=((v_type=='chalan') ? "<td>"+(parseFloat(x[0]['qantity'][i])).toFixed(2)+"</td>" :'');
        html+="<td>"+(parseFloat(x[0]['ammount'][i])).toFixed(2)+"</td>";
        html+=((v_type=='chalan') ? "<td>"+((parseFloat(x[0]['qantity'][i]))*(parseFloat(x[0]['ammount'][i]))).toFixed(2)+"</td>" : '');
        html+="</tr>";
        total_ammount+=parseFloat(x[0]['qantity'][i])*parseFloat(x[0]['ammount'][i])
      }
      html+=`</table>
             <table style='margin-left:20.6rem'>
                <tr style='border:none;'><td style='border:1px solid white;'>Total</td><td style='background-color:blue;border:1px solid white;width:115px;'> `+(total_ammount ? total_ammount.toFixed(2) : 0.00)+`</td></tr>
             </table>
      `;
      header=`<img style='width:100px;height:70px;margin-top:30px;margin-left:25px;' src='{{$base64}}'/>
              <span style='margin-left:30px;font-size:22px;'>Voucer No-1`+String(v_no).padStart(9,'0')+`</span>
              <span style='margin-left:30px;font-size:18px;'>{{$info->company_name}}</span>
              <span style='margin-left:30px;font-size:12px;'>{{$info->adress}}</span>
              <span style='margin-left:30px;font-size:12px;margin-bottom:15px;'>{{$info->phone}}</span>
              <span  style='margin-left:30px;font-size:12px;margin-bottom:15px;line-height:0.2;'>Payment Type : `+payment_type+`</span>
              <div style='margin-left:30px;text-decoration:underline;font-weight:bold;font-size:14px;'>
                `+category+`:`+data+`
              </div>
              <span style='font-size:12px;text-align:right;margin-right:30px;'>Date:`+$('#master-date').val()+`</span>`;
      footer=`<div style='margin-top:50px;'>
        <div style='text-align:right;margin-right:30px;font-size:8px;'>Print Date : `+dateFormat(new Date())+`
               </div>
      <p style='text-align:center;font-size:10px;color:#808080;'>Powered By : DevTunes Technology || 01731186740</p>
      </div>`
       // var head = HtmlToPdfMake(header);
    var val = HtmlToPdfMake(html,{
              tableAutoSize:true
            });
    setColor=val[1].table.body;
    for (var i = 0; i < setColor.length; i++) {
      setColor[i][1].fillOpacity=0.1;
    }
    var header = HtmlToPdfMake(header,{
              tableAutoSize:true
            });
    header[0].alignment="center";
        var footer = HtmlToPdfMake(footer);
        var dd = {info:{title:'invoice_'+v_no+(new Date()).getTime()},pageMargins:[20,200,20,40],pageSize:'A5',content:val,header:header,footer:footer};
    MakePdf.createPdf(dd).open();
    MasterModalClose();
    $('.master-submit').attr('disabled',false);
}
 $('#data').select2({
    theme:'bootstrap4',
    placeholder:'select',
    allowClear:true,
    ajax:{
      url:"{{URL::to('admin/search_customer')}}",
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
  // get child category
 function ModalClose(){
  document.getElementById('myForm').reset();
  $('#imagex').attr('src','http://localhost/accounts/public/storage/admin-lte/dist/img/avatar5.png');
  $('.invalid-feedback').hide();
  $('input').css('border','1px solid rgb(209,211,226)');
  $('select').css('border','1px solid rgb(209,211,226)');
 }
 $('.date').daterangepicker({
        showDropdowns: true,
        singleDatePicker: true,
        parentEl: ".bd-example-modal-lg .modal-body",
        locale: {
            format: 'DD-MM-YYYY',
        }
    });
function MasterP(v_id=null){
     details = voucerDetails.map(function(c){return c.details; });
      qantity = voucerDetails.map(function(c){return c.qantity; });
      ammount = voucerDetails.map(function(c){return c.ammount; });
      date=dateFormat(new Date(parseInt(voucer[0].dates)*1000));
      category=voucer[0].category;
      deposit=parseFloat(voucer[0].Deposit);
      expence=parseFloat(voucer[0].Expence);
      if (deposit==0.00) {
        payment_type='Expence'
      }else{
        payment_type='Deposit'
      }
      if(v_type=='chalan'){
        v_type='display:none;'
      }else{
        v_type=''
      }
      data=voucer[0].name;
      x=[{details,qantity,ammount}];
      total_ammount=0;
      html=`<div id="invoice">
            <div class="p-5  bg-primary">
              <table width="100%" class="table-borderless">
                <tr>
                  <td>
                    <img height="80px" width="100px" src="{{$base64}}"><br>
                    <span style="font-size:25px;">`+((payment_type=='Deposit') ? 'Debit Voucer' : 'Credit Voucer')+`</span>
                  </td>
                  <td class="float-right"><span class="font-weight-bold">{{$info->company_name}}</span><br>{{$info->adress}}<br>{{$info->phone}}</td>
                </tr>
              </table>
            </div>
            <div class="mr-5 ml-5 mt-3 mb-3">
              <table width="100%" class="table-borderless font-weight-bold">
                <tr>
                  <td>
                     Date
                  </td>
                  <td class="float-right">`+date+`</td>
                </tr>
                <tr>
                  <td>
                     Bill No.
                  </td>
                  <td class="float-right">`+'1'+String(v_id).padStart(9,'0')+`</td>
                </tr>
                <tr>
                  <td>
                     Category:
                  </td>
                  <td class="float-right">`+category+`</td>
                </tr>
                <tr>
                  <td>
                     Name:
                  </td>
                  <td class="float-right">`+data+`</td>
                </tr>
              </table>
            </div>
            <div id="tables" class="mr-5 ml-5">
              <table class="table  table-sm text-center table-bordered table-primary">
                <tr>
                  <th>Details</th>
                  <th style='`+v_type+`'>Quantity</th>
                  <th>Ammount</th>
                  <th style="`+v_type+`">Total</th>
                </tr>
      `;
      for (var i=0;i<details.length; i++) {
        html+="";
        html+="<td>"+x[0]['details'][i]+"</td>";
        html+="<td style='"+v_type+"'>"+(parseFloat(x[0]['qantity'][i])).toFixed(2)+"</td>";
        html+="<td>"+(parseFloat(x[0]['ammount'][i])).toFixed(2)+"</td>";
        html+="<td style='"+v_type+"'>"+(parseFloat(x[0]['qantity'][i])*parseFloat(x[0]['ammount'][i])).toFixed(2)+"</td>";
        html+="</tr>";
        total_ammount+=parseFloat(x[0]['qantity'][i])*parseFloat(x[0]['ammount'][i])
      }
      html+=`</table>
       </div>
       <div class="mr-5 ml-5 mt-3 mb-3">
    <table width="100%" class="table-striped font-weight-bold">
      <!-- total -->
      <tr>
        <td>
           Total Ammount৳
        </td>
        <td class="text-right">`+total_ammount.toFixed(2)+`</td>
      </tr>
    </table>
    <br>
    <h2>Note.</h2>
    <br>
    <br>
     </div>`
     $(html).printThis({
        importCSS:true,
        printDelay: 333,
        header: "",
        footer:`<p class='text-center'>
                  Software Developed By <strong>SOFTiMPIRE</strong>
                  <br>Adress:Barisal Bottola,Barisal<br>
                  Mobile:01873072253,01310588563
                </p>`,
        base: "noman"
      });
    MasterModalClose();
    $('.master-submit').attr('disabled',false);
}
 </script>
@endsection
