<?php

namespace App\Traits;

trait TranslatesResourceLabels
{
    use TranslatesNavigation;

    public static function getModelLabel(): string
    {
        return __(parent::getModelLabel());
    }

    public static function getPluralModelLabel(): string
    {
        return __(static::$pluralModelLabel ?? static::$navigationLabel ?? parent::getPluralModelLabel());
    }
}
