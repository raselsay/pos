@extends('layouts.master')
@section('content')
@section('link')
@php
  $lang=App\MultiSetting::select('value')->where('name','language')->first();
  App::setLocale(isset($lang->value) ? $lang->value : '' );
@endphp
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
      <h5 class="m-0 font-weight-bold">@lang('key.invoice.today_status.title')</h5>
     </div>
    <div class="card-body px-3 px-md-5">
        <!-- Modal --> 
        <a class="btn btn-primary text-light font-weight-bold mb-2" href="{{URL::to('admin/installment')}}">
          @lang('key.invoice.today_status.new_installment')<i class="ml-1 fas fa-plus"></i>
        </a>
        <table class="table table-sm text-center table-bordered table-striped data-table">
          <thead>
            <tr>
              <th>@lang('key.invoice.today_status.no')</th>
              <th>@lang('key.invoice.today_status.date')</th>
              <th>@lang('key.invoice.today_status.customer_name')</th>
              <th>@lang('key.invoice.today_status.phone')</th>
              <th>@lang('key.invoice.today_status.paid')</th>
              <th>@lang('key.invoice.today_status.total_installment')</th>
              <th>@lang('key.invoice.today_status.payable')</th>
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
          url:"{{ URL::to('/admin/day_by_day_installment_status') }}"
        },
        columns:[
          {
            data:'DT_RowIndex',
            name:'DT_RowIndex',
            orderable:false,
            searchable:false
          },
          {
            data:'pay_date',
            name:'pay_date',
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
            data:'paid',
            name:'paid',
          },
          {
            data:'total_inst',
            name:'total_inst',
          },
          {
            data:'total_payable',
            name:'total_payable',
          },
        ]
    });
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
  return false;
});
 })
$('table').on('click','.edit',function(){
  id=$(this).data('id');
  axios.get('admin/installment_status/'+id)
  .then((res)=>{
  })
})
   function ModalClose(){
  document.getElementById('myForm').reset();
  $('#photo').attr('src','http://localhost/accounts/public/storage/admin-lte/dist/img/avatar5.png');
  $('.invalid-feedback').hide();
  $('input').css('border','1px solid rgb(209,211,226)');
  $('select').css('border','1px solid rgb(209,211,226)');
 }
 </script>
@endsection
