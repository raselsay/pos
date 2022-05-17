@php
$lang=App\MultiSetting::select('value')->where('name','language')->first();
  App::setLocale(isset($lang->value) ? $lang->value : '' );
@endphp
@extends('layouts.master')
@section('content')
<section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>@lang('key.name_relation.name_relation.title')</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ URL::to('/home') }}">Home</a></li>
              <li class="breadcrumb-item">Accounts</li>
              <li class="breadcrumb-item active">Accounts-head</li>
            </ol>
          </div>
        </div>
      </div>
      <!-- /.container-fluid -->
    </section>
<div class="container">
	<div class="card m-0">
    <div class="card-header pt-3  flex-row align-items-center justify-content-between">
      <h5 class="m-0 font-weight-bold">@lang('key.name_relation.name_relation.title')</h5>
     </div>
    <div class="card-body px-3 px-md-5">
		  	<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">
          @lang('key.name_relation.name_relation.add_new')<i class="fas fa-plus"></i>
        </button>

        <!-- Modal -->
        <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">@lang('key.name_relation.name_relation.title_modal')</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="ModalClose()">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <!--modal body-->
              <div class="modal-body">
                <form action="" id="myForm">
                  <div class="form-group">
                    <label class="font-weight-bold">@lang('key.name_relation.name_relation.name'):</label>
                    <select class="form-control form-control-sm" id="name">
                      <option value="">--SELECT--</option>
                      @foreach($names as $name)
                      <option value="{{$name->id}}">{{$name->name}}</option>
                      @endforeach
                    </select>
                    <div id="category_msg" class="invalid-feedback">
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="font-weight-bold">@lang('key.name_relation.name_relation.account_head'):</label>
                    <input class="form-control form-control-sm" id="rel_name"  type="text" placeholder="@lang('key.name_relation.name_relation.account_head_placeholder')">
                    <div id="rel_name_msg" class="invalid-feedback">
                    </div>
                  </div>
                </form>
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
                  <table class="table table-sm table-bordered table-striped align-items-center display table-flush data-table text-center">
                    <thead class="thead-light">
                     <tr>
                        <th>@lang('key.name_relation.name_relation.no')</th>
                        <th>@lang('key.name_relation.name_relation.name')</th> 
                        <th>@lang('key.name_relation.name_relation.account_head')</th> 
                        <th>@lang('key.name_relation.name_relation.created_by')</th>
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
          url:"{{ URL::to('/admin/name_relation') }}"
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
            data:'rel_name',
            name:'rel_name',
          },

          {
            data:'username',
            name:'username',
          },
        ]
    });
 //ajax request from employee.js
function ajaxRequest(){
    $('.invalid-feedback').hide();
    $('input').css('border','1px solid rgb(209,211,226)');
    $('select').css('border','1px solid rgb(209,211,226)');
    let name=$('#name').val();
    let rel_name=$('#rel_name').val();
    let formData=new FormData();
    formData.append('name',name); 
    formData.append('rel_name',rel_name); 
    //axios post request
  axios.post('/admin/name_relation',formData)
  .then(function (response){
    if (response.data.message) {
      window.toastr.success(response.data.message);
      $('.data-table').DataTable().ajax.reload();
      ModalClose();
      $('#exampleModal').modal('hide');
    }
    var keys=Object.keys(response.data[0]);
    for(var i=0; i<keys.length;i++){
        $('#'+keys[i]+'_msg').html(response.data[0][keys[i]][0]);
        $('#'+keys[i]).css('border','1px solid red');
        $('#'+keys[i]+'_msg').show();
      }
  })
   .catch(function (error) {
    console.log(error.request.response);
  });

 }
 function ModalClose(){
  document.getElementById('myForm').reset();
  $('.invalid-feedback').hide();
  $('input').css('border','1px solid rgb(209,211,226)');
  $('select').css('border','1px solid rgb(209,211,226)');
 }
 </script>
@endsection
