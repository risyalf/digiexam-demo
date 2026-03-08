<?php

namespace App\Filament\Resources\UserTopics\Pages;

use App\Filament\Resources\UserTopics\UserTopicResource;
use App\Models\UserTopic;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Support\Str;

class ManageUserTopics extends ManageRecords
{
    protected static string $resource = UserTopicResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Buat Akses Guru')
                ->action(function($data) {
                    $userId = $data['user_id'];
                    $topicIds = $data['topic_id'];

                    $datas = [];

                    foreach ($topicIds as $key => $topicId) {
                        $datas[] = [
                            "id" => Str::uuid7(),
                            "user_id" => $userId,
                            "topic_id" => $topicId
                        ];
                    }

                    UserTopic::insert($datas);
                }),
        ];
    }
}
