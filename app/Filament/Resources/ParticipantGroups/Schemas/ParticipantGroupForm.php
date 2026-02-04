<?php

namespace App\Filament\Resources\ParticipantGroups\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ParticipantGroupForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([TextInput::make("name")->required()]);
    }
}
