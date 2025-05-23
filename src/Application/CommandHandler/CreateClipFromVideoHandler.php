<?php

namespace App\Application\CommandHandler;

use App\Application\Command\CreateClipFromVideo;
use App\Application\Command\CreateVideo;
use App\Entity\Clip;
use App\Entity\Video;
use App\Message\TaskMessage;
use App\Repository\ClipRepository;
use App\Repository\UserRepository;
use App\Repository\VideoRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Workflow\WorkflowInterface;

#[AsMessageHandler]
final class CreateClipFromVideoHandler
{
    public function __construct(
        private UserRepository $userRepository,
        private ClipRepository $clipRepository,
        private VideoRepository $videoRepository,
        private MessageBusInterface $messageBus,
        private WorkflowInterface $clipStateMachine,
    ) {
    }

    public function __invoke(CreateClipFromVideo $message): void
    {
        $user = $this->userRepository->findOneBy(['id' => $message->userId->toString()]);

        if (null === $user) {
            throw new \Exception(sprintf('User does not exist with id [%s]', $message->userId->toString()));
        }

        $videoId = Uuid::v4();

        $this->messageBus->dispatch(new CreateVideo(
            videoId: $videoId,
            originalName: $message->originalName,
            name: $message->name,
            mimeType: $message->mimeType,
            size: $message->size,
        ));

        /** @var ?Video $video */
        $video = $this->videoRepository->findOneBy(['id' => $videoId->toString()]);

        if (null === $video) {
            throw new \Exception(sprintf('Video does not exist with id [%s]', $$videoId->toString()));
        }

        $clip = new Clip(
            user: $user,
            clipId: $message->clipId,
            originalVideo: $video,
            configuration: $message->uploadVideoConfiguration->toEntity(),
        );

        if (!$this->clipStateMachine->can($clip, 'process_sound_extractor')) {
            throw new \RuntimeException(message: 'Clip is not in a valid state to process sound');
        }

        $this->clipStateMachine->apply($clip, 'process_sound_extractor');
        $this->clipRepository->save($clip);

        $this->messageBus->dispatch(new TaskMessage(clipId: $clip->getId(), service: 'sound_extractor'));

        return;
    }
}
