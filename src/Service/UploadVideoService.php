<?php

namespace App\Service;

use App\Application\Command\CreateClipFromUrl;
use App\Application\Command\CreateClipFromVideo;
use App\Entity\User;
use App\Model\UploadVideoConfiguration;
use App\Model\UploadVideoUrl;
use League\Flysystem\FilesystemOperator;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UploadVideoService
{
    public function __construct(
        private ValidatorInterface $validator,
        private FilesystemOperator $awsStorage,
        private Security $security,
        private MessageBusInterface $messageBus,
    ) {
    }

    public function upload(UploadedFile $file, UploadVideoConfiguration $configuration): JsonResponse
    {
        $constraints = new File([
            'mimeTypes' => [
                'video/mp4',
            ],
            'mimeTypesMessage' => 'Please upload a valid video file (MP4).',
        ]);

        $violations = $this->validator->validate($file, $constraints);

        if (count($violations) > 0) {
            return new JsonResponse([
                'message' => $violations[0]->getMessage(),
            ], Response::HTTP_BAD_REQUEST);
        }

        $user = $this->security->getUser();

        if (!$user instanceof User) {
            return new JsonResponse([
                'message' => 'You must be logged in to upload a video.',
            ], Response::HTTP_UNAUTHORIZED);
        }

        try {
            $clipId = Uuid::v4();
            $fileName = sprintf('%s.%s', $clipId->toString(), $file->guessExtension());
            $path = sprintf('%s/%s/%s', $user->getId(), $clipId->toString(), $fileName);

            $stream = fopen($file->getPathname(), 'r');

            if (false === $stream) {
                return new JsonResponse([
                    'message' => 'An error occurred during the upload.',
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            $this->awsStorage->writeStream($path, $stream, [
                'visibility' => 'public',
                'mimetype' => $file->getMimeType(),
            ]);

            if (is_resource($stream)) {
                fclose($stream);
            }

            $this->messageBus->dispatch(new CreateClipFromVideo(
                clipId: $clipId,
                userId: $user->getId(),
                uploadVideoConfiguration: $configuration,
                originalName: $file->getClientOriginalName(),
                name: $fileName,
                mimeType: $file->getMimeType(),
                size: $file->getSize(),
            ));

            return new JsonResponse([
                'message' => 'Video uploaded successfully.',
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse([
                'message' => 'An error occurred during the upload: '.$e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function uploadFromUrl(UploadVideoUrl $uploadVideoUrl, UploadVideoConfiguration $configuration): JsonResponse
    {
        $user = $this->security->getUser();

        if (!$user instanceof User) {
            return new JsonResponse([
                'message' => 'You must be logged in to upload a video.',
            ], Response::HTTP_UNAUTHORIZED);
        }

        try {
            $this->messageBus->dispatch(new CreateClipFromUrl(
                clipId: Uuid::v4(),
                userId: $user->getId(),
                uploadVideoConfiguration: $configuration,
                uploadVideoUrl: $uploadVideoUrl,
            ));

            return new JsonResponse([
                'message' => 'Video uploaded successfully.',
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse([
                'message' => 'An error occurred during the upload: '.$e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
