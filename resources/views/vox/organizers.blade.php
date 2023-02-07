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
                              <div class='col-md-12'>
                                <button onclick='window.location="/organizers/new"' style='margin-top:30px;'> <b> {{trans('lang.New')}} </b> </button><br>
                              </div>
                          </div>
                            <div class='row'>
                            <div class="container-body">
                              <table class="table">
                              @foreach($data as $detail)
                              <tr>
                                <td>
                                  @if(isset($detail['imageLocation']))
                                    <img src="{{$detail['imageLocation']}}" width="80px">
                                  @endif
                                </td>
                                <td>{{$detail['organizerName']}}</td>
                                <td><button onclick='window.location="/organizers/edit/{{$detail['id']}}"'>Edit</button></td>
                                <td><button onclick='confirmDelete("{{$detail['id']}}")'>Delete</button></td>
                              </tr>
                              @endforeach
                              </table>
                              @if(isset($meta['pagination']))  
                              <div class="pagination-wrapper post-pagination space-30">
                                  <ul class="pagination-list font-2">
                                      @if($page > 1)
                                        <li class="next"> <a href="/organizers?page={{($page-1)}}"> << </a> </li>
                                      @endif
                                      @for($i=1;$i<=$meta['pagination']['total_pages'];$i++)
                                        @if($page == $i)
                                          <li class="active">{{$i}}</li>
                                        @else
                                          <li> <a href="/organizers?page={{$i}}"> {{$i}} </a> </li>
                                        @endif
                                      @endfor
                                      @if($page < $meta['pagination']['total_pages'])
                                        <li class="next"> <a href="/organizers?page={{($page+1)}}"> >> </a> </li>
                                      @endif
                                  </ul>
                              </div>
                              @endif
                            </div>
                          </div>

                            
                          <div class="visible-lg visible-xs space-top-50"></div>
                        </div>  
                      </div>                    
@stop

@section('script')
<script>
  function confirmDelete(id){
    var result = confirm("Want to delete?");
    if (result) {
        window.location="/organizers/delete/"+id;
    }
    return false;
  }
</script>
@stop