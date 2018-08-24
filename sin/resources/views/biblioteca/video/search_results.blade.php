@extends('biblioteca.video.index')

@section('archivio')
     @include('biblioteca.video.search_partial')

      <div class="alert alert-info alert-dismissable fade in">Ricerca effettuata:<strong> {{$msgSearch}}</strong>
             <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
       </div>

       <div class="alert alert-info alert-dismissable fade in"><strong> {{$query}}</strong>
             <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
       </div>

       <div id="results" class="alert alert-success"> Numero di video Trovati: <strong> {{ $videos->total() }} </strong></div>


  @if ($videos->total() > 0)
     <table  id="table" class='table table-bordered'>
       <thead class='thead-inverse'>
       <tr>
         <th style="width:10%"  style="font-size:10px" > {{ App\Traits\SortableTrait::link_to_sorting_action('CASSETTA') }} </th>
         <th style="width:30%"  style="font-size:10px" >{{ App\Traits\SortableTrait::link_to_sorting_action('DATA_REGISTRAZIONE') }}</th>
         <th style="width:10%"  style="font-size:10px" >{{ App\Traits\SortableTrait::link_to_sorting_action('DESCRIZIONE') }}</th>
         </tr>
       </thead>
       <tbody>
       @forelse ($videos as $video)
            <tr>
             <td> {{ $video->cassetta }}</td>
             <td>{{ $video->data_registrazione }} </td>
             <td>{{ $video->descrizione }}</td>
           </tr>
       @empty
           <div class="alert alert-danger">
               <strong>Nessun risultato ottenuto</strong>
           </div>
       @endforelse
     </tbody>
     </table>

     {{ $videos->appends(request()->except('page'))->links() }}
   @endif

@endsection

 <!-- #results anchor -->
<script>
  window.location.hash = "results";
</script>
