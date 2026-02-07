<?php

namespace App\Filament\Resources\TestQuestions\Tables;

use App\Action\SaveImage;
use App\Models\TestQuestionOption;
use Exception;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TestQuestionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('no')
                    ->label('NO.')
                    ->rowIndex(),
                TextColumn::make('test.name')
                    ->label('NAMA SOAL')
                    ->alignCenter(),
                TextColumn::make('name')
                    ->label('NAMA')
                    ->html(),
                TextColumn::make('option_answers')
                    ->label('JAWABAN')
                    ->getStateUsing(fn($record) => true)
                    ->formatStateUsing(fn($state, $key) => "Lihat Jawaban")
                    ->badge()
                    ->iconColor('success')
                    ->weight('bold')
                    ->alignCenter()
                    ->action(
                        Action::make('view')
                            ->label("VIEW")
                            ->modal()
                            ->schema([
                                Repeater::make('options')
                                    ->schema([
                                        RichEditor::make('content')
                                            ->label('Opsi Jawaban'),
                                        Toggle::make('value')
                                            ->reactive()
                                            ->label(fn($state) => $state ? "Benar" : "Salah")
                                    ])
                                    ->orderColumn()
                            ])
                            ->mountUsing(
                                fn($form, $record) =>
                                $form->fill(['options' => $record->options])
                            )
                            ->action(function ($data) {
                                try {
                                    $correctAnswers = collect($data['options'])->filter(fn($value) => $value['value']);
                                    if (count($correctAnswers) == 0) {
                                        throw new Exception("TOLONG PILIH SATU JAWABAN BENAR!");
                                    }
                                    if (count($correctAnswers) > 1) {
                                        throw new Exception("JAWABAN BENAR TIDAK BOLEH LEBIH DARI SATU!");
                                    }
                                    DB::beginTransaction();
                                    foreach ($data['options'] as $key => $value) {
                                        $oldOption = TestQuestionOption::find($value['id']);

                                        preg_match_all('/https?:\/\/[^\s"\']+?\.(?:jpg|jpeg|png|webp)/i', $value['content'], $newDatas);
                                        preg_match_all('/https?:\/\/[^\s"\']+?\.(?:jpg|jpeg|png|webp)/i', $oldOption->content, $oldDatas);

                                        $newDatas = collect($newDatas)->flatten()->map(fn($value) => explode('/storage/', $value)[1]);
                                        $oldDatas = collect($oldDatas)->flatten()->map(fn($value) => explode('/storage/', $value)[1]);

                                        $notMatches = $oldDatas->diff($newDatas);
                                        $newImages = $newDatas->diff($oldDatas);

                                        foreach ($notMatches as $key => $notMatch) {
                                            Storage::disk('public')->delete($notMatch);
                                        }

                                        foreach ($newImages as $newData) {
                                            $binary = Storage::disk('public')->get($newData);

                                            $extension = pathinfo($newData, PATHINFO_EXTENSION);

                                            $newFilename = SaveImage::execute(
                                                $oldOption->testQuestion->test_id,
                                                uniqid(),
                                                $binary,
                                                $extension
                                            );

                                            $oldUrl = Storage::disk('public')->url($newData);
                                            $newUrl = Storage::disk('public')->url($newFilename);

                                            $value['content'] = str_replace($oldUrl, $newUrl, $value['content']);

                                            Storage::disk('public')->delete($newData);
                                        }

                                        $oldOption->update([
                                            'content' => $value['content'],
                                            'value' => $value['value'],
                                        ]);
                                    }
                                    DB::commit();

                                    Notification::make()->success()->title('SUKSES GANTI OPSI JAWABAN')->send();
                                } catch (\Throwable $th) {
                                    Notification::make()->danger()->title('ERROR')->body($th->getMessage())->send();
                                }
                            })
                    )
                    ->html(),
                TextColumn::make('created_at')
                    ->label('DIBUAT PADA')
                    ->alignCenter(),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make()
                    ->label('Ubah Pertanyaan'),
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
