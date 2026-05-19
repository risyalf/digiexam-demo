<?php

namespace App\Filament\Resources\Tests\Pages;

use App\Filament\Resources\Tests\TestResource;
use Filament\Resources\Pages\ManageRecords;
use Filament\Support\Enums\Width;
use Override;

class ManageTests extends ManageRecords
{
    protected static string $resource = TestResource::class;

    #[Override]
    public function getMaxContentWidth(): Width|string|null
    {
        return true;
    }

    protected function getHeaderActions(): array
    {
        return [
            // CreateAction::make(),
        ];
    }
}
