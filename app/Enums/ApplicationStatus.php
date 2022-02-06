<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class ApplicationStatus extends Enum
{
    const PENDING = 'Pending';
    const RECOMMENDED = 'Recommended';
    const ADMITTED = 'Admitted';
    const PROCESSED = 'Processed';
    const REJECTED = 'Rejected';
}
