<?php

namespace App\Filament\Resources\ParticipantGroups;

use App\Filament\Resources\ParticipantGroups\Pages\CreateParticipantGroup;
use App\Filament\Resources\ParticipantGroups\Pages\EditParticipantGroup;
use App\Filament\Resources\ParticipantGroups\Pages\ListParticipantGroups;
use App\Filament\Resources\ParticipantGroups\Pages\ViewParticipantGroup;
use App\Filament\Resources\ParticipantGroups\Schemas\ParticipantGroupForm;
use App\Filament\Resources\ParticipantGroups\Schemas\ParticipantGroupInfolist;
use App\Filament\Resources\ParticipantGroups\Tables\ParticipantGroupsTable;
use App\Models\ParticipantGroup;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class ParticipantGroupResource extends Resource
{
    protected static ?string $model = ParticipantGroup::class;

    protected static string|UnitEnum|null $navigationGroup = "DATA PESERTA";

    protected static ?string $pluralLabel = "Grup Peserta";

    protected static ?string $navigationLabel = "Grup Peserta";

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return ParticipantGroupForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ParticipantGroupInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ParticipantGroupsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
                //
            ];
    }

    public static function getPages(): array
    {
        return [
            "index" => ListParticipantGroups::route("/"),
            // "create" => CreateParticipantGroup::route("/create"),
            // "view" => ViewParticipantGroup::route("/{record}"),
            // "edit" => EditParticipantGroup::route("/{record}/edit"),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()->withoutGlobalScopes(
            [SoftDeletingScope::class],
        );
    }
}
