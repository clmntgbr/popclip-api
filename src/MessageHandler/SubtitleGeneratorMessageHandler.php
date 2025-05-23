<?php

namespace App\MessageHandler;

use App\Message\TaskMessage;
use App\Protobuf\ClipStatus;
use App\Protobuf\SubtitleGeneratorMessage;
use App\Repository\ClipRepository;
use App\Service\ProtobufService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Workflow\WorkflowInterface;

#[AsMessageHandler]
final class SubtitleGeneratorMessageHandler
{
    public function __construct(
        private readonly ProtobufService $protobufService,
        private WorkflowInterface $clipStateMachine,
        private ClipRepository $clipRepository,
        private MessageBusInterface $messageBus,
    ) {
    }

    public function __invoke(SubtitleGeneratorMessage $message): void
    {
        try {
            $clip = $this->protobufService->getClip($message->getClip());

            $canTransition = false;
            foreach ($this->clipStateMachine->getEnabledTransitions($clip) as $transition) {
                if (in_array($message->getClip()->getStatus(), $transition->getTos())) {
                    $canTransition = true;
                    break;
                }
            }

            if (!$canTransition) {
                $clip->setStatus(ClipStatus::name(ClipStatus::STATUS_ERROR));
                $this->clipRepository->save($clip);

                return;
            }

            foreach ($this->clipStateMachine->getEnabledTransitions($clip) as $transition) {
                if (in_array($message->getClip()->getStatus(), $transition->getTos())) {
                    $this->clipStateMachine->apply($clip, $transition->getName());
                    $this->clipRepository->save($clip);
                    break;
                }
            }

            if (!$this->clipStateMachine->can($clip, 'process_subtitle_merger')) {
                return;
            }

            $this->clipStateMachine->apply($clip, 'process_subtitle_merger');
            $this->clipRepository->save($clip);

            $this->messageBus->dispatch(new TaskMessage(clipId: $clip->getId(), service: 'subtitle_merger'));

            return;
        } catch (\Exception $e) {
            $this->clipStateMachine->apply($clip, 'subtitle_generator_failure');
            $this->clipRepository->save($clip);
        }
    }
}
