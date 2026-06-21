<?php

namespace App\Filament\Pages;

use App\Action\RecalculateAssessmentPoint;
use App\Enum\Menu;
use App\Models\Answer;
use App\Models\ParticipantAssessment;
use App\Models\TestQuestion;
use BackedEnum;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Actions\Action;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Size;
use Filament\Support\Enums\TextSize;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Override;
use UnitEnum;

class EvaluateEssayAnswer extends Page implements HasTable, HasForms
{
    use InteractsWithForms, InteractsWithTable, HasPageShield;

    protected string $view = 'filament.pages.evaluate-essay-answer';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::CheckCircle;

    protected static string|UnitEnum|null $navigationGroup = Menu::DATA_TES->value;

    protected static ?string $navigationLabel = "Evaluasi Essay";

    protected static ?string $title = "Evaluasi Essay";

    protected static ?int $navigationSort = 3;

    protected Width|string|null $maxContentWidth = Width::Full;


    public function table(Table $table): Table
    {
        return $table
            ->deferLoading()
            ->query(
                ParticipantAssessment::query()
                    ->select([
                        'id',
                        'participant_id',
                        'assessment_id'
                    ])
                    ->whereHas(
                        'answer',
                        fn($q) =>
                        $q->whereNotNull('essay_values')
                    )
                    ->with([
                        'participant.user',
                        'participant.participantGroup',
                        'assessment',
                        'assessment.module',
                        'assessment.topic',
                        'answer'
                    ])
            )
            ->columns([
                TextColumn::make('participant.user.name')
                    ->label("NAMA SISWA")
                    ->wrap()
                    ->copyable(),
                TextColumn::make('participant.participantGroup.name')
                    ->label("KELAS")
                    ->wrap()
                    ->copyable(),
                TextColumn::make('assessment.module.name')
                    ->label("MODUL")
                    ->wrap()
                    ->copyable(),
                TextColumn::make('assessment.topic.name')
                    ->label("TOPIK")
                    ->wrap()
                    ->copyable(),
                TextColumn::make('assessment.name')
                    ->label("ASSESSMENT")
                    ->wrap()
                    ->copyable(),
                TextColumn::make('answer.essay_evaluated')
                    ->label("STATUS")
                    ->wrap()
                    ->copyable()
                    ->formatStateUsing(fn($state) => $state ? 'SELESAI EVALUASI' : 'BELUM SELESAI EVALUASI')
                    ->badge()
                    ->size(TextSize::Large)
                    ->color(
                        fn($state) =>
                        $state ? Color::Emerald : Color::Red
                    ),
            ])
            ->recordActions([
                Action::make('evaluate')
                    ->label("EVALUASI")
                    ->button()
                    ->color(Color::Emerald)
                    ->mountUsing(function ($form, ParticipantAssessment $record) {
                        $essayValues = collect(
                            json_decode($record->answer?->essay_values ?? '[]', true)
                        );

                        $questions = TestQuestion::query()
                            ->whereIn('id', $essayValues->pluck('test_question_id'))
                            ->pluck('name', 'id');

                        $maxPoint = $record->assessment->max_essay_point;

                        $form->fill([
                            'essay_values' => $essayValues
                                ->map(fn($data) => [
                                    'answer_id' => $record->answer->id,
                                    'max_point' => $maxPoint,
                                    'test_name' => $questions[$data['test_question_id']] ?? '-',
                                    ...$data,
                                ])
                                ->toArray(),
                        ]);
                    })
                    ->schema([
                        Repeater::make('essay_values')
                            ->schema([
                                Hidden::make('answer_id'),
                                Hidden::make('test_question_id'),
                                RichEditor::make('test_name')
                                    ->label('PERTANYAAN')
                                    ->disabled(),
                                Textarea::make('value')
                                    ->label('JAWABAN')
                                    ->disabled(),
                                TextInput::make('point')
                                    ->label("POINT")
                                    ->numeric()
                                    ->required()
                                    ->maxValue(fn($get) => $get('max_point'))
                                    ->default(0),
                                TextInput::make('max_point')
                                    ->label("NILAI MAKSIMAL")
                                    ->disabled(),
                            ])
                            ->addable(false)
                            ->deletable(false)
                            ->orderColumn(),
                    ])
                    ->action(function ($data) {
                        try {
                            $answerId = $data['essay_values'][0]['answer_id'];
                            $answer = Answer::find($answerId);
                            $essayValues = collect(json_decode($answer->essay_values));
                            foreach ($data['essay_values'] as $key => $data) {
                                $value = $essayValues->where('test_question_id', $data['test_question_id'])->first();
                                $value->point = $data['point'];
                                $value->evaluated = true;
                            }
                            $answer->essay_values = json_encode($essayValues);
                            $answer->essay_evaluated = true;
                            $answer->save();

                            RecalculateAssessmentPoint::execute($answer->participant_assessment_id);

                            Notification::make()
                                ->title("SUCCESS EVALUASI JAWABAN!")
                                ->success()
                                ->send();
                        } catch (\Throwable $th) {
                            Notification::make()
                                ->title("ERROR")
                                ->body($th->getMessage())
                                ->danger()
                                ->send();
                        }
                    })
                    ->modalSubmitActionLabel("Submit")
                    ->disabled(fn($record) => $record->answer->essay_evaluated),
                // ->requiresConfirmation(),
            ]);
    }
}
