<?php

declare(strict_types=1);

namespace Tests\Officina\Unit;

use App\Officina\Models\ViewClienti;

it("includes 'altro cliente' in the clients", function (): void {
    $altro = ViewClienti::where('nominativo', 'like', 'Altro%')->get();
    expect($altro)->toHaveCount(1);
});
