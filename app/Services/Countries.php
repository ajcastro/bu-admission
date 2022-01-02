<?php

namespace App\Services;

class Countries
{
    static $countries;

    public static function countries()
    {
        return static::$countries ?? static::$countries = collect(config('countries'))->map(function ($item) {
            return (object) $item;
        });
    }

    public static function asSelectOptions()
    {
        return collect(static::countries())->pluck('name', 'name');
    }

    public static function getByCode($code)
    {
        return static::countries()->where('code', $code)->first();
    }

    public static function getByName($name)
    {
        return static::countries()->where('name', $name)->first();
    }

    public static function getCodeByName($name)
    {
        return static::getByName($name)->code ?? null;
    }
}
