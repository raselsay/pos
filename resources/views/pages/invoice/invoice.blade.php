@extends('layouts.master')
@section('content')
@section('link')
<style>
  .buffer {
    height: 20px;
    width: 20px;
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
<div class="container">
  <div class="card m-0">
    <div class="card-header pt-3  flex-row align-items-center justify-content-between">
      <h5 class="m-0 font-weight-bold">{{__('key.invoice.invoice.title')}} <img class='buffer float-right d-none'
          src="{{asset('storage/admin-lte/dist/img/buffer.gif')}}" alt=""></h5>
    </div>
    <div class="card-body px-3 px-md-5">
      <div id="CustomModal"></div>
      <div class="row">
        <div class="col-12 mb-2">
          <label class="font-weight-bold mb-1" for="customer">{{__('key.invoice.invoice.customer')}}</label>
          <div class="input-group ">
            <select class="form-control form-control-sm" id="customer" onchange="getBlnce(this.value)">
            </select>
            <div class="input-group-append">
              <button class="btn btn-sm btn-primary rounded-right font-weight-bold" onclick="CustomModal()">{{__('key.invoice.invoice.customer')}}<i class="fa fa-plus" aria-hidden="true"></i></button>
            </div>
            <span class="p-1 d-none" id="balance">@lang('key.invoice.invoice.balance') :<span id='c_bal'></span></span>
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
      <!--<button class="btn btn-sm btn-primary mb-3" id="add_item">Add Product</button> -->
      <table class="table-sm table-bordered" id="sales-table">
        <thead>
          <tr>
            <th class="text-center" width="15%">{{__('key.invoice.invoice.table.product')}}</th>
            <th class="text-center" width="15%">Store</th>
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
                <div class="input-group input-group-sm">
                  <input type="number" disabled="" class="form-control-sm form-control" id="total_item">
                </div>
              </td>
            </tr>
            @if($settings['invoice_discount']==1)
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
            @endif
            @if($settings['invoice_vat']==1)
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
            @endif
            @if($settings['invoice_labour']==1)
            <tr>
              <td class="font-weight-bold">{{__('key.invoice.invoice.labour')}}:</td>
              <td>
                <div class="input-group input-group-sm">
                  <input type="number" class="form-control form-control-sm" id="labour" onchange="calculation()">
                  <div class="input-group-append">
                    <span class="input-group-text" id="inputGroupPrepend">৳</span>
                  </div>
                </div>
              </td>
            </tr>
            @endif
            @if($settings['invoice_transport']==1)
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
            @endif
            <tr>
              <td class="font-weight-bold">{{__('key.invoice.invoice.total_payable')}}:</td>
              <td>
                <div class="input-group input-group-sm">
                  <input type="number" class="form-control form-control-sm" id="total_payable" disabled="">
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

                  </select>
                </div>
              </td>
            </tr>
            <tr>
              <td class="font-weight-bold">{{__('key.invoice.invoice.transaction')}}:</td>
              <td>
                <div class="input-group input-group-sm">
                  <input type="text" class="form-control form-control-sm" id="transaction_id"
                    placeholder="X33KDLDFXFKJ">
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
      </div>{{-- end row --}}
      
          <button class="btn btn-sm btn-primary text-center mb-3 mt-3 submit" type="submit" onclick="submit()"
            id="submit">{{__('key.buttons.save')}}</button>
          <button class="btn btn-sm btn-warning text-center mb-3 mt-3" onclick="submit(1)" id="submit">{{__('key.buttons.save_and_print')}}</button>
          <button class="btn btn-sm btn-info text-center mb-3 mt-3" onclick="submit(2)" id="submit">{{__('key.buttons.chalan')}}</button>
          <button class="btn btn-sm btn-secondary text-center mb-3 mt-3" onclick="remove()" id="submit">{{__('key.buttons.reset')}}</button>
          <input type="checkbox" class="form-check-input mt-4 ml-2" id="sms">
           <label class="form-check-label ml-4" for="flexCheckIndeterminate">
            {{__('key.invoice.invoice.sms')}}
           </label>
    </div>
  </div>
</div>
@endsection
@section('script')
<script src="{{asset('js/custom_modal.js')}}"></script>
<script type="text/javascript">
  $(document).ready(function(){
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
  $('#payment_method').select2({
    theme:'bootstrap4',
    placeholder:'select',
    allowClear:true,
    ajax:{
      url:"{{URL::to('admin/get_banks')}}",
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
  $('#site').select2({
    theme:'bootstrap4',
    placeholder:'select',
    allowClear:true,
  });
   $('#sales_type').select2({
    theme:'bootstrap4',
    placeholder:'select',
    allowClear:true,
  })
$('#barcode').focus();
})
function getBlnce(id){
  $('.submit').attr('disabled',true);
  if (id=='' || id==null || id==NaN) {
      $('#balance').addClass('d-none');
      return false;
    }
  axios.get('admin/customer_balance/'+id)
  .then(function(response){
    $('.submit').attr('disabled',false);
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
 let count=0;
  let i=0;
//add item function
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
  i=i+1;
  var html='<tr>';
      html+="<td><select class='form-control form-control-sm item' type='text' name='item[]' id='item"+i+"' data-allow-clear='true'><option value='' selected>Select</option></select></td>";
      html+="<td><select class='form-control form-control-sm store' type='text' name='store[]' id='store"+i+"' data-allow-clear='true'><option value='' selected>Select</option></select></td>";
      html+="<td><input class='form-control form-control-sm text-right av_qty'  type='text' placeholder='0.00' name='av_qty[]' disabled id='av_qty"+i+"'></td>";
      html+="<td><input class='form-control form-control-sm text-right qantity'  type='number' placeholder='0.00' name='qantity[]' id='qantity"+i+"' value='1'></td>";
      html+="<td><input class='form-control form-control-sm text-right price'  type='number' placeholder='0.00' name='price[]' id='price"+i+"'></td>";
      html+="<td class='d-none'><input class='form-control form-control-sm text-right discount'  type='hidden' placeholder='0.00' name='discount[]' id='discount"+i+"'></td>";
      html+="<td><input class='form-control form-control-sm text-right discount'  type='text' placeholder='text...' name='bundle[]' id='bundle"+i+"'></td>";
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
   $('#av_qty'+i).val(datx.qty)
   $('#price'+i).val(datx.price)
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
  customer=$('#customer').val();
 axios.get('admin/product_price_by_id/'+id+((customer!=null) ? '/'+customer : ''))
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
    sale_type=$("#sales_type").val();
    avl_val=parseFloat($(this).parent().prev().children().val());
    if(this_val>avl_val && sale_type!=2){
      stockWarning();
      $(this).val(avl_val);
    }
});
// validate all fields
function Validate(){
  let isValid=true;
  let i=0;
$('#customer').removeClass('is-invalid');
$('#payment_method').removeClass('is-invalid');
if($('#customer').val()==null){
  isValid=false
  $('#customer').addClass('is-invalid');
}
if ($('#pay').val()!='' && $('#payment_method').val()==null) {
   isValid=false;
   $('#payment_method').addClass('is-invalid');
}
if ($('#sales_type').val()!='' && $('#sales_type').val()==null) {
   isValid=false;
   $('#sales_type').addClass('is-invalid');
}
av_qty = $("input[name='av_qty[]']")
          .map(function(){  
            if($(this).val()==''){
              return 0;
            }else{
              return $(this).val();
            }
          }).get();
$("input[name='qantity[]']").each(function(){
  $(this).removeClass('is-invalid');
if ($(this).val()=='' || (parseFloat(av_qty[i])<parseFloat($(this).val()) && $('#sales_type').val()!=2)  ) {
  isValid=false;
  $(this).addClass('is-invalid');
}else{
  i=i+1;
}
})
$("select[name='store[]']").each(function(){
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
if ($(this).val()==''){
  isValid=false;
  $(this).addClass('is-invalid');
}
});
return isValid;
}
function submit(print=null){
   isValid=Validate();
   $('.buffer').removeClass('d-none');
if (isValid==true) {
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
   site=$('#site').val();
   sales_type=$('#sales_type').val();
   total=$('#final_total').val();
   payment_method=$('#payment_method').val();
   transaction=$('#transaction_id').val();
   note=$('#note').val();
   pay=$('#pay').val();
    formData=new FormData();
    formData.append('qantities[]',qantities);
    formData.append('store[]',store);
    formData.append('prices[]',prices);
    formData.append('product[]',items);
    formData.append('bundle[]',bundle);
    formData.append('customer',customer);
    formData.append('issue_date',issue_date);
    formData.append('date',date);
    formData.append('total_payable',total_payable);
    formData.append('total_item',total_item);
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
    formData.append('site',site);
    formData.append('sales_type',sales_type);
    formData.append('total',total);
    formData.append('payment_method',payment_method);
    formData.append('transaction',transaction);
    formData.append('pay',pay);
    formData.append('note',note);
    axios.post('admin/invoice',formData)
    .then(function(response){
      $('.buffer').addClass('d-none');
      if (!response.data.message){
        keys=Object.keys(response.data[0]);
        html='';
        for (var i = 0; i <keys.length; i++) {
          html+="<p style='color:red;line-height:1px;font-size:12px;'>"+response.data[0][keys[i]][0]+"</p>";
        }
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
        if(print==1){
          printObject(response.data.id,print);
        }else if(print==2){
          printObject(response.data.id,print)
        }
        else{
          remove();
          $('.submit').attr('disabled',false);
        }
        $('.buffer').addClass('d-none');
      }
    })
    .catch(function(error){
    })
  }else{
    $('.buffer').addClass('d-none');
  }
}
//datepicker.................
$('#date,#issue_date').daterangepicker({
        showDropdowns: true,
        singleDatePicker: true,
        parentEl: ".bd-example-modal-lg .modal-body",
        locale: {
            format: 'DD-MM-YYYY',
        }
  });
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
// custom modal form
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
  $('#writed').text(text.length);
}

$('#customer').change(function(){
  customer_id=$(this).val();
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
  })
// Print Invoice
function printObject(inv_id=2,cond=null){
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
                  return $(this).val();
                  }).get();
                  console.log(products.length);
      totalx=parseFloat($('#final_total').val());
      total_item=parseFloat($('#total_item').val());
      discount=parseFloat($('#discount').val());
      vat=parseFloat($('#vat').val());
      labour=parseFloat($('#labour').val());
      transport=parseFloat($('#transport_cost').val());
      total_payable=parseFloat($('#total_payable').val());
      pay=parseFloat($('#pay').val());
      if (isNaN(pay)){
          pay=parseFloat(0);
      }
      s_type=parseFloat($('#sales_type').val());
      c_bal=parseFloat($('#c_bal').text());
      due=total_payable-pay;
      x=[{product:products,qantities,prices,total,bundle}];
      balance=((s_type==0) ? (c_bal-due).toFixed(2) : (c_bal+due).toFixed(2));
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
           @lang('key.invoice.invoice.date')
        </td>
        <td style="float:right;">`+$('#date').val()+`</td>
      </tr>
      <tr style='`+(($('#sales_type').val()!=1) ? 'display:none;' : '')+`'>
        <td>
           @lang('key.invoice.invoice.issue_date')
        </td>
        <td style="float:right;">`+$('#issue_date').val()+`</td>
      </tr>
      <tr>
        <td>
           @lang('key.invoice.invoice.bill_no')
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
        <th style='border:1px solid grey;`+((cond==2) ? "display:none" :"")+`'>@lang('key.invoice.invoice.table.price')</th>
        <th style='border:1px solid grey'>Bundle</th>
        <th style='border:1px solid grey;`+((cond==2) ? "display:none" :"")+`'>@lang('key.invoice.invoice.table.total')</th>
      </tr>
      `;
      for (var i=0;i<products.length; i++) {
        html+="";
        html+="<td style='border:1px solid grey'>"+x[0]['product'][i]+"</td>";
        html+="<td style='border:1px solid grey'>"+(parseFloat(x[0]['qantities'][i])).toFixed(2)+"</td>";
        html+="<td style='border:1px solid grey;"+((cond==2) ? "display:none" :"")+"'>"+(parseFloat(x[0]['prices'][i])).toFixed(2)+"</td>";
        html+="<td style='border:1px solid grey'>"+x[0]['bundle'][i]+"</td>";
        html+="<td style='border:1px solid grey;"+((cond==2) ? "display:none" :"")+"'>"+(parseFloat(x[0]['total'][i])).toFixed(2)+"</td>";
        html+="</tr>";
      }
      html+=`</table>
       </div>
       <div style="margin-right:50px;margin-left:50px;margin-top:30px;margin-bottom:30px;">
    <table width="100%" style="color:black;font-weight:bold">
      <!-- total -->
      <tr style='background-color:#F1F1F1;`+( (cond==2) ? "display:none;" : '')+`'>
        <td>
           @lang('key.invoice.invoice.total') ৳
        </td>
        <td style="text-align:right">`+totalx.toFixed(2)+`</td>
      </tr>
      <!-- total item -->
      <tr style='`+( (cond==2) ? "background-color:#F1F1F1;" : '')+`'>
        <td>
            @lang('key.invoice.invoice.total_item')
        </td>
        <td style="text-align:right">`+total_item+`</td>
      </tr>
      <!-- Discount -->
      <tr style='background-color:#F1F1F1;`+( (cond==2) ? "display:none;" : '')+`'>
        <td>
            @lang('key.invoice.invoice.discount') %
        </td>
        <td style="text-align:right">`+(discount ? ((discount*totalx)/100).toFixed(2) :0.00 )+`</td>
      </tr>
      <!-- Vat -->
      <tr style='`+( (cond==2) ? "display:none;" : '')+`'>
        <td>
            @lang('key.invoice.invoice.vat') %
        </td>
        <td style="text-align:right">`+(vat ? ((vat*totalx)/100).toFixed(2) : 0.00)+`</td>
      </tr>
      <!-- Labour Cost -->
      <tr style='background-color:#F1F1F1;`+( (cond==2) ? "display:none;" : '')+`'>
        <td>
            @lang('key.invoice.invoice.labour') ৳
        </td>
        <td style="text-align:right"> `+(labour ? labour.toFixed(2) :0.00)+`</td>
      </tr>
      <!-- Transport Cost -->
      <tr style='`+( (cond==2) ? "display:none;" : '')+`'>
        <td>
            @lang('key.invoice.invoice.transport_cost') ৳
        </td>
        <td style="text-align:right">`+(transport ? transport.toFixed(2) :0.00)+`</td>
      </tr>
      <!-- Total Payable -->
      <tr style='background-color:#F1F1F1;`+( (cond==2) ? "display:none;" : '')+`'>
        <td>
             @lang('key.invoice.invoice.total_payable') ৳
        </td>
        <td style="text-align:right">`+(total_payable ? total_payable.toFixed(2) : 0.00)+`</td>
      </tr>
      <!-- Payment -->
      <tr style='`+( (cond==2) ? "display:none;" : '')+`'>
        <td>
            @lang('key.invoice.invoice.payment') ৳
        </td>
        <td style="text-align:right"> `+(pay ? (pay).toFixed(2) : 0.00)+`</td>
      </tr>
      <!-- balance -->
      <tr  style='background-color:#F1F1F1;`+( (cond==2) ? "display:none;" : '')+`'>
        <td>
            @lang('key.invoice.invoice.balance') ৳
        </td>
        <td style="text-align:right">`+((s_type==0) ? (c_bal-due).toFixed(2) : (c_bal+due).toFixed(2)) +`</td>
      </tr>
      <!-- Due -->
      <tr style="`+((PaymentCheck(total_payable,pay)['value']=='Paid' || PaymentCheck(total_payable,pay)['value']=='Over Paid') ? 'color:green' : 'color:red')+`;`+( (cond==2) ? "display:none;" : '')+`">
        <td>
            @lang('key.invoice.invoice.pay_status') ৳
        </td>
        <td style='line-height:0.5;text-align:right;'>`+PaymentCheck(total_payable,pay)['value']+`<br><strong style='font-size:10px;'>`+PaymentCheck(total_payable,pay)['text']+`<strong></td>
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
        footer:`<p style="text-align:center;">
                  Software Developed By <strong>SOFTiMPIRE</strong>
                  <br>Adress:Barisal Bottola,Barisal<br>
                  Mobile:01715279498
                </p>`,
        base: "noman"
      });
     if($('#sms').prop('checked')==true){
        str=$('#customer option:selected').text()
        str=str.split('(')
        num=str[1].split(')')[0]
        msg="Dear "+str[0]+" Welcome to "+"{{$info->company_name}}"+" your items("+products.join()+") total invoice:("+(total_payable).toFixed(2)+") payment:("+pay.toFixed(2)+") balance:("+balance+") and "+PaymentCheck(total_payable,pay)['value'];
        $.post('https://api.greenweb.com.bd/api.php?json',{token:"{{$sms->sms_sender}}",to:num,message:msg})
        .done((res,status)=>{
        })
     }
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
        case payablex===payx || payable===pay || pay+c_bal>payable:
        arr['value']='Paid';
        arr['text']='';
        return arr;
        break;
        case payablex>(payx+c_bal):
        if(s_type!=2){
          t=Math.abs(parseFloat(payable)-parseFloat(pay+c_bal)).toFixed(2)
        }else{
          t=Math.abs(parseFloat(payable)+parseFloat(pay+c_bal)).toFixed(2)
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