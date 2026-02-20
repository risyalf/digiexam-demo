<?php

namespace App\Enum;

use App\Traits\EnumOptions;

enum ParticipantStatus: string
{
    use EnumOptions;

    case IDLE = "IDLE";
    case LOGGED_IN = "LOGGED_IN";
    case IN_PROGRESS = "IN_PROGRESS";
    case FINISH = "FINISH";
    case SUBMITTED = "SUBMITTED";
    case LOCKED = "LOCKED";
}
