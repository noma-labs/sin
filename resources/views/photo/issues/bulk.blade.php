@extends("photo.main")

@php
    $issueLabels = [
        "not_yet_born" => "Persona non ancora nata",
        "already_deceased" => "Persona già deceduta",
        "year_mismatch_description" => "Anno non corrispondente alla descrizione",
        "year_like_number_in_description" => "Numero simile all'anno nella descrizione",
    ];

    $currentGroup = $datnumGroups->first();
    $suggestedDate = $currentGroup?->strip_date
        ? \Illuminate\Support\Carbon::parse($currentGroup->strip_date)->format("Y-m-d")
        : "";
    $editDateModalId = "edit-bulk-taken-at-modal";
@endphp

@section("title", "Aggiornamento Foto Per Striscia")

@section("content")
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0">Aggiornamento Foto Per Striscia</h5>
    <a
      href="{{ route("photos.issues.index") }}"
      class="btn btn-sm btn-outline-secondary"
    >
      ← Problemi singoli
    </a>
  </div>
  @include("partials.flash")

  @if ($datnumGroups->total() > 0 && $currentGroup)
    <div class="card border-danger">
      <div class="card-header bg-danger text-white">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            Striscia
            <strong>{{ $currentGroup->datnum }}</strong>
            &mdash;
            <span class="badge text-bg-light text-danger ms-1">
              {{ $issueLabels[$currentGroup->issue_type] ?? $currentGroup->issue_type }}
            </span>
            <small class="ms-2 opacity-75">
              striscia {{ $datnumGroups->currentPage() }} di {{ $datnumGroups->lastPage() }}
            </small>
          </div>
          <span class="badge text-bg-light text-danger">
            {{ $datnumGroups->total() }} strisce con problemi
          </span>
        </div>
      </div>

      <form
        method="POST"
        action="{{ route("photos.issues.bulk.update") }}"
        id="bulk-update-form"
      >
        @csrf
        @method("PUT")

        <div class="card-body">
          {{-- Strip metadata --}}
          <dl class="row mb-3">
            <dt class="col-sm-3">Tipo Problema</dt>
            <dd class="col-sm-9">
              <span class="badge text-bg-warning fs-6">
                {{ $issueLabels[$currentGroup->issue_type] ?? $currentGroup->issue_type }}
              </span>
            </dd>

            <dt class="col-sm-3">Striscia (DATNUM)</dt>
            <dd class="col-sm-9">
              <a
                href="{{ route("photos.stripes.show", $currentGroup->dbf_id) }}"
                class="text-decoration-none"
                target="_blank"
                rel="noopener noreferrer"
              >
                {{ $currentGroup->datnum }} – {{ $currentGroup->anum }} ↗
              </a>
            </dd>

            @if ($currentGroup->dbf_descrizione)
              <dt class="col-sm-3">Descrizione</dt>
              <dd class="col-sm-9">{{ $currentGroup->dbf_descrizione }}</dd>
            @endif

            @if ($currentGroup->strip_date)
              <dt class="col-sm-3">Data Striscia</dt>
              <dd class="col-sm-9">
                <span class="badge text-bg-info">
                  {{ \Illuminate\Support\Carbon::parse($currentGroup->strip_date)->format("d/m/Y") }}
                </span>
                <small class="text-muted ms-1">
                  (usata come suggerimento per la data foto)
                </small>
              </dd>
            @endif
          </dl>

          <hr />

          {{-- Bulk controls toolbar --}}
          <div
            class="d-flex align-items-center gap-3 mb-3 p-2 bg-light rounded"
          >
            <div class="form-check mb-0">
              <input
                type="checkbox"
                id="selectAll"
                class="form-check-input"
                onchange="toggleAll(this)"
              />
              <label for="selectAll" class="form-check-label fw-semibold">
                Seleziona tutte
              </label>
            </div>

            <div class="d-flex align-items-center gap-2">
              <label class="form-label mb-0 text-nowrap"> Nuova data: </label>
              <span
                id="taken_at_bulk_display"
                class="badge text-bg-info"
                style="min-width: 120px"
              >
                {{ old("taken_at", $suggestedDate) !== "" ? old("taken_at", $suggestedDate) : "N/A" }}
              </span>
              <input
                type="hidden"
                id="taken_at_bulk"
                name="taken_at"
                value="{{ old("taken_at", $suggestedDate) }}"
              />
              <button
                type="button"
                class="btn btn-sm btn-outline-primary"
                data-bs-toggle="modal"
                data-bs-target="#{{ $editDateModalId }}"
                onclick="
                  event.preventDefault();
                  event.stopPropagation();
                  syncTakenAtModalInput();
                "
              >
                ✏️ Modifica
              </button>
              @error("taken_at")
                <div class="invalid-feedback d-block">{{ $message }}</div>
              @enderror
            </div>

            <p class="mb-0 fw-semibold">
              Foto selezionate:
              <span id="selected-count" class="badge text-bg-primary ms-1"
                >0</span
              >
            </p>
          </div>

          @error("issue_ids")
            <div class="alert alert-danger py-2">{{ $message }}</div>
          @enderror

          {{-- Photo list with checkboxes --}}
          <div class="list-group list-group-flush">
            @foreach ($issues as $issue)
              <label
                class="list-group-item list-group-item-action py-2 px-2"
                for="issue-{{ $issue->id }}"
              >
                <div class="d-flex gap-2 align-items-start">
                  <div class="pt-1">
                    <input
                      type="checkbox"
                      class="form-check-input photo-checkbox"
                      id="issue-{{ $issue->id }}"
                      name="issue_ids[]"
                      value="{{ $issue->id }}"
                      onchange="updateCounts()"
                      @if (is_array(old("issue_ids")) && in_array((string) $issue->id, old("issue_ids"))) checked @endif
                    />
                  </div>

                  <img
                    src="{{ route("photos.preview", [$issue->photo_id]) }}"
                    alt="{{ $issue->file_name }}"
                    style="
                      width: 80px;
                      height: 60px;
                      object-fit: cover;
                      flex-shrink: 0;
                    "
                    loading="lazy"
                  />

                  <div class="flex-grow-1 overflow-hidden">
                    <p class="mb-0 text-truncate" style="font-size: 0.8rem">
                      <a
                        href="{{ route("photos.show", $issue->photo_id) }}"
                        class="text-decoration-none"
                        onclick="
                          event.preventDefault();
                          event.stopPropagation();
                        "
                        target="_blank"
                        rel="noopener noreferrer"
                      >
                        {{ $issue->file_name }}
                      </a>
                    </p>

                    <p
                      class="mb-0 text-muted text-truncate"
                      style="font-size: 0.7rem"
                    >
                      {{ $issue->source_file }}
                    </p>

                    <div
                      class="d-flex gap-2 flex-wrap mt-1"
                      style="font-size: 0.75rem"
                    >
                      <span>
                        📅 {{ $issue->taken_at ? \Illuminate\Support\Carbon::parse($issue->taken_at)->format("Y-m-d") : "N/A" }}
                      </span>

                      @if ($issue->photo_persona_name)
                        <span class="text-muted">
                          👤 {{ $issue->photo_persona_name }}
                          @if ($issue->nome)
                            ({{ $issue->nome }} {{ $issue->cognome }})
                          @endif
                        </span>
                      @endif
                    </div>
                  </div>
                </div>
              </label>
            @endforeach
          </div>
        </div>

        <div
          class="card-footer d-flex justify-content-between align-items-center"
        >
          @if ($datnumGroups->onFirstPage())
            <button class="btn btn-secondary" disabled>← Indietro</button>
          @else
            <a
              href="{{ $datnumGroups->previousPageUrl() }}"
              class="btn btn-primary"
            >
              ← Indietro
            </a>
          @endif

          @if ($datnumGroups->hasMorePages())
            <a
              href="{{ $datnumGroups->nextPageUrl() }}"
              class="btn btn-primary"
            >
              Avanti →
            </a>
          @else
            <button class="btn btn-secondary" disabled>Avanti →</button>
          @endif
        </div>
      </form>

      <x-modal modal-title="Modifica Data Foto" :modal-id="$editDateModalId">
        <x-slot:body>
          <div class="mb-0">
            <label for="taken_at_modal_input" class="form-label"
              >Data Foto</label
            >
            <input
              type="text"
              id="taken_at_modal_input"
              class="form-control"
              value="{{ old("taken_at", $suggestedDate) }}"
              placeholder="yyyy-mm-dd"
              pattern="\d{4}-\d{2}-\d{2}"
            />
          </div>
        </x-slot:body>
        <x-slot:footer>
          <button
            type="button"
            class="btn btn-secondary"
            data-bs-dismiss="modal"
          >
            Annulla
          </button>
          <button
            type="submit"
            id="confirm-bulk-update-btn"
            class="btn btn-primary"
            form="bulk-update-form"
            disabled
            data-bs-dismiss="modal"
            onclick="applyTakenAtFromModal()"
          >
            Conferma
          </button>
        </x-slot:footer>
      </x-modal>
    </div>
  @else
    <div class="alert alert-success">
      <strong>Nessun problema rilevato!</strong>
      Non ci sono strisce con problemi aperti da aggiornare in blocco.
    </div>
  @endif
@endsection

<script>
  function updateCounts() {
    const selectedCount = document.getElementById("selected-count");
    const confirmBulkUpdateButton = document.getElementById(
      "confirm-bulk-update-btn",
    );
    const selectAll = document.getElementById("selectAll");
    if (!selectedCount || !confirmBulkUpdateButton || !selectAll) {
      return;
    }

    const checkboxes = document.querySelectorAll(".photo-checkbox");
    const checked = Array.from(checkboxes).filter((cb) => cb.checked);
    const count = checked.length;

    selectedCount.textContent = count;
    confirmBulkUpdateButton.disabled = count === 0;

    if (count === 0) {
      selectAll.indeterminate = false;
      selectAll.checked = false;
    } else if (count === checkboxes.length) {
      selectAll.indeterminate = false;
      selectAll.checked = true;
    } else {
      selectAll.indeterminate = true;
    }
  }

  function toggleAll(selectAllCheckbox) {
    if (!selectAllCheckbox) {
      return;
    }

    document
      .querySelectorAll(".photo-checkbox")
      .forEach((cb) => (cb.checked = selectAllCheckbox.checked));
    updateCounts();
  }

  function syncTakenAtModalInput() {
    const hiddenTakenAt = document.getElementById("taken_at_bulk");
    const modalInput = document.getElementById("taken_at_modal_input");
    if (!hiddenTakenAt || !modalInput) {
      return;
    }

    modalInput.value = hiddenTakenAt.value;
  }

  function applyTakenAtFromModal() {
    const hiddenTakenAt = document.getElementById("taken_at_bulk");
    const displayTakenAt = document.getElementById("taken_at_bulk_display");
    const modalInput = document.getElementById("taken_at_modal_input");
    if (!hiddenTakenAt || !displayTakenAt || !modalInput) {
      return;
    }

    const selectedDate = modalInput.value.trim();
    hiddenTakenAt.value = selectedDate;
    displayTakenAt.textContent = selectedDate !== "" ? selectedDate : "N/A";
  }

  // Restore count on page load (after validation error redirect)
  document.addEventListener("DOMContentLoaded", () => {
    updateCounts();
    syncTakenAtModalInput();
  });
</script>
