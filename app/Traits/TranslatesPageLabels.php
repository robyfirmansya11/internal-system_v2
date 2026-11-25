<?php

namespace App\Traits;

trait TranslatesPageLabels
{
    use TranslatesNavigation;

    public function getTitle(): string|\Illuminate\Contracts\Support\Htmlable
    {
        $title = parent::getTitle();

        return is_string($title) ? __($title) : $title;
    }
}
