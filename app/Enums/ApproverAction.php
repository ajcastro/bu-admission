<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class ApproverAction extends Enum
{
    const RECOMMEND = 'Recommend';
    const ADMIT = 'Admit';
    const PROCESS = 'Process';
}
