<?php

namespace App\Filament\Resources\TestQuestions;

use App\Enum\Menu;
use App\Filament\Resources\TestQuestions\Pages\CreateTestQuestion;
use App\Filament\Resources\TestQuestions\Pages\EditTestQuestion;
use App\Filament\Resources\TestQuestions\Pages\ListTestQuestions;
use App\Filament\Resources\TestQuestions\Schemas\TestQuestionForm;
use App\Filament\Resources\TestQuestions\Tables\TestQuestionsTable;
use App\Models\TestQuestion;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class TestQuestionResource extends Resource
{
    protected static ?string $model = TestQuestion::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = Menu::DATA_MODUL->value;

    protected static ?string $navigationLabel = "Menu Soal";

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Schema $schema): Schema
    {
        return TestQuestionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TestQuestionsTable::configure($table)
            ->filters([
                SelectFilter::make('test_id')
                    ->label('Test')
                    ->relationship('test', 'name')
                    ->default(fn() => request()->query('test_id')),
            ]);
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
            'index' => ListTestQuestions::route('/'),
            // 'create' => CreateTestQuestion::route('/create'),
            // 'edit' => EditTestQuestion::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
