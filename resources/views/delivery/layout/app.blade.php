<!DOCTYPE html>
<html>
<head>
  @php
  $lang=App\MultiSetting::select('value')->where('name','language')->first();
  @endphp
  {{App::setLocale(isset($lang->value) ? $lang->value : '' )}}
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  @php
  $info=DB::table('information')->select('company_name','logo','adress','phone')->get()->first();
  $path = asset('storage/logo/'.$info->logo);
  $type = pathinfo($path, PATHINFO_EXTENSION);
  $data = file_get_contents($path);
  $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
  @endphp
  <title>{{$info->company_name}}</title>
  <!-- Tell the browser to be responsive to screen width -->
  <!-- Font Awesome -->
  <link rel="stylesheet" type="text/css" href="{{ asset('css/app.css') }}">
  <link rel="shortcut icon" href="{{asset('storage/logo/'.$info->logo)}}" type="image/ico">
  <!-- <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css"> -->
  <!-- Ionicons -->
  @yield('link')
  <style>
    .delete{
      color:red;
    }
    .receive{
      background-color:#F8A300;
      margin-right: 5px;
    }
    .invoice{
      background-color:#8DC78A;
      margin-right: 5px;
    }
    .input-group{
      margin-top:5px;
    }
    .nav-user-dropdown{
      min-width: 230px;
    }
    .nav-user-info{
      line-height: 1.4;
      padding: 12px;
      color: #fff;
      font-size: 13px;
      border-radius: 2px 2px 0 0;
    }
    .preloader {
       position: absolute;
       top: 0;
       left: 0;
       width: 100%;
       height: 100%;
       z-index: 9999;
       background-image: url('{{asset('storage/admin-lte/dist/img/loader2.gif')}}');
       background-repeat: no-repeat; 
       background-color: #FFF;
       background-position: center;
       /*background-size: 400px,400px;*/
    }
  </style>
</head>

<body class="hold-transition sidebar-mini layout-fixed">
  {{-- loader here --}}
    <div class="preloader"></div>
  {{-- loader end --}}
<div class="wrapper">
    <input type="hidden" value="{{csrf_token()}}">
  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-purple navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
    </ul>
    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      <!-- user Dropdown -->
      <li class="nav-item dropdown nav-user">
                            <a class="nav-link nav-user-img" href="#" id="navbarDropdownMenuLink2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-user-circle"></i></a>
                            <div class="dropdown-menu dropdown-menu-right nav-user-dropdown" aria-labelledby="navbarDropdownMenuLink2">
                                <div class="nav-user-info">
                                    <h5 class="mb-0 text-dark nav-user-name">{{Auth::user()->name}}</h5>
                                    <span class="status"></span><span class="ml-2 text-success">@lang('key.master.available')</span>
                                </div>
                                <a class="dropdown-item" href="#"><i class="fas fa-user mr-2"></i>@lang('key.master.account')</a>
                                @role('Super-Admin')
                                <a class="dropdown-item" href="{{URL::to('register')}}"><i class="fas fa-user mr-2"></i>@lang('key.master.register')</a>
                                @endrole
                                <a class="dropdown-item" href="{{URL::to('admin/change_password')}}"><i class="fas fa-cog mr-2"></i>@lang('key.master.change_password')</a>
                                <a class="dropdown-item" href="{{ route('logout') }}" 

                                    onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
          <i class="fa fa-power-off mr-2" aria-hidden="true"></i>
          <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
              @csrf
          </form>
           @lang('key.master.logout')
        </a>
        </div>
    </li>
     
      <!-- Notifications Dropdown Menu -->
      {{-- <li class="nav-item">
        <a class="nav-link" data-widget="control-sidebar" data-slide="true" href="#" role="button">
          <i class="fas fa-th-large"></i>
        </a>
      </li> --}}
    </ul>
  </nav>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary  elevation-4">
    <!-- Brand Logo -->
    <a href="{{URL::to('/home')}}" class="brand-link">
      <img src="{{asset('storage/logo/'.$info->logo)}}" alt="Company Logo" class="brand-image img-circle elevation-3"
           style="opacity: .8">
      <span class="brand-text font-weight-light">{{$info->company_name}}</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        
        <div class="info">
          <a href="#" class="d-block"><i class="fas fa-circle text-success"></i> {{Auth::user()->name}}</a>
        </div>
      </div>

      <!-- Sidebar Menu -->
      <nav class="mt-2 pb-5">
        <ul class="nav nav-pills nav-sidebar flex-column nav-child-indent" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
         
          <li class="nav-item">
            <a href="{{ URL::to('/home') }}" class="nav-link">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>
                {{__('key.sidebar.dashboard')}}
              </p>
            </a>
          </li>
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    @yield('content')
  </div>
  <!-- /.content-wrapper -->
  <footer class="main-footer">
    <strong>Copyright &copy; 2021 <a href="http://softimpire.com">SOFTiMPIRE</a>.</strong>
    All rights reserved.
    <div class="float-right d-none d-sm-inline-block">
      <b>Version</b>1.0.0
    </div>
  </footer>

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
  </aside>
</div>
{{-- voucer modal --}}
<script src="{{asset('js/app.js')}}"></script>
@yield('script')
<script>
  $("body").css("overflow", "hidden");
  window.onload=function(){
     $('.preloader').fadeOut('slow');
     $("body").css("overflow", "initial");
  }
  // alert($("[name='v_type'] option:selected").val())
$(document).ready(function(){
    const url = window.location;
    $('ul.nav-sidebar a').filter(function() {
        return this.href == url;
    }).parent().addClass('active');
    $('ul.nav-treeview a').filter(function() {
        return this.href == url;
    }).parentsUntil(".sidebar-menu > .nav-treeview").addClass('menu-open');
    $('ul.nav-treeview a').filter(function() {
        return this.href == url;
    }).addClass('active');
    $('li.has-treeview a').filter(function() {
        return this.href == url;
    }).addClass('active');
    $('ul.nav-treeview a').filter(function() {
        return this.href == url;
    }).parentsUntil(".sidebar-menu > .nav-treeview").children(0).addClass('active');
    $("li a").filter(function(){
        return this.href == url;
    }).addClass('active')
    AddVoucerDetails();
    
});

  let add_no=0;
  function AddVoucerDetails(){
    add_no=add_no+1;
    options="<tr>";
    options+="<td><input class='form-control form-control-sm' type='text' placeholder='@lang('key.voucer.voucer-master.details_placeholder')' name='Mdetails[]'></td>";
    options+="<td><input class='form-control form-control-sm' type='number'  name='Mqantity[]' value='1'></td>";
    options+="<td><input class='form-control form-control-sm' type='number' placeholder='@lang('key.voucer.voucer-master.ammount_placeholder')' name='Mammount[]'></td>";
    options+="<td><input class='form-control form-control-sm' type='number'  name='Mtotal[]'></td>";
    options+="<td><button type='button' onclick='recordMasterRemove(this)' class='btn btn-sm btn-danger remove'>X</button></td>"
    options+="</tr>";
    $('#myMasterForm tbody').append(options);
    Mcalculation();
    VoucerTypeChange()
  }
  function MasterRemoveAll(){
    add_no=0;
    $('#myMasterForm tbody').empty();
  }
  function recordMasterRemove(this_val){
    if (add_no<=1){
      alert('you can,t remove this item');
        return false;
      }else{
        $(this_val).parent().parent().remove();
        add_no=add_no-1;
      }
  }

function Mcalculation(){
  FinalTotal=0;
  $("#myMasterForm input[name='Mqantity[]']").map(function(){
    qantity=$(this).val();
    ammount=$(this).parent().next().children("input[name='Mammount[]']").val();
    total=qantity*ammount;
    FinalTotal+=qantity*ammount
    $(this).parent().next().next().children("input[name='Mtotal[]']").val(total)
  }).get();
  $('#master-ammount').val(FinalTotal);
}
$('#myMasterForm').on('keyup change','input',function(){
  Mcalculation();
})
function MasterModal(){
  $('#modal-voucer').modal('show');
  $('#VModalLabel').text("@lang('key.voucer.voucer-master.title_modal')");
  MasterModalClose();
  $('#master-bank').select2({
    theme:'bootstrap4',
    placeholder:'Select',
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
        return {
          results:response,
        }
      },
      cache:true,
    }
  })
  $('#master-category').select2({
    theme:'bootstrap4',
    placeholder:'Select',
    allowClear:true,
    ajax:{
      url:'{{URL::to('admin/search_name')}}',
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
}
function getMasterCat(data){
  if (Number.isInteger(parseInt(data.value))) {
    category=data.options[data.selectedIndex].text;
  }else{
    category='';
  }
  $('#data-label').text(category+':');
  $('#master-data').html('');
  $('.master-data').removeClass('d-none');
   $('#master-data').select2({
      theme:'bootstrap4',
      placeholder:'Select '+category,
      allowClear:true,
      ajax:{
        url:"{{URL::to('admin/relation_search')}}/"+data.value,
        type:"post",
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
  }
function MasterValid(){
  let isValid=true;
$('#master-category').removeClass('is-invalid');
  if($('#master-category').val()==''){
    $('#master-category').addClass('is-invalid');
  }
$("input[name='Mqantity[]']").each(function(){
  $(this).removeClass('is-invalid');
  if ($(this).val()=='' || $(this).val()<0) {
    isValid=false;
    $(this).addClass('is-invalid');
  }
})
  $("input[name='Mammount[]']").each(function(){
    $(this).removeClass('is-invalid');
    if ($(this).val()==''){
      isValid=false;
      $(this).addClass('is-invalid');
    }
  })
}
function MasterAjaxRequest(print=null){
    valid=MasterValid();
    // MasterPdf();
    if(valid==false){
      return false;
    }
    $('.master-submit').attr('disabled',true);
    $('.invalid-feedback').hide();
    $('select,input').removeClass('is-invalid');
    $('select,input').css('border','1px solid rgb(209,211,226)');
    let Mdetails=$("#myMasterForm input[name='Mdetails[]']").map(function(){return $(this).val();}).get();
    let Mqantity=$("#myMasterForm input[name='Mqantity[]']").map(function(){return $(this).val();}).get();
    let Mammount=$("#myMasterForm input[name='Mammount[]']").map(function(){return $(this).val();}).get();
    let main_date=$('#master-date').val();
    let main_category=$('#master-category option:selected').val();
    let main_data=$('#master-data').val();
    let main_payment_type=$('#master-payment_type').val();
    let main_bank=$('#master-bank').val();
    let main_ammount=$('#master-ammount').val();
    let formData= new FormData();
    let details=$("#myMasterForm input[name='Mqantity[]']").map(function(){return $(this).val();}).get();
    formData.append('details[]',Mdetails);
    formData.append('qantity[]',Mqantity);
    formData.append('ammount[]',Mammount);
    formData.append('date',main_date);
    formData.append('category',main_category);
    formData.append('data',main_data);
    formData.append('payment_type',main_payment_type);
    formData.append('bank',main_bank);
    formData.append('total_ammount',main_ammount);
    //axios post request
  axios.post('/admin/voucer',formData)
  .then(function (response){
    if (response.data.message){
      window.toastr.success(response.data.message);
      $('.data-table').DataTable().ajax.reload();
      if(print==1){
        MasterPdf(response.data['v_id']);
      }else{
          MasterModalClose();
          $('.master-submit').attr('disabled',false);
      }
      $('#modal-voucer').modal('hide');
      return false;
    }
    var keys=Object.keys(response.data);
    for(var i=0; i<keys.length;i++){
        $('#master_'+keys[i]+'_msg').html(response.data[keys[i]][0]);
        $('#master-'+keys[i]).addClass('is-invalid');
        $('#master-'+keys[i]).css('border','1px solid red');
        $('#master_'+keys[i]+'_msg').show();
        $('.master-submit').attr('disabled',false);
      }
  })
   .catch(function (error) {
    console.log(error);
  });
 }
function MasterPdf(v_id=null){
      details = $("input[name='Mdetails[]']")
                  .map(function(){return $(this).val();}).get();
      qantity = $("input[name='Mqantity[]']")
                  .map(function(){return $(this).val();}).get();
      ammount = $("input[name='Mammount[]']")
                  .map(function(){return $(this).val();}).get();
      date=parseFloat($('#master-date').val());
      category=parseFloat($('#master-category').val());
      payment_type=parseFloat($('#master-payment_type').val());
      data=parseFloat($('#master-data').val());
      total_ammount=parseFloat($('#master-ammount').val());
      v_type=($("[name='v_type']").prop('checked')==true) ? '' : 'display:none;';
      x=[{details,qantity,ammount}];
      html=`<div id="invoice">
            <div style='background-color:#007BFF;padding:50px;color:white;'>
              <table width="100%" style="border:none;">
                <tr>
                  <td>
                    <img height="80px" width="100px" src="{{$base64}}"><br>
                    <span style="font-size:25px;">`+((payment_type=='Deposit') ? "@lang('key.voucer.voucer.debit_voucer')" : "@lang('key.voucer.voucer.credit_voucer')")+`</span>
                  </td>
                  <td style="float:right;"><span style="font-weight:bold;">{{$info->company_name}}</span><br>{{$info->adress}}<br>{{$info->phone}}</td>
                </tr>
              </table>
            </div>
            <div style="margin-right:50px;margin-left:50px;margin-top:30px;margin-bottom:30px;">
              <table width="100%" style="border:none;font-weight:bold;">
                <tr>
                  <td>
                     @lang('key.voucer.voucer.date')
                  </td>
                  <td style="float:right;">`+$('#master-date').val()+`</td>
                </tr>
                <tr>
                  <td>
                     @lang('key.voucer.voucer-master.bill_no').
                  </td>
                  <td style="float:right;">`+'1'+String(v_id).padStart(9,'0')+`</td>
                </tr>
                <tr>
                  <td>
                     @lang('key.voucer.voucer.category'):
                  </td>
                  <td style="float:right;">`+$('#master-category option:selected').text()+`</td>
                </tr>
                <tr>
                  <td>
                     @lang('key.voucer.voucer.name'):
                  </td>
                  <td style="float:right;">`+$('#master-data option:selected').text()+`</td>
                </tr>
              </table>
            </div>
            <div id="tables" style='margin-right:50px;margin-left:50px;'>
              <table width="100%" style="text-align:center;border:1px solid grey;">
                <tr>
                  <th style='border:1px solid grey;'>@lang('key.voucer.voucer-master.details')</th>
                  <th style='`+v_type+`border:1px solid grey;'>@lang('key.voucer.voucer-master.qantity')</th>
                  <th style='border:1px solid grey;'>@lang('key.voucer.voucer-master.ammount')</th>
                  <th style="`+v_type+`border:1px solid grey;">@lang('key.voucer.voucer-master.total')</th>
                </tr>
      `;
      for (var i=0;i<details.length; i++) {
        html+="";
        html+="<td style='border:1px solid grey;'>"+x[0]['details'][i]+"</td>";
        html+="<td style='"+v_type+"border:1px solid grey;'>"+(parseFloat(x[0]['qantity'][i])).toFixed(2)+"</td>";
        html+="<td style='border:1px solid grey;'>"+(parseFloat(x[0]['ammount'][i])).toFixed(2)+"</td>";
        html+="<td style='"+v_type+"border:1px solid grey;'>"+(parseFloat(x[0]['qantity'][i])*parseFloat(x[0]['ammount'][i])).toFixed(2)+"</td>";
        html+="</tr>";
      }
      html+=`</table>
       </div>
       <div style='margin-right:50px;margin-left:50px;margin-top:30px;margin-bottom:30px;'>
    <table width="100%" style="color:black;font-weight:bold">
      <!-- total -->
      <tr style='background-color:#F1F1F1'>
        <td>
           @lang('key.voucer.voucer-master.total_ammount')৳
        </td>
        <td style="text-align:right;">`+total_ammount.toFixed(2)+`</td>
      </tr>
    </table>
    <br>
    <h2>@lang('key.voucer.voucer-master.note').</h2>
    <br>
    <br>
     </div>`
     $(html).printThis({
        importCSS:true,
        printDelay: 333,
        header: "",
        footer:`<p style="text-align:center;">
                  Software Developed By <strong>SOFTiMPIRE</strong>
                  <br>Adress:Barisal Bottola,Barisal<br>
                  Mobile:01873072253,01310588563
                </p>`,
        base: "noman"
      });
    MasterModalClose();
    $('.master-submit').attr('disabled',false);
}
 $('#master-date').daterangepicker({
 showDropdowns:true,
 singleDatePicker: true,
 locale: {
    format: 'DD-MM-YYYY',
  },
  minDate: '01-01-1950',
  maxDate: '01-01-2050'
});
function MasterModalClose(){
  MasterRemoveAll();
  // $('#myMasterForm input').val('');
  $('#myMasterForm select').val(null || 'bill').change(); 
  document.getElementById("myMasterForm").reset();
  // $("#myMasterForm select option[value='']").attr('selected',true);
  $('#myMasterForm .invalid-feedback').hide();
  $('#myMasterForm select,input').removeClass('is-invalid');
  $('#myMasterForm select,input').css('border','1px solid rgb(209,211,226)');
  AddVoucerDetails();
  $('#master-date').daterangepicker({
 showDropdowns:true,
 singleDatePicker: true,
 locale: {
    format: 'DD-MM-YYYY',
  },
  minDate: '01-01-1950',
  maxDate: '01-01-2050'
});
  VoucerTypeChange();
}
function dateFormat(date){
let date_ob = date;
let dates = ("0" + date_ob.getDate()).slice(-2);
let month = ("0" + (date_ob.getMonth() + 1)).slice(-2);
let year = date_ob.getFullYear();
return(dates + "-" + month + "-" + year);
}

function VoucerTypeChange(){
  if($("#v_type").prop('checked')==true){
    check=true
  }else{
    check=false
  }
   if (check==false){
    $('#th-qantity,#th-total').addClass('d-none');
    $("input[name='Mqantity[]'],input[name='Mtotal[]']").map(function(){
       $(this).parent().addClass('d-none');
    });
    
   }else{
    $('#th-qantity,#th-total').removeClass('d-none');
     $("input[name='Mqantity[]'],input[name='Mtotal[]']").map(function(){
       $(this).parent().removeClass('d-none');
    });
   }
}
</script>
</body>
</html>