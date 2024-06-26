<div>
    <form wire:submit="save">
        <div class="row">
            <div class="col-md-8">
                <div class="row">
                    <div class="col-md-4">
                        <label for="titolo" class="control-label">Titolo</label>
                        <input
                            class="form-control"
                            type="text"
                            id="titolo"
                            name="titolo"
                            wire:model="titolo"
                        />
                    </div>
                    <div class="col-md-4">
                        <label for="anno_scolastico" class="control-label">
                            Anno Scolastico
                        </label>
                        <input
                            class="form-control"
                            type="text"
                            id="anno_scolastico"
                            name="anno_scolastico"
                            wire:model="annoScolastico"
                        />
                    </div>
                    <div class="col-md-4">
                        <label for="data" class="control-label">
                            Data Creazione
                        </label>
                        <input
                            class="form-control"
                            type="date"
                            id="data"
                            name="data"
                            wire:model="data"
                        />
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <label for="studente">Classe</label>
                        <input
                            class="form-control"
                            type="text"
                            id="data"
                            name="data"
                            wire:model="data"
                        />
                    </div>
                    <div class="col-md-6">
                        <label for="studente">Alunni</label>
                        <livewire:search-persona
                            placeholder="Cerca alunno"
                            noResultsMessage="Nessun risultato"
                        />
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <label for="note">Note</label>
                        <input
                            class="form-control"
                            type="text"
                            id="note"
                            name="note"
                            wire:model="note"
                        />
                    </div>
                    <div class="col-md-6">
                        <label for="studente">Alunni</label>
                        <livewire:search-persona
                            placeholder="Cerca alunno"
                            noResultsMessage="Nessun risultato"
                        />
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <button
                            class="btn btn-success"
                            type="submit"
                            value="Upload"
                        >
                            Salva
                        </button>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="row">
                    <div class="col-md-12">
                        <label for="xfile" class="form-label">
                            Scegli file
                        </label>
                        <input type="file" id="xfile" name="file" />
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
