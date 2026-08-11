@extends("nomadelfia.persone.index")

@section("content")
  @include("partials.header", ["title" => "Aggiungi Persona"])
  <div class="row">
    <div class="col-md-4 offset-md-4">
      <h4>Dati Anagrafici</h4>
      <form method="POST" action="{{ route("nomadelfia.person.store") }}">
        @csrf
        <div class="mb-3">
          <label for="fornominativo" class="form-label">Nominativo</label>
          <input
            type="text"
            class="form-control @error('nominativo') is-invalid @enderror"
            id="fornominativo"
            name="nominativo"
            value="{{ old("nominativo") }}"
            placeholder="Nominativo"
          />
          @error('nominativo')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        <div class="mb-3">
          <label for="fornome" class="form-label">Nome</label>
          <input
            type="text"
            class="form-control @error('nome') is-invalid @enderror"
            id="fornome"
            name="nome"
            value="{{ old("nome") }}"
            placeholder="Nome"
          />
          @error('nome')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        <div class="mb-3">
          <label for="forcognome" class="form-label">Cognome</label>
          <input
            type="text"
            class="form-control @error('cognome') is-invalid @enderror"
            id="forcognome"
            name="cognome"
            placeholder="Cognome"
            value="{{ old("cognome") }}"
          />
          @error('cognome')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        <div class="mb-3">
          <label for="fornascita" class="form-label">Data di Nascita</label>
          <input
            type="date"
            class="form-control @error('data_nascita') is-invalid @enderror"
            name="data_nascita"
            value="{{ old("data_nascita") }}"
          />
          @error('data_nascita')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        <div class="mb-3">
          <label for="forluogo" class="form-label">Luogo di nascita</label>
          <input
            type="text"
            class="form-control @error('luogo_nascita') is-invalid @enderror"
            id="forluogo"
            placeholder="Luogo di nascita"
            name="luogo_nascita"
            value="{{ old("luogo_nascita") }}"
          />
          @error('luogo_nascita')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        <div class="mb-3">
          <label class="form-label">Sesso</label>
          <div class="form-check">
            <input
              class="form-check-input @error('sesso') is-invalid @enderror"
              type="radio"
              name="sesso"
              value="M"
              id="male"
              @if (old('sesso') == 'M') checked @endif
            />
            <label class="form-check-label" for="male">Maschio</label>
          </div>
          <div class="form-check">
            <input
              class="form-check-input @error('sesso') is-invalid @enderror"
              type="radio"
              name="sesso"
              value="F"
              id="female"
              @if (old('sesso') == 'F') checked @endif
            />
            <label class="form-check-label" for="female">Femmina</label>
          </div>
          @error('sesso')
            <div class="text-danger small mt-1">{{ $message }}</div>
          @enderror
        </div>

        <div class="mb-3">
          <button class="btn btn-success" type="submit">Inserisci</button>
        </div>
      </form>
    </div>
  </div>
@endsection
