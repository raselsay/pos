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
            <h1>@lang('key.employee.employee_salary.title')</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ URL::to('/home') }}">Home</a></li>
              <li class="breadcrumb-item">Employee</li>
              <li class="breadcrumb-item active">Employee Salary</li>
            </ol>
          </div>
        </div>
      </div>
      <!-- /.container-fluid -->
    </section>
<div class="container">
  <div class="card m-0">
    <div class="card-header pt-3  flex-row align-items-center justify-content-between">
      <h5 class="m-0 font-weight-bold">@lang('key.employee.employee_salary.title')</h5>
     </div>
    <div class="card-body px-3 px-md-5">
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">
          @lang('key.employee.employee_salary.add_new')<i class="fas fa-plus"></i>
        </button>

        <!-- Modal -->
        <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">@lang('key.employee.employee_salary.add_new_salary')</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="ModalClose()">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <!--modal body-->
              <div class="modal-body">
                <form action="" id="myForm">
                  <div class="form-group">
                      <label class="font-weight-bold">@lang('key.employee.employee_salary.month'):</label>
                        <select class="form-control form-control-sm " id="month">
                          <option value="">Select</option>
                          @foreach($months as $value)
                          <option value="{{$value}}">{{$value}}</option>
                          @endforeach
                        </select>
                        <div id="month_msg" class="invalid-feedback">
                        </div>
                    </div>
                  <div class="row">
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                          <label class="font-weight-bold">@lang('key.employee.employee_salary.date'):</label>
                            <input class="form-control form-control-sm " id="date">
                          <div id="date_msg" class="invalid-feedback">
                          </div>
                        </div>
                        <div class="form-group">
                          <label class="font-weight-bold">@lang('key.employee.employee_salary.balance'):</label>
                            <input class="form-control form-control-sm " id="balance" disabled="">
                          <div id="balance_msg" class="invalid-feedback">
                          </div>
                        </div>
                        
                        <div class="form-group">
                          <label class="font-weight-bold">@lang('key.employee.employee_salary.medical_fund'):</label>
                            <input class="form-control form-control-sm " id="medical">
                          <div id="medical_msg" class="invalid-feedback">
                          </div>
                        </div>
                        <div class="form-group">
                          <label class="font-weight-bold">@lang('key.employee.employee_salary.over_time'):</label>
                            <input class="form-control form-control-sm " id="over_time">
                          <div id="over_time_msg" class="invalid-feedback">
                          </div>
                        </div>
                        <div class="form-group">
                          <label class="font-weight-bold">@lang('key.employee.employee_salary.tax'):</label>
                            <input class="form-control form-control-sm " id="tax">
                          <div id="tax_msg" class="invalid-feedback">
                          </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                          <label class="font-weight-bold">@lang('key.employee.employee_salary.employee'):</label>
                          <select class="form-control form-control-sm" id="employee"  type="text" placeholder="Enter Category Name..." onchange="getBasicSalary(this.value)">
                          </select>
                          <div id="employee_msg" class="invalid-feedback">
                          </div>
                        </div>
                        <div class="form-group">
                          <label class="font-weight-bold">@lang('key.employee.employee_salary.basic_salary'):</label>
                            <input class="form-control form-control-sm " id="basic" disabled="">
                          <div id="basic_msg" class="invalid-feedback">
                          </div>
                        </div>
                        
                        <div class="form-group">
                          <label class="font-weight-bold">@lang('key.employee.employee_salary.provident_fund'):</label>
                            <input class="form-control form-control-sm " id="provident">
                          <div id="provident_msg" class="invalid-feedback">
                          </div>
                        </div>
                        <div class="form-group">
                          <label class="font-weight-bold">@lang('key.employee.employee_salary.bonus'):</label>
                            <input class="form-control form-control-sm " id="bonus">
                          <div id="bonus_msg" class="invalid-feedback">
                          </div>
                        </div>
                        <div class="form-group">
                          <label class="font-weight-bold">@lang('key.employee.employee_salary.payable'):</label>
                            <input class="form-control form-control-sm " id="payable" disabled="">
                          <div id="payable_msg" class="invalid-feedback">
                          </div>
                        </div>
                    </div>
                  </div>
                  
                  
                </form>
              </div><!--end m body -->
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="ModalClose()" data-dismiss="modal">@lang('key.buttons.close')</button>
                <button type="button" class="btn btn-primary" onclick="ajaxRequest()">@lang('key.buttons.save')</button>
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
                        <th>@lang('key.employee.employee_salary.no')</th>
                        <th>@lang('key.employee.employee_salary.date')</th>
                        <th>@lang('key.employee.employee_salary.month')</th>
                        <th>@lang('key.employee.employee.name')</th>
                        <th>@lang('key.employee.employee.phone')</th>
                        <th>@lang('key.employee.employee_salary.payable')</th>
                        <th>@lang('key.employee.employee_salary.action')</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                  </table>
                </div>  <!--end datatable-->
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
          url:"{{ URL::to('/admin/employee_salary') }}"
        },
        columns:[
          {
            data:'DT_RowIndex',
            name:'DT_RowIndex',
            orderable:false,
            searchable:false
          },
          {
            data:'date',
            name:'date',
          },
          {
            data:'month',
            name:'month',
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
            data:'payable',
            name:'payable',
          },
          {
            data:'action',
            name:'action',
          },
        ]
    });
 //ajax request from employee.js
 $('#employee').select2({
    theme:'bootstrap4',
    placeholder:'select',
    allowClear:true,
    ajax:{
      url:"{{URL::to('admin/search_employee')}}",
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
function ajaxRequest(){
    $('.invalid-feedback').hide();
    $('input').css('border','1px solid rgb(209,211,226)');
    $('select').css('border','1px solid rgb(209,211,226)');
    let month=$('#month').val();
    let date=$('#date').val();
    let basic=$('#basic').val();
    let tax=$('#tax').val();
    let balance=$('#balance').val();
    let employee=$('#employee').val();
    let medical=$('#medical').val();
    let over_time=$('#over_time').val();
    let bonus=$('#bonus').val();
    let provident=$('#provident').val();
    let payable=$('#payable').val();
    let formData=new FormData();
    formData.append('month',month);
    formData.append('date',date);
    formData.append('basic',basic);
    formData.append('tax',tax);
    formData.append('employee',employee);
    formData.append('medical',medical);
    formData.append('balance',balance);
    formData.append('bonus',bonus);
    formData.append('over_time',over_time);
    formData.append('provident',provident);
    formData.append('payable',payable);
    //axios post request
  axios.post('/admin/employee_salary',formData)
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
    console.log(error.request);
  });

 }
 function getBasicSalary(data){
      if(data!=''){
        axios.get('admin/employee_balance/'+data)
          .then((res)=>{ 
            $('#basic').val(res.data[0].basic);
            $('#balance').val(res.data[0].total);
            Calculation();
            if(parseFloat(res.data[0].total)<0){
              $('#balance').addClass('bg-danger')
              $('#balance').removeClass('bg-success')

            }else{
              $('#balance').addClass('bg-success')
              $('#balance').removeClass('bg-danger')
            }
        })
      }
 }
 function Calculation(){
    basic=parseFloat($('#basic').val())
    balance=parseFloat($('#balance').val())
    medical=parseFloat($('#medical').val())
    provident=parseFloat($('#provident').val())
    over_time=parseFloat($('#over_time').val())
    bonus=parseFloat($('#bonus').val())
    tax=parseFloat($('#tax').val())
    payable=parseFloat($('#payable').val())
    if(isNaN(basic)){
      basic=0
    }
    if(isNaN(balance)){
      balance=0
    }
    if(isNaN(medical)){
      medical=0
    }
    if(isNaN(provident)){
      provident=0
    }
    if(isNaN(over_time)){
      over_time=0
    }
    if(isNaN(bonus)){
      bonus=0
    }
   if(isNaN(tax)){
      tax=0
    }
   if(isNaN(payable)){
      payable=0
   }
  total_payable=((medical+over_time+bonus+basic)-(tax*basic/100))+balance
  $('#payable').val(total_payable.toFixed(2));
    
 }
 $(document).on('change keyup','input',function(){
  Calculation()
 })
 $('#employee').on('select2:unselecting', function (e) {
    var data = e.params.data;
    $('#basic').val('')
    $('#balance').val('')
    $('#medical').val('')
    $('#tax').val('')
    $('#provident').val('')
    $('#payable').val('')
    $('#balance').removeClass('bg-success bg-danger')
});
 function ModalClose(){
  document.getElementById('myForm').reset();
  $('.invalid-feedback').hide();
  $('input').css('border','1px solid rgb(209,211,226)');
  $('select').css('border','1px solid rgb(209,211,226)');
 }
 $('#date').daterangepicker({
        showDropdowns: true,
        singleDatePicker: true,
        parentEl: ".bd-example-modal-lg .modal-body",
        direction: 'ltr',
        locale: {
            format: 'DD-MM-YYYY',
            monthNames: moment.months(),
        },
        
  });
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
    axios.delete('/admin/employee_salary/'+id,{_method:'DELETE'})
      .then((res)=>{
        if (res.data.message) {
          window.toastr.success(res.data.message);
          $('.data-table').DataTable().ajax.reload();
        }
      })
      .catch((error)=>{
        alert((JSON.parse(error.request.response)).message);
      })
  }
});
 })
 </script>
@endsection
