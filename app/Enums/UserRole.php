<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static Applicant()
 * @method static static Admin()
 * @method static static ProgramAdviser()
 * @method static static Dean()
 * @method static static Registrar()
 */
final class UserRole extends Enum
{
    const Applicant = 'Applicant';
    const Admin = 'Admin';
    const ProgramAdviser = 'Program Adviser';
    const Dean = 'Dean';
    const Registrar = 'Registrar';
}
