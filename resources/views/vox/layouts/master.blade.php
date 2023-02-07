<!DOCTYPE html>
<!--[if IE 7]><html class="no-js ie7 oldie" lang="en-US"> <![endif]-->
<!--[if IE 8]><html class="no-js ie8 oldie" lang="en-US"> <![endif]-->
<!--[if gt IE 8]><!-->
<html lang="en">
  <head>
    <meta charset="utf-8">
    <!--[if IE]><meta http-equiv="X-UA-Compatible" content="IE=edge"><![endif]-->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="agency, ecommerce">
    <meta name="author" content="Vox">
    <meta name="token" content="{{csrf_token()}}">
    <title>Vox</title>
    <!-- Favicon -->
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="/assets/ico/apple-touch-icon.png">
    <link rel="shortcut icon" href="/assets/ico/favicon.ico">

    <!-- Font -->
    <link href='https://fonts.googleapis.com/css?family=Open+Sans:300,400,700,400italic' rel='stylesheet' type='text/css'>
    <link href='https://fonts.googleapis.com/css?family=Montserrat:400,700' rel='stylesheet' type='text/css'>
    <link href='https://fonts.googleapis.com/css?family=Oswald:400,300,700' rel='stylesheet' type='text/css'> 


    <!-- CSS Global -->
    <link href="{{URL::to('/')}}/assets/plugins/css/bootstrap.min.css" rel="stylesheet" type="text/css">        
    <link href="{{URL::to('/')}}/assets/plugins/css/bootstrap-select.min.css" rel="stylesheet" type="text/css">  
    <link href="{{URL::to('/')}}/assets/plugins/css/bootstrap-datetimepicker.min.css" rel="stylesheet" type="text/css">  
    
    <link href="{{URL::to('/')}}/assets/plugins/css/subscribe-better.css" rel="stylesheet" type="text/css">
    <link href="{{URL::to('/')}}/assets/plugins/css/jquery.countdown.css" rel="stylesheet" type="text/css">
    <link href="{{URL::to('/')}}/assets/plugins/css/jquery.mCustomScrollbar.min.css" rel="stylesheet" type="text/css">
    <link href="{{URL::to('/')}}/assets/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">   
    <link href="{{URL::to('/')}}/assets/plugins/font-elegant/elegant.css" rel="stylesheet" type="text/css">   
    <link href="{{URL::to('/')}}/assets/plugins/ionicons-2.0.1/css/ionicons.min.css" rel="stylesheet" type="text/css">   
    <link href="{{URL::to('/')}}/assets/plugins/rs-plugin/css/settings.css"  rel="stylesheet" media="screen" />
    <link href="{{URL::to('/')}}/assets/plugins/prettyphoto/css/prettyPhoto.css" rel="stylesheet" type="text/css"> 
    
    <!-- Custom Style -->
    <link href="{{URL::to('/')}}/assets/css/style-vox.css" rel="stylesheet" type="text/css">
<link href="{{URL::to('/')}}/assets/plugins/css/owl.carousel.css" rel="stylesheet" type="text/css">   
    <!--[if lt IE 9]>
    <script src="{{URL::to('/')}}/assets/plugins/iesupport/html5shiv.js"></script>
    <script src="{{URL::to('/')}}/assets/plugins/iesupport/respond.js"></script>
    <![endif]-->
    <link href="https://unpkg.com/ionicons@4.2.2/dist/css/ionicons.min.css" rel="stylesheet">
    @yield('css_additional')
  </head>
  <body id="home" class="wide">
    
    <!-- PRELOADER -->
    <div id="loading">
      <div class="loader"></div>
    </div>
    <!-- /PRELOADER -->

    <!-- WRAPPER -->
    <div id="full-site-wrapper">
      <main class="wrapper"> 
        <!-- Header -->
        <header>  
          <section class="header-topbar notification-bar">
            <div class="container theme-container">  
              <div class="border">
                <div class="row">
                  <div class="col-md-10 col-sm-10"> 
                    <p class="gray-color">  </p>
                  </div>
                  <div class="col-md-2 col-sm-2">

                  </div>
                </div>    
              </div>
            </div>
          </section>
          <section class="main-header-admin">
            <div class="header-wrap upper-text"> 
              <div class="container theme-container">
                <div class="top-bar">
                  <div class="row">
                    <div class="col-md-10 col-sm-5" valign='middle'> 
                      <div class="logo  navbar-brand">
                          <h2 class="logo-title  font-2"> <a href="/"><img src="https://www.voxteneo.com/media/v2wga2ta/vox-logo.svg?width=40" width="40px"></a> </h2>
                      </div>
                    </div>
                    <div class="col-md-2 col-sm-5 top-right text-right hidden-xs">
                      <ul class="top-elements">     
                        @if(Session::get('user_id') != '')
                        <li class="dropdown">
                          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" >
                            <i class="fa fa-user fa-2x"></i>
                          </a>
                          <ul class="dropdown-menu" style="margin-left:-100px;">  
                            <li><a href="/account">{{trans('lang.My Account')}}</a></li>                                           
                            <li><a href="/change-password">{{trans('lang.Change Password')}}</a></li>
                            <li><a href="/logout">{{trans('lang.Logut')}}</a></li>
                          </ul>
                        </li>
                        @else
                        
                        @endif
                        
                      </ul>


                    </div>

                  </div>
                </div>
              </div>
            </div>  
          </section>
        </header>
        <!-- /Header -->

        <!-- CONTENT AREA -->

        @include('vox.layouts.menu')
        
        
        <section class="wrapper space-100">
          <div class="theme-admin-container container">
            <div class="row">
              @if(isset($page_type) && $page_type != 'admin_login')
              <div class="is-open">                
                @include('vox.layouts.sidebar-menu')                      
                @yield('content')
              </div>
              @else
              <div>                
                @yield('content')
              </div>
              @endif
            </div>
          </div>
        </section>  
          

        <!-- / CONTENT AREA -->
        @if(isset($page_type) && $page_type == 'admin_login')

          @include('admin.layouts.footer')
        @endif
        <div id="to-top" class="to-top"> <i class="arrow_carrot-up"></i> </div>

      </main>
      <!-- /WRAPPER -->

      

    </div> <!-- Full Site Wrapper -->


    <main itemscope itemtype="http://schema.org/Service">
      <meta itemprop="serviceType" content="Gifting" />
    </main>
    
    <!-- JS Global -->
    <script> 
      var customer_id = "{{Session::get('customer_id')}}";
      var ip_address = "{{(isset($_SERVER['HTTP_CF_CONNECTING_IP']) ? $_SERVER['HTTP_CF_CONNECTING_IP'] : $_SERVER['REMOTE_ADDR'])}}";
      
      var data = {
        'customer_id' : customer_id,
        'ip_address' : ip_address,
      };
    </script> 
    <script src="{{URL::to('/')}}/assets/plugins/js/jquery-2.1.1.min.js"></script>        
    <script src="{{URL::to('/')}}/assets/plugins/js/bootstrap.min.js"></script>
    <script src="{{URL::to('/')}}/assets/plugins/js/bootstrap-select.min.js"></script>
    <script src="{{URL::to('/')}}/assets/plugins/js/datetimepicker/bootstrap-datetimepicker.js"></script>
    <script src="{{URL::to('/')}}/assets/plugins/js/datetimepicker/locales/bootstrap-datetimepicker.id.js"></script>
    <script src="{{URL::to('/')}}/assets/plugins/js/owl.carousel.min.js"></script>
    <script src="{{URL::to('/')}}/assets/plugins/js/jquery.subscribe-better.min.js"></script>
    <script src="{{URL::to('/')}}/assets/plugins/js/jquery.plugin.min.js"></script>   
    <script src="{{URL::to('/')}}/assets/plugins/js/jquery.countdown.js"></script>   
    <script src="{{URL::to('/')}}/assets/plugins/js/jquery.sticky.js"></script>
    <script src="{{URL::to('/')}}/assets/plugins/js/isotope.pkgd.min.js"></script>        
    <script src="{{URL::to('/')}}/assets/plugins/rs-plugin/js/jquery.themepunch.tools.min.js"></script>
    <script src="{{URL::to('/')}}/assets/plugins/rs-plugin/js/jquery.themepunch.revolution.min.js"></script>
    <script src="{{URL::to('/')}}/assets/plugins/js/clamp.js"></script>

    <!-- Custom JS -->
    <script src="{{URL::to('/')}}/assets/js/ns.js"></script>

    <!--[if (gte IE 9)|!(IE)]><!-->   
    <!--        <script src="{{URL::to('/')}}/assets/js/jquery.cookie.js"></script>        
            <script src="{{URL::to('/')}}/assets/plugins/style-switcher/style.switcher.js"></script>-->
    <!--<![endif]-->
    
    @yield('script')
  </body>
</html>