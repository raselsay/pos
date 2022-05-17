@extends('layouts.master')
@section('content')
@php
  $lang=App\MultiSetting::select('value')->where('name','language')->first();
  App::setLocale(isset($lang->value) ? $lang->value : '' );
@endphp
<section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>{{__('key.bank.title')}}</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ URL::to('/home') }}">Home</a></li>
              <li class="breadcrumb-item">Bank</li>
              <li class="breadcrumb-item active">Manage Bank</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>
<div class="container">
	<div class="card m-0">
    <div class="card-header pt-3  flex-row align-items-center justify-content-between">
      <h5 class="m-0 font-weight-bold">{{__('key.bank.title')}}</h5>
     </div>
    <div class="card-body px-3 px-md-5">
		  	<button type="button" class="btn btn-primary" onclick="addNew()">
          {{__('key.bank.add_new')}} <i class="fas fa-plus"></i>
        </button>

        <!-- Modal -->
        <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{__('key.bank.add
              _new')}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="ModalClose()">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <!--modal body-->
              <div class="modal-body">
                <form id="myForm">
                <input type="hidden" id="id">
                <div class="form-group">
                  <label class="font-weight-bold">{{__('key.bank.bank_name')}}:</label>
                  <input class="form-control form-control-sm" id="name"  type="text" placeholder="{{__('key.bank.bank_name_placeholder')}}">
                  <div id="name_msg" class="invalid-feedback">
                  </div>
                </div>
                <div class="form-group">
                  <label class="font-weight-bold">{{__('key.bank.account_number')}}:</label>
                  <input class="form-control form-control-sm" id="number"  type="text" placeholder="{{__('key.bank.account_number_placeholder')}}">
                  <div id="number_msg" class="invalid-feedback">
                  </div>
                </div>
                <div class="form-group">
                  <label class="font-weight-bold">{{__('key.bank.branch')}}:</label>
                  <input class="form-control form-control-sm" id="branch"  type="text" placeholder="{{__('key.bank.branch_placeholder')}}">
                  <div id="branch_msg" class="invalid-feedback">
                  </div>
                </div>
                <div class="input-group input-group-sm mb-3">
                  <div class="input-group-prepend">
                    <span class="input-group-text" id="inputGroup-sizing-sm">৳</span>
                  </div>
                  <input type="text" class="form-control form-control-sm" id="balance" placeholder="{{__('key.bank.balance_placeholder')}}" aria-label="Small" aria-describedby="inputGroup-sizing-sm">
                  <div id="balance_msg" class="invalid-feedback">
                  </div>
               </div>
               <!--end 2nd column -->
               </form>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="ModalClose()" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary submit" onclick="ajaxRequest($('#id').val())">Save changes</button>
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
                        <th>{{__('key.bank.no')}}</th>
                        <th>{{__('key.bank.bank_name')}}</th>
                        <th>{{__('key.bank.account_number')}}</th>
                        <th>{{__('key.bank.branch')}}</th>
                        <th>{{__('key.bank.balance')}}</th>
                        <th>{{__('key.bank.action')}}</th>
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
          url:"{{ URL::to('/admin/all_banks') }}"
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
            data:'number',
            name:'number',
          },
          {
            data:'branch',
            name:'branch',
          },
           {
            data:'total',
            name:'total',
          },
          {
            data:'action',
            name:'action',
          }
        ]
    });
function addNew(){
document.getElementById('myForm').reset();
ModalClose();
 $('#balance').attr('disabled',false);
$('#id').val('');
$('#exampleModalLabel').text('Add New Bank');
$('.submit').text('Save');
$('#exampleModal').modal('show');
}
$(document).on('click','.edit',function(){
  $('#exampleModalLabel').text('Update Bank Account');
  $('.submit').text('Update');
  $('#balance').attr('disabled',true);
$('#exampleModal').modal('show');
  ModalClose();
  id=$(this).data('id');
  $('#id').val(id);
  axios.get('admin/get_banks/'+id)
  .then(function(response){
    keys=Object.keys(response.data);
    for (var i = 0; i < keys.length; i++) {
      $('#'+keys[i]).val(response.data[keys[i]]);
    }
  })
})
 //ajax request from employee.js
function ajaxRequest(id){
    $('.submit').attr('disabled',true);
    $('.invalid-feedback').hide();
    $('input').css('border','1px solid rgb(209,211,226)');
    $('select').css('border','1px solid rgb(209,211,226)');
    let name=$('#name').val();
    let number=$('#number').val();
    let branch=$('#branch').val();
    let balance=$('#balance').val();
    let formData= new FormData();
    formData.append('name',name);
    formData.append('number',number);
    formData.append('branch',branch);
    formData.append('balance',balance);
    
    //axios post request
    if (!id) {
      axios.post('/admin/banks',formData)
      .then(function (response){
        if (response.data.message) {
          window.toastr.success(response.data.message);
          $('#exampleModal').modal('hide')
          ModalClose();
          $('.data-table').DataTable().ajax.reload();
          $('.submit').attr('disabled',false);
        }
        var keys=Object.keys(response.data[0]);
        for(var i=0; i<keys.length;i++){
            $('#'+keys[i]+'_msg').html(response.data[0][keys[i]][0]);
            $('#'+keys[i]).css('border','1px solid red');
            $('#'+keys[i]+'_msg').show();
            $('.submit').attr('disabled',false);
          }
      })
       .catch(function (error) {
        $('.submit').attr('disabled',false);
        alert((JSON.parse(error.request.response)).message);
      });
    }else{
      axios.post('/admin/banks/'+id,formData)
      .then(function (response){
        if (response.data.message) {
          window.toastr.success(response.data.message);
          $('.data-table').DataTable().ajax.reload();
          ModalClose();
          $('#exampleModal').modal('hide')
           $('.submit').attr('disabled',false);
        }
        var keys=Object.keys(response.data[0]);
        for(var i=0; i<keys.length;i++){
            $('#'+keys[i]+'_msg').html(response.data[0][keys[i]][0]);
            $('#'+keys[i]).css('border','1px solid red');
            $('#'+keys[i]+'_msg').show();
          }
      })
       .catch(function (error) {
        $('.submit').attr('disabled',false);
        alert((JSON.parse(error.request.response)).message);
      });
    }
 }
 function ModalClose(){
  $('input').val('');
  $("select option[value='']").attr('selected',true);
  $('.invalid-feedback').hide();
  $('input').css('border','1px solid rgb(209,211,226)');
  $('select').css('border','1px solid rgb(209,211,226)');
 }
 </script>
@endsection
