<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static Major()
 * @method static static Thesis()
 * @method static static Foundation()
 * @method static static Cognate()
 */
final class SubjectCategory extends Enum
{
    const Major = 'Major';
    const Thesis = 'Thesis';
    const Foundation = 'Foundation';
    const Cognate = 'Cognate';
}
