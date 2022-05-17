@php
  $lang=App\MultiSetting::select('value')->where('name','language')->first();
  App::setLocale(isset($lang->value) ? $lang->value : '' );
@endphp
@extends('layouts.master')
@section('content')
@section('link')
<style>
  #date{
    margin:0 auto;
  }
  #submit{
    margin:0 auto;
    margin-top: 20px;
  }
  #buffer{
    width:50px;
    height:50px;
    margin-bottom: 5px;
  }
</style>
@endsection

<div class="container">
	<div class="card m-0">
    <div class="card-header pt-3  flex-row align-items-center justify-content-between">
      <h5 class="m-0 font-weight-bold">@lang('key.backup.title')</h5>
     </div>
    <div class="card-body px-3 px-md-5">
      <button class="btn-lg btn-danger backup">@lang('key.backup.create_new_backup')</button><img class="d-none" src="{{asset('storage/admin-lte/dist/img/buffer.gif')}}" alt="" id="buffer">
      <table class="table-sm mt-4 table-bordered">
        <thead>
          <th>@lang('key.backup.file_name')</th>
          <th>@lang('key.backup.action')</th>
        </thead>
        <tbody>
        </tbody>
      </table>
    </div>
    
  </div>
</div>
@endsection
@section('script')
<script>
 // $('#date').daterangepicker({
 //        showDropdowns: true,
 //        locale: {
 //            format: 'DD-MM-YYYY',
 //            separator:' to ',
 //            customRangeLabel: "Custom",
 //        },
 //        minDate: '01-01-1970',
 //        maxDate: '01/01/2050'
 //  })
$('#fromDate').daterangepicker({
  showDropdowns:true,
 singleDatePicker: true,
 locale: {
    format: 'DD-MM-YYYY',
  },
 minDate: '01-01-1970',
 maxDate: '01-01-2050'
});
$('#toDate').daterangepicker({
 showDropdowns:true,
 singleDatePicker: true,
 locale: {
    format: 'DD-MM-YYYY',
  },
  minDate: '01-01-1950',
  maxDate: '01-01-2050'
});

function getFileName(){
    axios.get('/admin/get_filename')
  .then(function (response){
    html=null;
    for (var i = 2; i < response.data.length; i++) {
        html+="<tr>";
        html+="<td class='text-danger'>"+response.data[i]+"</td>";
        html+="<td><button class='btn-sm btn-primary download'>"+"DOWNLOAD"+"</button><button class='btn-sm text-light btn-danger ml-1 delete'>"+"Delete"+"</button></td>";
        html+="</tr>";
    }
    $('tbody').html(html);
    })
     .catch(function(error) {
      console.log(error.request);
    });
}
$(document).ready(function(){
  getFileName();
})

  $(document).on('click','.delete',function(){
    let data=$(this).parent().prev().text()
      axios.get('/admin/backup-delete/'+data)
      .then(function (response){
        if (response.data.message==='success') {
          getFileName();
        }
      })
       .catch(function (error) {
        console.log(error.request);
      });
  })
  $(document).on('click','.download',function(){
    let data=$(this).parent().prev().text()
       axios.get('admin/backup-download/'+data, {responseType: 'blob'}).then(res=>{
        // let blob = new Blob([res.data], {type:'application/*'})
        FileSaver.saveAs(new Blob([res.data],{type:'application/*'}),data);
        getFileName();
      })
       .catch(function (error) {
        console.log(error.request);
      });
  })

  $('.backup').click(function(){
      $('#buffer').removeClass('d-none');
      axios.get('/admin/backup-db')
      .then(function (response){
        getFileName();
      $('#buffer').addClass('d-none');
      })
       .catch(function (error) {
        console.log(error.request);
      });
  })

</script>
@endsection
