<?php

namespace App\Filament\Resources\ParticipantGroups;

use App\Enum\Menu;
use App\Filament\Resources\ParticipantGroups\Pages\ManageParticipantGroups;
use App\Models\ParticipantGroup;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class ParticipantGroupResource extends Resource
{
    protected static ?string $model = ParticipantGroup::class;

    protected static string|UnitEnum|null $navigationGroup = Menu::DATA_PESERTA->value;

    protected static ?string $pluralLabel = "Grup Peserta";

    protected static ?string $navigationLabel = "Grup Peserta";

    protected static string|BackedEnum|null $navigationIcon = Heroicon::UserGroup;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make("name")->required()]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            TextEntry::make("id")->label("ID"),
            TextEntry::make("created_at")->dateTime(),
            TextEntry::make("updated_at")->dateTime(),
            TextEntry::make("deleted_at")
                ->dateTime()
                ->visible(
                    fn(ParticipantGroup $record): bool => $record->trashed(),
                ),
            TextEntry::make("createdBy.name"),
            TextEntry::make("updatedBy.name"),
            TextEntry::make("deletedBy.name"),
            TextEntry::make("name"),
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
                TextColumn::make("createdBy.name")
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make("updatedBy.name")
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make("deletedBy.name")
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make("name")->searchable(),
            ])
            ->filters([TrashedFilter::make()])
            ->recordActions([
                ViewAction::make(),
                EditAction::make()->modalHeading("Ubah Grup Peserta"),
                DeleteAction::make(),
                ForceDeleteAction::make(),
                RestoreAction::make(),
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
            "index" => ManageParticipantGroups::route("/"),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()->withoutGlobalScopes(
            [SoftDeletingScope::class],
        );
    }
}
