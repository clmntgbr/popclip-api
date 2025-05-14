<?php

namespace App\Application\CommandHandler;

use App\Application\Command\CreateVideo;
use App\Entity\Video;
use App\Repository\VideoRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class CreateVideoHandler
{
    public function __construct(
        private VideoRepository $videoRepository,
    ) {
    }

    public function __invoke(CreateVideo $message): void
    {
        $video = new Video(
            videoId: $message->videoId,
            originalName: $message->originalName,
            name: $message->name,
            mimeType: $message->mimeType,
            size: $message->size,
        );

        $this->videoRepository->save($video);

        return;
    }
}
