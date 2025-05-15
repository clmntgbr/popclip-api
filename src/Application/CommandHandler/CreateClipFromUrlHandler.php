<?php

namespace App\Application\CommandHandler;

use App\Application\Command\CreateClipFromUrl;
use App\Entity\Clip;
use App\Message\TaskMessage;
use App\Repository\ClipRepository;
use App\Repository\UserRepository;
use App\Repository\VideoRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Workflow\WorkflowInterface;

#[AsMessageHandler]
final class CreateClipFromUrlHandler
{
    public function __construct(
        private UserRepository $userRepository,
        private ClipRepository $clipRepository,
        private VideoRepository $videoRepository,
        private MessageBusInterface $messageBus,
        private WorkflowInterface $clipStateMachine,
    ) {
    }

    public function __invoke(CreateClipFromUrl $message): void
    {
        $user = $this->userRepository->findOneBy(['id' => $message->userId->toString()]);

        if (null === $user) {
            throw new \Exception(sprintf('User does not exist with id [%s]', $message->userId->toString()));
        }

        $clip = new Clip(
            user: $user,
            url: $message->uploadVideoUrl->url,
            clipId: Uuid::v4(),
            configuration: $message->uploadVideoConfiguration->toEntity(),
        );

        if (!$this->clipStateMachine->can($clip, 'process_video_downloader')) {
            throw new \RuntimeException(message: 'Clip is not in a valid state to video downloader');
        }

        $this->clipStateMachine->apply($clip, 'process_video_downloader');
        $this->clipRepository->save($clip);

        $this->messageBus->dispatch(new TaskMessage(clipId: $clip->getId(), service: 'video_downloader'));

        return;
    }
}
