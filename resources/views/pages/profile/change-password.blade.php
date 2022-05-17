@extends('layouts.master')
@section('content')
<section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Change Password</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ URL::to('/home') }}">Home</a></li>
              <li class="breadcrumb-item">Option</li>
              <li class="breadcrumb-item active">Change Password</li>
            </ol>
          </div>
        </div>
      </div>
      <!-- /.container-fluid -->
    </section>
<div class="container">
	<div class="card m-0">
    <div class="card-header pt-3  flex-row align-items-center justify-content-between">
      <h5 class="m-0 font-weight-bold">Change Password</h5>
     </div>
    <div class="card-body px-3 px-md-5">
		  <div class="col-12 col-md-6">
        <form action="" id="myForm">
          <div class="form-group">
            <label class="font-weight-bold">Old Password:</label>
            <input class="form-control form-control-sm" id="old_password"  type="password" placeholder="Enter Old Password...">
            <div id="old_password_msg" class="invalid-feedback">
            </div>
          </div>
          <div class="form-group">
            <label class="font-weight-bold">New Password:</label>
            <input class="form-control form-control-sm" id="password"  type="password" placeholder="Enter New Password...">
            <div id="password_msg" class="invalid-feedback">
            </div>
          </div>
          <div class="form-group">
            <label class="font-weight-bold">Confirm Password:</label>
            <input class="form-control form-control-sm" id="password_confirmation"  type="password" placeholder="Enter Confirm New Password...">
            <div id="password_confirmation_msg" class="invalid-feedback">
            </div>
          </div>
        </form>
      </div>
       <!--end 2nd column -->
      </div>
              <div class="card-footer">
                <button type="button" onclick="ModalClose()" class="btn btn-secondary ml-4">Reset</button>
                <button type="button" class="btn btn-primary" onclick="ajaxRequest()">Save changes</button>
              </div>
            </div>
          </div>
   
@endsection
@section('script')
<script type="text/javascript">
  
 //ajax request from employee.js
function ajaxRequest(){
    $('.invalid-feedback').hide();
    $('input').css('border','1px solid rgb(209,211,226)');
    $('select').css('border','1px solid rgb(209,211,226)');
    let old_password=$('#old_password').val();
    let new_password=$('#password').val();
    let confirm_password=$('#password_confirmation').val();
    let formData=new FormData();
    formData.append('old_password',old_password); 
    formData.append('password',new_password);
    formData.append('password_confirmation',confirm_password); 
    //axios post request
  axios.post('/admin/change_password',formData)
  .then(function (response){
    if (response.data.message) {
      window.toastr.success(response.data.message);
      $('.data-table').DataTable().ajax.reload();
      ModalClose()
      return false;
    }
    var keys=Object.keys(response.data.error)
    for(var i=0; i<keys.length;i++){
        $('#'+keys[i]+'_msg').html(response.data['error'][keys[i]][0]);
        $('#'+keys[i]).css('border','1px solid red');
        $('#'+keys[i]+'_msg').show();
      }
  })
   .catch(function (error) {
    console.log(error.request);
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
