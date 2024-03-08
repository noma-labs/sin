<div class="row">
    <div class="col-md-3">
        <h2>Postulanti {{ $postulanti->total }}</h2>
        <h4>Uomini {{ count($postulanti->uomini) }}</h4>
        @foreach ($postulanti->uomini as $uomo)
            <div>{{ $uomo->nominativo }}</div>
        @endforeach

        <h4>Donne {{ count($postulanti->donne) }}</h4>
        @foreach ($postulanti->donne as $donna)
            <div>{{ $donna->nominativo }}</div>
        @endforeach
    </div>
    <div class="col-md-3">
        <h2>Ospiti {{ $ospiti->total }}</h2>
        <h4>Uomini {{ count($ospiti->uomini) }}</h4>
        @foreach ($ospiti->uomini as $uomo)
            <div>{{ $uomo->nominativo }}</div>
        @endforeach

        <h4>Donne {{ count($ospiti->donne) }}</h4>
        @foreach ($ospiti->donne as $donna)
            <div>{{ $donna->nominativo }}</div>
        @endforeach
    </div>
    <div class="col-md-3">
        <h2>Figli 18...21 {{ $fra1821->total }}</h2>
        <h4>Uomini {{ count($fra1821->uomini) }}</h4>
        @foreach ($fra1821->uomini as $uomo)
            <div>{{ $uomo->nominativo }}</div>
        @endforeach

        <h4>Donne {{ count($fra1821->donne) }}</h4>
        @foreach ($fra1821->donne as $donna)
            <div>{{ $donna->nominativo }}</div>
        @endforeach
    </div>
    <div class="col-md-3">
        <h2>Figli 21 {{ $mag21->total }}</h2>
        <h4>Uomini {{ count($mag21->uomini) }}</h4>
        @foreach ($mag21->uomini as $uomo)
            <div>{{ $uomo->nominativo }}</div>
        @endforeach

        <h4>Donne {{ count($mag21->donne) }}</h4>
        @foreach ($mag21->donne as $donna)
            <div>{{ $donna->nominativo }}</div>
        @endforeach
    </div>
</div>
