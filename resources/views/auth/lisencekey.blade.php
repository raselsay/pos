<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  @php
  $info=DB::table('information')->select('company_name','logo')->get()->first();
  @endphp
  <title>{{$info->company_name}}</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="icon" href="{{asset('storage/logo/'.$info->logo)}}">
  <link rel="stylesheet" href="{{asset('css/app.css')}}">
  <style>
    .login-page{
      background:linear-gradient(0deg, rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.4)), url({{asset('storage/admin-lte/dist/img/accounting2.png')}});
      background-repeat: no-repeat;
      background-attachment: fixed;
      background-size: cover;
    }
    .login-box{
      width:400px !important;
    }
  </style>
</head>
<body class="hold-transition">
<div class="login-page">
   @if (session('dateover'))
          <div class="alert alert-danger">
              {{ session('dateover') }}
          </div>
    @endif
     @if (session('error'))
          <div class="alert alert-danger">
              {{ session('error') }}
          </div>
    @endif
    @if (session('internet'))
        <div class="alert alert-danger">
            {{ session('internet') }}
        </div>
    @endif
  <h1 class="text-light">
    {{$info->company_name}}
  </h1>
<div class="login-box">
      <div class="login-logo clearfix">
        <a href="#"><img class="rounded" height="100px" width="auto" src="{{asset('storage/logo/'.$info->logo)}}" alt=""></a>
      </div>
  <!-- /.login-logo -->
  <div class="card">
    <div class="card-body login-card-body">
      <p class="login-box-msg">Lisence Key</p>
      <form action="{{ route('lisence') }}" method="post">
        @csrf
        <div class="form-group mb-3">
          <div class="row">
            <div class="col-3">
              <input id="lisence1" type="text" class="form-control form-control-sm" name="field_1" required  placeholder="BGFK5">
            </div>
            <div class="col-3">
              <input id="lisence2" type="text" class="form-control form-control-sm" name="field_2" required autofocus placeholder="DFA44">
            </div>
            <div class="col-3">
              <input id="lisence3" type="text" class="form-control form-control-sm" name="field_3" required autofocus placeholder="HDFDS">
            </div>
            <div class="col-3">
              <input id="lisence4" type="text" class="form-control form-control-sm" name="field_4" required autofocus placeholder="JJKDS">
            </div>
          </div>
        </div>
        <div class="row">
          <!-- /.col -->
          <div class="col-8"></div>
          <div class="col-4">
            <button type="submit" class="btn btn-primary btn-block">Submit</button>
          </div>
          <!-- /.col -->
        </div>
      </form>
      <!-- /.social-auth-links -->
    </div>
    <!-- /.login-card-body -->
  </div>
</div>
</div>
<script src="{{asset('js/app.js')}}"></script>
<!-- Bootstrap 4 -->

</body>
</html>
