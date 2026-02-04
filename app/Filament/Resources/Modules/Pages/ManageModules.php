<?php

namespace App\Filament\Resources\Modules\Pages;

use App\Filament\Resources\Modules\ModuleResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageModules extends ManageRecords
{
    protected static string $resource = ModuleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
