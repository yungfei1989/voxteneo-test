@extends('vox.layouts.master')

@section('content')
  <div class="admin-container">
                        <div class="admin-container-warp">
                          <div class="row">                            
                            <div class="container-body">
                              <form class="register-form row  space-top-15" method='post' action='/organizers/save'>
                                  {{ csrf_field() }}
                                  @if(isset($data))
                                  <input type='hidden' name="id" value='{{$data['id']}}'>
                                  @endif
                                  <div class="form-group col-md-12">
                                      <input required id="imageLocation" value="{{isset($data['imageLocation']) ? $data['imageLocation'] : ''}}" type="text" data-original-title="{{trans('lang.Image Location')}}" class="form-control name input-your-name" placeholder="{{trans('lang.Image Location')}}" name="imageLocation" value="" data-toggle="tooltip" data-placement="bottom" title="{{trans('lang.Image Location')}}">
                                  </div>
                                  <div class="form-group col-md-12">
                                      <input required id="organizerName" value="{{isset($data['organizerName']) ? $data['organizerName'] : ''}}" type="text" data-original-title="{{trans('lang.Organization Name')}}" class="form-control name input-your-name" placeholder="{{trans('lang.Organization Name')}}" name="organizerName" value="" data-toggle="tooltip" data-placement="bottom" title="{{trans('lang.Organization Name')}}">
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