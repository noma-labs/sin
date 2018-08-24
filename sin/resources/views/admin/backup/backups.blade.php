@extends('admin.index')

@section('archivio')

@include('partials.header', ['title' => 'Amministrazione Backup databases'])
    <div class="row">
      <div class="col-md-12 my-2">
           <a  id="create-new-backup-button" href="{{ route('admin.backup.create') }}" class="btn btn-success float-right">
            Crea un nuovo backup
           </a>
       </div>
        <div class="col-md-12">
          @if (count($backups))
              <table class="table table-striped table-bordered">
                  <thead class='thead-inverse'>
                  <tr>
                      <th>File</th>
                      <th>Dimensione</th>
                      <th>Data</th>
                      <th>Tempo</th>
                      <th></th>
                  </tr>
                  </thead>
                  <tbody>
                  @foreach($backups as $backup)
                      <tr>
                          <td>{{ $backup['file_name'] }}</td>
                          <td>{{ human_filesize($backup['file_size']) }}</td>
                          <td>
                              {{ Carbon::createFromTimestamp($backup['last_modified']) }}
                          </td>
                          <td>
                              {{ Carbon::createFromTimestamp($backup['last_modified'])->diffForHumans() }}
                          </td>
                          <td class="text-right">
                              <a class="btn btn-xs btn-default"
                                 href="{{ route('admin.backup.download',['file_name' => $backup['file_name']]) }}"><i
                                      class="fa fa-cloud-download"></i> Scarica</a>
                              <a class="btn btn-xs btn-danger" data-button-type="delete"
                                 href="{{ route('admin.backup.delete', ['file_name' => $backup['file_name']]) }}"><i class="fa fa-trash-o"></i>
                                  Elimina</a>
                          </td>
                      </tr>
                  @endforeach
                  </tbody>
              </table>
          @else
            <div class="well">
                <h4>Non ci sono backup.</h4>
            </div>
          @endif
        </div>
    </div>
@endsection
