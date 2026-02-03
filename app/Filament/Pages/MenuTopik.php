<?php

namespace App\Filament\Pages;

use App\Models\Module;
use App\Models\Topic;
use App\Traits\HasRefreshFunction;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

class MenuTopik extends Page implements HasTable, HasForms
{
    use HasRefreshFunction, InteractsWithTable, InteractsWithForms;

    protected string $view = 'filament.pages.menu-topik';

    public array $dataFilter = [
        'module' => null,
        'name' => null,
        'created' => null
    ];

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('FILTER')
                    ->components([
                        Select::make('dataFilter.module')
                            ->options(
                                Module::query()
                                    ->pluck('name', 'id')
                            ),
                        TextInput::make('dataFilter.name')
                            ->label('NAMA TOPIK'),
                        DatePicker::make('dataFilter.created')
                            ->label('DIBUAT PADA')
                    ])
                    ->footerActions([
                        Action::make('search')
                            ->label('SEARCH')
                            ->action(fn() => $this->dispatch('do-refresh'))
                    ])
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Topic::query()
                    ->with('module')
                    ->when($this->dataFilter['name'], fn($q) => $q->whereRaw("name like '%{$this->dataFilter['name']}%'"))
                    ->when($this->dataFilter['module'], function ($q) {
                        $q->whereHas('module', fn($q) => $q->where('id', $this->dataFilter['module']));
                    })
                    ->when($this->dataFilter['created'], fn($q) => $q->whereDate('created_at', $this->dataFilter['created']))
            )
            ->columns([
                TextColumn::make('no')
                    ->label('NO.')
                    ->rowIndex(),
                TextColumn::make('module.name')
                    ->label('NAMA MODUL')
                    ->alignCenter(),
                TextColumn::make('name')
                    ->label('NAMA')
                    ->alignCenter(),
                TextColumn::make('description')
                    ->label('DESKRIPSI')
                    ->alignCenter(),
                TextColumn::make('created_at')
                    ->label('DIBUAT PADA')
                    ->alignCenter(),
            ])
            ->headerActions([
                CreateAction::make('BUAT TOPIK')
                    ->icon(Heroicon::DocumentPlus)
                    ->color(Color::Green)
                    ->schema([
                        Select::make('module_id')
                            ->options(
                                Module::query()
                                    ->pluck('name', 'id')
                            )
                            ->required(),
                        TextInput::make('name')
                            ->label('NAMA TOPIK')
                            ->required(),
                        TextInput::make('description')
                            ->label('DESKRIPSI'),
                    ]),
            ])
            ->recordActions(
                ActionGroup::make([
                    EditAction::make('EDIT TOPIK')
                        ->icon(Heroicon::OutlinedPencil)
                        ->color(Color::Yellow)
                        ->schema([
                            Select::make('module_id')
                                ->options(
                                    Module::query()
                                        ->pluck('name', 'id')
                                )
                                ->required(),
                            TextInput::make('name')
                                ->label('NAMA TOPIK')
                                ->required(),
                            TextInput::make('description')
                                ->label('DESKRIPSI'),
                        ]),
                    DeleteAction::make('HAPUS TOPIK')
                        ->icon(Heroicon::OutlinedTrash)
                        ->color(Color::Red)
                        ->requiresConfirmation()
                ])
            );
    }
}
