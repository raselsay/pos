@extends('layouts.master')
@section('content')
@php
  $lang=App\MultiSetting::select('value')->where('name','language')->first();
  App::setLocale(isset($lang->value) ? $lang->value : '' );
@endphp
<div class="container">
	<div class="card m-0">
    <div class="card-header pt-3  flex-row align-items-center justify-content-between">
      <h5 class="m-0 font-weight-bold">@lang('key.transport.transport.title')</h5>
     </div>
    <div class="card-body px-3 px-md-5">
		  	<button type="button" class="btn btn-primary" {{-- data-toggle="modal" data-target="#exampleModal" --}} onclick="AddNew()">
          @lang('key.transport.transport.add_new') <i class="fas fa-plus"></i>
        </button>

        <!-- Modal -->
        <div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" id="Modalx">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="ModalClose()">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <!--modal body-->
              <div class="modal-body">
                <form id="myForm">
                  <input type="hidden" id="id">
                  <div class="form-group">
                    <label for="name" class="font-weight-bold">@lang('key.transport.transport.transport_name'):</label>
                    <input class="form-control form-control-sm" id="name"  type="text" placeholder="@lang('key.transport.transport.transport_name_placeholder')">
                    <div id="name_msg" class="invalid-feedback">
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="phone" class="font-weight-bold">@lang('key.transport.transport.phone'):</label>
                    <input class="form-control form-control-sm" id="phone"  type="text" placeholder="@lang('key.transport.transport.phone_placeholder')">
                    <div id="phone_msg" class="invalid-feedback">
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="phone" class="font-weight-bold">@lang('key.transport.transport.driver_phone'):</label>
                    <input class="form-control form-control-sm" id="driver_phone"  type="text" placeholder="@lang('key.transport.transport.driver_phone_placeholder')">
                    <div id="driver_phone_msg" class="invalid-feedback">
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="adress" class="font-weight-bold">@lang('key.transport.transport.adress'):</label>
                    <input class="form-control form-control-sm" id="adress"  type="text" placeholder="@lang('key.transport.transport.adress_placeholder')">
                    <div id="adress_msg" class="invalid-feedback">
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="adress" class="font-weight-bold">@lang('key.transport.transport.type'):</label>
                    <select class="form-control form-control-sm" id="type">
                      <option value="">--select--</option>
                      <option value="Import">@lang('key.transport.transport.import')</option>
                      <option value="Export">@lang('key.transport.transport.export')</option>
                    </select>
                    <div id="type_msg" class="invalid-feedback">
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="opening_balance" class="font-weight-bold">@lang('key.transport.transport.status'):</label>
                        <select class="form-control form-control-sm" id="status">
                        <option value="1">@lang('key.transport.transport.active')</option>
                        <option value="0">@lang('key.transport.transport.deactive')</option>
                        </select>
                        <div id="status_msg" class="invalid-feedback">
                        </div>
                  </div>
                  
                </form>
               <!--end second column -->
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="ModalClose()" data-dismiss="modal">@lang('key.buttons.close')</button>
                <button type="button" class="btn btn-primary submit" onclick="ajaxRequest($('#id').val())"></button>
              </div>
            </div>
          </div>
        </div>
        {{-- datatable start --}}
        {{-- <div class="container-fluid" id="container-wrapper"> --}}
            <!-- Datatables -->
                <div class="table-responsive mt-2">
                  <table class="table table-sm table-bordered table-striped align-items-center display table-flush data-table">
                    <thead class="thead-light">
                     <tr>
                        <th>@lang('key.transport.transport.no')</th>
                        <th>@lang('key.transport.transport.transport_name')</th>
                        <th>@lang('key.transport.transport.phone')</th>
                        <th>@lang('key.transport.transport.adress')</th>
                        <th>@lang('key.transport.transport.driver_phone')</th>
                        <th>@lang('key.transport.transport.status')</th>
                        <th>@lang('key.transport.transport.type')</th>
                        <th>@lang('key.transport.transport.action')</th>
                    </tr>
                    </thead>
                    <tbody>
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
          url:"{{ URL::to('/admin/transport') }}"
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
            data:'phone',
            name:'phone',
          },
          {
            data:'adress',
            name:'adress',
          },
           {
            data:'driver_phone',
            name:'driver_phone',
          },
          {
            data:'status',
            name:'status',
          },
           {
            data:'type',
            name:'type',
          },
          {
            data:'action',
            name:'action',
          }
        ]
    });
function AddNew(){
document.getElementById('myForm').reset();
$('#id').val('');
$('#exampleModalLabel').text("@lang('key.transport.transport.title_modal')");
$('.submit').text("@lang('key.buttons.save')");
$('#Modalx').modal('show');
}
$(document).on('click','.edit',function(){
  $('#exampleModalLabel').text("@lang('key.transport.transport.title_update')");
  $('.submit').text("@lang('key.buttons.update')");
$('#Modalx').modal('show');
  id=$(this).data('id');
  $('#id').val(id);
  axios.get('admin/get_transport/'+id)
  .then(function(response){
    $('#name').val(response.data.name);
    $('#email').val(response.data.email);
    $('#adress').val(response.data.adress);
    $('#phone').val(response.data.phone);
    $('#type').val(response.data.type);
  })
})
 //ajax request from employee.js
function ajaxRequest(id){
    $('.submit').attr("disabled",true);
    $('.invalid-feedback').hide();
    $('input').css('border','1px solid rgb(209,211,226)');
    $('select').css('border','1px solid rgb(209,211,226)');
    let name=$('#name').val();
    let phone=$('#phone').val();
    let driver_phone=$('#driver_phone').val();
    let adress=$('#adress').val();
    let type=$('#type').val();
    let status=$('#status').val();
    let formData= new FormData();
    formData.append('name',name);
    formData.append('phone',phone);
    formData.append('driver_phone',driver_phone);
    formData.append('adress',adress);
    formData.append('type',type);
    formData.append('status',status);
    //axios post request
    if (!id){
         axios.post('/admin/transport',formData)
        .then(function (response){
          if (response.data.message) {
            window.toastr.success(response.data.message);
            $('.data-table').DataTable().ajax.reload();
            document.getElementById('myForm').reset();
            $("#Modalx").modal('hide');
            ModalClose();
            $('.submit').attr("disabled",false);

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
      axios.post('/admin/transport/'+id,formData)
        .then(function (response){
          if (response.data.message) {
            window.toastr.success(response.data.message);
            $('.data-table').DataTable().ajax.reload();
            $("#Modalx").modal('hide');
            ModalClose();
            $('.submit').attr("disabled",false);
          }
          var keys=Object.keys(response.data[0]);
          for(var i=0; i<keys.length;i++){
              $('#'+keys[i]+'_msg').html(response.data[0][keys[i]][0]);
              $('#'+keys[i]).css('border','1px solid red');
              $('#'+keys[i]+'_msg').show();
            }
        })
         .catch(function(error){
          console.log(error.request);
        });
    }
  

 }
 function ModalClose(){
  document.getElementById('myForm').reset();
  $('.invalid-feedback').hide();
  $('input').css('border','1px solid rgb(209,211,226)');
  $('select').css('border','1px solid rgb(209,211,226)');
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
    axios.delete('/admin/supplier/'+id,{_method:'DELETE'})
      .then((res)=>{
        if (res.data.message=='success') {
          window.toastr.success('Supplier Deleted Success');
          $('.data-table').DataTable().ajax.reload();
        }
      })
      .catch((error)=>{
        console.log(error.request);
      })
  }
});
 })
 </script>
@endsection
