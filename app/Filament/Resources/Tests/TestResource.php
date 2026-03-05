<?php

namespace App\Filament\Resources\Tests;

use App\Action\ExportTestFormDocx;
use App\Action\ImportTestFormDocx;
use App\Enum\AssessmentType;
use App\Enum\Menu;
use App\Filament\Resources\TestQuestions\TestQuestionResource;
use App\Filament\Resources\Tests\Pages\ManageTests;
use App\Models\Test;
use App\Models\TestQuestion;
use App\Models\TestQuestionOption;
use App\Models\Topic;
use BackedEnum;
use Exception;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use UnitEnum;

class TestResource extends Resource
{
    protected static ?string $model = Test::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Document;

    protected static string|UnitEnum|null $navigationGroup = Menu::DATA_MODUL->value;

    protected static ?string $navigationLabel = "Menu Soal";

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('topic_id')
                    ->label('NAMA TOPIK')
                    ->options(Topic::query()->pluck('name', 'id'))
                    ->searchable()
                    ->required(),
                TextInput::make('name')
                    ->label('NAMA')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('no')
                    ->label('NO.')
                    ->rowIndex(isFromZero:false),
                TextColumn::make('topic.name')
                    ->label('NAMA TOPIK')
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
                SelectFilter::make('topic_id')
                    ->options(
                        Topic::query()
                            ->pluck('name', 'id')
                    )
                    ->searchable()
                    ->label('Topic'),
                SelectFilter::make('name')
                    ->options(
                        Test::query()
                            ->pluck('name', 'id')
                    )
                    ->searchable()
                    ->label('Nama Soal'),
            ])
            ->headerActions([
                Action::make('import')
                    ->label('IMPORT SOAL')
                    ->icon(Heroicon::ArrowDown)
                    ->color('info')
                    ->schema([
                        Select::make('topic')
                            ->options(
                                Topic::query()
                                    ->pluck('name', 'id')
                            )
                            ->searchable()
                            ->required(),
                        FileUpload::make('attachment')
                            ->label('Pilih File Word (.docx)')
                            ->disk('public')
                            ->directory('temp-uploads')
                            // ->acceptedFileTypes([
                            //     'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                            // ])
                            ->storeFileNamesIn('original_name')
                            ->maxSize(5120)
                            ->required(),
                    ])
                    ->action(function (array $data) {
                        $folderPath = "questions/images/ID_TEST_BARU/";
                        try {
                            DB::beginTransaction();
                            $originalName = $data['original_name'] ?? 'Dokumen Tanpa Nama';
                            $originalName = pathinfo($originalName, PATHINFO_FILENAME);

                            $filePath = storage_path('app/public/' . $data['attachment']);

                            $exists = Test::query()
                                ->where('name', $originalName)
                                ->exists();

                            if ($exists) {
                                throw new Exception("SUDAH ADA SOAL DENGAN NAMA $originalName. TOLONG GANTI NAMA FILE!");
                            }

                            if (!file_exists($filePath)) {
                                throw new Exception("FILE HASIL UPLOAD TIDAK DITEMUKAN DI SERVER.");
                            }

                            $test = Test::create([
                                'topic_id' => $data['topic'],
                                'name' => $originalName
                            ]);

                            $folderPath = str_replace("ID_TEST_BARU", $test->id, $folderPath);

                            $questions = ImportTestFormDocx::execute($test->id, $filePath);

                            $questionOptions = collect($questions)->map(function ($data) use ($test) {
                                $question = TestQuestion::create([
                                    'test_id' => $test->id,
                                    'name' => $data['question'],
                                    'type' => $data['type'] == 'PILIHAN GANDA' ? AssessmentType::PILIHAN_GANDA : AssessmentType::ESAI,
                                    'ordering' => $data['number'],
                                    // 'options' => json_encode($data['answers']),
                                    // 'correct_answer' => collect($correctAnswer)->first()['id']
                                ]);

                                if (count($data) == 0) {
                                    return;
                                }

                                return collect($data['answers'])->map(fn($answer) => [
                                    'created_at' => now()->toDateTimeString(),
                                    'updated_at' => now()->toDateTimeString(),
                                    'created_by' => auth()->user()->id,
                                    'updated_by' => auth()->user()->id,
                                    'test_question_id' => $question->id,
                                    'content' => $answer['text'],
                                    'value' => $answer['value'] == "true" ? DB::raw('true') : DB::raw('false'),
                                ]);
                            });

                            $questionOptions = $questionOptions
                                ->flatten(1)
                                ->values()
                                ->toArray();

                            TestQuestionOption::insert($questionOptions);

                            unlink($filePath);

                            Notification::make()->success()->title("SUKSES IMPORT SOAL {$originalName}")->send();
                            DB::commit();
                        } catch (\Throwable $th) {
                            DB::rollBack();
                            if (!str_contains($folderPath, 'ID_TEST_BARU')) {
                                Storage::disk('public')->deleteDirectory($folderPath);
                            }
                            Notification::make()
                                ->danger()
                                ->title('IMPORT GAGAL')
                                ->body($th->getMessage())
                                ->send();
                        }
                    }),
                Action::make('export')
                    ->label('EXPORT FORMAT')
                    ->icon(Heroicon::ArrowUp)
                    ->color('info')
                    ->action(fn() => ExportTestFormDocx::execute('IMPORT-FORMAT-SOAL.docx')),
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('detail')
                    ->color('info')
                    ->icon(Heroicon::MagnifyingGlass)
                    ->url(fn(Test $record): string => TestQuestionResource::getUrl('index', [
                        'test_id' => $record->id,
                    ]), true)
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ])
            ->paginated();
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageTests::route('/'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }
}
