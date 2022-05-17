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

  <!-- Font Awesome -->
  <link rel="icon" href="{{asset('storage/logo/'.$info->logo)}}">
  <link rel="stylesheet" href="{{asset('css/app.css')}}">
  <!-- icheck bootstrap -->
  <!-- <link rel="stylesheet" href="../../plugins/icheck-bootstrap/icheck-bootstrap.min.css"> -->
  <!-- Theme style -->
  <!-- Google Font: Source Sans Pro -->
  <style>
    .login-page{
      /*background-image:url("") rgba(0, 0, 0, 0.1);*/
     
      background:linear-gradient(0deg, rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.4)), url({{asset('storage/admin-lte/dist/img/accounting2.png')}});
      background-repeat: no-repeat;
      background-attachment: fixed;
      background-size: cover;
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
    @if (session('message'))
          <div class="alert alert-success">
              {{ session('message') }}
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
      

      <p class="login-box-msg">Sign in as Delivery Admin to start your session</p>
      <form action="{{ route('delivery.login.submit') }}" method="POST">
        @csrf
        <div class="input-group mb-3">
          <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="Email">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-envelope"></span>
            </div>
          </div>
          @error('email')
              <span class="invalid-feedback" role="alert">
                  <strong>{{ $message }}</strong>
              </span>
          @enderror
        </div>
        <div class="input-group mb-3">
          <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" placeholder="Password">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
            </div>
          </div>
          @error('password')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
         @enderror
        </div>
        <div class="row">
          <div class="col-8">
            <div class="icheck-primary">
              <input type="checkbox" id="remember" {{ old('remember') ? 'checked' : '' }}>
              <label for="remember">
                Remember Me
              </label>
            </div>
          </div>
          <!-- /.col -->
          <div class="col-4">
            <button type="submit" class="btn btn-primary btn-block">Sign In</button>
          </div>
          <!-- /.col -->
        </div>
      </form>
      <!-- /.social-auth-links -->

      <p class="mb-1">
        @if (Route::has('password.request'))
            <a class="btn btn-link" href="{{ route('password.request') }}">
                {{ __('Forgot Your Password?') }}
            </a>
        @endif
      </p>
    </div>
    <!-- /.login-card-body -->
  </div>
</div>
</div>
<script src="{{asset('js/app.js')}}"></script>
<!-- Bootstrap 4 -->

</body>
</html>
