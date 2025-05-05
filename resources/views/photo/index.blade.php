@extends("layouts.app")

@section("title", "Foto (Enrico)")


@section("content")

<form action="{{ route('photos.index') }}" method="GET" class="mb-3">
    <div class="d-flex flex-wrap gap-2">
        @foreach ($years as $year)
            <button type="submit" name="year" value="{{ $year->year }}" class="btn btn-sm btn-secondary">
                {{ $year->year }}
                <span class="badge bg-light text-dark">{{ $year->count }}</span>
            </button>
        @endforeach
    </div>
</form>


<div class="row justify-content-around">
    <div class="col-md-8">
      <h2>Favorite Photos</h2>
      <ul class="list-group">
          @foreach ($photos as $photo)
              <li class="list-group-item d-flex align-items-center">

                  <img src={{ asset("storage/foto-sport/$photo->folder_title/$photo->file_name") }}
                      alt="Photo"
                      class="img-thumbnail me-3"
                      style="width: 500px; height: auto;"
                      >
                  <div>
                      <p class="mb-1"><strong>File Name:</strong> {{$photo->file_name}}</p>
                      <p class="mb-1"><strong>Folder:</strong> {{$photo->folder_title}}</p>
                      <p class="mb-0"><strong>Subject:</strong> {{$photo->subject}}</p>

                      <a href="{{ route('photos.show', $photo->sha) }}" class="btn btn-primary mt-2">View</a>

                      @if ($photo->favorite === 0)
                          <form action="{{ route('photos.favorite', $photo->sha) }}"  class="mt-2">
                              @csrf
                              <button type="submit" class="btn btn-success">Favorite</button>
                          </form>
                      @else
                          <form action="{{ route('photos.unfavorite', $photo->sha) }}" method="POST" class="mt-2">
                              @csrf
                              @method('PUT')
                              <button type="submit" class="btn btn-danger">Unfavorite</button>
                          </form>
                      @endif


                      @if ($photo->taken_at)
                          <p class="mb-0"><strong>Taken At:</strong> {{$photo->taken_at}}</p>
                      @else


                      <form action="{{ route('photos.update', $photo->sha) }}" method="POST" class="mt-2">
                          @csrf
                          @method('PUT')
                          <input type="date" name="taken_at" value="{{ $photo->taken_at }}" class="form-control" required>
                          <button type="submit" class="btn btn-success">Update</button>
                      </form>
                      @endif
                  </div>
              </li>
          @endforeach
      </ul>
    </div>
    <div class="col-md-4">
      <h2>Metadata from enrico DBF</h2>
      <ul>
          @foreach ($enrico as $photo)
              <li>
                  <span class="badge text-bg-secondary">{{$photo->data}}</span>
                  {{$photo->datnum}}
                  {{$photo->anum}}
                  {{$photo->localita}}
                  {{$photo->argomento}}
                  {{$photo->descrizione}}
              </li>
          @endforeach
      </ul>
    </div>
  </div>


<script>
    document.addEventListener("DOMContentLoaded", function () {
        console.log("DOM fully loaded and parsed");
            const canvas = document.getElementById("canvas");
            const ctx = canvas.getContext("2d");
            const image = new Image();
            image.onload = function () {
                canvas.width = image.width;
                canvas.height = image.height;
                ctx.drawImage(image, 0, 0);

                console.log("loaded");
                ctx.beginPath();
                    ctx.rect(region.x, region.y, region.width, region.height);

                // Example RegionInfo data (replace with actual data)
                const regionInfo = [{
                    "Area": {
                        "H": 0.02284,
                        "W": 0.04878,
                        "X": 0.12021,
                        "Y": 0.62310
                    },
                    "Name": "SANTOLINI TER",
                    "Rotation": 0.00000,
                    "Type": "Face"
                    },{
                    "Area": {
                        "H": 0.04695,
                        "W": 0.07491,
                        "X": 0.17509,
                        "Y": 0.57170
                    },
                    "Name": "BEPPE MG",
                    "Rotation": 0.00000,
                    "Type": "Face"
                    },{
                    "Area": {
                        "H": 0.05964,
                        "W": 0.07840,
                        "X": 0.25348,
                        "Y": 0.51840
                    },
                    "Name": "SANTOLINI SA",
                    "Rotation": 0.00000,
                    "Type": "Face"
                    },{
                    "Area": {
                        "H": 0.05838,
                        "W": 0.06272,
                        "X": 0.31707,
                        "Y": 0.45051
                    },
                    "Name": "BEPPONE GZ",
                    "Rotation": 0.00000,
                    "Type": "Face"
                    },{
                    "Area": {
                        "H": 0.05076,
                        "W": 0.06272,
                        "X": 0.41115,
                        "Y": 0.38832
                    },
                    "Name": "STEFANO EW",
                    "Rotation": 0.00000,
                    "Type": "Face"
                    },{
                    "Area": {
                        "H": 0.03934,
                        "W": 0.06620,
                        "X": 0.48780,
                        "Y": 0.32043
                    },
                    "Name": "KOPA",
                    "Rotation": 0.00000,
                    "Type": "Face"
                    },{
                    "Area": {
                        "H": 0.04569,
                        "W": 0.06620,
                        "X": 0.58885,
                        "Y": 0.26015
                    },
                    "Name": "DINO SI",
                    "Rotation": 0.00000,
                    "Type": "Face"
                    },{
                    "Area": {
                        "H": 0.03173,
                        "W": 0.05923,
                        "X": 0.11672,
                        "Y": 0.67069
                    },
                    "Name": "LILIANA BIGI",
                    "Rotation": 0.00000,
                    "Type": "Face"
                    },{
                    "Area": {
                        "H": 0.02284,
                        "W": 0.06446,
                        "X": 0.31098,
                        "Y": 0.69924
                    },
                    "Name": "GIORGIO LA",
                    "Rotation": 0.00000,
                    "Type": "Face"
                    },{
                    "Area": {
                        "H": 0.02919,
                        "W": 0.04878,
                        "X": 0.44599,
                        "Y": 0.60470
                    },
                    "Name": "RENATO BEC",
                    "Rotation": 0.00000,
                    "Type": "Face"
                    },{
                    "Area": {
                        "H": 0.04061,
                        "W": 0.06620,
                        "X": 0.52787,
                        "Y": 0.53426
                    },
                    "Name": "DENIS BED",
                    "Rotation": 0.00000,
                    "Type": "Face"
                    },{
                    "Area": {
                        "H": 0.03426,
                        "W": 0.07840,
                        "X": 0.60540,
                        "Y": 0.46637
                    },
                    "Name": "GIOVANNI SIR",
                    "Rotation": 0.00000,
                    "Type": "Face"
                    },{
                    "Area": {
                        "H": 0.06091,
                        "W": 0.07491,
                        "X": 0.72909,
                        "Y": 0.41497
                    },
                    "Name": "GIANNI MOT",
                    "Rotation": 0.00000,
                    "Type": "Face"
                    }];

                // Draw rectangles for each region
                regionInfo.forEach(region => {
                    ctx.beginPath();
                    ctx.rect(region.x, region.y, region.width, region.height);
                    ctx.lineWidth = 2;
                    ctx.strokeStyle = "red";
                    ctx.stroke();
                });
            };
            image.src = "";

    });
</script>
@endsection
