@extends('layouts.master')
@section('content')
@php
  $lang=App\MultiSetting::select('value')->where('name','language')->first();
  App::setLocale(isset($lang->value) ? $lang->value : '' );
@endphp
@section('link')
<style>
.navButtonContainer button{
   padding:10px;
   color:black;
   text-decoration:none;
   border:none;
   outline:none;
}
.navButtonContainer button:hover{
   padding:10px;
   color:white;
   text-decoration:none;
   border:none;
   background:red;
}
.navButtonContainer button:active{
   padding:10px;
   color:white;
   text-decoration:none;
   border:none;
   background:green;
}
.tabContainer{
  width:100%;
  height:85%;
  padding-top:50px;
  display:none;
  /* padding:20px; */
}
thead{
  text-align:center;
}
tbody td:nth-child(2) { 
  text-align:center;
  }
</style>
@endsection
<div class="container">
	<div class="card m-0">
    <div class="card-header pt-3  flex-row align-items-center justify-content-between">
      <h5 class="m-0 font-weight-bold">@lang('key.setting.setting.title')</h5>
     </div>
    <div class="card-body px-3 px-md-5">
    
    <div class="navButtonContainer">
       <button onclick="ShowTab(0,'grey')">@lang('key.setting.setting.invoice.title')</button>
       <button onclick="ShowTab(1,'grey')">@lang('key.setting.setting.purchase.title')</button>
       <button onclick="ShowTab(2,'grey')">@lang('key.setting.setting.installment.title')</button>
       <button onclick="ShowTab(3,'grey')">@lang('key.setting.setting.general.title')</button>
    </div>
    <!-- for Invoice tab -->
    <div id='setting_tabs'>
      <div class="tabContainer">
          <h4 class='font-weight-bold'>@lang('key.setting.setting.invoice.title')</h4>
          <table class='table table-sm table-bordered col-6'>
          <thead class='text-secondary'>
              <tr>
                  <th>@lang('key.setting.setting.label')</th>
                  <th>@lang('key.setting.setting.switch')</th>
              </tr>
          </thead>
          <tbody>
              <tr>
                  <td class='font-weight-bold'>@lang('key.setting.setting.vat')</td>
                  <td><input type="checkbox" name='invoice_vat'></td>
              </tr>
              <tr>
                  <td class='font-weight-bold'>@lang('key.setting.setting.discount')</td>
                  <td><input type="checkbox" name='invoice_discount'></td>
              </tr>
              <tr>
                  <td class='font-weight-bold'>@lang('key.setting.setting.labour_cost')</td>
                  <td><input type="checkbox" name='invoice_labour'></td>
              </tr>
              <tr>
                  <td class='font-weight-bold'>@lang('key.setting.setting.transport_cost')</td>
                  <td><input type="checkbox" name='invoice_transport'></td>
              </tr>
          </tbody>
          </table>
      </div>
      <!-- for purchase tab -->
      <div class="tabContainer">
      <h4 class='font-weight-bold'>@lang('key.setting.setting.purchase.title')</h4>
          <table class='table table-sm table-bordered  col-6'>
          <thead class='text-secondary'>
              <tr>
                  <th>@lang('key.setting.setting.label')</th>
                  <th>@lang('key.setting.setting.switch')</th>
              </tr>
          </thead>
            <tbody>
                <tr>
                    <td class='font-weight-bold'>@lang('key.setting.setting.labour_cost')</td>
                    <td><input type="checkbox" name='purchase_labour'></td>
                </tr>
                <tr>
                    <td class='font-weight-bold'>@lang('key.setting.setting.transport_cost')</td>
                    <td><input type="checkbox" name='purchase_transport'></td>
                </tr>
            </tbody>
          </table>
      </div>
      <!-- end purchase tab -->
      {{-- installment tab --}}
      <div class="tabContainer">
        <h4 class='font-weight-bold'>@lang('key.setting.setting.installment.title')</h4>
        <table class='table table-sm table-bordered col-6'>
          <thead class='text-secondary'>
              <tr>
                  <th>@lang('key.setting.setting.label')</th>
                  <th>@lang('key.setting.setting.switch')</th>
              </tr>
          </thead>
          <tbody>
            <tr>
              <td class='font-weight-bold'>@lang('key.setting.setting.vat')</td>
              <td><input type="checkbox" name='installment_vat'></td>
            </tr>
            <tr>
                <td class='font-weight-bold'>@lang('key.setting.setting.discount')</td>
                <td><input type="checkbox" name='installment_discount'></td>
            </tr>
            <tr>
                <td class='font-weight-bold'>@lang('key.setting.setting.labour_cost')</td>
                <td><input type="checkbox" name='installment_labour'></td>
            </tr>
            <tr>
                <td class='font-weight-bold'>@lang('key.setting.setting.transport_cost')</td>
                <td><input type="checkbox" name='installment_transport'></td>
            </tr>
          </tbody>
          </table>
      </div>
      {{-- end installment tab --}}
      <!-- general setting tab -->
      <div class="tabContainer">
        <h4 class='font-weight-bold'>@lang('key.setting.setting.general.title')</h4>
        <table class='table table-sm table-bordered text-center col-6'>
          <thead class='text-secondary'>
              <tr>
                  <th>@lang('key.setting.setting.label')</th>
                  <th>@lang('key.setting.setting.switch')</th>
              </tr>
          </thead>
          <tbody>
          </tbody>
              <tr>
                  <td class='font-weight-bold'>@lang('key.setting.setting.language')</td>
                  <td><select class='form-control form-control-sm' name='language'>
                          <option value="">Select</option>
                          <option value="bn">@lang('key.setting.setting.general.bangla')</option>
                          <option value="en">@lang('key.setting.setting.general.english')</option>
                      </select>
                  </td>
              </tr>
              </tr>
          </table>
      </div>
    </div>
    <!-- end general setting tab -->
    <button onclick='Request()' class='btn btn-sm btn-primary submit'>@lang('key.buttons.update')</button>
    </div>
  </div>
</div>
@endsection
@section('script')
<script type="text/javascript">
    let button=$('.navButtonContainer button');
    let tab=$('.tabContainer');
    function ShowTab(index,colorCode){
      i=0;
      b=0;
      tab.each(function(){
        tab[i].style.background='white';
        tab[i].style.color='';
        tab[i].style.display='none';
        i=i+1;
      })
      button.each(function(){
        button[b].style.background='gray';
        button[b].style.color='black';
        b=b+1;
      })
      tab[index].style.backgroundColor='';
      button[index].style.backgroundColor='pink';
      tab[index].style.display='block';
      // console.dir(tab[index]);
    }
    $(document).ready(function(){
      ShowTab(0,'pink');
    })
    function Request(){
      $('.submit').attr('disabled',true);
        inputs=$("#setting_tabs input[type='checkbox']");
        select=$("#setting_tabs select option:selected");
        console.dir(inputs);
        formData=new FormData;
        for (let i = 0; i < inputs.length; i++) {
          if($(inputs[i]).is(":checked")){
            formData.append(inputs[i].name,1);
          }else{
            formData.append(inputs[i].name,0);
          }
        }
        for (let sel = 0; sel < select.length; sel++) {
          formData.append($(select[sel]).parent()[0].name,select[sel].value)
        }
      axios.post('admin/setting/make-setting',formData)
      .then((res)=>{
        if(res.data.message){
          toastr.success(res.data.message);
          $('.submit').attr('disabled',false);
        }
      })
    }
    function getData(){
      axios.get('admin/setting/get_data')
      .then((res)=>{
        for(i=0;i<res.data.length;i++){
          x=$("[name='"+res.data[i]['name']+"']");
          value=res.data[i]['value'];
          if(x[0].type=='checkbox' && value==1){
            $("[name='"+res.data[i]['name']+"']").attr('checked',true);
          }
          if(x[0].nodeName=='SELECT'){
           $("[name='"+res.data[i]['name']+"']").val(value).change();
          }
        }
      })
    }
    getData();
 </script>

@endsection
