<?php

namespace App\Interfaces;

interface Validateable
{
    public static function getValidationRules($id = null);
}
