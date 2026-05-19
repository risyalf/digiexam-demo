<?php

namespace App\Filament\Resources\UserTopics\Pages;

use App\Filament\Resources\UserTopics\UserTopicResource;
use App\Models\UserTopic;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use Filament\Support\Enums\Width;
use Illuminate\Support\Str;

class ManageUserTopics extends ManageRecords
{
    protected static string $resource = UserTopicResource::class;

    protected Width|string|null $maxContentWidth = Width::Full;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Buat Akses Guru')
                ->action(function($data) {
                    $userId = $data['user_id'];
                    $topicIds = $data['topic_id'];

                    $datas = [];

                    foreach ($topicIds as $topicId) {
                        $exists = UserTopic::query()
                                    ->where([
                                        "user_id" => $userId,
                                        "topic_id" => $topicId
                                    ])
                                    ->exists();

                        if ($exists) {
                            continue;
                        }
                        $datas[] = [
                            "id" => Str::uuid7(),
                            "user_id" => $userId,
                            "topic_id" => $topicId
                        ];
                    }

                    if (count($datas) > 0) {
                        UserTopic::insert($datas);
                    }
                }),
        ];
    }
}
