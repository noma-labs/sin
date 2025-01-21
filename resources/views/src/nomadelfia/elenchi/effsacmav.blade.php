<div class="row">
    <div class="col-md-3">
        <h2>Effettivi Uomini {{ count($effettivi->uomini) }}</h2>
        @foreach ($effettivi->uomini as $uomo)
            <div>{{ $uomo->nominativo }}</div>
        @endforeach
    </div>
    <div class="col-md-3">
        <h2>Effettivi Donne {{ count($effettivi->donne) }}</h2>
        @foreach ($effettivi->donne as $donna)
            <div>{{ $donna->nominativo }}</div>
        @endforeach
    </div>
    <div class="col-md-3">
        <h2>Sacerdoti {{ count($sacerdoti) }}</h2>
        @forelse ($sacerdoti as $sacerdote)
            <div>{{ $sacerdote->nominativo }}</div>
        @endforelse
    </div>
    <div class="col-md-3">
        <h2>Mamme Di vocazione {{ count($mvocazione) }}</h2>
        @forelse ($mvocazione as $mammavoc)
            <div>{{ $mammavoc->nominativo }}</div>
        @endforelse
    </div>
</div>
