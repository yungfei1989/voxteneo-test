<div class="site-breadcumb col-md-6 space-80">                        
    @if(isset($page_type) && in_array($page_type,['account','other']))
    <ol class="breadcrumb breadcrumb-menubar">
        <li> 
          <a href="{{'/'}}" class="gray-color"> {{trans('lang.Home')}} </a> 
          > {{$page_name}} </li>                             
    </ol>
    @else
    <h2 class="section-title size-48 no-margin space-bottom-20">
      {{trans('language.product detail')}}
    </h2> 
    <ol class="breadcrumb breadcrumb-menubar">
        <li> 
          <a href="{{'/'}}" class="gray-color"> {{trans('lang.Home')}} </a> 
          > 
          <a href="#" class="gray-color"> {{$data->category->parentCategory->name}} </a> >
          {{$data->category->name}} </li>                             
    </ol>
    @endif
</div>