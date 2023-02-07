@extends('vox.layouts.master')

@section('content')
  <div class="admin-container">
      @if(session()->has('message'))
                              <div class="alert alert-success">
                                  {{ session()->get('message') }}
                              </div>
                          @endif
                        <div class="admin-container-warp">
                          <div class="row">                            
                            <div class="container-body">
                              <form class="register-form row  space-top-15" method='post' action='/password/update'>
                                  {{ csrf_field() }}
                                  <div class="form-group col-md-12">
                                      <input required id="oldPassword" type="password" data-original-title="{{trans('lang.Old Password')}}" class="form-control name input-your-name" placeholder="{{trans('lang.Old Password')}}" name="oldPassword" value="" data-toggle="tooltip" data-placement="bottom" title="{{trans('lang.Old Password')}}">
                                  </div>
                                  <div class="form-group col-md-12">
                                      <input required id="newPassword" type="password" data-original-title="{{trans('lang.New Password')}}" class="form-control name input-your-name" placeholder="{{trans('lang.New Password')}}" name="newPassword" value="" data-toggle="tooltip" data-placement="bottom" title="{{trans('lang.Organization Name')}}">
                                  </div>
                                  <div class="form-group col-md-12">
                                      <input required id="repeatPassword" type="password" data-original-title="{{trans('lang.Repeat Password')}}" class="form-control name input-your-name" placeholder="{{trans('lang.Tepeat Password')}}" name="repeatPassword" value="" data-toggle="tooltip" data-placement="bottom" title="{{trans('lang.Organization Name')}}">
                                  </div>
                                  
                                  <div class="form-group col-md-12">
                                      <button class="theme-btn-1 larg-btn btn submit-btn"> <b> {{trans('lang.save')}} </b> </button>
                                  </div>
                              </form>
                            </div>
                          </div>

                            
                          <div class="visible-lg visible-xs space-top-50"></div>
                        </div>  
                      </div>                    
@stop