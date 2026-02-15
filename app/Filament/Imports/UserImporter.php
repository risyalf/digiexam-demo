<?php

namespace App\Filament\Imports;

use App\Action\GenerateRandomString;
use App\Action\GenerateTestNumber;
use App\Models\Module;
use App\Models\Participant;
use App\Models\ParticipantGroup;
use App\Models\Role;
use App\Models\User;
use Exception;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Filament\Forms\Components\Select;
use Illuminate\Support\Number;

class UserImporter extends Importer
{
    protected static ?string $model = User::class;

    public static function getOptionsFormComponents(): array
    {
        return [
            Select::make("role")
                ->label("ROLE")
                ->options(Role::query()->pluck("name", "name")->toArray())
                ->required(),
        ];
    }

    public static function getColumns(): array
    {
        return [
            ImportColumn::make("name")
                ->requiredMapping()
                ->rules(["required"]),
            ImportColumn::make("order_number")
                ->exampleHeader('nomor urut')
                ->rules(["required"]),
            ImportColumn::make("email")
                ->requiredMapping()
                ->rules(["email"]),
            ImportColumn::make("nis")
                ->rules(["required"]),
            ImportColumn::make("password")
                ->requiredMapping(),
            ImportColumn::make("group"),
            ImportColumn::make("module"),
        ];
    }

    public function resolveRecord(): User
    {
        return new User();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body =
            "Your user import has completed and " .
            Number::format($import->successful_rows) .
            " " .
            str("row")->plural($import->successful_rows) .
            " imported.";

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .=
                " " .
                Number::format($failedRowsCount) .
                " " .
                str("row")->plural($failedRowsCount) .
                " failed to import.";
        }

        return $body;
    }

    public function fillRecord(): void
    {
        foreach ($this->getCachedColumns() as $column) {
            $name = $column->getName();

            if ($name === 'group' || $name === 'module' || $name === 'order_number') {
                continue;
            }

            if (! array_key_exists($name, $this->data)) {
                continue;
            }

            $state = $this->data[$name];

            $column->fillRecord($state);
        }
    }

    public function saveRecord(): void
    {
        $this->record->save();

        if (!empty($this->data['group']) && !empty($this->data['module'])) {
            $module = Module::query()
                        ->where('name', $this->data['module'])
                        ->first();

            $group = ParticipantGroup::firstOrCreate([
                'name' => $this->data['group'],
            ]);

            $participant = Participant::create([
                'module_id' => $module->id,
                'user_id' => $this->record->id,
                'participant_group_id' => $group->id,
                'order_number' => $this->data['order_number'],
            ]);
        }

        if ($role = $this->options['role'] ?? null) {
            $this->record->assignRole($role);
        }
    }
}
