<?php

namespace App\Enum;

use Filament\Support\Contracts\HasLabel;

enum Menu:string implements HasLabel
{
    case ADMIN = 'ADMIN';
    case DATA_MODUL = 'DATA MODUL';
    case DATA_PESERTA = 'DATA PESERTA';
    case DATA_TES = 'DATA TES';

    public function getLabel(): ?string
    {
        return $this->name;
    }
}
