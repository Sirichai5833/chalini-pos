<?php

namespace App\View\Components;

use Illuminate\View\Component;

class SweetAlert extends Component
{
    public $icon, $title, $text, $confirmButtonText;

    public function __construct($icon = 'success', $title = '', $text = '', $confirmButtonText = 'OK')
    {
        $this->icon = $icon;
        $this->title = $title;
        $this->text = $text;
        $this->confirmButtonText = $confirmButtonText;
    }

    public function render()
    {
        return view('components.sweet-alert');
    }
}
