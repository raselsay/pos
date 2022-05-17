@extends('layouts.master')
@section('content')
@section('link')
<style>
  .buffer{
    height: 20px;
    width:20px;
  }
</style>
@endsection
@php
  $info=DB::table('information')->select('company_name','logo','phone','adress')->get()->first();
  $lang=App\MultiSetting::select('value')->where('name','language')->first();
  App::setLocale(isset($lang->value) ? $lang->value : '' );
@endphp
<div class="container">
  <div class="card m-0">
    <div class="card-header pt-3  flex-row align-items-center justify-content-between">
      <h5 class="m-0 font-weight-bold">@lang('key.purchase.opening_stock.title')<img class='buffer float-right d-none' src="{{asset('storage/admin-lte/dist/img/buffer.gif')}}" alt=""></h5>
     </div>
    <div class="card-body px-3 px-md-5">
    <form>
      <div class="row">
        <div class="col-12 col-md-2">
          <div class="form-group">
            <label class="font-weight-bold">@lang('key.purchase.purchase.total_item'):</label>
            <input disabled=""  class="form-control form-control-sm" id="total_item">
          </div>
        </div>
        <div class="col-12 col-md-8">
          <div class="form-group ">
            <label class="font-weight-bold">@lang('key.purchase.purchase.total_item')</label>
            <input type="text" onkeyup="hitBarcode(this)" class="form-control form-control-sm" placeholder='Barcode Here' id="barcode">
          </div>
        </div>
        <div class="col-12 col-md-2">
          <div class="form-group float-right">
            <label class="font-weight-bold">@lang('key.purchase.purchase.date'):</label>
            <input  class="form-control form-control-sm" id="date">
          </div>
        </div>
      </div>
    </form>
<!--<button class="btn btn-sm btn-primary mb-3" id="add_item">Add Product</button> -->
        <table width="100%" class="table-sm table-bordered" id="sales-table">
            <thead>
                  <tr>
                        <th class="text-center" width="15%">@lang('key.purchase.purchase.table.product')</th>
                        <th class="text-center" width="15%">Store</th>
                        <th class="text-center" width="15%">@lang('key.purchase.purchase.table.stock')</th>
                        <th class="text-center" width="15%">@lang('key.purchase.purchase.table.qantity')</th>
                        <th class="text-center" width="15%">@lang('key.purchase.purchase.table.price')</th>
                        <th class="text-center" width="15%">@lang('key.purchase.purchase.table.total')</th>
                        <th class="text-center" width="10%">@lang('key.purchase.purchase.table.action')</th>
                  </tr>
                
            </thead>
        <tbody>
<!--               <form name='invoice[]' id='invoice'>-->
<!--                @csrf -->
        </tbody> 
      </table>
      <button class="btn btn-sm btn-primary mb-3 float-right" id="add_item">+</button>
      <div class="row footer-form mt-5">
            <div class="col-12 col-md-4">
                {{-- <table>
                  <tr>
                    <td class="font-weight-bold">Total Item:</td>
                    <td>
                      <input type="text" disabled="" class="form-control-sm form-control" id="total_item">
                    </td>
                  </tr>
                </table> --}}
                <button class="btn btn-sm btn-primary text-center mb-3 mt-3 submit" type="submit" onclick="submit()" id="submit">@lang('key.buttons.save')</button>
<!--               </form> -->
                {{--invoice slip modal here --}}
                {{-- /invoic modal --}}
            </div>
      </div>
    </div>
  </div>
</div>
@endsection
@section('script')
<script src='{{asset('js/pdf.js')}}'></script>
<script type="text/javascript">
$(document).ready(function(){
  $('#barcode').focus()
  $('#supplier').select2({
    theme:'bootstrap4',
    placeholder:'select',
    allowClear:true,
    ajax:{
      url:"{{URL::to('admin/search_supplier')}}",
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
 let count=0;
 let i=0;
//add item function 
function addItem(datx={id:null,text:null,qty:null,buy_price:null}){
  if(datx.id!=null){
    el=$("[name='item[]'] [value="+datx.id+"]"+"option:selected")
    if(el.length>0){
      q=el.parent().parent().next().next().children().val();
      el.parent().parent().next().next().children().val(parseInt(q)+1);
      calculation();
      return false;
    }
  }
  count=count+1;
  i=i+1;
  var html='<tr>';
      html+="<td><select class='form-control form-control-sm item' type='text' name='item[]' id='item"+i+"' data-allow-clear='true'><option value='' selected>Select</option></select></td>";
      html+="<td><select class='form-control form-control-sm store' type='text' name='store[]' id='store"+i+"' data-allow-clear='true'><option value='' selected>Select</option></select></td>";
      html+="<td><input class='form-control form-control-sm text-right qantity'  type='text' placeholder='0.00' name='av_qty[]' disabled id='av_qty"+i+"'></td>";
      html+="<td><input class='form-control form-control-sm text-right qantity'  type='number' placeholder='0.00' name='qantity[]' id='qantity"+i+"' value='1'></td>";
      html+="<td><input class='form-control form-control-sm text-right price'  type='number' placeholder='0.00' name='price[]' id='price"+i+"'></td>";
      html+="<td><input class='form-control form-control-sm text-right total'  type='text' placeholder='0.00' name='total[]' id='total"+i+"'></td>";
      html+="<td class='text-center'><button id='remove' class='btn btn-sm btn-danger'>X</button></td>";
      html+='</tr>';
  $('#sales-table tbody').append(html);
  $('#total_item').val(count);
  $('#item'+i).select2({
      theme:"bootstrap4",
      allowClear:true,
      placeholder:'select',
      ajax:{
      url:"{{URL::to('admin/select2')}}",
      type:'post',
      dataType:'json',
      delay:20,
      data:function(params){
        return {
          searchTerm:params.term,
          _token:'{{csrf_token()}}',
          }
      },
      processResults:function(response){
        item=$("select[name='item[]'] option:selected")
                  .map(function(){return $(this).val();}).get();
         res=response.map(function(currentValue, index, arr){
          if (item.includes(currentValue.id)){
            response[index]['disabled']=true;
          }
        });
        return {
          results:response,
        }
      },
      cache:true,
    }
  })
  $('#store'+i).select2({
      theme:"bootstrap4",
      allowClear:true,
      placeholder:'select',
      ajax:{
      url:"{{URL::to('admin/get_store_by_user')}}",
      type:'post',
      dataType:'json',
      delay:20,
      data:function(params){
        return {
          searchTerm:params.term,
          _token:'{{csrf_token()}}',
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
  if(datx.id!=null){
    $('#item'+i).html("<option selected value='"+datx.id+"'>"+datx.text+"</option>").trigger('change');
    $('#av_qty'+i).val(datx.qty);
    $('#price'+i).val(datx.price);
  }
  calculation()
}
//............end add item function...........
function hitBarcode(this_val){
  reg = new RegExp('^[0-9]+$');
  data=parseInt(this_val.value);
  if(reg.test(data)){
    $(this_val).attr('disabled',true);
    axios.get('admin/product_by_barcode/'+data)
    .then((res)=>{
      $(this_val).attr('disabled',false)
      $(this_val).val('');
      $(this_val).focus();
      if(res.data[0]){
        addItem(res.data[0]);
      }
    })
  }
}
//............remove item function............
function remove(){
  count=0;
  $('#sales-table tbody').children().remove();
  $('.card-body input').val('');
  $(".card-body select option[value='']").attr('selected',true);
  $(".card-body select").val(null).change();
  $('#date').daterangepicker({
        showDropdowns: true,
        singleDatePicker: true,
        parentEl: ".bd-example-modal-lg .modal-body",
        locale: {
            format: 'DD-MM-YYYY',
        }
  });
  $('#barcode').focus();
}

// get product wise price
$('body').on('select2:select',"select[name='store[]']", function (e){
  store_id=e.params.data.id;
  this_cat=$(this);
  product_id=this_cat.parent().prev().children("[name='item[]']").val();
  if (store_id=='' || product_id=='') {
    return false;
  }
 axios.get('admin/product_qantity/'+product_id+'/'+store_id)
      .then(function(response){
            this_cat.parent().next().children("[name='av_qty[]']").val(response.data[0].total);
          })
          .catch(function(error){
          console.log(error.request);
        })
 })
// select item and  find qntity and price
$('body').on('select2:select',"select[name='item[]']", function (e){
  id=e.params.data.id;
  store_id=$(this).parent().next().children("[name='store[]']").val();
  this_cat=$(this);
 axios.get('admin/product_price_by_id/'+id)
      .then(function(response){
            this_cat.parent().next().next().next().next().children("[name='price[]']").val(response.data);
            calculation();
          })
          .catch(function(error){
          console.log(error.request);
        })
  if(store_id==''){
    return false;
  }
  axios.get('admin/product_qantity/'+id+'/'+store_id)
      .then(function(response){
            this_cat.parent().next().next().children("[name='av_qty[]']").val(response.data[0].total);
          })
          .catch(function(error){
          console.log(error.request);
        })
 })
//<=======end category wise product==========>
$('#add_item').click(function(){
  addItem();
});
$('tbody').on('click','#remove',function(){
  $(this).parent().parent().remove();
  count=count-1;
  $('#total_item').val(count);
  calculation();  
})
function totalCalculation(){
  total_payable=parseFloat($('#final_total').val());
  discount=parseFloat($('#discount').val());
  vat=parseFloat($('#vat').val());
  labour=parseFloat($('#labour').val());
  if (!isNaN(total_payable)) {
    if (isNaN(discount)) {
      discount=0;
    }
    if (isNaN(vat)) {
      vat=0;
    }
    if (isNaN(labour)) {
      labour=0;
    }
    total_payableX=(total_payable*discount)/100;
    vat=(total_payable*vat)/100;
    $('#total_payable').val(((total_payable-total_payableX)+labour+vat).toFixed(2));
  }
}
function calculation(){
  let x=0;
  let totalcal=0;
  var total_item=$('#total_item').val();
  var qantity=$("input[name='qantity[]']")
              .map(function(){return (($(this).val()=='')? 0:$(this).val());}).get();
 $("input[name='price[]']")
  .map(function(){
      price=(($(this).val()=='')? 0:$(this).val())
      total=parseFloat(price)*parseFloat(qantity[x]);
      if (!isNaN(total)) {
      $(this).parent().next().children("input[name='total[]']").val(total)
      totalcal+=total;
      $('#final_total').val(totalcal);
      $('#total_payable').val(totalcal);
      totalCalculation();
      }
    x=x+1;
  }).get();
}
$(document).on('keyup change','.price, .qantity',function(e){
    calculation();
})
$(document).on('keyup','#discount,#vat,#labour',function(){
  totalCalculation()
});
// show Modal with data
function CreatePdf(inv_id){
  isValid=Validate();
  if(isValid){
      products = $("select[name='item[]'] option:selected")
                  .map(function(){return $(this).text();}).get();
      qantities = $("input[name='qantity[]']")
                  .map(function(){return $(this).val();}).get();
      prices = $("input[name='price[]']")
                  .map(function(){return $(this).val();}).get();
      total = $("input[name='total[]']")
                  .map(function(){return $(this).val();}).get();
      totalx=$('#final_total').val();
      total_item=$('#total_item').val();
      discount=$('#discount').val();
      vat=$('#vat').val();
      labour=$('#labour').val();
      total_payable=$('#total_payable').val();
      pay=$('#pay').val();
      x=[{product:products,qantities,prices,total}];
      html=`
      <table style='font-weight:bold;'>
        <tr style='border:none;' bgcolor='#4395D1'><td>Invoice ID</td><td> : `+inv_id+`</td></tr>
        <tr style='border:none;'><td>Date</td><td> : `+$('#date').val()+`</td></tr>
        <tr style='border:none;'><td>Customer</td><td> : `+$('#customer option:selected').text()+`</td></tr>
      </table>
      <h6 style='text-align:center;font-size:15px'>Product List</h6>
      <table style='font-size:10px;'>
      <tr style='text-align:center;width:25%'>
        <th>Product Name</th>
        <th>Qantity</th>
        <th>Price</th>
        <th>Total</th>
      </tr>
      `;
      for (var i=0;i<products.length; i++) {
        html+="<tr style='text-align:center;width:25%'>";
        html+="<td>"+x[0]['product'][i]+"</td>";
        html+="<td>"+x[0]['qantities'][i]+"</td>";
        html+="<td>"+x[0]['prices'][i]+"</td>";
        html+="<td>"+x[0]['total'][i]+"</td>";
        html+="</tr>";
      }
      html+=`</table>
        <table>
            <tr style='border:none;'><td>Total</td><td> : `+totalx+` /=</td></tr>
            <tr style='border:none;'><td>Total Item</td><td> : `+total_item+`</td></tr>
            <tr style='border:none;'><td>Total Payable</td><td> : `+(total_payable ? total_payable : 0)+` /=</td></tr>
            <tr style='border:none;'><td>Payment</td><td> : `+(pay ? pay : 0)+` /=</td></tr>
        </table>
        <h5 style='background-color:black;color:white;text-align:center;padding:10px;'>
        `+PaymentCheck(total_payable,pay)+`
        </h5>
        <h5 style='background-color:black;color:white;text-align:center;padding:10px;'>
        Balance :`+(parseFloat($('#c_bal').text())-(parseFloat(total_payable)-parseFloat(pay)))+`
        </h5>
      `;
      header=`<div style='text-align:center;line-height:0.1;'>
                  <h6 style='margin-top:30px;line-height:0.5;'>`+'{{$info->company_name}}'+`</h6>
                  <p style-'font-size:12px;'>`+'{{$info->adress}}'+`</p>
                  <p style-'font-size:12px;'>Mobile:`+'{{$info->phone}}'+`</p>
              </div>
               <div style='text-align:right;margin-right:30px;font-size:12px;'>Print Date : `+dateFormat(new Date())+` 
                </div>`;
      footer=`<div style='margin-top:50px;'><p style='text-align:center;font-size:10px;color:#808080;'>Powered By : DevTunes Technology || 01731186740</p></div>`
       // var head = HtmlToPdfMake(header);
    var val = HtmlToPdfMake(html,{
              tableAutoSize:true
            });
    val[0].table.body[0][0].fillColor='#4395D1';
    var header = HtmlToPdfMake(header,{
              // tableAutoSize:true
            });
    var footer = HtmlToPdfMake(footer);
        var dd = {info:{title:'invoice_'+inv_id+(new Date()).getTime()},pageMargins:[20,100,20,40],pageSize:'A5',content:val,header:header,footer:footer};
    MakePdf.createPdf(dd).open();
    }
    function PaymentCheck(payable,pay){
      payablex=parseInt(payable)
      payx=parseInt(pay)
      switch(true){
        case payablex===payx:
        return 'Paid';
        break;
        case payablex<payx:
        return 'Over Paid';
        break;
        case payablex>payx:
        t=(parseFloat(payable)-parseFloat(pay)).toFixed(2)
        ta=t.toString().split('.');
        return 'Due:'+t+'/= ('+n2words(ta[0])+' point '+n2words(ta[1])+')';
        break;
      }
    }
    // function WordConv(num){
    //   num=num.toString().split('.');
    //   return (n2words(num[0]))+" point "+(n2words(num[1]))
    // }
  }
// validate all fields
function Validate(){
  let isValid=true;
  let i=0;
$('#customer').removeClass('is-invalid');
if($('#customer').val()==''){
  isValid=false
  $('#customer').addClass('is-invalid');
}
$("input[name='qantity[]']").each(function(){
  $(this).removeClass('is-invalid');
if ($(this).val()==''){
  isValid=false;
  $(this).addClass('is-invalid');
}else{
  i=i+1;
}
})
$("input[name='price[]']").each(function(){
  $(this).removeClass('is-invalid');
if ($(this).val()=='') {
  isValid=false;
  $(this).addClass('is-invalid');
}
})
$("select[name='item[]']").each(function(){
  $(this).removeClass('is-invalid');
if ($(this).val()=='') {
  isValid=false;
  $(this).addClass('is-invalid');
}
});
return isValid;
}
function submit(){
   isValid=Validate();
   // isValid=true;
   
if (isValid==true) {
  $('.buffer').removeClass('d-none');
  $('.submit').attr('disabled',true);
       qan=document.getElementsByName('qantity[]');
   qantities = $("input[name='qantity[]']")
              .map(function(){return $(this).val();}).get();
   prices = $("input[name='price[]']")
              .map(function(){return $(this).val();}).get();
   items = $("select[name='item[]']")
              .map(function(){return $(this).val();}).get();
   store = $("select[name='store[]']")
              .map(function(){return $(this).val();}).get();

   supplier=$('#supplier').val();
   date=$('#date').val();
   total_payable=$('#total_payable').val();
   total_item=$('#total_item').val();
   transport_cost=$('#transport_cost').val();
   transport=$('#transport').val();
   purchase_type=$('#purchase_type').val();
   total=$('#final_total').val();
   payment_method=$('#payment_method').val();
   transaction=$('#transaction_id').val();
   pay=$('#pay').val();
    formData=new FormData();
    formData.append('qantities[]',qantities);
    formData.append('prices[]',prices);
    formData.append('product[]',items);   
    formData.append('store[]',store);
    formData.append('date',date);
    formData.append('total_item',total_item);
    axios.post('admin/opening_stock',formData)
    .then(function(response){
      $('.buffer').addClass('d-none');
      if (!response.data.message){
        keys=Object.keys(response.data[0]);
        html='';
        for (var i = 0; i <keys.length; i++) {
          html+="<p style='color:red;line-height:1px;font-size:12px;'>"+response.data[0][keys[i]][0]+"</p>";
        }
        // alert(html);
        Swal.fire({
          title: 'Error !',
          icon:false,
          html:html,
          showCloseButton: true,
          showCancelButton: false,
          focusConfirm: false,
          confirmButtonText:'Ok',
        })
        $('.submit').attr('disabled',false);
      }else if(response.data.message){
        window.toastr.success(response.data.message);
        remove();
        $('.submit').attr('disabled',false);
      }
    })
    .catch(function(error){
      $('.submit').attr('disabled',false);
    })
  }
}
//datepicker.................

$('#date').daterangepicker({
        showDropdowns: true,
        singleDatePicker: true,
        parentEl: ".bd-example-modal-lg .modal-body",
        locale: {
            format: 'DD-MM-YYYY',
        }
  });

function dateFormat(date){
let date_ob = date;
let dates = ("0" + date_ob.getDate()).slice(-2);
let month = ("0" + (date_ob.getMonth() + 1)).slice(-2);
let year = date_ob.getFullYear();
return(dates + "-" + month + "-" + year);
}
 </script>
@endsection