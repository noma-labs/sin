{{-- The modal to upload, change or delete the photo of the person, opened by the elements with data-bs-target="#modal-foto". --}}
<x-modal modal-id="modal-foto" modal-title="Foto di {{ $persona->nominativo }}">
  <x-slot:body>
    @if ($persona->avatar_path)
      <div class="text-center mb-3">
        <img
          src="{{ route("anagrafe.person.avatar.show", $persona->id) }}"
          alt="Foto di {{ $persona->nominativo }}"
          class="img-thumbnail"
          style="max-width: 200px; max-height: 240px; object-fit: cover"
        />
      </div>
    @endif

    <form
      method="POST"
      id="formFotoPersona{{ $persona->id }}"
      action="{{ route("anagrafe.person.avatar.store", $persona->id) }}"
      enctype="multipart/form-data"
    >
      @csrf
      <label class="form-label" for="avatar">
        {{ $persona->avatar_path ? "Sostituisci con una nuova foto" : "Scegli una foto" }}
      </label>
      <input
        id="avatar"
        type="file"
        class="form-control @error("avatar") is-invalid @enderror"
        name="avatar"
        accept="image/jpeg,image/png,image/webp"
        required
      />
      @error("avatar")
        <div class="invalid-feedback">{{ $message }}</div>
      @enderror

      <div class="form-text">JPG, PNG o WEBP, massimo 5 MB.</div>
    </form>

    @if ($persona->avatar_path)
      <form
        method="POST"
        id="formEliminaFotoPersona{{ $persona->id }}"
        action="{{ route("anagrafe.person.avatar.destroy", $persona->id) }}"
      >
        @method("DELETE")
        @csrf
      </form>
    @endif
  </x-slot:body>
  <x-slot:footer>
    @if ($persona->avatar_path)
      <button
        class="btn btn-outline-danger me-auto"
        form="formEliminaFotoPersona{{ $persona->id }}"
      >
        Elimina foto
      </button>
    @endif

    <button class="btn btn-success" form="formFotoPersona{{ $persona->id }}">
      Carica foto
    </button>
  </x-slot:footer>
</x-modal>
