@extends('layouts.master')
@section('content')
@php
  $lang=App\MultiSetting::select('value')->where('name','language')->first();
  App::setLocale(isset($lang->value) ? $lang->value : '' );
@endphp
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
<div class="container">
	<div class="card m-0">
    <div class="card-header pt-3  flex-row align-items-center justify-content-between">
      <h5 class="m-0 font-weight-bold">@lang('key.customer.customer.title')</h5>
     </div>
    <div class="card-body px-3 px-md-5">
              <button type="button" class="btn btn-primary mb-1" {{-- data-toggle="modal" data-target="#exampleModal" --}} onclick="AddNew()">
              @lang('key.customer.customer.add_new') <i class="fas fa-plus"></i>
              </button>
              <a class="btn btn-primary float-right" href="{{URL::to('admin/running-total')}}">@lang('key.customer.customer.ledger')</a>
              <!-- Modal -->  
              <div class="modal fade bd-example-modal-lg" id="Modalx">
                <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="exampleModalLabel"></h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="ModalClose()">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-body" id="forms">
                  <form class="form-horizontal" id="myForm">
                    <input type="hidden" id="id">
                    <div class="text-center">
                        <img id="imagex" src="{{asset('storage/admin-lte/dist/img/avatar5.png')}}" class="d-flex image-upload" style="height:100px;width:100px;">
                        <input class="d-none" type="file" id="file" name="" onchange="readURL(this)">
                        <label for="file"  class="file">@lang('key.customer.customer.choose')</label>
                        <div id="photo_msg" class="invalid-feedback">
                         </div>
                    </div>
                 {{-- forms inputs --}}
                 <div class="input-group mt-4">
                 <label class="control-label col-sm-3 text-lg-right" for="name">@lang('key.customer.customer.company_name') :</label>
                 <div class="col-sm-9">
                   <input type="text" class="form-control form-control-sm" id="company_name" placeholder="@lang('key.customer.customer.company_name_placeholder')">
                   <div id="company_name_msg" class="invalid-feedback">
                   </div>
                 </div>
               </div>
               <div class="input-group">
                 <label class="control-label col-sm-3 text-lg-right" for="name">@lang('key.customer.customer.customer_name'):</label>
                 <div class="col-sm-9">
                   <input type="text" class="form-control form-control-sm" id="name" placeholder="@lang('key.customer.customer.customer_name_placeholder')">
                   <div id="name_msg" class="invalid-feedback">
                   </div>
                 </div>
               </div>
               <div class="input-group">
                 <label class="control-label col-sm-3 text-lg-right" for="name">Spo:</label>
                 <div class="col-sm-9">
                   <select type="text" class="form-control form-control-sm" id="spo">
                   </select>
                   <div id="name_msg" class="invalid-feedback">
                   </div>
                 </div>
               </div>
               <div class="input-group">
                 <label class="control-label col-sm-3 text-lg-right" for="name">Office Contact:</label>
                 <div class="col-sm-9">
                   <select type="text" class="form-control form-control-sm" id="contact">
                   </select>
                   <div id="contact_msg" class="invalid-feedback">
                   </div>
                 </div>
               </div>
               <div class="input-group input-group-sm">
                  <label class="control-label col-sm-3 text-lg-right" for="name">@lang('key.customer.customer.opening_balance') :</label>
                  <div class="col-sm-7">
                      <input type="text" class="form-control form-control-sm" id="opening_balance" placeholder="@lang('key.customer.customer.opening_balance_placeholder')">
                      
                      <div id="opening_balance_msg" class="invalid-feedback">
                      </div>
                  </div>
                  <div class='col-sm-2'>
                    <select type="text" class='form-control form-control-sm' id='balance_type'>
                        <option value="1">@lang('key.customer.customer.balance')</option>
                        <option value="0">@lang('key.customer.customer.due')</option>
                    </select>
                    <div id="opening_balance_msg" class="invalid-feedback">
                      </div>
                  </div>
               </div>
               <div class="input-group">
                 <label class="control-label col-sm-3 text-lg-right" for="name">@lang('key.customer.customer.maximum_due') :</label>
                 <div class="col-sm-9">
                   <input type="text" class="form-control form-control-sm" id="maximum_due" placeholder="@lang('key.customer.customer.maximum_due_placeholder')">
                   <div id="maximum_due_msg" class="invalid-feedback">
                   </div>
                 </div>
               </div>
               <div class="input-group">
                 <label class="control-label col-sm-3 text-lg-right" for="phone1">@lang('key.customer.customer.phone1') :</label>
                 <div class="col-sm-9">
                   <input type="text" class="form-control form-control-sm" id="phone1" placeholder="@lang('key.customer.customer.phone1_placeholder')">
                   <div id="phone1_msg" class="invalid-feedback">
                   </div>
                 </div>
               </div>
               <div class="input-group">
                 <label class="control-label col-sm-3 text-lg-right" for="phone2">@lang('key.customer.customer.phone2') :</label>
                 <div class="col-sm-9">
                   <input type="text" class="form-control form-control-sm" id="phone2" placeholder="@lang('key.customer.customer.phone2_placeholder')">
                   <div id="phone2_msg" class="invalid-feedback">
                   </div>
                 </div>
               </div>
               <div class="input-group">
                 <label class="control-label col-sm-3 text-lg-right" for="name">@lang('key.customer.customer.email') :</label>
                 <div class="col-sm-9">
                   <input type="text" class="form-control form-control-sm" id="email" placeholder="@lang('key.customer.customer.email_placeholder')">
                   <div id="email_msg" class="invalid-feedback">
                   </div>
                 </div>
               </div>
               <div class="input-group">
                 <label class="control-label col-sm-3 text-lg-right" for="birthDate">@lang('key.customer.customer.birth_date') :</label>
                 <div class="col-sm-9">
                   <input type="text" class="form-control form-control-sm" id="birth_date" placeholder="@lang('key.customer.customer.birth_date_placeholder')">
                   <div id="birth_date_msg" class="invalid-feedback">
                   </div>
                 </div>
               </div>
               <div class="input-group">
                 <label class="control-label col-sm-3 text-lg-right" for="mariageDate">@lang('key.customer.customer.marriage_date') :</label>
                 <div class="col-sm-9">
                   <input type="text" class="form-control form-control-sm" id="mariage_date" placeholder="@lang('key.customer.customer.marriage_date_placeholder')">
                   <div id="mariage_date_msg" class="invalid-feedback">
                   </div>
                 </div>
               </div>
               <div class="input-group">
                 <label class="control-label col-sm-3 text-lg-right" for="adress">@lang('key.customer.customer.adress'):</label>
                 <div class="col-sm-9">
                   <input type="text" class="form-control form-control-sm" id="adress" placeholder="@lang('key.customer.customer.adress_placeholder')">
                   <div id="adress_msg" class="invalid-feedback">
                   </div>
                 </div>
               </div>
               <div class="input-group">
                 <label class="control-label col-sm-3 text-lg-right" for="city">@lang('key.customer.customer.city') :</label>
                 <div class="col-sm-9">
                   <input type="text" class="form-control form-control-sm" id="city" placeholder="@lang('key.customer.customer.city_placeholder')">
                   <div id="city_msg" class="invalid-feedback">
                   </div>
                 </div>
               </div>
               <div class="input-group">
                 <label class="control-label col-sm-3 text-lg-right" for="postalCode">@lang('key.customer.customer.postal_code') :</label>
                 <div class="col-sm-9">
                   <input type="text" class="form-control form-control-sm" id="postal_code" placeholder="@lang('key.customer.customer.postal_code_placeholder')">
                   <div id="postal_code_msg" class="invalid-feedback">
                   </div>
                 </div>
               </div>
               <div class="input-group">
                 <label class="control-label col-sm-3 text-lg-right" for="stutus">@lang('key.customer.customer.status') :</label>
                 <div class="col-sm-9">
                   <select  class="form-control form-control-sm" id="stutus">
                     <option value="">--SELECT--</option>
                     <option value="1">@lang('key.customer.customer.active')</option>
                     <option value="0">@lang('key.customer.customer.deactive')</option>
                   </select>
                   <div id="stutus_msg" class="invalid-feedback">
                   </div>
                 </div>
               </div>
               <div class="input-group">
                 <label class="control-label col-sm-3 text-lg-right" for="group">@lang('key.customer.customer.group') :</label>
                 <div class="col-sm-9">
                   <select  class="form-control form-control-sm" id="group_types">
                     <option value="">--SELECT--</option>
                     @foreach($groups as $group)
                     <option value="{{$group->id}}">{{$group->name}}</option>
                     @endforeach
                   </select>
                   <div id="group_types_msg" class="invalid-feedback">
                   </div>
                 </div>
               </div>
                 </form>
                </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-dark" onclick="ModalClose()" data-dismiss='modal' aria-label='Close'>@lang('key.buttons.close')</button>
                    <button type="button" class="btn btn-primary submit" onclick="ajaxRequest()"></button>
                  </div>
              </div>
            </div>
              
            </div>
        <!--End modal-->
        <table class="table table-sm text-center table-bordered table-striped data-table">
          <thead>
            <tr>
              <th>@lang('key.customer.customer.no')</th>
              <th>@lang('key.customer.customer.customer_name')</th>
              <th>@lang('key.customer.customer.phone1')</th>
              <th>@lang('key.customer.customer.adress')</th>
              <th>Spo</th>
              <th>@lang('key.customer.customer.action')</th>
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
          url:"{{ URL::to('/admin/all-customer') }}"
        },
        columns:[
          {
            data:'DT_RowIndex',
            name:'DT_RowIndex',
            orderable:false,
            searchable:false
          },
          {
            data:'name',
            name:'name',
          },
          {
            data:'phone1',
            name:'phone1',
          },
          {
            data:'adress',
            name:'adress',
          },
          {
            data:'spo_name',
            name:'spo_name',
          },
          {
            data:'action',
            name:'action',
          }
        ]
    });
   $('#spo').select2({
    theme:'bootstrap4',
    placeholder:'select',
    allowClear:true,
    ajax:{
      url:"{{URL::to('admin/get_spo')}}",
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
    $('#contact').select2({
    theme:'bootstrap4',
    placeholder:'select',
    allowClear:true,
    ajax:{
      url:"{{URL::to('admin/get_ofcontact')}}",
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

$(document).on('click','.edit',function(){
  $('#exampleModalLabel').text("@lang('key.customer.customer.title_update')");
  $('.submit').text("@lang('key.buttons.update')");
$('#Modalx').modal('show');
  id=$(this).data('id');
  $('#id').val(id);
  axios.get('admin/get-customer/'+id)
  .then(function(response){
    var keys=Object.keys(response.data[0]);
    for (var i = 0; i < keys.length; i++) {
      if (keys[i]!=='opening_balance') {
         $('#'+keys[i]).val(response.data[0][keys[i]])
      }else{
         if(parseFloat(response.data[0][keys[i]])>0){
            $('#'+keys[i]).val(response.data[0][keys[i]])
            $('#balance_type').val(1)
         }else{
            $('#'+keys[i]).val(response.data[0][keys[i]])
            $('#balance_type').val(0)
         }
      }
      if(keys[i]=='spo_id' && response.data[0]['spo_name']!=null){
          $('#spo').html("<option value='"+response.data[0][keys[i]]+"'>"+response.data[0]['spo_name']+"</option>")
      }
    }
    $('#imagex').attr('src','{{asset('storage/customer')}}/'+((response.data[0]['photo']==null) ? 'fixed.jpg' : response.data[0]['photo']))
  })
})
function AddNew(){
document.getElementById('myForm').reset();
$('#id').val('');
$('#exampleModalLabel').text("Add New Group");
$('#imagex').attr("src","{{URL::to('storage/admin-lte/dist/img/avatar5.png')}}");
$('.submit').text("@lang('key.buttons.save')");
$('#Modalx').modal('show');
}
 //ajax request from employee.js
function ajaxRequest(){
    $('.invalid-feedback').hide();
    $('input').css('border','1px solid rgb(209,211,226)');
    $('select').css('border','1px solid rgb(209,211,226)');
    let id=$('#id').val();
    let company_name=$('#company_name').val();
    let client_name=$('#name').val();    
    let spo=$('#spo option:selected').val();
    let contact=$('#contact option:selected').val();
    let opening_balance=$('#opening_balance').val();
    let maximum_due=$('#maximum_due').val();
    let phone1=$('#phone1').val();
    let phone2=$('#phone2').val();
    let email=$('#email').val();
    let birth_date=$('#birth_date').val();
    let mariage_date=$('#mariage_date').val();
    let adress=$('#adress').val();
    let city=$('#city').val();
    let postal_code=$('#postal_code').val();
    let stutus=$('#stutus').val();
    let group=$('#group_types').val();
    let balance_type=$('#balance_type').val();
    let file=document.getElementById('file').files;
    let formData= new FormData();

    formData.append('company_name',company_name);
    formData.append('name',client_name);    
    formData.append('spo',spo);    
    formData.append('contact',contact);
    formData.append('opening_balance',opening_balance);
    formData.append('balance_type',balance_type);
    formData.append('maximum_due',maximum_due);
    formData.append('phone1',phone1);
    formData.append('phone2',phone2);
    formData.append('email',email);
    formData.append('birth_date',birth_date);
    formData.append('mariage_date',mariage_date);
    formData.append('adress',adress);
    formData.append('city',city);
    formData.append('postal_code',postal_code);
    formData.append('stutus',stutus);
    formData.append('group_types',group);
    if (file[0]!=null) {
      formData.append('photo',file[0]); 
    }
    if(id){
        //axios post request
          axios.post('/admin/customer/'+$('#id').val(),formData)
          .then(function (response){
            if (response.data.message) {
              window.toastr.success(response.data.message);
              document.getElementById('myForm').reset();
              $('#imagex').attr('src','http://localhost/accounts/public/storage/admin-lte/dist/img/avatar5.png');
              $('.invalid-feedback').hide();
              $('.data-table').DataTable().ajax.reload();
              $('input').css('border','1px solid rgb(209,211,226)');
              $('select').css('border','1px solid rgb(209,211,226)');
              $('#exampleModal').modal('hide');
              ModalClose();
            }
            var keys=Object.keys(response.data[0]);
            for(var i=0; i<keys.length;i++){
                $('#'+keys[i]+'_msg').html(response.data[0][keys[i]][0]);
                $('#'+keys[i]).css('border','1px solid red');
                $('#'+keys[i]+'_msg').show();
              }
          })
           .catch(function (error) {
            console.log(error.request);
          });
     }else{
        axios.post('/admin/customer',formData)
          .then(function (response){
            if (response.data.message) {
              window.toastr.success(response.data.message);
              document.getElementById('myForm').reset();
              $('#imagex').attr('src','http://localhost/accounts/public/storage/admin-lte/dist/img/avatar5.png');
              $('.invalid-feedback').hide();
              $('.data-table').DataTable().ajax.reload();
              $('input').css('border','1px solid rgb(209,211,226)');
              $('select').css('border','1px solid rgb(209,211,226)');
              $('#exampleModal').modal('hide');
              ModalClose();
            }
            var keys=Object.keys(response.data[0]);
            for(var i=0; i<keys.length;i++){
                $('#'+keys[i]+'_msg').html(response.data[0][keys[i]][0]);
                $('#'+keys[i]).css('border','1px solid red');
                $('#'+keys[i]+'_msg').show();
              }
          })
           .catch(function (error) {
            console.log(error.request);
          });
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
    axios.delete('/admin/customer/'+id,{_method:'DELETE'})
      .then((res)=>{
        if (res.data.message=='success') {
          window.toastr.success('Supplier Deleted Success');
          $('.data-table').DataTable().ajax.reload();
        }
      })
      .catch((error)=>{
        console.log(error.request);
        alert(JSON.parse(error.request.response).message)
      })
  }
});
 })
   function ModalClose(){
  document.getElementById('myForm').reset();
  $('#photo').attr('src','http://localhost/accounts/public/storage/admin-lte/dist/img/avatar5.png');
  $('.invalid-feedback').hide();
  $('input').css('border','1px solid rgb(209,211,226)');
  $('select').css('border','1px solid rgb(209,211,226)');
  $("#spo").empty().change();  
  // $("#spo").html('');
  $('#Modalx').modal('hide')
 }
 $('#birth_date,#mariage_date').daterangepicker({
        showDropdowns: true,
        singleDatePicker: true,
        locale:{
            format: 'DD-MM-YYYY',
            separator:' to ',
            customRangeLabel: "Custom",
        },
        minDate: '01-01-1970',
        maxDate: '01/01/2050'
  }) </script>
@endsection
