@extends('layouts.master')
@section('content')
@php
  $lang=App\MultiSetting::select('value')->where('name','language')->first();
  App::setLocale(isset($lang->value) ? $lang->value : '' );
@endphp
<div class="container">
	<div class="card m-0">
    <div class="card-header pt-3  flex-row align-items-center justify-content-between">
      <h5 class="m-0 font-weight-bold">@lang('key.supplier.supplier.title')</h5>
     </div>
    <div class="card-body px-3 px-md-5">
		  	<button type="button" class="btn btn-primary" {{-- data-toggle="modal" data-target="#exampleModal" --}} onclick="AddNew()">
          @lang('key.supplier.supplier.add_new') <i class="fas fa-plus"></i>
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
                    <label for="name" class="font-weight-bold">@lang('key.supplier.supplier.name'):</label>
                    <input class="form-control form-control-sm" id="name"  type="text" placeholder="@lang('key.supplier.supplier.name_placeholder')">
                    <div id="name_msg" class="invalid-feedback">
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="email" class="font-weight-bold">@lang('key.supplier.supplier.email'):</label>
                    <input class="form-control form-control-sm" id="email"  type="text" placeholder="@lang('key.supplier.supplier.email_placeholder')">
                    <div id="email_msg" class="invalid-feedback">
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="phone" class="font-weight-bold">@lang('key.supplier.supplier.phone'):</label>
                    <input class="form-control form-control-sm" id="phone"  type="text" placeholder="@lang('key.supplier.supplier.phone_placeholder')">
                    <div id="phone_msg" class="invalid-feedback">
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="adress" class="font-weight-bold">@lang('key.supplier.supplier.adress'):</label>
                    <input class="form-control form-control-sm" id="adress"  type="text" placeholder="@lang('key.supplier.supplier.adress_placeholder')">
                    <div id="adress_msg" class="invalid-feedback">
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="opening_balance" class="font-weight-bold">@lang('key.supplier.supplier.opening_balance'):</label>
                    <div class='row'>
                      <div class='col-sm-9'>
                          <input class="form-control form-control-sm" id="opening_balance"  type="text" placeholder="@lang('key.supplier.supplier.opening_balance_placeholder')">
                          <div id="opening_balance_msg" class="invalid-feedback">
                          </div>
                        </div>
                        <div class='col-sm-3'>
                          <select class="form-control form-control-sm" id="balance_type" >
                          <option value="1">@lang('key.supplier.supplier.balance')</option>
                          <option value="0">@lang('key.supplier.supplier.due')</option>
                          </select>
                          <div id="balance_type_msg" class="invalid-feedback">
                          </div>
                        </div>
                      </div>
                  </div>
                  <div class="form-group">
                    <label for="adress" class="font-weight-bold">@lang('key.supplier.supplier.supplier_type'):</label>
                    <select class="form-control form-control-sm" id="supplier_type">
                      <option value="">--select--</option>
                      <option value="Distributor">@lang('key.supplier.supplier.distributor')</option>
                      <option value="Whole Saler">@lang('key.supplier.supplier.whole_saler')</option>
                      <option value="Company">@lang('key.supplier.supplier.company')</option>
                    </select>
                    <div id="supplier_type_msg" class="invalid-feedback">
                    </div>
                  </div>
                </form>
               <!--end second column -->
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="ModalClose()" data-dismiss="modal">Close</button>
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
                        <th>@lang('key.supplier.supplier.no')</th>
                        <th>@lang('key.supplier.supplier.name')</th>
                        <th>@lang('key.supplier.supplier.phone')</th>
                        <th>@lang('key.supplier.supplier.adress')</th>
                        <th>@lang('key.supplier.supplier.supplier_type')</th>
                        <th>Action</th>
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
          url:"{{ URL::to('/admin/supplier') }}"
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
            data:'supplier_type',
            name:'supplier_type',
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
$('#exampleModalLabel').text("@lang('key.supplier.supplier.title_modal')");
$('.submit').text("@lang('key.buttons.save')");
$('#Modalx').modal('show');
}
$(document).on('click','.edit',function(){
  $('#exampleModalLabel').text("@lang('key.supplier.supplier.title_update')");
  $('.submit').text('Update');
$('#Modalx').modal('show');
  id=$(this).data('id');
  $('#id').val(id);
  axios.get('admin/get-supplier/'+id)
  .then(function(response){
    $('#name').val(response.data[0].name);
    $('#email').val(response.data[0].email);
    $('#adress').val(response.data[0].adress);
    $('#phone').val(response.data[0].phone);
    $('#supplier_type').val(response.data[0].supplier_type);

    $('#opening_balance').val(Math.abs(response.data[0].opening_balance));
    if (response.data[0].opening_balance>0) {
        balance_type=1;
    }else{
      balance_type=0;
    }
    $('#balance_type').val(balance_type);
  })
})
 //ajax request from employee.js
function ajaxRequest(id){
    $('.invalid-feedback').hide();
    $('input').css('border','1px solid rgb(209,211,226)');
    $('select').css('border','1px solid rgb(209,211,226)');
    let name=$('#name').val();
    let email=$('#email').val();
    let adress=$('#adress').val();
    let phone=$('#phone').val();
    let opening_balance=$('#opening_balance').val();
    let balance_type=$('#balance_type').val();
    let supplierType=$('#supplier_type').val();
    let formData= new FormData();
    formData.append('name',name);
    formData.append('email',email);
    formData.append('adress',adress);
    formData.append('phone',phone);
    formData.append('opening_balance',opening_balance);
    formData.append('balance_type',balance_type);
    formData.append('supplier_type',supplierType);
    //axios post request
    if (!id){
         axios.post('/admin/supplier',formData)
        .then(function (response){
          if (response.data.message=='success') {
            window.toastr.success('Supplier Added Success');
            $('.data-table').DataTable().ajax.reload();
            ModalClose()
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
      axios.post('/admin/supplier/'+id,formData)
        .then(function (response){
          if (response.data.message=='success') {
            window.toastr.success('Supplier Updated Success');
            $('.data-table').DataTable().ajax.reload();
            ModalClose()
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
 function ModalClose(){
  document.getElementById('myForm').reset();
  $('.invalid-feedback').hide();
  $('input').css('border','1px solid rgb(209,211,226)');
  $('select').css('border','1px solid rgb(209,211,226)');
  $('#Modalx').modal('hide')
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
