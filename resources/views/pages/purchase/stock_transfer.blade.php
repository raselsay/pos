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
@endphp
<div class="container">
  <div class="card m-0">
    <div class="card-header pt-3  flex-row align-items-center justify-content-between">
      <h5 class="m-0 font-weight-bold">Stock Transfer<img class='buffer float-right d-none' src="{{asset('storage/admin-lte/dist/img/buffer.gif')}}" alt=""></h5>
     </div>
    <div class="card-body px-3 px-md-5">
    <form>
      <div class="row">
        
        <div class="col-12 col-md-3">
          <div class="form-group">
            <label class="font-weight-bold">To Store</label>
            <select class="form-control form-control-sm" id="store">
            </select>
          </div>
        </div>
        <div class="col-12 col-md-3">
          <div class="form-group">
            <label class="font-weight-bold">Total Item</label>
            <input type="text" disabled="" class="form-control-sm form-control" id="total_item">
            </select>
          </div>
        </div>
        <div class="col-12 col-md-6">
          <div class="form-group float-right">
            <label class="font-weight-bold d-block">Date:</label>
            <input  class="form-control-sm" id="date">
          </div>
        </div>
      </div>
    </form>
<!--<button class="btn btn-sm btn-primary mb-3" id="add_item">Add Product</button> -->
        <table width="100%" class="table-sm table-bordered" id="sales-table">
            <thead>
                  <tr>
                        <th class="text-center" width="20%">Product</th>
                        <th class="text-center" width="15%">Store</th>
                        <th class="text-center" width="10%">Avl.Qty</th>
                        <th class="text-center" width="10%">qantity</th>
                        <th class="text-center" width="15%">price</th>
                        <th class="text-center" width="15%">total</th>
                        <th class="text-center" width="15%">Action</th>
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
                <button class="btn btn-sm btn-primary text-center mb-3 mt-3" type="submit" onclick="submit()" id="submit">submit</button>
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
  $('#store').select2({
    theme:'bootstrap4',
    placeholder:'select',
    allowClear:true,
    ajax:{
      url:"{{URL::to('admin/get_store')}}",
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
        item=$("select[name='store[]'] option:selected")
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
  $('#payment_method').select2({
    theme:'bootstrap4',
    placeholder:'select',
    allowClear:true,
  })
   $('#purchase_type').select2({
    theme:'bootstrap4',
    placeholder:'select',
    allowClear:true,
  })
getBank()

})

function getBank(){
  axios.get('admin/get_account')
  .then((response)=>{
    html=''
    response.data.forEach((data)=>{
        html+='<option value='+data.id+'>'+data.name+'</option>'
    })
    $('#payment_method').html(html);
  })
  .catch((error)=>{
    console.log(error);
  })
}
function getBlnce(id){
  if (id=='' || id==null || id==NaN) {
      $('#balance').addClass('d-none');
      return false;
    }
  axios.get('admin/customer_balance/'+id)
  .then(function(response){
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
 var count=0;
//add item function 
function addItem(){
  count=count+1;
  var html='<tr>';
      html+="<td><select class='form-control form-control-sm item' type='text' name='item[]' id='item"+count+"' data-allow-clear='true'><option value='' selected>Select</option></select></td>";
      html+="<td><select class='form-control form-control-sm store' type='text' name='store[]' id='store"+count+"' data-allow-clear='true'><option value='' selected>Select</option></select></td>";
      html+="<td><input class='form-control form-control-sm text-right qantity'  type='text' placeholder='0.00' name='av_qty[]' disabled id='av_qty"+count+"'></td>";
      html+="<td><input class='form-control form-control-sm text-right qantity'  type='text' placeholder='0.00' name='qantity[]' id='qantity"+count+"'></td>";
      html+="<td><input class='form-control form-control-sm text-right price'  type='text' placeholder='0.00' name='price[]' id='price"+count+"'></td>";
      html+="<td><input class='form-control form-control-sm text-right total'  type='text' placeholder='0.00' name='total[]' id='total"+count+"'></td>";
      html+="<td class='text-center'><button id='remove' class='btn btn-sm btn-danger'>X</button></td>";
      html+='</tr>';
  $('#sales-table tbody').append(html);
  $('#total_item').val(count);
  $('#item'+count).select2({
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
  $('#store'+count).select2({
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
        item=$("#store").val();
         res=response.map(function(currentValue, index, arr){
          if (item==currentValue.id){
            response[index]['disabled']=true;
          }
        })
        return {
          results:response,
        }
      },
      cache:true,
    }
  })

  
}
//............end add item function...........

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
  addItem();
}
// get category wise product
$('body').on('select2:select',"select[name='category[]']", function (e){
  id=e.params.data.id;
  this_cat=$(this);
 axios.get('admin/product_by_cat/'+id)
      .then(function(response){
          html="<option value='' selected>select</option>"
              response.data.forEach(function(data){
                html+="<option value='"+data.id+"'>"+data.product_name+"</option>";
              })
          this_cat.parent().next().children("[name='item[]']").html(html);
          })
          .catch(function(error){
          console.log(error.request);
        })
 })
// get product wise price
$('body').on('select2:select',"select[name='item[]']", function (e){
  id=e.params.data.id;
  this_cat=$(this);
 axios.get('admin/product_buy_price_by_id/'+id)
      .then(function(response){
            this_cat.parent().next().next().next().next().children("[name='price[]']").val(response.data);
          })
          .catch(function(error){
          console.log(error.request);
        })
 })
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
  store_id=$(this).parent().next().children("[name='store[]']").val();
  this_cat=$(this);
  product_id=this_cat.val();
  if (store_id=='' || product_id=='') {
    return false;
  }
 axios.get('admin/product_qantity/'+product_id+'/'+store_id)
      .then(function(response){
            this_cat.parent().next().next().children("[name='av_qty[]']").val(response.data[0].total);
          })
          .catch(function(error){
          console.log(error.request);
        })
 })
//<=======end category wise product==========>


$(document).ready(function(){
  addItem();
})
$('#add_item').click(function(){
  addItem();
});
$('tbody').on('click','#remove',function(){
  if (parseInt($('#total_item').val())>1) {
  $(this).parent().parent().remove();
  count=count-1;
  $('#total_item').val(count);
  calculation();
  }else{
    alert('You cannot remove this item')
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
$(document).on('keyup change','.qantity,.price',function(){
  calculation();
})

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
$("input[name='store[]']").each(function(){
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
   to_store=$('#store').val();
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
    formData.append('to_store',to_store);
    formData.append('supplier',supplier);
    formData.append('date',date);
    formData.append('total_payable',total_payable);
    formData.append('total_item',total_item);
    formData.append('discount',discount);
    formData.append('vat',vat);
    formData.append('labour',labour);
    formData.append('transport_cost',transport_cost);
    formData.append('transport',transport);
    formData.append('purchase_type',purchase_type);
    formData.append('total',total);
    formData.append('payment_method',payment_method);
    formData.append('transaction',transaction);
    formData.append('pay',pay);
    axios.post('admin/stock_transfer',formData)
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
        $('.buffer').addClass('d-none');
        $('.submit').attr('disabled',false);
      }else if(response.data.message){
        window.toastr.success(response.data.message);
        remove();
        $('.buffer').addClass('d-none');
        $('.submit').attr('disabled',false);
      }
    })
    .catch(function(error){
      $('.buffer').addClass('d-none');
      $('.submit').attr('disabled',false);
      console.log(error);
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