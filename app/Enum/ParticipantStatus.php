<?php

namespace App\Enum;

use App\Traits\EnumOptions;

enum ParticipantStatus: string
{
    use EnumOptions;

    case IDLE = 'IDLE';
    case LOGGED_IN = 'LOGGED_IN';
    case IN_PROGGRESS = 'IN_PROGGRESS';
    case SUBMITTED = 'SUBMITTED';
    case LOCKED = 'LOCKED';
}
