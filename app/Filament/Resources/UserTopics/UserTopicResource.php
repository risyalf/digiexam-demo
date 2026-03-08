<?php

namespace App\Filament\Resources\UserTopics;

use App\Enum\Menu;
use App\Filament\Resources\UserTopics\Pages\ManageUserTopics;
use App\Models\Topic;
use App\Models\User;
use App\Models\UserTopic;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

class UserTopicResource extends Resource
{
    protected static ?string $model = UserTopic::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::UserPlus;

    protected static string|UnitEnum|null $navigationGroup = Menu::DATA_GURU->value;

    protected static ?string $pluralLabel = "Data Guru";

    protected static ?string $navigationLabel = "Data Guru";

    protected static ?string $recordTitleAttribute = 'Data Guru';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->label('NAMA')
                    ->searchable()
                    ->options(
                        User::role('guru')
                            ->pluck('name', 'id')
                    ),
                Select::make('topic_id')
                    ->label('TOPIK')
                    ->searchable()
                    ->multiple()
                    ->options(
                        Topic::query()
                            ->pluck('name', 'id')
                    ),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('NAMA'),
                TextColumn::make('topic.name')
                    ->label('TOPIC'),
            ])
            ->filters([
                SelectFilter::make('user_id')
                    ->label('NAMA')
                    ->options(
                        User::query()
                            ->pluck('name', 'id')
                    ),
                SelectFilter::make('topic_id')
                    ->label('TOPIC')
                    ->options(
                        Topic::query()
                            ->pluck('name', 'id')
                    ),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageUserTopics::route('/'),
        ];
    }
}
