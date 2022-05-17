@extends('layouts.master')
@section('content')
@php
  $lang=App\MultiSetting::select('value')->where('name','language')->first();
  App::setLocale(isset($lang->value) ? $lang->value : '' );
@endphp
<div class="container">
	<div class="card m-0">
    <div class="card-header pt-3  flex-row align-items-center justify-content-between">
      <h5 class="m-0 font-weight-bold">@lang('key.category.category.title')</h5>
     </div>
    <div class="card-body px-3 px-md-5">
		  	<button type="button" class="btn btn-primary" onclick="addNew()">
          @lang('key.category.category.add_new')<i class="fas fa-plus"></i>
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
                    <label class="font-weight-bold">@lang('key.category.category.name'):</label>
                    <input class="form-control form-control-sm" id="name"  type="text" placeholder="Enter Category Name...">
                    <div id="category_msg" class="invalid-feedback">
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
                        <th>@lang('key.category.category.no')</th>
                        <th>@lang('key.category.category.name')</th>
                        <th>@lang('key.category.category.created_by')</th>
                        <th>@lang('key.category.category.action')</th>
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
          url:"{{ URL::to('/admin/category') }}"
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
            data:'username',
            name:'username',
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
$('#exampleModalLabel').text("@lang('key.category.category.title_modal')");
$('.submit').text("@lang('key.buttons.save')");
}
 $(document).on('click','.edit',function(){
  $('#exampleModalLabel').text("@lang('key.category.category.title_update')");
  $('.submit').text("@lang('key.buttons.update')");
  $('#Modalx').modal('show');
  id=$(this).data('id');
  $('#id').val(id);
  axios.get('admin/category_get/'+id)
  .then(function(response){
    var keys=Object.keys(response.data);
    for (var i = 0; i < keys.length; i++) {
      if (keys[i]=='name'){
      $('#name').val(response.data[keys[i]]);
      }    
    }
  })
})
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
    axios.delete('/admin/category/'+id,{_method:'DELETE'})
      .then((res)=>{
        if (res.data.message=='success') {
          window.toastr.success('Product Type Deleted Success');
          $('.data-table').DataTable().ajax.reload();
        }
      })
      .catch((error)=>{
        console.log(error.request);
      })
  }
});
})
 //ajax request from employee.js
function ajaxRequest(){
  $('.submit').addClass('disabled').attr('disabled',true);
    $('.invalid-feedback').hide();
    $('input').css('border','1px solid rgb(209,211,226)');
    $('select').css('border','1px solid rgb(209,211,226)');
    let name=$('#name').val();
    let formData=new FormData();
    formData.append('name',name);
    let id=$('#id').val();
    //axios post request
    if (!id) {
        axios.post('/admin/category',formData)
        .then(function (response){
        if (response.data.message) {
          window.toastr.success(response.data.message);
          $('.data-table').DataTable().ajax.reload();
          $('.submit').removeClass('disabled').attr('disabled',false);
          $('#Modalx').modal('hide');
          ModalClose();
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
      axios.post('/admin/category/'+id,formData)
        .then(function (response){
        if (response.data.message) {
          window.toastr.success(response.data.message);
          $('.data-table').DataTable().ajax.reload();
          $('.submit').removeClass('disabled').attr('disabled',false);
          $('#Modalx').modal('hide');
          ModalClose();
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
