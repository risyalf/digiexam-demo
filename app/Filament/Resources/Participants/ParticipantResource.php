<?php

namespace App\Filament\Resources\Participants;

use App\Action\GenerateParticipantTestNumberAndPassword;
use App\Enum\Menu;
use App\Filament\Imports\ParticipantImporter;
use App\Filament\Resources\Participants\Pages\ManageParticipants;
use App\Models\Participant;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ImportAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class ParticipantResource extends Resource
{
    protected static ?string $model = Participant::class;

    protected static string|UnitEnum|null $navigationGroup = Menu::DATA_PESERTA->value;

    protected static ?string $pluralLabel = "Peserta";

    protected static ?string $navigationLabel = "Peserta";

    protected static string|BackedEnum|null $navigationIcon = Heroicon::User;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make("user_id")
                ->relationship(
                    "user",
                    "name",
                    fn($query) => $query->role("siswa"),
                )
                ->searchable()
                ->preload()
                ->required(),
            Select::make("participant_group_id")
                ->relationship("participantGroup", "name")
                ->preload()
                ->searchable()
                ->required(),
        ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            TextEntry::make("id")->label("ID"),
            TextEntry::make("created_at")->dateTime(),
            TextEntry::make("updated_at")->dateTime(),
            TextEntry::make("user.name")->label("User"),
            TextEntry::make("participantGroup.name")->label(
                "Participant Group",
            ),
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
                TextColumn::make("user.name")
                    ->searchable()
                    ->label("Nama Peserta"),
                TextColumn::make("participantGroup.name")
                    ->searchable()
                    ->label("Kelas Peserta"),
                TextColumn::make("module.name")
                    ->searchable()
                    ->label("Modul"),
                TextColumn::make("test_number")
                    ->label('Nomor Test')
                    ->searchable(),
                TextColumn::make("test_password")
                    ->label('Password Test')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Action::make('generate')
                    ->label('Generate Nomor Test Dan Password')
                    ->color(Color::Cyan)
                    ->icon(Heroicon::CloudArrowUp)
                    ->action(function() {
                        $participantIds = Participant::query()
                                            ->whereNull('test_number')
                                            ->whereNull('test_password')
                                            ->pluck('id')
                                            ->toArray();

                        GenerateParticipantTestNumberAndPassword::execute($participantIds);
                    }),
                ImportAction::make()
                    ->icon(Heroicon::Plus)
                    ->color(Color::Emerald)
                    ->importer(ParticipantImporter::class),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make()->modalHeading("Ubah Peserta"),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            "index" => ManageParticipants::route("/"),
        ];
    }
}
