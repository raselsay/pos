@extends('layouts.master')
@section('content')
@php
  $lang=App\MultiSetting::select('value')->where('name','language')->first();
  App::setLocale(isset($lang->value) ? $lang->value : '' );
@endphp
<div class="container">
	<div class="card m-0">
    <div class="card-header pt-3  flex-row align-items-center justify-content-between">
      <h5 class="m-0 font-weight-bold">@lang('key.unit.unit.title')</h5>
     </div>
    <div class="card-body px-3 px-md-5">
		  	<button type="button" class="btn btn-primary" onclick="addNew()">
          @lang('key.unit.unit.add_new') <i class="fas fa-plus"></i>
        </button>

        <!-- Modal -->
        <div class="modal fade" id="Modalx" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">@lang('key.unit.unit.title_modal')</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="ModalClose()">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <!--modal body-->
              <div class="modal-body">
                
                <form id="myForm">
                  <input type="hidden" id="id">
                    <div class="form-group">
                      <label class="font-weight-bold">@lang('key.unit.unit.unit'):</label>
                      <input class="form-control form-control-sm" id="product_type"  type="text" placeholder="@lang('key.unit.unit.unit_placeholder')">
                      <div id="product_type_msg" class="invalid-feedback">
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
                        <th>@lang('key.unit.unit.no')</th>
                        <th>@lang('key.unit.unit.unit')</th>
                        <th>@lang('key.unit.unit.created_by')</th>
                        <th>@lang('key.unit.unit.action')</th>
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
          url:"{{ URL::to('/admin/product_type') }}"
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
$('#id').val('');
$('#exampleModalLabel').text("@lang('key.unit.unit.title_modal')");
$('.submit').text("@lang('key.buttons.save')");
$('#Modalx').modal('show');
}
 $(document).on('click','.edit',function(){
  $('#exampleModalLabel').text("@lang('key.unit.unit.title_update')");
  $('.submit').text("@lang('key.buttons.update')");
  $('#Modalx').modal('show');
  id=$(this).data('id');
  $('#id').val(id);
  axios.get('admin/product_type/'+id)
  .then(function(response){
    var keys=Object.keys(response.data);
    for (var i = 0; i < keys.length; i++) {
      if (keys[i]=='name'){
      $('#product_type').val(response.data[keys[i]]);
      }    
    }
  })
})
 //ajax request from employee.js
function ajaxRequest(){
    $('.submit').attr("disabled",true);
    $('.invalid-feedback').hide();
    $('input').css('border','1px solid rgb(209,211,226)');
    $('select').css('border','1px solid rgb(209,211,226)');
    let id=$('#id').val();
    let productType=$('#product_type').val();
    let formData= new FormData();
    formData.append('product_type',productType);
    //axios post request
  if (!id) {
     axios.post('/admin/product_type',formData)
    .then(function (response){
      if (response.data.message) {
        window.toastr.success(response.data.message);
        $('.data-table').DataTable().ajax.reload();
        $("#Modalx").modal('hide');
        $('.submit').attr('disabled',false);
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
       axios.post('/admin/product_type/'+id,formData)
      .then(function (response){
        if (response.data.message=='success') {
          window.toastr.success('Product Type Updated Success');
          $('.data-table').DataTable().ajax.reload();
          $("#Modalx").modal('hide');
          $('.submit').attr('disabled',false);
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
  axios.delete('/admin/product_type/'+id,{_method:'DELETE'})
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
 function ModalClose(){
  $('input').val('');
  $("select option[value='']").attr('selected',true);
  $('.invalid-feedback').hide();
  $('input').css('border','1px solid rgb(209,211,226)');
  $('select').css('border','1px solid rgb(209,211,226)');
 }
 </script>
@endsection
