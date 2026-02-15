<?php

namespace App\Filament\Imports;

use App\Models\Module;
use App\Models\Participant;
use App\Models\ParticipantGroup;
use App\Models\User;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Number;

class ParticipantImporter extends Importer
{
    protected static ?string $model = Participant::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('user')
                ->label('nis')
                ->exampleHeader('nis')
                ->relationship('user', 'nis')
                ->requiredMapping()
                ->rules(['required']),
            ImportColumn::make("order_number")
                ->label('nomor urut')
                ->exampleHeader('nomor urut')
                ->rules(["required"]),
            ImportColumn::make('participantGroup')
                ->label('kelas')
                ->exampleHeader('kelas')
                ->relationship('participantGroup', 'name')
                ->requiredMapping()
                ->rules(['required']),
            ImportColumn::make('module')
                ->label('modul')
                ->exampleHeader('modul')
                ->relationship('module', 'name')
                ->requiredMapping()
                ->rules(['required']),
        ];
    }

    public function resolveRecord(): Participant
    {
        return new Participant();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your participant import has completed and ' . Number::format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
