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
            <h1>{{__('key.fund_transfer.title')}}</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ URL::to('/home') }}">Home</a></li>
              <li class="breadcrumb-item">Bank</li>
              <li class="breadcrumb-item active">Fund Transfer</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>
<div class="container">
	<div class="card m-0">
    <div class="card-header pt-3  flex-row align-items-center justify-content-between">
      <h5 class="m-0 font-weight-bold">{{__('key.fund_transfer.title')}}</h5>
     </div>
    <div class="card-body px-3 px-md-5">
		  	<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">
          {{__('key.buttons.add_new')}} <i class="fas fa-plus"></i>
        </button>

        <!-- Modal -->
        <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{__('key.fund_transfer.title')}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="ModalClose()">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <!--modal body-->
              <div class="modal-body">
                <div class="form-group">
                  <label class="font-weight-bold">{{__('key.fund_transfer.from')}}:</label>
                  <select class="form-control form-control-sm bank" name='bank[]' id="from" onchange="getBalance(this)">
                  </select>
                  <div id="from_msg" class="invalid-feedback">
                  </div>
                </div>
                <div class="form-group">
                  <label class="font-weight-bold">{{__('key.fund_transfer.to')}}:</label>
                  <select class="form-control form-control-sm bank" name='bank[]' id="to" onchange="getBalance(this)">
                  </select>
                  <div id="to_msg" class="invalid-feedback">
                  </div>
                </div>
                <div class="form-group">
                  <label class="font-weight-bold">{{__('key.fund_transfer.transaction')}}:</label>
                  <input type="text" id='transaction' class='form-control form-control-sm' placeholder='XBNBC3H422JH'>
                  <div id="transaction_msg" class="invalid-feedback">
                  </div>
                </div>
                <div class="input-group input-group-sm mb-3">
                  <div class="input-group-prepend">
                    <span class="input-group-text bg-primary" id="inputGroup-sizing-sm">৳</span>
                  </div>
                  <input type="text" class="form-control form-control-sm" id="ammount" placeholder="{{__('key.fund_transfer.ammount_placeholder')}}" aria-label="Small" aria-describedby="inputGroup-sizing-sm">
                  <div id="ammount_msg" class="invalid-feedback">
                  </div>
               </div>

               <div class="form-group">
                  <label class="font-weight-bold">{{__('key.fund_transfer.details')}}:</label>
                  <textarea class="form-control form-control-sm" name="" id="details" rows="3" placeholder="{{__('key.fund_transfer.details_placeholder')}}"></textarea>
                  <div id="details_msg" class="invalid-feedback">
                  </div>
                </div>
               <!--end 2nd column -->
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="ModalClose()" data-dismiss="modal">{{__('key.buttons.close')}}</button>
                <button type="button" class="btn btn-primary submit" onclick="ajaxRequest()">{{__('key.buttons.save')}}</button>
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
                        <th>{{__('key.fund_transfer.no')}}</th>
                        <th>{{__('key.fund_transfer.date')}}</th>
                        <th>{{__('key.fund_transfer.details')}}</th>
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
          url:"{{ URL::to('/admin/fund_transfer') }}"
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
            data:'details',
            name:'details',
          },
        ]
    });
 //ajax request from employee.js
function ajaxRequest(){
    $('.submit').attr('disabled',true);
    $('.invalid-feedback').hide();
    $('input').css('border','1px solid rgb(209,211,226)');
    $('select').css('border','1px solid rgb(209,211,226)');
    let from=$('#from').val();
    let to=$('#to').val();
    let ammount=$('#ammount').val();
    let transaction=$('#transaction').val();
    let details=$('#details').val();
    let formData= new FormData();
    formData.append('from',from);
    formData.append('to',to);
    formData.append('ammount',ammount);
    formData.append('transaction',transaction);
    formData.append('details',details);
    //axios post request
  axios.post('/admin/fund_transfer',formData)
  .then(function (response){
    if (response.data.message) {
      window.toastr.success(response.data.message);
      ModalClose()
      $('.data-table').DataTable().ajax.reload();
      $('.submit').attr('disabled',false);
    }
    var keys=Object.keys(response.data[0]);
    for(var i=0; i<keys.length;i++){
        $('#'+keys[i]+'_msg').html(response.data[0][keys[i]][0]);
        $('#'+keys[i]).addClass('is-invalid');
        $('#'+keys[i]).css('border','1px solid red');
        $('#'+keys[i]+'_msg').show();
      }
  })
   .catch(function (error){
    $('.submit').attr('disabled',false);
    console.log(error.request);
  });

 }
  $('.bank').select2({
    theme:'bootstrap4',
    placeholder:'select',
    allowClear:true,
    ajax:{
      url:"{{URL::to('admin/get_banks')}}",
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
        item=$("select[name='bank[]'] option:selected")
                  .map(function(){return $(this).val();}).get();
         res=response.map(function(currentValue, index, arr){
          if (item.includes(currentValue.id)){
            response[index]['disabled']=true;
          }
        });
        return {
          results:response,
        }
      },
      cache:true,
    }
  })
 function ModalClose(){
  $('#exampleModal select').val(null).change();
  $(".card-body select option[value='']").attr('selected',true);
  $('.invalid-feedback').hide();
  $('input').css('border','1px solid rgb(209,211,226)');
  $('select').css('border','1px solid rgb(209,211,226)');
  $('#exampleModal').modal('hide');
 }
function getBalance(element){
    if (element.value){
      id=element.value;
      axios.get('admin/get_balance/'+id)
      .then((res)=>{
     Swal.fire({
        title: "Balance:"+res.data.total,
        text: "Are you sure!Do You want Fund Transfer from this account",
        // icon: "warning",
        showCancelButton: true,
        // dangerMode: true,
        confirmButtonColor: "#DD6B55",
        cancelButtonText: "CANCEL",
        confirmButtonText: "CONFIRM",
     })
    })
  }
}

 </script>
@endsection
