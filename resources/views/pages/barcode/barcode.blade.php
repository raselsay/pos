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
  .barcode{
    border: 1px solid black;
    padding: 40px;
    overflow: hidden;
  }
</style>
@endsection
<div class="container">
	<div class="card m-0">
     <div class="card-header pt-3  flex-row align-items-center justify-content-between">
      <h5 class="m-0 font-weight-bold">@lang('key.generate_barcode.title')</h5>
     </div>
    <div class="card-body px-3 px-md-5">
      @if ($errors->any())
          <div class="alert alert-danger">
              <ul>
                  @foreach ($errors->all() as $error)
                      <li>{{ $error }}</li>
                  @endforeach
              </ul>
          </div>
      @endif
       <form action="{{URL::to('admin/barcode')}}" method="POST" target="_blank">
        @csrf
        <div class="input-group">
          <select class="form-control" name="product" id="product">
          </select>
        </div>
        <div class="input-group">
          <input class="form-control form-control-sm" name="qantity" id="product" placeholder="@lang('key.generate_barcode.qantity_placeholder')">
          </input>
        </div>
        <div class="col-md-2" id="submit">
          <button class="btn btn-sm btn-primary" type="submit">@lang('key.generate_barcode.create_barcode')</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
@section('script')
<script src="{{asset('js/pdf.js')}}"></script>
<script>
 $('#product').select2({
      theme:"bootstrap4",
      allowClear:true,
      placeholder:"@lang('key.generate_barcode.product_placeholder')",
      ajax:{
      url:"{{URL::to('admin/product_code')}}",
      type:'post',
      dataType:'json',
      delay:20,
      data:function(params){
        return {
          searchTerm:params.term,
          _token:'{{csrf_token()}}',
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

 var html=`
<svg>``</svg>

 `;
   var val = HtmlToPdfMake(html,{
  tableAutoSize:true
});
    var dd = {content:val};
    MakePdf.createPdf(dd).open();
</script>
@endsection
