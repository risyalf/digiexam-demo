<?php

namespace App\Enum;

enum AssessmentStatus: string
{
    case BELUM_DIMULAI = 'Belum Dimulai';
    case SEDANG_BERLANGSUNG = 'Sedang Berlangsung';
    case BERHENTI = 'Berhenti';
    case SELESAI = 'Selesai';
}
