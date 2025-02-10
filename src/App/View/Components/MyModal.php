<?php

declare(strict_types=1);

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

final class MyModal extends Component
{
    public function __construct(
        public string $buttonStyle,
        public string $buttonTitle,
        public string $modalTitle,
    ) {}

    public function render(): View|Closure|string
    {
        return view('components.modal');
    }
}
