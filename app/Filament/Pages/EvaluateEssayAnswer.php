<?php

namespace App\Filament\Pages;

use App\Action\RecalculateAssessmentPoint;
use App\Enum\Menu;
use App\Models\Answer;
use App\Models\Assessment;
use App\Models\Module;
use App\Models\ParticipantAssessment;
use App\Models\ParticipantGroup;
use App\Models\TestQuestion;
use App\Models\Topic;
use App\Traits\HasRefreshFunction;
use BackedEnum;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Actions\Action;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Alignment;
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
    use InteractsWithForms, InteractsWithTable, HasPageShield, HasRefreshFunction;

    protected string $view = 'filament.pages.evaluate-essay-answer';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::CheckCircle;

    protected static string|UnitEnum|null $navigationGroup = Menu::DATA_TES->value;

    protected static ?string $navigationLabel = "Evaluasi Essay";

    protected static ?string $title = "Evaluasi Essay";

    protected static ?int $navigationSort = 3;

    protected Width|string|null $maxContentWidth = Width::Full;

    public array $filterFormData = [
        'module_id' => '',
        'topic_id' => '',
        'group_id' => '',
        'assessment_id' => '',
        'check_status'
    ];

    protected function getForms(): array
    {
        return [
            'filterForm'
        ];
    }

    public function filterForm(Schema $schema): Schema
    {
        return $schema
            ->statePath('filterFormData')
            ->components([
                Section::make('')
                    ->label('Filter')
                    ->collapsible()
                    ->collapsed()
                    ->components([
                        Select::make('module_id')
                            ->label('Modul')
                            ->searchable()
                            ->options(
                                fn($get) =>
                                Module::query()
                                    ->pluck('name', 'id')
                            ),
                        Select::make('topic_id')
                            ->label('Topik')
                            ->searchable()
                            ->options(
                                fn($get) =>
                                Topic::query()
                                    ->when($get('module_id'), fn($q, $v) => $q->where('module_id', $v))
                                    ->pluck('name', 'id')
                            ),
                        Select::make('group_id')
                            ->label('Kelas')
                            ->searchable()
                            ->options(
                                ParticipantGroup::query()
                                    ->pluck('name', 'id')
                            ),
                        Select::make('assessment_id')
                            ->label('Assessment')
                            ->searchable()
                            ->options(
                                fn($get) =>
                                Assessment::query()
                                    ->when($get('module_id'), fn($q, $v) => $q->where('module_id', $v))
                                    ->when($get('topic_id'), fn($q, $v) => $q->where('topic_id', $v))
                                    ->when(
                                        $get('group_id'),
                                        fn($q, $v) =>
                                        $q->whereHas('participant_groups', fn($q) => $q->where('participant_group_id', $v))
                                    )
                                    ->pluck('name', 'id')
                            ),
                        Select::make('check_status')
                            ->label('STATUS CEK')
                            ->options([
                                true => "SUDAH",
                                false => "BELUM",
                            ])
                    ])
                    ->footerActionsAlignment(Alignment::Right)
                    ->footerActions([
                        Action::make('filter')
                            ->icon(Heroicon::MagnifyingGlass)
                            ->color(Color::Emerald)
                            ->label('Filter')
                            ->action(fn() => $this->dispatch('do-refresh'))
                    ]),
            ]);
    }

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
                            ->when(isset($this->filterFormData['check_status']), fn($q, $v) => $q->where('essay_evaluated', $this->filterFormData['check_status']))
                    )
                    ->with([
                        'participant.user',
                        'participant.participantGroup',
                        'assessment',
                        'assessment.module',
                        'assessment.topic',
                        'answer'
                    ])
                    ->when($this->filterFormData['module_id'], fn($q, $v) => $q->whereHas('assessment', fn($q) => $q->where('module_id', $v)))
                    ->when($this->filterFormData['topic_id'], fn($q, $v) => $q->whereHas('assessment', fn($q) => $q->where('topic_id', $v)))
                    ->when(
                        $this->filterFormData['group_id'],
                        fn($q, $v) =>
                        $q->whereHas('assessment.participant_groups', fn($q) => $q->where('participant_group_id', $v))
                    )
                    ->when($this->filterFormData['assessment_id'], fn($q, $v) => $q->where('assessment_id', $v))
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
