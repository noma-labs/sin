<?php

namespace App\Http\Livewire;

use App\Scuola\Models\Anno;
use Carbon\Carbon;
use Domain\Nomadelfia\PopolazioneNomadelfia\Exports\PopolazioneExport;
use Domain\Nomadelfia\PopolazioneNomadelfia\Models\PopolazioneAttuale;
use Domain\Nomadelfia\PopolazioneNomadelfia\Models\Posizione;
use Domain\Nomadelfia\PopolazioneNomadelfia\Models\Stato;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filters\DateFilter;
use Rappasoft\LaravelLivewireTables\Views\Filters\MultiSelectFilter;

class PopolazioneTable extends DataTableComponent
{
    protected $model = PopolazioneAttuale::class;

    public function configure(): void
    {
        $this->setPrimaryKey('id')
            ->setAdditionalSelects(['id as id'])
            ->setFilterLayoutSlideDown()
            ->setTableRowUrl(function ($row) {
                return route('nomadelfia.persone.dettaglio', ['idPersona' => $row->id]);
            });

    }

    public function columns(): array
    {
        return [
            Column::make('Num Elenco', 'numero_elenco')
                ->sortable()
                ->searchable(),
            Column::make('Nominativo', 'nominativo')
                ->sortable()
                ->searchable(),
            Column::make('Nome', 'nome')
                ->sortable()
                ->searchable(),
            Column::make('Cognome', 'cognome')
                ->sortable()
                ->searchable(),
            Column::make('Data Nascita', 'data_nascita')
                ->sortable(),
            Column::make('Data Entrata', 'data_entrata')
                ->sortable(),
            Column::make('Posizione', 'posizione')
                ->sortable(),
            //            Column::make('Azienda', 'azienda')
            //                ->sortable()
            //                ->collapseOnMobile(),
            Column::make('Scuola', 'scuola')
                ->sortable(),
            Column::make('Stato', 'stato')
                ->sortable()
                ->collapseOnMobile(),
            Column::make('Famiglia', 'famiglia')
                ->sortable()
                ->collapseOnMobile(),
        ];
    }

    public function filters(): array
    {
        return [
            MultiSelectFilter::make('Posizione')
                ->options(
                    Posizione::query()
                        ->orderBy('nome')
                        ->get()
                        ->keyBy('nome')
                        ->map(fn ($tag) => Str::title($tag->nome))
                        ->toArray()
                )
                ->filter(function (Builder $builder, array $values) {
                    $builder->whereIn('posizione', $values);
                }),
            MultiSelectFilter::make('Stato')
                ->options(
                    Stato::query()
                        ->orderBy('nome')
                        ->get()
                        ->keyBy('nome')
                        ->map(fn ($tag) => Str::title($tag->nome))
                        ->toArray()
                )
                ->filter(function (Builder $builder, array $values) {
                    $builder->whereIn('stato', $values);
                }),
            MultiSelectFilter::make('Classe')
                ->options(
                    Anno::getLastAnno()
                        ->classiTipoAttuali()
                        ->get()
                        ->keyBy('nome')
                        ->map(fn ($tag) => Str::title($tag->nome))
                        ->toArray()
                )
                ->filter(function (Builder $builder, array $values) {
                    $builder->whereIn('scuola', $values);
                }),
            DateFilter::make('Nato Dopo')
                ->filter(function (Builder $builder, string $value) {
                    $builder->where('data_nascita', '>=', $value);
                }),
            DateFilter::make('Nato Prima')
                ->filter(function (Builder $builder, string $value) {
                    $builder->where('data_nascita', '<', $value);
                }),
        ];
    }

    public function bulkActions(): array
    {
        return [
            'exportSelected' => 'Export Excel',
        ];
    }

    public function exportSelected()
    {
        $users = $this->getSelected();

        $this->clearSelected();
        $now = Carbon::now()->format('Y-m-d');

        return (new PopolazioneExport($users))->download("popolazione-{$now}.xlsx");
    }
}
