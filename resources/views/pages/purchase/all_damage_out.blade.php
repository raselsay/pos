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
.control-label{
  padding-right: 15px;
}
.input-group{
  margin-top: 5px;
}
.form-control:focus{
  background-color: rgb(188, 248, 240);
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
 $lang=App\MultiSetting::select('value')->where('name','language')->first();
  App::setLocale(isset($lang->value) ? $lang->value : '' );
}
@endphp
<div class="container">
	<div class="card m-0">
    <div class="card-header pt-3  flex-row align-items-center justify-content-between">
      <h5 class="m-0 font-weight-bold">Damage Out List</h5>
     </div>
    <div class="card-body px-3 px-md-5">
        <!-- Modal --> 
        <a class="btn btn-primary text-light font-weight-bold mb-2" href="{{URL::to('admin/damage_out')}}">
          New Damage Out<i class="ml-1 fas fa-plus"></i>
        </a>
        <a class="btn btn-primary text-light font-weight-bold mb-2 float-right" href="{{URL::to('admin/stock_ledger')}}">
          Stock Ledger<i class="ml-1 fas fa-plus"></i>
        </a>
        <table class="table table-sm text-center table-bordered table-striped data-table">
          <thead>
            <tr>
              <th>@lang('key.purchase.purchase_list.no')</th>
              <th>@lang('key.purchase.purchase_list.date')</th>              
              <th>INV-ID</th>              
              <th>Products</th>
              <th>@lang('key.purchase.purchase_list.action')</th>
            </tr>
          </thead>
          <tbody>
          </tbody>
        </table>
    </div>
  </div>
</div>
@endsection
@section('script')
<script type="text/javascript">
   $('.data-table').DataTable({
        processing:true,
        serverSide:true,
        ajax:{
          url:"{{ URL::to('/admin/all_damage_out') }}"
        },
        columns:[
          {
            data:'DT_RowIndex',
            name:'DT_RowIndex',
            orderable:false,
            searchable:false
          },
          {
            data:'dates',
            name:'dates',
          },
          {
            data:'id',
            name:'id',
          },
          {
            data:'products',
            name:'products',
          },
          {
            data:'action',
            name:'action',
          }
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
    axios.delete('/admin/damage_out/'+id,{_method:'DELETE'})
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
   axios.get('admin/purchase_data/'+id)
   .then((res)=>{
     invoice=res.data.invoice;
     sales=res.data.sales;
     balance=res.data.balance;
     printObject(id,invoice,sales,balance)
   })
 })
function printObject(inv_id,invoice,sales,balance){
  isValid=true;
  if(isValid){
      products = sales.map(function(c){return c.product_name; });
      qantities = sales.map(function(c){return c.qantity; });
      prices = sales.map(function(c){return c.price; });
      discounts = sales.map(function(c){return c.discount; });
      total = sales.map(function(c){return parseFloat((c.price-(c.price*c.discount)/100)*c.qantity); });
      totalx=parseFloat(invoice.total);
      total_item=parseFloat(invoice.total_item);
      vat=parseFloat(invoice.vat);
      labour=parseFloat(invoice.labour);
      transport=parseFloat(invoice.transport);
      total_payable=parseFloat(invoice.total_payable);
      pay=parseFloat(invoice.ammount);
      c_name=invoice.name+'('+invoice.phone+')';
      s_type=parseInt(invoice.action_id);
      if(s_type==0){
        s_type_name="@lang('key.purchase.purchase.normal_purchase')"
      }else if(s_type==2){
        s_type_name="@lang('key.purchase.purchase.purchase_return')"
      }else{
        s_type_name="@lang('key.purchase.purchase.advance_purchase')"
      }
      if (isNaN(pay)){
          pay=parseFloat(0);
      }
      x=[{product:products,qantities,prices,total,discounts}];
      html=`
<div id="invoice">
  <div style='background-color:#007BFF;padding:50px;color:white;'>
    <table width="100%" style="border:none;">
      <tr>
        <td>
          <img height="80px" width="100px" src="{{$base64}}"><br>
          <span style="font-size:25px;">@lang('key.invoice.invoice.invoice')</span>
        </td>
        <td style="float:right;"><span style="font-weight:bold;">{{$info->company_name}}</span><br>{{$info->adress}}<br>{{$info->phone}}</td>
      </tr>
    </table>
  </div>
  <div style="margin-right:50px;margin-left:50px;margin-top:30px;margin-bottom:30px;">
    <table width="100%" style="border:none;font-weight:bold;">
      <tr>
        <td>
           @lang('key.purchase.purchase.purchase_type').
        </td>
        <td style="float:right;">`+s_type_name+`</td>
      </tr>
      <tr>
        <td>
           @lang('key.purchase.purchase.date').
        </td>
        <td style="float:right;">`+dateFormat(new Date(parseInt(invoice.dates)*1000))+`</td>
      </tr>
      <tr class='`+((s_type)==1 ? '' : 'd-none')+`'>
        <td>
          @lang('key.purchase.purchase.issue_date').
        </td>
        <td style="float:right;">`+dateFormat(new Date(parseInt(invoice.issue_date)*1000))+`</td>
      </tr>
      <tr>
        <td>
           @lang('key.purchase.purchase.bill_no').
        </td>
        <td style="float:right;">`+'1'+String(inv_id).padStart(9,'0')+`</td>
      </tr>
      <tr>
        <td>
           @lang('key.purchase.purchase.supplier')
        </td>
        <td style="float:right;">`+c_name+`</td>
      </tr>
    </table>
  </div>
  <div id="tables" style='margin-right:50px;margin-left:50px;'>
    <table width='100%' style="text-align:center;border:1px solid grey;">
      <tr>
        <th style='border:1px solid grey'>@lang('key.purchase.purchase.table.product')</th>
        <th style='border:1px solid grey'>@lang('key.purchase.purchase.table.qantity')</th>
        <th style='border:1px solid grey'>@lang('key.purchase.purchase.table.price')</th>
        <th style='border:1px solid grey'>@lang('key.purchase.purchase.table.total')</th>
      </tr>

      `;
      for (var i=0;i<products.length; i++) {
        html+="";
        html+="<td style='border:1px solid grey'>"+x[0]['product'][i]+"</td>";
        html+="<td style='border:1px solid grey'>"+(parseFloat(x[0]['qantities'][i])).toFixed(2)+"</td>";
        html+="<td style='border:1px solid grey'>"+(parseFloat(x[0]['prices'][i])).toFixed(2)+"</td>";
        html+="<td style='border:1px solid grey'>"+(parseFloat(x[0]['prices'][i])*parseFloat(x[0]['qantities'][i])).toFixed(2)+"</td>";
        html+="</tr>";
      }
      html+=`</table>
       </div>
       <div style='margin-right:50px;margin-left:50px;margin-top:30px;margin-bottom:30px;'>
    <table width="100%" style="color:black;font-weight:bold">
      <!-- total -->

      <tr style='background-color:#F1F1F1'>
        <td>
           @lang('key.purchase.purchase.total') ৳
        </td>
        <td style="text-align:right;">`+totalx.toFixed(2)+`</td>
      </tr>
      <!-- total item -->
      <tr>
        <td>
            @lang('key.purchase.purchase.total_item')
        </td>
        <td style="text-align:right;">`+total_item+`</td>
      </tr>
      <!-- Labour Cost -->
      <tr style='background-color:#F1F1F1'>
        <td>
            @lang('key.purchase.purchase.labour') ৳
        </td>
        <td style="text-align:right;"> `+(labour ? labour.toFixed(2) :0.00)+`</td>
      </tr>
      <!-- Transport Cost -->
      <tr>
        <td>
            @lang('key.purchase.purchase.transport_cost') ৳
        </td>
        <td style="text-align:right;">`+(transport ? transport.toFixed(2) :0.00)+`</td>
      </tr>
      <!-- Total Payable -->
      <tr style='background-color:#F1F1F1'>
        <td>
             @lang('key.purchase.purchase.total_payable') ৳
        </td>
        <td style="text-align:right;">`+(total_payable ? total_payable.toFixed(2) : 0.00)+`</td>
      </tr>
      <!-- Payment -->
      <tr>
        <td>
            @lang('key.purchase.purchase.payment') ৳
        </td>
        <td style="text-align:right;"> `+(pay ? (pay).toFixed(2) : 0.00)+`</td>
      </tr>
      <!-- balance -->
      <tr style='background-color:#F1F1F1'>
        <td>
            @lang('key.purchase.purchase.balance') ৳
        </td>
        <td style="text-align:right;">`+balance+`</td>
      </tr>
      <!-- Due -->
      <tr style="`+(PaymentCheck(total_payable,pay)['value']=='Paid' || PaymentCheck(total_payable,pay)['value']=='Over Paid' ? 'color:green;' : 'color:red;')+`">
        <td>
            @lang('key.purchase.purchase.pay_status') ৳
        </td>
        <td style="text-align:right;" style='line-height:0.5;'>`+PaymentCheck(total_payable,pay)['value']+`<br><strong style='font-size:10px;'>`+PaymentCheck(total_payable,pay)['text']+`<strong></td>
      </tr>
    </table>
    <br>
    <h2>@lang('key.purchase.purchase.note').</h2>
    <p>`+((invoice.note!=null)? invoice.note : '' )+`</p>
     </div>`
     $(html).printThis({
        // importCSS:true,
        // loadCSS:[''],
        printDelay: 333,
        header: "",
        footer:`<p style="text-align:center;">
                  Software Developed By <strong>SOFTiMPIRE</strong>
                  <br>Adress:Barisal Bottola,Barisal<br>
                  Mobile:01873072253,01310588563
                </p>`,
        base: "noman"
      });
  }
  function PaymentCheck(payable,pay=0){
      payablex=parseInt(payable)
      payx=parseInt(pay)
      balance=parseFloat(balance).toFixed(2)
      arr=[];
      switch(true){
        case payable<pay:
        arr['value']='Over Paid';
        arr['text']='';
        return arr;
        break;
        case payablex===payx || payable===pay || balance<=0.00:
        arr['value']='Paid';
        arr['text']='';
        return arr;
        break;
        case balance>0.00:
        t=balance;
        ta=t.toString().split('.');
        text= n2words(Math.abs(ta[0]))+' point '+n2words(ta[1]);
        arr['value']='Due : '+ Math.abs(t).toFixed(2);
        arr['text']=text;
        return arr;
        break;
      }
    }
}
 </script>
@endsection
