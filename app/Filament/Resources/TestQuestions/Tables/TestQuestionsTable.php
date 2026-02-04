<?php

namespace App\Filament\Resources\TestQuestions\Tables;

use App\Action\ExportTestFormDocx;
use App\Action\ImportTestFormDocx;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\FileUpload;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

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
                    ->label('NAMA MODUL')
                    ->alignCenter(),
                TextColumn::make('name')
                    ->label('NAMA')
                    ->alignCenter(),
                TextColumn::make('created_at')
                    ->label('DIBUAT PADA')
                    ->alignCenter(),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->headerActions([
                Action::make('import')
                    ->label('IMPORT SOAL')
                    ->icon(Heroicon::ArrowDown)
                    ->color(Color::Green)
                    ->schema([
                        FileUpload::make('attachment')
                            ->label('Pilih File Word (.docx)')
                            ->disk('public')
                            ->directory('temp-uploads')
                            // ->acceptedFileTypes([
                            //     'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                            //     'application/msword',
                            //     'application/octet-stream',
                            // ])
                            ->required(),
                    ])
                    ->action(function(array $data) {
                        $filePath = storage_path('app/public/' . $data['attachment']);
                        ImportTestFormDocx::execute($filePath);
                    }),
                Action::make('export')
                    ->label('EXPORT FORMAT')
                    ->icon(Heroicon::ArrowUp)
                    ->color(Color::Yellow)
                    ->action(fn () => ExportTestFormDocx::execute('IMPORT-FORMAT-SOAL.docx')),
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
