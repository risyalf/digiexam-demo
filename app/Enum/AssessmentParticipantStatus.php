<?php

namespace App\Enum;

use App\Traits\EnumOptions;

enum AssessmentParticipantStatus: string
{
    use EnumOptions;

    case LOGGED_IN = 'Log In';
    case ACTIVE = 'Aktif';
    case PAUSED = 'Hentikan Sementara';
    case LOCKED = 'Terkunci';
    case FINISH = 'Selesai';
}
