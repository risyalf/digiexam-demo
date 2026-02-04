<?php

namespace App\Filament\Resources\ParticipantGroups\Schemas;

use App\Models\ParticipantGroup;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ParticipantGroupInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('id')
                    ->label('ID'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('deleted_at')
                    ->dateTime()
                    ->visible(fn (ParticipantGroup $record): bool => $record->trashed()),
                TextEntry::make('created_by'),
                TextEntry::make('updated_by'),
                TextEntry::make('deleted_by')
                    ->placeholder('-'),
                TextEntry::make('name'),
            ]);
    }
}
