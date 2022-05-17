@php
  $lang=App\MultiSetting::select('value')->where('name','language')->first();
  App::setLocale(isset($lang->value) ? $lang->value : '' );
@endphp
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
    $path = asset('storage/logo/'.$info->logo);
    $type = pathinfo($path, PATHINFO_EXTENSION);
    $data = file_get_contents($path);
    $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
    $setting=DB::table('multi_settings')->select('name','value')->get();
    foreach($setting as $value){
    $settings[$value->name]=$value->value;
}
@endphp
<div class="container">
  <div class="card m-0">
    <div class="card-header pt-3  flex-row align-items-center justify-content-between">
      <h5 class="m-0 font-weight-bold">@lang('key.purchase.purchase.title_update') <img class='buffer float-right d-none' src="{{asset('storage/admin-lte/dist/img/buffer.gif')}}" alt=""></h5>
     </div>
    <div class="card-body px-3 px-md-5">
    <form>
      <input type="hidden" id="payment_id">
      <div class="row">
         <div class="col-12 col-md-3">
          <label class="font-weight-bold mb-1" for="supplier">@lang('key.purchase.purchase.supplier')</label>
          <div class="input-group ">
            <select class="form-control form-control-sm" id="supplier" onchange="getBlnce(this.value)">
            </select>
            <div class="input-group-append">
              <button class="btn btn-sm btn-primary rounded-right" onclick="CustomModal()">ADD+</button>
            </div>
            <span class="p-1 d-none" id="balance">@lang('key.purchase.purchase.balance'):<span id='c_bal'></span></span>
          </div>
        </div>

        <div class="col-12 col-md-3">
          <div class="form-group">
            <label class="font-weight-bold">@lang('key.purchase.purchase.transport')</label>
            <select class="form-control form-control-sm" id="transport">
            </select>
          </div>
        </div>
        <div class="col-12 col-md-2">
          <div class="form-group">
            <label class="font-weight-bold">@lang('key.purchase.purchase.purchase_type')</label>
            <select class="form-control form-control-sm" id="purchase_type">
              <option value="0">@lang('key.purchase.purchase.normal_purchase')</option>
              <option value="1">@lang('key.purchase.purchase.advance_purchase')</option>
              <option value="2">@lang('key.purchase.purchase.purchase_return')</option>
            </select>
          </div>
        </div>
        <div class="col-12 col-md-2">
          <div class="form-group d-none">
            <label class="font-weight-bold">@lang('key.purchase.purchase.issue_date'):</label>
            <input disabled="" class="form-control form-control-sm" id="issue_date">
          </div>
        </div>
        <div class="col-12 col-md-2">
          <div class="form-group">
            <label class="font-weight-bold">@lang('key.purchase.purchase.date'):</label>
            <input class="form-control form-control-sm" id="date">
          </div>
        </div>
        <div class="col-12">
          <div class="form-group">
            <label class="font-weight-bold">@lang('key.purchase.purchase.barcode')</label>
            <input type="text" onkeyup="hitBarcode(this)" class="form-control form-control-sm" id="barcode" placeholder='@lang('key.purchase.purchase.barcode_placeholder')'>
          </div>
        </div>
      </div>
    </form>
<!--<button class="btn btn-sm btn-primary mb-3" id="add_item">Add Product</button> -->
        <table width="100%" class="table-sm table-bordered" id="sales-table">
            <thead>
                  <tr>
                        <th class="text-center" width="15%">@lang('key.purchase.purchase.table.product')</th>
                        <th class="text-center" width="15%">@lang('key.purchase.purchase.table.product')</th>

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
                <table>
                  <tr>
                    <td class="font-weight-bold">@lang('key.purchase.purchase.total'):</td>
                    <td width="50%">
                      <div class="input-group input-group-sm">
                          <input type="text" class="form-control form-control-sm" id="final_total" disabled="">
                          <div class="input-group-append">
                            <span class="input-group-text" id="inputGroupPrepend">৳</span>
                          </div>
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <td class="font-weight-bold">@lang('key.purchase.purchase.total_item'):</td>
                    <td>
                      <input type="text" disabled="" class="form-control-sm form-control mt-1" id="total_item">
                    </td>
                  </tr>
                  <tr>
                    <td class="font-weight-bold">@lang('key.purchase.purchase.labour'):</td>
                    <td>
                      <div class="input-group input-group-sm">
                          <input type="number" class="form-control form-control-sm" id="labour">
                          <div class="input-group-append">
                            <span class="input-group-text" id="inputGroupPrepend">৳</span>
                          </div>
                      </div>
                      </td>
                  </tr>
                  <tr>
                    <td class="font-weight-bold">@lang('key.purchase.purchase.transport_cost'):</td>
                    <td>
                      <div class="input-group input-group-sm">
                          <input type="number" class="form-control form-control-sm" id="transport_cost">
                          <div class="input-group-append">
                            <span class="input-group-text" id="inputGroupPrepend">৳</span>
                          </div>
                      </div>
                      </td>
                  </tr>
                  <tr>
                    <td class="font-weight-bold">@lang('key.purchase.purchase.total_payable'):</td>
                    <td>
                      <div class="input-group input-group-sm">
                          <input type="text" class="form-control form-control-sm" id="total_payable" disabled="">
                          <div class="input-group-append">
                            <span class="input-group-text" id="inputGroupPrepend">৳</span>
                          </div>
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <td class="font-weight-bold">@lang('key.purchase.purchase.payment_method'):</td>
                    <td>
                      <div class="input-group input-group-sm">
                          <select type="text" class="form-control form-control-sm" id="payment_method">
                            <option value="">--SELECT--</option>
                          </select>
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <td class="font-weight-bold">@lang('key.purchase.purchase.transaction'):</td>
                    <td>
                      <div class="input-group input-group-sm">
                          <input type="text" class="form-control form-control-sm" id="transaction_id" placeholder="X33KDLDFXFKJ">
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <td class="font-weight-bold">@lang('key.purchase.purchase.ammount'):</td>
                    <td>
                      <div class="input-group input-group-sm">
                          <input type="text" class="form-control form-control-sm" id="pay">
                          <div class="input-group-append">
                            <span class="input-group-text" id="inputGroupPrepend">৳</span>
                          </div>
                      </div>
                    </td>
                  </tr>
                </table> 
            </div>
            <div class="col-12 col-md-8">
              <div class="form-group">
                <textarea class="form-control form-control-sm mt-1" id="note" rows="3" placeholder="@lang('key.purchase.purchase.note_placeholder')"></textarea>
                <p class="float-right"><span id="writed">0</span><span>/500</span></p>
              </div>
            </div>
      </div> {{-- end row --}}
      <button class="btn btn-sm btn-primary text-center mb-3 mt-3" type="submit" onclick="submit()" id="submit">@lang('key.buttons.save')</button>
      <button class="btn btn-sm btn-warning text-center mb-3 mt-3" type="submit" onclick="submit(1)" id="submit">@lang('key.buttons.save_and_print')</button>
    </div>
  </div>
</div>
@endsection
@section('script')
<script src='{{asset('js/pdf.js')}}'></script>
<script type="text/javascript">
  let invoice=<?php echo $invoice; ?>;
  let sales=<?php echo $sales; ?>;
  let avlqty=<?php echo $avlqty; ?>;
  let count=1;
  let s_length=(sales.length-1);
function InitData(){
  var html='<tr>';
  for ( i = 0; i < invoice.total_item; i++) {
      count=count+i;
      html+="<input type='hidden' name='sale_id[]' value='"+sales[i].id+"'>"
      html+="<td><select class='form-control form-control-sm item' type='text' name='item[]' id='item"+i+"' data-allow-clear='true'><option value='' selected>Select</option></select></td>";
      html+="<td><select class='form-control form-control-sm store' type='text' name='store[]' id='store"+i+"' data-allow-clear='true'><option value='' selected>Select</option></select></td>";
      html+="<td><input type='number' class='form-control form-control-sm text-right qantity'  type='text' placeholder='0.00' name='av_qty[]' disabled id='av_qty"+i+"' value='"+avlqty[i]+"'></td>";
      html+="<td><input type='number' class='form-control form-control-sm text-right qantity'  type='text' placeholder='0.00' name='qantity[]' id='qantity"+i+"' value='"+sales[i].qantity+"'></td>";
      html+="<td><input type='number' class='form-control form-control-sm text-right price'  type='text' placeholder='0.00' name='price[]' id='price"+i+"' value='"+sales[i].price+"'></td>";
      html+="<td><input type='number' class='form-control form-control-sm text-right total'  type='text' placeholder='0.00' name='total[]' id='total"+i+"' value='"+(sales[i].qantity*sales[i].price)+"'></td>";
      html+="<td class='text-center'><button id='remove' class='btn btn-sm btn-danger' disabled>X</button></td>";
      html+='</tr>';
  }
  $('#sales-table tbody').append(html);
  $('#total_item').val(invoice.total_item);
  $('#total_payable').val(invoice.total_payable);
  $('#final_total').val(invoice.total);
  $('#discount').val(invoice.discount);
  $('#transport_cost').val(invoice.transport)
  $('#labour').val(invoice.labour_cost)
  $('#vat').val(invoice.vat);
  $('#pay').val(invoice.ammount)
  $('#payment_id').val(invoice.payment_id);
  $('#note').val(invoice.note);
  $('#purchase_type').val(invoice.action_id).trigger({
      type:'select2:select',
      params: {
        data:{
          id:invoice.action_id
        },
    }
  });
  
  Select2();
}
function Select2(){
    for (i = 0; i <invoice.total_item; i++) {
          $('#item'+i).select2({
            theme:"bootstrap4",
            allowClear:true,
            placeholder:'select',
            tags:true,
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
          $('#item'+i).html("<option value='"+sales[i].product_id+"'>"+sales[i].product_name+"</option>");
        $('#store'+i).select2({
            theme:"bootstrap4",
            allowClear:true,
            placeholder:'select',
            tags:true,
            ajax:{
            url:"{{URL::to('admin/get_store')}}",
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
          $('#store'+i).html("<option value='"+sales[i].store_id+"'>"+sales[i].store_name+"</option>");
    }
  }
$(document).ready(function(){
  $('#barcode').focus();
  InitData()
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
  if (invoice.name!=null){
    $('#supplier').html("<option value='"+invoice.supplier_id+"'>"+invoice.name+"("+invoice.phone+")</option>")
  }
  $('#transport').select2({
    theme:'bootstrap4',
    placeholder:'select',
    allowClear:true,
    ajax:{
      url:"{{URL::to('admin/get_transport_import')}}",
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
  if (invoice.transport_id!=null){
    $('#transport').html("<option value='"+invoice.transport_id+"'>"+invoice.t_name+"("+invoice.t_phone+")</option>")
  }
  $('#payment_method').select2({
    theme:"bootstrap4",
      allowClear:true,
      placeholder:'select',
      ajax:{
      url:"{{URL::to('admin/get_banks')}}",
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
  if (invoice.bank_id!=null) {
      $('#payment_method').html("<option value='"+invoice.bank_id+"'>"+invoice.bank_name+"</option>");
  }
  $('#purchase_type').select2({
    theme:'bootstrap4',
    placeholder:'select',
    allowClear:true,
  })
// getBank()
})
function getBlnce(id){
  if (id=='' || id==null || id==NaN) {
      $('#balance').addClass('d-none');
      return false;
    }
  axios.get('admin/supplier_balance/'+id)
  .then(function(response){
    total=response.data.total;
     switch(true){
        case total>=0:
        $('#c_bal').text(total);
        $('#balance').removeClass('d-none');
        $('#balance').removeClass('bg-danger');
        $('#balance').addClass('bg-success');
        break;
        case total<0:
        $('#c_bal').text(total);
        $('#balance').removeClass('d-none');
        $('#balance').removeClass('bg-success');
        $('#balance').addClass('bg-danger');
        break;
     }
  })
}
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
  s_length=(s_length+1)
  count=count+1
  var html='<tr>';
      html+="<input type='hidden' name='sale_id[]' value='0'>"
      html+="<td><select class='form-control form-control-sm item' type='text' name='item[]' id='item"+s_length+"' data-allow-clear='true'><option value='' selected>Select</option></select></td>";
      html+="<td><select class='form-control form-control-sm store' type='text' name='store[]' id='store"+s_length+"' data-allow-clear='true'><option value='' selected>Select</option></select></td>";
      html+="<td><input class='form-control form-control-sm text-right qantity'  type='text' placeholder='0.00' name='av_qty[]' disabled id='av_qty"+s_length+"'></td>";
      html+="<td><input type='number' class='form-control form-control-sm text-right qantity'  type='text' placeholder='0.00' name='qantity[]' id='qantity"+s_length+"' value='1'></td>";
      html+="<td><input type='number' class='form-control form-control-sm text-right price'  type='text' placeholder='0.00' name='price[]' id='price"+s_length+"'></td>";
      html+="<td><input type='number' class='form-control form-control-sm text-right total'  type='text' placeholder='0.00' name='total[]' id='total"+s_length+"'></td>";
      html+="<td class='text-center'><button id='remove' class='btn btn-sm btn-danger'>X</button></td>";
      html+='</tr>';
  $('#sales-table tbody').append(html);
  $('#total_item').val(count);
  $('#item'+s_length).select2({
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
  $('#store'+s_length).select2({
            theme:"bootstrap4",
            allowClear:true,
            placeholder:'select',
            tags:true,
            ajax:{
            url:"{{URL::to('admin/get_store')}}",
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
    $('#item'+s_length).html("<option selected value='"+datx.id+"'>"+datx.text+"</option>").trigger('change');
   $('#av_qty'+s_length).val(datx.qty)
   $('#price'+s_length).val(datx.buy_price)
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
  $('.card-body input,textarea').val('');
  $(".card-body select").val(null).change();
  $('#sales_type').val(0).change();
  $(".card-body select option[value='']").attr('selected',true);
  $('#date,#issue_date').daterangepicker({
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
  if (parseInt($('#total_item').val())>1) {
  $(this).parent().parent().remove();
  count=count-1;
  $('#total_item').val(count);
  var final_total=parseFloat($("#final_total").val());
  var this_total=$(this).parent().prev().children().val();
  if (isNaN(final_total)){
    final_total=0;
  }else if(isNaN(this_total)){
    this_total=0;
  }else if(isNaN(final_total) && isNaN(this_total)){
    final_total=0;
    this_total=0
  }
  final_total=final_total-this_total
  $("#final_total").val(final_total);
  $("#total_payable").val(final_total);
  }else{
    alert('You cannot remove this item');
  }
  
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
  var total_item=$('#total_item').val();
  var qantity=0;
  var price=0;
  var total=0;
  var final_total=0;
   for (var i = 0; i <= total_item; i++) {
     qantity=$("#qantity"+i).val();
     if (qantity>0) {
      var price=$("#price"+i).val()
      if (price>0) {
        total=qantity*price;
        $("#total"+i).val(total);
        if (total>0) {
          final_total=final_total+parseFloat($("#total"+i).val());
          $('#final_total').val(final_total);
          $("#total_payable").val(final_total);
          totalCalculation();
        }
      }
     }
   }
}
$(document).on('keyup change','.qantity,.price',function(){
  calculation();
})
$(document).on('keyup change','#labour,#transport_cost',function(){
  totalCalculation()
});
$(document).on('keyup change','#note',function(){
  Note();
});
// validate all fields
function Validate(){
  let isValid=true;
$('#customer').removeClass('is-invalid');
if($('#customer').val()==''){
  isValid=false
  $('#customer').addClass('is-invalid');
}
$("input[name='qantity[]']").each(function(){
  $(this).removeClass('is-invalid');
if ($(this).val()=='') {
  isValid=false;
  
  $(this).addClass('is-invalid');
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
$("input[name='store[]']").each(function(){
  $(this).removeClass('is-invalid');
if ($(this).val()=='') {
  isValid=false;
  $(this).addClass('is-invalid');
}
})
return isValid;
}
function submit(print=null){
    isValid=Validate();
  //  isValid=true;
   $('.buffer').removeClass('d-none');
if (isValid==true) {
  $('.submit').attr('disabled',true);
       qan=document.getElementsByName('qantity[]');
   sale_id = $("input[name='sale_id[]']")
              .map(function(){return $(this).val();}).get();
   qantities = $("input[name='qantity[]']")
              .map(function(){return $(this).val();}).get();
   prices = $("input[name='price[]']")
              .map(function(){return $(this).val();}).get();
   items = $("select[name='item[]']")
              .map(function(){return $(this).val();}).get();
   store = $("select[name='store[]']")
              .map(function(){return $(this).val();}).get();
   discounts = $("input[name='discount[]']")
              .map(function(){return $(this).val();}).get();
   supplier=$('#supplier').val();
   date=$('#date').val();
   issue_date=$('#issue_date').val();
   total_payable=$('#total_payable').val();
   total_item=$('#total_item').val();
   discount=$('#discount').val();
   vat=$('#vat').val();
   labour=$('#labour').val();
   transport_cost=$('#transport_cost').val();
   transport=$('#transport').val();
   purchase_type=$('#purchase_type').val();
   total=$('#final_total').val();
   payment_method=$('#payment_method').val();
   transaction=$('#transaction_id').val();
   payment_id=$('#payment_id').val();
   pay=$('#pay').val();
   note=$('#note').val();
    formData=new FormData();
    formData.append('sale_id[]',sale_id);
    formData.append('qantities[]',qantities);
    formData.append('prices[]',prices);
    formData.append('product[]',items);    
    formData.append('store[]',store);
    formData.append('supplier',supplier);
    formData.append('issue_date',issue_date);
    formData.append('date',date);
    formData.append('total_payable',total_payable);
    formData.append('total_item',total_item);
    formData.append('payment_id',payment_id);
    @if($settings['invoice_discount']==1)
    formData.append('discount',discount);
    @endif
    @if($settings['invoice_vat']==1)
    formData.append('vat',vat);
    @endif
    @if($settings['invoice_labour']==1)
    formData.append('labour',labour);
    @endif
    @if($settings['invoice_transport']==1)
    formData.append('transport_cost',transport_cost);
    @endif
    formData.append('transport',transport);
    formData.append('purchase_type',purchase_type);
    formData.append('total',total);
    formData.append('payment_method',payment_method);
    formData.append('transaction',transaction);
    formData.append('pay',pay);
    axios.post('admin/purchase-update/'+invoice.id,formData)
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
      }else if(response.data.message){
        window.toastr.success(response.data.message);
        if(print==1){
          printObject(response.data.id,response.data.balance);
        }else{
          remove();
          $('.submit').attr('disabled',false);
        }
      }
    })
    .catch(function(error){
      console.log(error);
    })
  }
}
//datepicker.................
$('#date').daterangepicker({
        showDropdowns: true,
        singleDatePicker: true,
        startDate: dateFormat(new Date(parseInt(invoice.dates)*1000)),
        parentEl: ".bd-example-modal-lg .modal-body",
        locale: {
            format:'DD-MM-YYYY',
        }
});
$('#issue_date').daterangepicker({
        showDropdowns: true,
        singleDatePicker: true,
        startDate: dateFormat((isNaN(parseInt(invoice.issue_date)*1000) ? new Date(): new Date(parseInt(invoice.issue_date)*1000))),
        parentEl: ".bd-example-modal-lg .modal-body",
        locale: {
            format: 'DD-MM-YYYY',
        }
});
$('#dates').val(parseInt(invoice.dates)*1000)
$('#purchase_type').on("select2:select", function(e){
  if (e.params.data.id==1) {
    $('#issue_date').parent().removeClass('d-none');
    $('#issue_date').attr('disabled',false)
  }else{
    $('#issue_date').parent().addClass('d-none');
    $('#issue_date').attr('disabled',true);
  }
})
function dateFormat(date){
let date_ob = date;
let dates = ("0" + date_ob.getDate()).slice(-2);
let month = ("0" + (date_ob.getMonth() + 1)).slice(-2);
let year = date_ob.getFullYear();
return(dates + "-" + month + "-" + year);
}
function Note(){
    text=($('#note').val()).toString();
    $('#writed').removeClass('text-danger')
    if(text.length>500){
      $('#writed').addClass('text-danger')
    }
    $('#writed').text(text.length)
  }
// Print Invoice
function printObject(inv_id=null,balance){
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
      totalx=parseFloat($('#final_total').val());
      total_item=$('#total_item').val();
      discount=parseFloat($('#discount').val());
      vat=parseFloat($('#vat').val());
      labour=parseFloat($('#labour').val());
      transport=parseFloat(parseFloat($('#transport_cost').val()));
      total_payable=parseFloat($('#total_payable').val());
      pay=parseFloat($('#pay').val());
      balance=parseFloat(balance)
      x=[{product:products,qantities,prices,total}];
      if (isNaN(pay)){
          pay=parseFloat(0);
      }
      s_type=parseFloat($('#purchase_type').val());
      c_bal=parseFloat($('#c_bal').text());
      due=total_payable-pay;
      x=[{product:products,qantities,prices,total}];
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
           @lang('key.purchase.purchase.purchase_type')
        </td>
        <td style="float:right;">`+$('#purchase_type option:selected').text()+`</td>
      </tr>
      <tr>
        <td>
           @lang('key.purchase.purchase.date').
        </td>
        <td style="float:right;">`+$('#date').val()+`</td>
      </tr>
      <tr style='`+(($('#sales_type').val()!=1) ? 'display:none;' : '')+`'>
        <td>
           @lang('key.purchase.purchase.issue_date').
        </td>
        <td style="float:right;">`+$('#issue_date').val()+`</td>
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
        <td style="float:right;">`+$('#supplier option:selected').text()+`</td>
      </tr>
    </table>
  </div>
  <div id="tables" style='margin-right:50px;margin-left:50px;'>
    <table width="100%" style="text-align:center;border:1px solid grey;">
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
        html+="<td style='border:1px solid grey'>"+(parseFloat(x[0]['total'][i])).toFixed(2)+"</td>";
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
      </tr style='background-color:#F1F1F1'>
      <!-- Discount -->
      <tr style='background-color:#F1F1F1'>
        <td>
            @lang('key.purchase.purchase.discount') %
        </td>
        <td style="text-align:right;">`+(discount ? ((discount*totalx)/100).toFixed(2) :0.00 )+`</td>
      </tr>
      <!-- Vat -->
      <tr>
        <td>
            @lang('key.purchase.purchase.vat') %
        </td>
        <td style="text-align:right;">`+(vat ? ((vat*totalx)/100).toFixed(2) : 0.00)+`</td>
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
      <tr style="`+((PaymentCheck(total_payable,pay)['value']=='Paid' || PaymentCheck(total_payable,pay)['value']=='Over Paid') ? 'color:green;' : 'color:red;')+`">
        <td>
            @lang('key.purchase.purchase.pay_status') ৳
        </td>
        <td style="text-align:right;" style='line-height:0.5;'>`+PaymentCheck(total_payable,pay)['value']+`<br><strong style='font-size:10px;'>`+PaymentCheck(total_payable,pay)['text']+`<strong></td>
      </tr>
    </table>
    <br>
    <h2>@lang('key.purchase.purchase.note').</h2>
    <p>`+$('#note').val()+`</p>
     </div>`
     $(html).printThis({
        printDelay: 333,
        header: "",
        footer:`<p style="text-align:center;">
                  Software Developed By <strong>SOFTiMPIRE</strong>
                  <br>Adress:Barisal Bottola,Barisal<br>
                  Mobile:01873072253,01310588563
                </p>`,
        base: "noman"
      });
     remove();
     $('.submit').attr('disabled',false);
  }
  function PaymentCheck(payable,pay=0){
      payablex=parseInt(payable)
      payx=parseInt(pay)
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
          t=Math.abs(balance).toFixed(2);
          ta=t.toString().split('.');
          text= n2words(ta[0])+' point '+n2words(ta[1]);
          arr['value']='Due : '+ t;
          arr['text']=text;
        return arr;
        break;
      }
    }
}
 </script>
@endsection