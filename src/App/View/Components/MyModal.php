<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Modal extends Component
{
    public function __construct(
        public string $buttonStyle,
        public string $buttonTitle,
        public string $modalTitle,
    )
    {

    }

    public function render(): View|Closure|string
    {
        return view('components.modal');
    }
}
