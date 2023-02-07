@extends('vox.layouts.master')

@section('content')
            <!-- CONTENT AREA -->

            <!--Breadcrumb Section Start-->
            <section class="breadcrumb-bg login-breadcrumb">                
                <div class="theme-container container ">
                    <div class="space-top-30"></div>
                    <div class="site-breadcumb col-md-4 space-80">                        
                        <h1 class="section-title size-48 no-margin space-bottom-20"> {{trans('lang.login')}} / {{trans('lang.register')}} </h1> 
                        <ol class="breadcrumb breadcrumb-menubar">
                            <li> <a href="/" class="gray-color"> {{trans('lang.Home')}} </a> 
                              {{trans('lang.login / register')}} </li>                             
                        </ol>
                    </div>  
                </div>
            </section>
            <!--Breadcrumb Section End-->

            <section class="login-reg-wrap space-100">
                <div class="theme-container container">

                    <!-- Login Starts -->
                    <div class="row">
                        <div class="col-md-4 col-sm-5">
                            <div class="login-wrap">
                                <h2 class="section-title no-margin size-18"> {{trans('lang.log in your account')}} </h2>
                                <p class="space-30 gray-color"></p>
                                @if (\Session::has('login_message'))
                                    <div class="alert alert-success">
                                        <ul>
                                            <li>{!! \Session::get('login_message') !!}</li>
                                        </ul>
                                    </div>
                                @endif
                                <form class="login-form row space-top-15" onSubmit="return false;">
                                    <div class="form-group col-md-12">
                                        <input id="email_login" required="" type="text" data-original-title="{{trans('Email is required')}}" class="form-control name input-your-name" placeholder="{{trans('lang.Email')}}" name="email" value="{{old('email')}}" data-toggle="tooltip" data-placement="bottom" title="">
                                    </div>
                                    <div class="form-group col-md-12">
                                        <input id="password_login" required="" type="password" data-original-title="{{trans('Password is required')}}" class="form-control email input-email" placeholder="{{trans('lang.Password')}}" name="password" value="" data-toggle="tooltip" data-placement="bottom" title="">
                                    </div>
                                    <div class="form-group col-md-12">
                                        <button type="submit" class="theme-btn btn login-btn"> <b> {{trans('lang.Login')}} </b> </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="space-15 visible-xs"></div>
                        <div class="col-md-8 col-sm-7">
                            <div class="register-wrap">
                                <h2 class="section-title no-margin size-18"> {{trans('lang.Don\'t have an Account? Register Now')}} </h2>
                                <p class="space-30 gray-color"></p>
                                @if (\Session::has('message'))
                                    <div class="alert alert-success">
                                        <ul>
                                            <li>{!! \Session::get('message') !!}</li>
                                        </ul>
                                    </div>
                                @endif
                                @if($errors->any())
                                <h4>{{$errors->first()}}</h4>
                                @endif
                                <form class="register-form row  space-top-15" onSubmit="return false;">
                                    <div class="form-group col-md-6">
                                        <input required id="first_name" type="text" data-original-title="{{trans('lang.Login')}}" class="form-control name input-your-name" placeholder="{{trans('lang.First Name')}}" name="first_name" value="" data-toggle="tooltip" data-placement="bottom" title="{{trans('lang.First Name')}}">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <input required id="last_name" type="text" data-original-title="{{trans('lang.Login')}}" class="form-control name input-your-name" placeholder="{{trans('lang.Last Name')}}" name="last_name" value="" data-toggle="tooltip" data-placement="bottom" title="{{trans('lang.Last Name')}}">
                                    </div>
                                    <div class="form-group col-md-12">
                                        <input required id="email" type="text" data-original-title="{{trans('lang.Email is required')}}" class="form-control email input-email" placeholder="{{trans('lang.Email')}}" name="email" value="" data-toggle="tooltip" data-placement="bottom" title="{{trans('lang.Email')}}">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <input required id="password" type="password" class="form-control website input-website" placeholder="{{trans('lang.Password')}}" name="password" value="" data-toggle="tooltip" data-placement="bottom" title="">
                                    </div>  
                                    <div class="form-group col-md-6">
                                        <input required id="password_confirmation" type="password" class="form-control website input-website" placeholder="{{trans('lang.Retype password')}}" name="retype_password" value="" data-toggle="tooltip" data-placement="bottom" title="">
                                    </div>  
                                    <div class="form-group col-md-12">
                                        <button class="theme-btn-1 larg-btn btn submit-btn"> <b> {{trans('lang.register now')}} </b> </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <!-- / Login Ends -->

                </div>
                <div class="space-40 visible-lg"></div>
            </section>
            <!-- / CONTENT AREA -->


@stop

@section('script')
<script>
  function isEmail(email) {
    var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    return regex.test(email);
  }

  jQuery('.submit-btn').on('click',function(e){
    e.preventDefault();
    if(!isEmail(jQuery('#email').val())){
      alert('email is not valid');
      return false;
    }
    if(jQuery('#password').val() != jQuery('#password_confirmation').val()){
      alert('Password and confirm password does not match');
      return false;
    }
    var data = {
      'firstName' : jQuery('#first_name').val(),
      'lastName' : jQuery('#last_name').val(),
      'email' : jQuery('#email').val(),
      'password' : jQuery('#password').val(),
      'repeatPassword' : jQuery('#password_confirmation').val()
    }; 
    jQuery.ajax({
      headers: {
            'X-CSRF-TOKEN': jQuery('meta[name="token"]').attr('content')
          },
      url: '/signup',
      method: 'POST',
      data: data,
      success: function (result){
        if(result.error == 1){
          alert(result.message);
        }
        else{
          alert(result.message);
          jQuery('#first_name').val('');
          jQuery('#last_name').val('');
          jQuery('#email').val('');
          jQuery('#password').val('');
          jQuery('#password_confirmation').val('');
        }
      }
    });
  })
  
  jQuery('.login-btn').on('click',function(e){
    e.preventDefault();
    var data = {
      'email' : jQuery('#email_login').val(),
      'password' : jQuery('#password_login').val()
    }; 
    jQuery.ajax({
      headers: {
            'X-CSRF-TOKEN': jQuery('meta[name="token"]').attr('content')
          },
      url: '/login-submit',
      method: 'POST',
      data: data,
      success: function (result){
        if(result.error == 1){
          alert(result.message);
        }
        else{
          window.location="/dashboard";
        }
      }
    });
  })
</script>
@stop