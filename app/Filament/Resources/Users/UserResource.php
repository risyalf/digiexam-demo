<?php

namespace App\Filament\Resources\Users;

use App\Enum\Menu;
use App\Filament\Imports\UserImporter;
use App\Filament\Resources\Users\Pages\ManageUsers;
use App\Models\User;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\ImportAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|UnitEnum|null $navigationGroup = Menu::ADMIN;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::User;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make("name")->required(),
            TextInput::make("email")
                ->label("Email address")
                ->email()
                ->required(),
            TextInput::make("nis")->label("NIS"),
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

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            TextEntry::make("id")->label("ID"),
            TextEntry::make("created_at")->dateTime()->placeholder("-"),
            TextEntry::make("updated_at")->dateTime()->placeholder("-"),
            TextEntry::make("deleted_at")
                ->dateTime()
                ->visible(fn(User $record): bool => $record->trashed()),
            TextEntry::make("name"),
            TextEntry::make("email")->label("Email address"),
            TextEntry::make("nis")->label("NIS"),
            TextEntry::make("email_verified_at")->dateTime()->placeholder("-"),
            IconEntry::make("is_locked")->boolean(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make("id")->label("ID")->searchable()->hidden(),
                TextColumn::make("created_at")
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make("updated_at")
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make("deleted_at")
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make("name")->searchable(),
                TextColumn::make("email")->label("Email address")->searchable(),
                TextColumn::make("nis")->label("NIS")->searchable(),
            ])
            ->filters([TrashedFilter::make()])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
                ForceDeleteAction::make(),
                RestoreAction::make(),
            ])
            ->headerActions([
                ImportAction::make()
                    ->icon(Heroicon::Plus)
                    ->color(Color::Emerald)
                    ->importer(UserImporter::class),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            "index" => ManageUsers::route("/"),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()->withoutGlobalScopes(
            [SoftDeletingScope::class],
        );
    }
}
