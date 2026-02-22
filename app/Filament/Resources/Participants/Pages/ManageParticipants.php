<?php

namespace App\Filament\Resources\Participants\Pages;

use App\Filament\Resources\Participants\ParticipantResource;
use App\Models\Participant;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Database\QueryException;

class ManageParticipants extends ManageRecords
{
    protected static string $resource = ParticipantResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label("Tambah Peserta")
                ->before(function (CreateAction $action) {
                    $data = $action->getData();
                    $existsOrderNumber = Participant::query()
                        ->where(
                            "participant_group_id",
                            $data["participant_group_id"],
                        )
                        ->where("order_number", $data["order_number"])
                        ->where("module_id", $data["module_id"])
                        ->exists();
                    $existsData = Participant::query()
                        ->where("user_id", $data["user_id"])
                        ->where(
                            "participant_group_id",
                            $data["participant_group_id"],
                        )
                        ->where("module_id", $data["module_id"])
                        ->exists();
                    if ($existsOrderNumber) {
                        Notification::make()
                            ->warning()
                            ->title(
                                "Nomor Urut sudah dipakai,cek Kelas,No Urut dan Modul",
                            )
                            ->send();
                        $action->halt();
                    }
                    if ($existsData) {
                        Notification::make()
                            ->warning()
                            ->title("Data sudah ada")
                            ->send();
                        $action->halt();
                    }
                }),
        ];
    }
}
