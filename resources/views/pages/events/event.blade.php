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
                    <label class="font-weight-bold">Date:</label>
                    <input class="form-control form-control-sm" id="date"  type="text" placeholder="00-00-0000">
                    <div id="date_msg" class="invalid-feedback">
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="font-weight-bold">Issue Date:</label>
                    <input class="form-control form-control-sm" id="issue_date"  type="text" placeholder="00-00-0000">
                    <div id="issue_date_msg" class="invalid-feedback">
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="font-weight-bold">Comment:</label>
                    <textarea class="form-control form-control-sm" id="comment"  type="text" placeholder="Enter Comments..."></textarea>
                    <div id="comment_msg" class="invalid-feedback">
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
                  <table style="font-size:14px;" class="table table-sm table-bordered table-striped align-items-center display table-flush data-table">
                    <thead class="thead-light">
                     <tr>
                        <th width="5">@lang('key.category.category.no')</th>
                        <th width="5">Date</th>
                        <th width="5">Agrement Date</th>
                        <th width="20">Customer</th>                        
                        <th width="10">Spo Name</th>
                        <th width="10">Creator</th>
                        <th width="15">Mobile</th>
                        <th width="30">Description</th>
                        <th width="10">Current Balance</th>
                        <th width="10">Action</th>

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
          url:"{{ URL::to('/admin/events') }}"
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
            data:'issue_date',
            name:'issue_date',
          },
          {
            data:'name',
            name:'name',
          },
          {
            data:'spo_name',
            name:'spo_name',
          },
          {
            data:'username',
            name:'username',
          },
          {
            data:'phone1',
            name:'phone1',
          },
           {
            data:'description',
            name:'description',
          },
          
          {
            data:'balance',
            name:'balance',
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
ModalClose()
}
 $(document).on('click','.edit',function(){
  $('#exampleModalLabel').text("@lang('key.category.category.title_update')");
  $('.submit').text("@lang('key.buttons.update')");
  $('#Modalx').modal('show');
  id=$(this).data('id');
  $('#id').val(id);
  axios.get('admin/events_data/'+id)
  .then(function(response){
    console.log(response.data)
    var keys=Object.keys(response.data[0]);
    for (var i = 0; i < keys.length; i++) {
      if (keys[i]=='description'){
      $('#comment').val(response.data[0][keys[i]]);
      }
      if (keys[i]=='issue_date'){
      $('#issue_date').val(dateFormat(new Date(response.data[0][keys[i]]*1000)));
      }
      if (keys[i]=='date'){
      $('#date').val(dateFormat(new Date(response.data[0][keys[i]]*1000)));
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
    axios.delete('/admin/events/'+id,{_method:'DELETE'})
      .then((res)=>{
        if (res.data.message) {
          window.toastr.success(res.data.message);
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
    let comment=$('#comment').val();    
    let date=$('#date').val();
    let issue_date=$('#issue_date').val();    
    let customer=$('#customer').val();

    let formData=new FormData();
    formData.append('comment',comment);    
    formData.append('date',date);
    formData.append('issue_date',issue_date);    
    formData.append('customer',customer);
    let id=$('#id').val();
    //axios post request
    if (!id) {
        axios.post('/admin/events',formData)
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
      axios.post('/admin/events/'+id,formData)
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
  $('#date,#issue_date').daterangepicker({
        showDropdowns: true,
        singleDatePicker: true,
        parentEl: ".bd-example-modal-lg .modal-body",
        locale: {
            format: 'DD-MM-YYYY',
        }
  });
 }

 $('#date,#issue_date').daterangepicker({
        showDropdowns: true,
        singleDatePicker: true,
        parentEl: ".bd-example-modal-lg .modal-body",
        locale: {
            format: 'DD-MM-YYYY',
        }
  });
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
 function dateFormat(date){
    let date_ob = date;
    let dates = ("0" + date_ob.getDate()).slice(-2);
    let month = ("0" + (date_ob.getMonth() + 1)).slice(-2);
    let year = date_ob.getFullYear();
    return(dates + "-" + month + "-" + year);
}
 </script>
@endsection
