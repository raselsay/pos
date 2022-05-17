@extends('layouts.master')
@section('content')
<section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Manage Site</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ URL::to('/home') }}">Home</a></li>
              <li class="breadcrumb-item">Site</li>
              <li class="breadcrumb-item active">Manage Site</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>
<div class="container">
	<div class="card m-0">
    <div class="card-header pt-3  flex-row align-items-center justify-content-between">
      <h5 class="m-0 font-weight-bold">Manage Site</h5>
     </div>
    <div class="card-body px-3 px-md-5">
		  	<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">
          Add New <i class="fas fa-plus"></i>
        </button>

        <!-- Modal -->
        <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Add 
                New Site</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="ModalClose()">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <!--modal body-->
              <div class="modal-body">
              	<div class="form-group">
                  <label class="font-weight-bold">Customer:</label>
                  <select class="form-control form-control-sm" id="customer">
                  </select>
                  <div id="customer_msg" class="invalid-feedback">
                  </div>
                </div>
                <div class="form-group">
                  <label class="font-weight-bold">Site Name:</label>
                  <input class="form-control form-control-sm" id="name"  type="text" placeholder="Enter Site Name...">
                  <div id="name_msg" class="invalid-feedback">
                  </div>
                </div>
                <div class="form-group">
                  <label class="font-weight-bold">Adress:</label>
                  <input class="form-control form-control-sm" id="adress"  type="text" placeholder="Enter Adress...">
                  <div id="capacity_msg" class="invalid-feedback">
                  </div>
                </div>
               <div class="form-group">
                  <label class="font-weight-bold">Mobile:</label>
                  <input type="text" class="form-control form-control-sm" id="mobile" placeholder="Enter Area....">
                  <div id="mobile_msg" class="invalid-feedback">
                  </div>
                </div>
                <div class="form-group">
                  <label class="font-weight-bold">Status:</label>
                  <select class="form-control form-control-sm" id="status">
                  <option value="1">Active</option>
                  <option value="0">Deactive</option>
                   </select>
                  <div id="status_msg" class="invalid-feedback">
                  </div>
                </div>
               <!--end 2nd column -->
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="ModalClose()" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="ajaxRequest()">Save changes</button>
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
                        <th>No.</th>
                        <th>Customer</th>
                        <th>Site Name</th>
                        <th>Adress</th>
                        <th>Mobile</th>                        
                        <th>Status</th>

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
          url:"{{ URL::to('/admin/customer_site') }}"
        },
        columns:[
          {
            data:'DT_RowIndex',
            name:'DT_RowIndex',
            orderable:false,
            searchable:false
          },
          {
            data:'customer_name',
            name:'customer_name',
          },
          {
            data:'name',
            name:'name',
          },
          {
            data:'adress',
            name:'adress',
          },
          {
            data:'mobile',
            name:'mobile',
          },
          {
            data:'status',
            name:'status',
          },
          {
            data:'action',
            name:'action',
          }
        ]
    });
 //ajax request from employee.js
function ajaxRequest(){
    $('.invalid-feedback').hide();
    $('input').css('border','1px solid rgb(209,211,226)');
    $('select').css('border','1px solid rgb(209,211,226)');
    let customer=$('#customer').val();
    let name=$('#name').val();
    let adress=$('#adress').val();
    let mobile=$('#mobile').val();
    let status=$('#status').val();
    let formData= new FormData();
    formData.append('customer',customer);
    formData.append('name',name);
    formData.append('adress',adress);
    formData.append('mobile',mobile);
    formData.append('status',status);
    //axios post request
  axios.post('/admin/customer_site',formData)
  .then(function (response){
    if (response.data.message) {
      window.toastr.success(response.data.message);
      $('.data-table').DataTable().ajax.reload();
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
 function ModalClose(){
  $('input').val('');
  $("select option[value='']").attr('selected',true);
  $('.invalid-feedback').hide();
  $('input').css('border','1px solid rgb(209,211,226)');
  $('select').css('border','1px solid rgb(209,211,226)');
  $('#exampleModal').modal('hide');
 }
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
 </script>
@endsection
