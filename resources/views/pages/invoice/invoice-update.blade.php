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
    $lang=App\MultiSetting::select('value')->where('name','language')->first();
    App::setLocale(isset($lang->value) ? $lang->value : '' );
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
      <h5 class="m-0 font-weight-bold">{{__('key.invoice.invoice.title_update')}} <img class='buffer float-right d-none' src="{{asset('storage/admin-lte/dist/img/buffer.gif')}}" alt=""></h5>
     </div>
    <div class="card-body px-3 px-md-5">
      <div id="CustomModal"></div>
      <input type="hidden" id="payment_id">
      <div class="row">
         <div class="col-12 mb-2">
          <label class="font-weight-bold mb-1" for="customer">{{__('key.invoice.invoice.customer')}}</label>
          <div class="input-group ">
            <select class="form-control form-control-sm" id="customer" onchange="getBlnce(this.value)">
            </select>
            <div class="input-group-append">
              <button class="btn btn-sm btn-primary rounded-right" onclick="CustomModal()">{{__('key.invoice.invoice.customer')}}+</button>
            </div>
            <span class="p-1 d-none" id="balance">{{__('key.invoice.invoice.balance')}} :<span id='c_bal'></span></span>
          </div>
        </div>
        <div class="col-12 col-md-3">
          <div class="form-group">
            <label class="font-weight-bold">Customer Site</label>
            <select type="text" onkeyup="hitBarcode(this)" class="form-control form-control-sm" placeholder='' id="site">
            </select>
          </div>
        </div>
        <div class="col-12 col-md-3">
          <div class="form-group">
            <label class="font-weight-bold">{{__('key.invoice.invoice.transport')}}</label>
            <select class="form-control form-control-sm" id="transport">
            </select>
          </div>
        </div>
        <div class="col-12 col-md-2">
          <div class="form-group">
            <label class="font-weight-bold">{{__('key.invoice.invoice.sale_type')}}</label>
            <select class="form-control form-control-sm" id="sales_type">
              <option value="0">{{__('key.invoice.invoice.normal_sale')}}</option>
              <option value="1">{{__('key.invoice.invoice.advance_sale')}}</option>
              <option value="2">{{__('key.invoice.invoice.sale_return')}}</option>
            </select>
          </div>
        </div>
        <div class="col-12 col-md-2">
          <div class="form-group d-none">
            <label class="font-weight-bold">{{__('key.invoice.invoice.issue_date')}}:</label>
            <input disabled="" class="form-control form-control-sm" id="issue_date">
          </div>
        </div>
        <div class="col-12 col-md-2">
          <div class="form-group">
            <label class="font-weight-bold">{{__('key.invoice.invoice.date')}}:</label>
            <input class="form-control form-control-sm" id="date">
          </div>
        </div>
        {{-- barcode --}}
        <div class="col-12">
          <div class="form-group">
            <label class="font-weight-bold">{{__('key.invoice.invoice.barcode')}}</label>
            <input type="text" onkeyup="hitBarcode(this)" class="form-control form-control-sm" placeholder='{{__('key.invoice.invoice.barcode_placeholder')}}' id="barcode">
          </div>
        </div>
      </div>
        <table width="100%" class="table-sm table-bordered" id="sales-table">
            <thead>
                  <tr>
                        <th class="text-center" width="15%">{{__('key.invoice.invoice.table.product')}}</th>                        <th class="text-center" width="15%">{{__('key.invoice.invoice.table.product')}}</th>

                        <th class="text-center" width="10%">{{__('key.invoice.invoice.table.stock')}}</th>
                        <th class="text-center" width="15%">{{__('key.invoice.invoice.table.qantity')}}</th>
                        <th class="text-center" width="15%">{{__('key.invoice.invoice.table.price')}}</th>
                        <th class="text-center" width="10%">Bundle</th>
                        <th class="text-center" width="15%">{{__('key.invoice.invoice.table.total')}}</th>
                        <th class="text-center" width="5%">{{__('key.invoice.invoice.table.action')}}</th>
                  </tr>
                
            </thead>
        <tbody>
        </tbody> 
      </table>
      <button class="btn btn-sm btn-primary mb-3 float-right" id="add_item">+</button>
      <div class="row footer-form mt-5">
            <div class="col-12 col-md-4">
                <table>
                  <tr>
                    <td class="font-weight-bold">{{__('key.invoice.invoice.total')}}:</td>
                    <td width="50%">
                      <div class="input-group input-group-sm">
                          <input type="number" class="form-control form-control-sm" id="final_total" disabled="">
                          <div class="input-group-append">
                            <span class="input-group-text" id="inputGroupPrepend">৳</span>
                          </div>
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <td class="font-weight-bold">{{__('key.invoice.invoice.total_item')}}:</td>
                    <td>
                      <input type="number" disabled="" class="form-control-sm form-control mt-1" id="total_item">
                    </td>
                  </tr>
                  <tr>
                    <td class="font-weight-bold">{{__('key.invoice.invoice.discount')}}:</td>
                    <td>
                      <div class="input-group input-group-sm">
                          <input type="number" class="form-control form-control-sm" id="discount">
                          <div class="input-group-append">
                            <span class="input-group-text" id="inputGroupPrepend">%</span>
                          </div>
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <td class="font-weight-bold">{{__('key.invoice.invoice.vat')}}:</td>
                    <td>
                      <div class="input-group input-group-sm">
                          <input type="number" class="form-control form-control-sm" id="vat">
                          <div class="input-group-append">
                            <span class="input-group-text" id="inputGroupPrepend">%</span>
                          </div>
                      </div>
                      </td>
                  </tr>
                  <tr>
                    <td class="font-weight-bold">{{__('key.invoice.invoice.labour')}}:</td>
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
                    <td class="font-weight-bold">{{__('key.invoice.invoice.transport_cost')}}:</td>
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
                    <td class="font-weight-bold">{{__('key.invoice.invoice.total_payable')}}:</td>
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
                    <td class="font-weight-bold">{{__('key.invoice.invoice.payment_method')}}:</td>
                    <td>
                      <div class="input-group input-group-sm">
                          <select type="text" class="form-control form-control-sm" id="payment_method">
                            <option value="">--SELECT--</option>
                          </select>
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <td class="font-weight-bold">{{__('key.invoice.invoice.transaction')}}:</td>
                    <td>
                      <div class="input-group input-group-sm">
                          <input type="text" class="form-control form-control-sm" id="transaction_id" placeholder="X33KDLDFXFKJ">
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <td class="font-weight-bold">{{__('key.invoice.invoice.ammount')}}:</td>
                    <td>
                      <div class="input-group input-group-sm">
                          <input type="number" class="form-control form-control-sm" id="pay">
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
                <textarea class="form-control form-control-sm mt-1" id="note" rows="3" placeholder="{{__('key.invoice.invoice.note_placeholder')}}"></textarea>
                <p class="float-right"><span id="writed">0</span><span>/500</span></p>
              </div>
            </div>
      </div> {{-- end row --}}
      <button class="btn btn-sm btn-primary text-center mb-3 mt-3" type="submit" onclick="submit()" id="submit">{{__('key.buttons.save')}}</button>
      <button class="btn btn-sm btn-warning text-center mb-3 mt-3" type="submit" onclick="submit(1)" id="submit">{{__('key.buttons.save_and_print')}}</button>
      <button class="btn btn-sm btn-info text-center mb-3 mt-3" type="submit" onclick="submit(2)" id="submit">{{__('key.buttons.chalan')}}</button>
    </div>
  </div>
</div>
@endsection
@section('script')
<script src="{{asset('js/custom_modal.js')}}"></script>
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
      html+="<td class='d-none'><input type='number' class='form-control form-control-sm text-right discount'  type='text' placeholder='0.00' name='discount[]' id='price"+i+"' value='"+sales[i].discount+"'></td>";
      html+="<td><input class='form-control form-control-sm text-right discount'  type='text' placeholder='text...' name='bundle[]' id='bundle"+i+"' value='"+((sales[i].bundle!=null) ? sales[i].bundle : '')+"'></td>";
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
  $('#sales_type').val(invoice.action_id).trigger({
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
  InitData()
  $('#barcode').focus();
  $('#customer').select2({
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
  if (invoice.name!=null){
    $('#customer').html("<option value='"+invoice.customer_id+"'>"+invoice.name+"("+invoice.phone1+")</option>")
  }
  $('#transport').select2({
    theme:'bootstrap4',
    placeholder:'select',
    allowClear:true,
    ajax:{
      url:"{{URL::to('admin/get_transport_export')}}",
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
  $('#customer_site').select2({
      theme:"bootstrap4",
      allowClear:true,
      placeholder:'select',
  })
  if (invoice.site_id!=null) {
      $('#site').html("<option value='"+invoice.site_id+"'>"+invoice.site_name+"</option>");
  }
  $('#sales_type').select2({
    theme:'bootstrap4',
    placeholder:'select',
    allowClear:true,
  })
// getBank()
})
function getBlnce(id){
  $('.submit').attr('disabled',true);
  if (id=='' || id==null || id==NaN) {
      $('#balance').addClass('d-none');
      return false;
    }
  axios.get('admin/customer_balance/'+id)
  .then(function(response){
    ('.submit').attr('disabled',false);
    total=response.data[0].total;
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
function stockWarning(){
        Swal.fire({
          title: 'Stock Out This Product',
          icon:false,
          showCloseButton: true,
          showCancelButton: false,
          focusConfirm: false,
          confirmButtonText:'Ok',
        })
}
//add item function 
function addItem(datx={id:null,text:null,qty:null,price:null}){
  if(datx.id!=null){
    el=$("[name='item[]'] [value="+datx.id+"]"+"option:selected")
    if(el.length>0){
        q=el.parent().parent().next().next().children().val();
        if(parseInt(q)>=parseInt(datx.qty) || parseInt(datx.qty)==0){
          stockWarning();
          return false;
        }
        el.parent().parent().next().next().children().val(parseInt(q)+1);
        calculation();
        return false;
    }
    if(parseInt(datx.qty)==0){
      stockWarning();
      return false;
    }
  }
  count=count+1;
  s_length=s_length+1;
  var html='<tr>';
      html+="<input type='hidden' name='sale_id[]' value='0'>"
      html+="<td><select class='form-control form-control-sm item' type='text' name='item[]' id='item"+s_length+"' data-allow-clear='true'><option value='' selected>Select</option></select></td>";
      html+="<td><select class='form-control form-control-sm store' type='text' name='store[]' id='store"+s_length+"' data-allow-clear='true'><option value='' selected>Select</option></select></td>";
      html+="<td><input class='form-control form-control-sm text-right qantity'  type='text' placeholder='0.00' name='av_qty[]' disabled id='av_qty"+s_length+"'></td>";
      html+="<td><input type='number' class='form-control form-control-sm text-right qantity'  type='text' placeholder='0.00' name='qantity[]' id='qantity"+s_length+"' value='1'></td>";
      html+="<td><input type='number' class='form-control form-control-sm text-right price'  type='text' placeholder='0.00' name='price[]' id='price"+s_length+"'></td>";
      html+="<td class='d-none'><input type='number' class='form-control form-control-sm text-right price'  type='text' placeholder='0.00' name='discount[]' id='price"+s_length+"'></td>";
      html+="<td><input class='form-control form-control-sm text-right discount'  type='text' placeholder='text...' name='bundle[]' id='bundle"+i+"'></td>";
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
   $('#price'+s_length).val(datx.price)
  }
  calculation()
}
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
//............end add item function...........
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
  transport_cost=parseFloat($('#transport_cost').val());
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
    if (isNaN(transport_cost)) {
      transport_cost=0;
    }
    total_payableX=(total_payable*discount)/100;
    vat=(total_payable*vat)/100;
    $('#total_payable').val(((total_payable-total_payableX)+labour+vat+transport_cost).toFixed(2));
  }
}
function calculation(){
  let x=0;
  let totalcal=0;
  var total_item=$('#total_item').val();
  var qantity=$("input[name='qantity[]']")
              .map(function(){return (($(this).val()=='')? 0:$(this).val());}).get();
  var discount=$("input[name='discount[]']")
              .map(function(){return (($(this).val()=='')? 0:$(this).val());}).get();
 $("input[name='price[]']")
  .map(function(){
      price=(($(this).val()=='')? 0:$(this).val())
      total=((parseFloat(price)*parseFloat(qantity[x]))-((parseFloat(price)*parseFloat(qantity[x]))*discount[x])/100).toFixed(2);
      if (!isNaN(total)) {
      $(this).parent().next().next().next().children("input[name='total[]']").val(total)
      totalcal+=parseFloat(total);
      $('#final_total').val(totalcal.toFixed(2));
      $('#total_payable').val(totalcal);
      totalCalculation();
      }
    x=x+1;
  }).get();
}
$(document).on('keyup change','.qantity,.price,.discount',function(){
  calculation();
})

$(document).on('keyup change','#discount,#vat,#labour,#transport_cost',function(){
  totalCalculation()
});
$(document).on('keyup change','input',function(){
  totalCalculation()
});
$(document).on('keyup change','#note',function(){
  Note();
});
$(document).on('keyup change',"#discount,#vat,[name='discount[]']",function(){
  console.log($(this).val())
  if(parseFloat($(this).val())>100){
     $(this).val(100)
  }
});
$(document).on('keyup change',"[name='qantity[]']",function(){
    this_val=parseFloat($(this).val());
    avl_val=parseFloat($(this).parent().prev().children().val());
    if(this_val>avl_val){
      stockWarning();
      $(this).val(avl_val);
    }
});
$(document).on('keyup change',"[name='qantity[]']",function(){
    this_val=parseFloat($(this).val());
    avl_val=parseFloat($(this).parent().prev().children().val());
    if(this_val>avl_val){
      Swal.fire({
          title: 'Product Stock Limit Over !',
          icon:false,
          showCloseButton: true,
          showCancelButton: false,
          focusConfirm: false,
          confirmButtonText:'Ok',
        })
      $(this).val(avl_val);
    }
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
$("select[name='store[]']").each(function(){
  $(this).removeClass('is-invalid');
if ($(this).val()=='' || $(this).val()==null) {
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
   bundle = $("input[name='bundle[]']")
              .map(function(){return $(this).val();}).get();
   customer=$('#customer').val();
   date=$('#date').val();
   issue_date=$('#issue_date').val();
   total_payable=$('#total_payable').val();
   total_item=$('#total_item').val();
   discount=$('#discount').val();
   vat=$('#vat').val();
   labour=$('#labour').val();
   transport_cost=$('#transport_cost').val();
   transport=$('#transport').val();
   sales_type=$('#sales_type').val();
   total=$('#final_total').val();
   payment_method=$('#payment_method').val();
   transaction=$('#transaction_id').val();
   payment_id=$('#payment_id').val();
   pay=$('#pay').val();
   note=$('#note').val();   
   site=$('#site').val();
    formData=new FormData();
    formData.append('sale_id[]',sale_id);
    formData.append('qantities[]',qantities);
    formData.append('prices[]',prices);
    formData.append('product[]',items);    
    formData.append('store[]',store);
    formData.append('bundle[]',bundle);
    formData.append('customer',customer);
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
    formData.append('sales_type',sales_type);
    formData.append('total',total);
    formData.append('payment_method',payment_method);
    formData.append('transaction',transaction);
    formData.append('pay',pay);
    formData.append('note',note);    
    formData.append('site',site);
    axios.post('admin/invoice-update/'+invoice.id,formData)
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
          printObject(response.data.id,response.data.balance,print);
        }else if(print==2){
          printObject(response.data.id,response.data.balance,print);
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
$('#sales_type').on("select2:select", function(e){
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
// custom modal 
function CustomModal(){
data=CustomModalForm({
      setting:{
          title:'Add New Customer',
          unique:0,
          SubmitButton:{
            text:'Save',
            class:'btn  btn-primary CustomSubmit',
            type:'',
          }
        },
        forms:{
          form1:{
            category:'input',
            label:'Name',
            type:'text',
            class:'form-control form-control-sm',
            id:'customer_name',
            placeholder:'Enter Customer Name',
          },
          form2:{
            category:'input',
            label:'Phone',
            type:'text',
            class:'form-control form-control-sm',
            id:'customer_phone',
            placeholder:'Enter Phone',
          },
        }
    });
      $('#CustomModal').html(data)
      $('#CustomModalForm').modal('show')
    }
  $(document).on('click','.CustomSubmit',function(){
 data=$('#myCustomForm').serializeArray();
 var formData=new FormData;
 for(i=0;i<data.length;i++){
  formData.append(data[i]['name'],data[i]['value']);
 }
 unique_id=$('#unique_id').val();
     axios.post('admin/inv-customer',formData)
     .then((res)=>{
        if (res.data.message) {
          toastr.success(res.data.message);
          document.getElementById('myCustomForm').reset();
          $('#CustomModalForm').modal('hide')
        }else{
          keys=Object.keys(res.data)
          for (var i = 0; i < keys.length; i++) {
            alert(res.data[keys[i]]+'\n');
          }
        }
     })
     .catch((error)=>{
      console.log(error);
     })
})
  function Note(){
    text=($('#note').val()).toString();
    $('#writed').removeClass('text-danger')
    if(text.length>500){
      $('#writed').addClass('text-danger')
    }
    $('#writed').text(text.length)
  }
$('#customer').change(function(){
  CustomerSite();
})
$(document).ready(function(){
  CustomerSite();
})
function CustomerSite(){
    customer_id=$('#customer').val();
    if(customer_id!='' || customer_id!=null){
          $('#site').select2({
          theme:'bootstrap4',
          placeholder:'select',
          allowClear:true,
          ajax:{
            url:"{{URL::to('admin/search_site')}}"+'/'+customer_id,
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
}
  
 
// Print Invoice
function printObject(inv_id=null,balance,cond=null){
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
      bundle = $("input[name='bundle[]']")
                  .map(function(){
                  return ($(this).val()=='') ? 0 : $(this).val();
                  }).get();
      totalx=parseFloat($('#final_total').val());
      total_item=parseFloat($('#total_item').val());
      discount=parseFloat($('#discount').val());
      vat=parseFloat($('#vat').val());
      labour=parseFloat($('#labour').val());
      transport=parseFloat($('#transport_cost').val());
      total_payable=parseFloat($('#total_payable').val());
      pay=parseFloat($('#pay').val());
      if (isNaN(pay)){
          pay=0;
      }
      s_type=parseFloat($('#sales_type').val());
      balance=parseFloat(balance)
      due=total_payable-pay;
      x=[{product:products,qantities,prices,total,bundle}];
      html=`
<div id="invoice">
  <div style='background-color:#007BFF;padding:50px;color:white;'>
    <table width="100%" style="border:none;">
      <tr>
        <td>
          <img height="80px" width="100px" src="{{$base64}}"><br>
          <span style="font-size:25px;">`+((cond==2) ? "@lang('key.common.chalan')" : "@lang('key.invoice.invoice.invoice')" )+`</span>
        </td>
        <td style="float:right;"><span style="font-weight:bold;">{{$info->company_name}}</span><br>{{$info->adress}}<br>{{$info->phone}}</td>
      </tr>
    </table>
  </div>
  <div style="margin-right:50px;margin-left:50px;margin-top:30px;margin-bottom:30px;">
    <table width="100%" style="border:none;font-weight:bold;">
      <tr>
        <td>
           @lang('key.invoice.invoice.sale_type')
        </td>
        <td style="float:right;">`+$('#sales_type option:selected').text()+`</td>
      </tr>
      <tr>
        <td>
           @lang('key.invoice.invoice.date').
        </td>
        <td style="float:right;">`+$('#date').val()+`</td>
      </tr>
      <tr style='`+(($('#sales_type').val()!=1) ? 'display:none;' : '')+`'>
        <td>
           @lang('key.invoice.invoice.issue_date').
        </td>
        <td style="float:right;">`+$('#issue_date').val()+`</td>
      </tr>
      <tr>
        <td>
           @lang('key.invoice.invoice.bill_no').
        </td>
        <td style="float:right;">`+'1'+String(inv_id).padStart(9,'0')+`</td>
      </tr>
      <tr>
        <td>
           @lang('key.invoice.invoice.customer')
        </td>
        <td style="float:right;">`+$('#customer option:selected').text()+`</td>
      </tr>
      <tr>
        <td>
           Site
        </td>
        <td style="float:right;">`+$('#site option:selected').text()+`</td>
      </tr>
    </table>
  </div>
  <div id="tables" style='margin-right:50px;margin-left:50px;'>
    <table width="100%" style="text-align:center;border:1px solid grey;">
      <tr>
        <th style='border:1px solid grey'>@lang('key.invoice.invoice.table.product')</th>
        <th style='border:1px solid grey'>@lang('key.invoice.invoice.table.qantity')</th>
        <th style='border:1px solid grey;`+( (cond==2) ? "display:none" : '')+`'>@lang('key.invoice.invoice.table.price')</th>
        <th style='border:1px solid grey;'>Bundle</th>
        <th style='border:1px solid grey;`+( (cond==2) ? "display:none" : '')+`'>@lang('key.invoice.invoice.table.total')</th>
      </tr>

      `;
      for (var i=0;i<products.length; i++) {
        html+="";
        html+="<td style='border:1px solid grey'>"+x[0]['product'][i]+"</td>";
        html+="<td style='border:1px solid grey'>"+(parseFloat(x[0]['qantities'][i])).toFixed(2)+"</td>";
        html+="<td style='border:1px solid grey;"+( (cond==2) ? "display:none" : '')+"'>"+(parseFloat(x[0]['prices'][i])).toFixed(2)+"</td>";
        html+="<td style='border:1px solid grey'>"+(parseFloat(x[0]['bundle'][i])).toFixed(2)+"</td>";
        html+="<td style='border:1px solid grey;"+( (cond==2) ? "display:none" : '')+"'>"+(parseFloat(x[0]['total'][i])).toFixed(2)+"</td>";
        html+="</tr>";
      }
      html+=`</table>
       </div>
       <div style='margin-right:50px;margin-left:50px;margin-top:30px;margin-bottom:30px;'>
    <table width="100%" style="color:black;font-weight:bold">
      <!-- total -->
      <tr style='background-color:#F1F1F1;`+( (cond==2) ? "display:none" : '')+`'>
        <td>
           @lang('key.invoice.invoice.total') ৳
        </td>
        <td style="text-align:right;">`+totalx.toFixed(2)+`</td>
      </tr>
      <!-- total item -->
      <tr style="`+( (cond==2) ? "background-color:#F1F1F1" : '')+`">
        <td>
            @lang('key.invoice.invoice.total_item')
        </td>
        <td style="text-align:right;">`+total_item+`</td>
      </tr>
      <!-- Discount -->
      <tr  style='background-color:#F1F1F1;`+( (cond==2) ? "display:none" : '')+`'>
        <td>
            @lang('key.invoice.invoice.discount') %
        </td>
        <td style="text-align:right;">`+(discount ? ((discount*totalx)/100).toFixed(2) :0.00 )+`</td>
      </tr>
      <!-- Vat -->
      <tr style="`+( (cond==2) ? "display:none" : '')+`">
        <td>
            @lang('key.invoice.invoice.vat') %
        </td>
        <td style="text-align:right;">`+(vat ? ((vat*totalx)/100).toFixed(2) : 0.00)+`</td>
      </tr>
      <!-- Labour Cost -->
      <tr  style='background-color:#F1F1F1;`+( (cond==2) ? "display:none" : '')+`'>
        <td>
            @lang('key.invoice.invoice.labour') ৳
        </td>
        <td style="text-align:right;"> `+(labour ? labour.toFixed(2) :0.00)+`</td>
      </tr>
      <!-- Transport Cost -->
      <tr style="`+( (cond==2) ? "display:none" : '')+`">
        <td>
            @lang('key.invoice.invoice.transport_cost') ৳
        </td>
        <td style="text-align:right;">`+(transport ? transport.toFixed(2) :0.00)+`</td>
      </tr>
      <!-- Total Payable -->
      <tr style='background-color:#F1F1F1;`+( (cond==2) ? "display:none" : '')+`'>
        <td>
             @lang('key.invoice.invoice.total_payable') ৳
        </td>
        <td style="text-align:right;">`+(total_payable ? total_payable.toFixed(2) : 0.00)+`</td>
      </tr>
      <!-- Payment -->
      <tr style="`+( (cond==2) ? "display:none" : '')+`">
        <td>
            @lang('key.invoice.invoice.payment') ৳
        </td>
        <td style="text-align:right;"> `+(pay ? (pay).toFixed(2) : 0.00)+`</td>
      </tr>
      <!-- balance -->
      <tr  style='background-color:#F1F1F1;`+( (cond==2) ? "display:none" : '')+`'>
        <td>
            @lang('key.invoice.invoice.balance') ৳
        </td>
        <td style="text-align:right;">`+balance+`</td>
      </tr>
      <!-- Due -->
      <tr style="`+((PaymentCheck(total_payable,pay)['value']=='Paid' || PaymentCheck(total_payable,pay)['value']=='Over Paid') ? 'color:green' : 'color:red')+`;`+( (cond==2) ? "display:none" : '')+`">
        <td>
            @lang('key.invoice.invoice.pay_status')
        </td>
        <td style="text-align:right;" style='line-height:0.5;'>`+PaymentCheck(total_payable,pay)['value']+`<br><strong style='font-size:10px;'>`+PaymentCheck(total_payable,pay)['text']+`<strong></td>
      </tr>
    </table>
    <br>
    <h2>@lang('key.invoice.invoice.note').</h2>
    <p>`+$('#note').val()+`</p>
     </div>`

     $(html).printThis({
        importCSS:true,
        printDelay: 333,
        header: "",
        footer:`<p style="text-align:center">
                  Software Developed By <strong>SOFTiMPIRE</strong>
                  <br>Adress:Barisal Bottola,Barisal<br>
                  Mobile:01873072253,01310588563 
                </p>`,
        base: "noman"
      });
     remove();
     $('#submit').attr('disabled',false);
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
        case payablex===payx || payable===pay || balance>=0.00:
        arr['value']='Paid';
        arr['text']='';
        return arr;
        break;
        case payablex>payx:
        if(balance<=0.00){
          t=Math.abs(balance).toFixed(2)
        }else{
          t=0.00
        }
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