<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make("name")->required(),
            TextInput::make("email")
                ->label("Email address")
                ->email()
                ->required(),
            TextInput::make("password")
                ->label("Password")
                ->placeholder("password")
                ->required(fn(string $operation) => $operation === "create")
                ->dehydrated(fn($state) => filled($state))
                ->afterStateHydrated(
                    fn($component, $record) => $component->state(""),
                )
                ->password(),
            CheckboxList::make("roles")
                ->relationship("roles", "name")
                ->searchable(),
        ]);
    }
}
