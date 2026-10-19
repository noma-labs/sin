<?php

declare(strict_types=1);

arch('anagrafe does not depend on nomadelfia resources')
    ->expect('App\Anagrafe')
    ->not->toUse('App\Nomadelfia')
    ->ignoring('App\Nomadelfia\Concerns');

arch('modules unrelated to the community do not depend on nomadelfia resources')
    ->expect(['App\Biblioteca', 'App\Officina', 'App\Photo', 'App\Admin'])
    ->not->toUse('App\Nomadelfia');
