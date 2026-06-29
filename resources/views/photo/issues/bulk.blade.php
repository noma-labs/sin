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
  <div class="d-flex justify-content-between align-items-center my-3">
    <h5 class="mb-0">Aggiornamento Foto Per Striscia</h5>
  </div>
  <div class="row g-2 mb-3">
    <div class="col-md-6">
      <div class="card bg-light border-0">
        <div
          class="card-body py-2 d-flex justify-content-between align-items-center"
        >
          <p class="small text-muted mb-0">Foto con problemi aperti</p>
          <p class="h5 mb-0 text-danger">{{ $photosWithIssuesCount }}</p>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="card bg-light border-0">
        <div
          class="card-body py-2 d-flex justify-content-between align-items-center"
        >
          <p class="small text-muted mb-0">Strisce con problemi aperti</p>
          <p class="h5 mb-0 text-danger">{{ $stripesWithIssuesCount }}</p>
        </div>
      </div>
    </div>
  </div>
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
            {{ $stripesWithIssuesCount }} strisce con problemi
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
                  {{ \Illuminate\Support\Carbon::parse($currentGroup->strip_date)->format("Y-m-d") }}
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
              <label class="form-label mb-0 text-nowrap">Data foto:</label>
              <span id="taken_at_bulk_display" class="fw-semibold">
                {{ old("taken_at", $suggestedDate) !== "" ? old("taken_at", $suggestedDate) : "N/A" }}
              </span>
              <input
                type="hidden"
                id="taken_at_bulk"
                name="taken_at"
                value="{{ old("taken_at", $suggestedDate) }}"
              />
              <input
                type="hidden"
                id="description_bulk"
                name="description"
                value="{{ old("description", "") }}"
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

          {{-- Photo grid with checkboxes --}}
          <div class="row g-3">
            @foreach ($issues as $issue)
              <div class="col-md-6 col-xl-4">
                <label
                  class="card h-100 list-group-item-action"
                  for="issue-{{ $issue->id }}"
                >
                  <div class="card-body p-2">
                    <div
                      class="d-flex justify-content-between align-items-start mb-2"
                    >
                      <input
                        type="checkbox"
                        class="form-check-input photo-checkbox"
                        id="issue-{{ $issue->id }}"
                        name="issue_ids[]"
                        value="{{ $issue->id }}"
                        onchange="updateCounts()"
                        @if (is_array(old("issue_ids")) && in_array((string) $issue->id, old("issue_ids"))) checked @endif
                      />
                      {{ $issue->file_name }}
                    </div>

                    <img
                      src="{{ route("photos.preview", [$issue->photo_id]) }}"
                      alt="{{ $issue->file_name }}"
                      class="w-100 rounded mb-2"
                      style="height: 220px; object-fit: cover"
                      loading="lazy"
                    />

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
                </label>
              </div>
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
          <div class="mt-3 mb-0">
            <label for="note_modal_input" class="form-label">
              Nota aggiornamento (opzionale)
            </label>
            <textarea
              id="note_modal_input"
              class="form-control"
              rows="3"
              maxlength="1000"
              placeholder="Inserisci una nota"
              >{{ old("description", "") }}</textarea
            >
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
    const hiddenDescription = document.getElementById("description_bulk");
    const modalInput = document.getElementById("taken_at_modal_input");
    const noteModalInput = document.getElementById("note_modal_input");
    if (
      !hiddenTakenAt ||
      !hiddenDescription ||
      !modalInput ||
      !noteModalInput
    ) {
      return;
    }

    modalInput.value = hiddenTakenAt.value;
    noteModalInput.value = hiddenDescription.value;
  }

  function applyTakenAtFromModal() {
    const hiddenTakenAt = document.getElementById("taken_at_bulk");
    const hiddenDescription = document.getElementById("description_bulk");
    const displayTakenAt = document.getElementById("taken_at_bulk_display");
    const modalInput = document.getElementById("taken_at_modal_input");
    const noteModalInput = document.getElementById("note_modal_input");
    if (
      !hiddenTakenAt ||
      !hiddenDescription ||
      !displayTakenAt ||
      !modalInput ||
      !noteModalInput
    ) {
      return;
    }

    const selectedDate = modalInput.value.trim();
    const selectedNote = noteModalInput.value.trim();
    hiddenTakenAt.value = selectedDate;
    hiddenDescription.value = selectedNote;
    displayTakenAt.textContent = selectedDate !== "" ? selectedDate : "N/A";
  }

  // Restore count on page load (after validation error redirect)
  document.addEventListener("DOMContentLoaded", () => {
    updateCounts();
    syncTakenAtModalInput();
  });
</script>
