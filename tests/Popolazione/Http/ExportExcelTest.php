<?php

declare(strict_types=1);

it('can download the population in excel', function () {
    $this->withoutExceptionHandling();
    login();
    $this->get(route('nomadelfia.popolazione.export.excel'))
        ->assertStatus(200)
        ->assertDownload();
});
