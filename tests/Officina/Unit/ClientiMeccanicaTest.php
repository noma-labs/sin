<?php

namespace Tests\Officina\Unit;


use App\Officina\Models\ViewClienti;

it('includes altro cliente ', function () {
    $altro = ViewClienti::where('nominativo', 'like', 'Altro%')->get();
    dd($altro);
    expect($altro)->toHaveCount(1);
});