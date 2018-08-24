@if( Session::has( 'success' ))
<!-- <div class="alert alert-primary" >
  This is a primary alert—check it out!
</div> -->
<div class="alert alert-success" role="alert"> {{ Session::get( 'success' )}}</strong>
      <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
</div>
@elseif( Session::has( 'warning' ))
<div class="alert alert-warning" role="alert"> {{ Session::get( 'warning' )}}</strong>
      <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
</div>
@elseif( Session::has( 'error' ))
<div class="alert alert-danger" role="alert"> {{ Session::get( 'error' )}}</strong>
      <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
</div>
@endif


<!-- to be removed  and transform the users controllers to withError -->
<!-- @if(Session::has('flash_message'))
          <div class="container">
              <div class="alert alert-success"><em> {!! session('flash_message') !!}</em>
              </div>
          </div>
@endif -->
