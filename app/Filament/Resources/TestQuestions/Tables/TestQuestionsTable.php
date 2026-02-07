<?php

namespace App\Filament\Resources\TestQuestions\Tables;

use App\Action\ExportTestFormDocx;
use App\Action\ImportTestFormDocx;
use App\Enum\AssessmentType;
use App\Models\Test;
use App\Models\TestQuestion;
use App\Models\Topic;
use Exception;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TestQuestionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('no')
                    ->label('NO.')
                    ->rowIndex(),
                TextColumn::make('topic.name')
                    ->label('NAMA TOPIK')
                    ->alignCenter(),
                TextColumn::make('name')
                    ->label('NAMA')
                    ->alignCenter(),
                TextColumn::make('options')
                    ->label('JAWABAN')
                    ->formatStateUsing(fn() => "LIHAT OPSI JAWABAN")
                    ->weight('bold')
                    ->action(
                        Action::make('view_options')
                            ->modalHeading('OPSI JAWABAN')
                            ->modalSubmitAction(false)
                            ->modal()
                            ->schema([
                                TextEntry::make('opsi')
                                    ->hiddenLabel()
                                    ->getStateUsing(function($record) {
                                        $options = json_decode($record->options);
                                        $text = "";
                                        foreach ($options as $index => $option) {
                                            $value = $option->value == 'true' ? 'BENAR' : 'SALAH';
                                            $text .= "JAWABAN : {$option->text} ($value) <br><br>";
                                        }
                                        return $text;
                                    })
                                    ->html()
                            ])
                    )
                    ->alignCenter(),
                TextColumn::make('created_at')
                    ->label('DIBUAT PADA')
                    ->alignCenter(),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
