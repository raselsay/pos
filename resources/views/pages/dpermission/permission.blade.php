@php
  $lang=App\MultiSetting::select('value')->where('name','language')->first();
  App::setLocale(isset($lang->value) ? $lang->value : '' );
@endphp
@extends('layouts.master')
@section('content')
<div class="container">
	<div class="card m-0">
    <div class="card-header pt-3  flex-row align-items-center justify-content-between">
      <h5 class="m-0 font-weight-bold">Delivery Permission</h5>
     </div>
    <div class="card-body px-3 px-md-5">
		  	<button type="button" class="btn btn-primary" onclick="addNew()">
          @lang('key.permission.add_new')<i class="fas fa-plus"></i>
        </button>
        <!-- Modal -->
        <div class="modal fade"  tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" id="Modalx">
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
                    <label class="font-weight-bold">Delivery User:</label>
                    <select class="form-control form-control-sm" name="delivery" id="delivery">
                      <option value="">Select</option>
                      @foreach($user as $users)
                      <option value="{{$users->id}}">{{$users->name}}</option>
                      @endforeach
                    </select>
                    <div id="delivery_msg" class="invalid-feedback">
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="font-weight-bold">@lang('key.permission.warehouse_permission.warehouse'):</label>
                    <select class="form-control form-control-sm" name="store" id="store">
                      <option value="">Select</option>
                      @foreach($store as $stores)
                      <option value="{{$stores->id}}">{{$stores->name}}</option>
                      @endforeach
                    </select>
                    <div id="store_msg" class="invalid-feedback">
                    </div>
                  </div>
                </form>
               <!--end 2nd column -->
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="ModalClose()" data-dismiss="modal">@lang('key.buttons.close')</button>
                <button type="button" class="btn btn-primary submit" onclick="ajaxRequest()">@lang('key.buttons.save')</button>
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
                        <th>No</th>
                        <th>Delivery Admin</th>
                        <th>Store</th>
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
          url:"{{ URL::to('/admin/dpermission') }}"
        },
        columns:[
          {
            data:'DT_RowIndex',
            name:'DT_RowIndex',
            orderable:false,
            searchable:false
          },
          {
            data:'del_admin',
            name:'del_admin',
          },
          {
            data:'store_name',
            name:'store_name',
          },
          {
            data:'action',
            name:'action',
          }
        ]
    });
function addNew(){
document.getElementById('myForm').reset();
$('#Modalx').modal('show');
$('#id').val('');
$('#exampleModalLabel').text("@lang('key.permission.warehouse_permission.title_modal')");
$('.submit').text("@lang('key.buttons.save')");
}

function ajaxRequest(){
  // $('.submit').addClass('disabled').attr('disabled',true);
    $('.invalid-feedback').hide();
    $('input').css('border','1px solid rgb(209,211,226)');
    $('select').css('border','1px solid rgb(209,211,226)');
    let delivery=$('#delivery').val();
    let store=$('#store').val();
    let formData=new FormData();
    formData.append('delivery',delivery);
    formData.append('store',store);
    let id=$('#id').val();
    //axios post request
    if (!id) {
        axios.post('/admin/dpermission',formData)
        .then(function (response){
        console.log(response);
        if (response.data.message) {
          window.toastr.success(response.data.message);
          $('.data-table').DataTable().ajax.reload();
          $('.submit').removeClass('disabled').attr('disabled',false);
        }
        var keys=Object.keys(response.data);
        for(var i=0; i<keys.length;i++){
            $('#'+keys[i]+'_msg').html(response.data[keys[i]][0]);
            $('#'+keys[i]).css('border','1px solid red');
            $('#'+keys[i]+'_msg').show();
          }
        })
        .catch(function (error) {
        console.log(error.request);
        });
    }else{
      axios.post('/admin/dpermission/'+id,formData)
        .then(function (response){
        console.log(response);
        if (response.data.message) {
          window.toastr.success(response.data.message);
          $('.data-table').DataTable().ajax.reload();
          $('.submit').removeClass('disabled').attr('disabled',false);
        }
        var keys=Object.keys(response.data);
        for(var i=0; i<keys.length;i++){
            $('#'+keys[i]+'_msg').html(response.data[keys[i]][0]);
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
 }
 </script>
@endsection
