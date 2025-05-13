<?php

namespace App\Controller;

use App\Model\UploadVideoConfiguration;
use App\Model\UploadVideoUrl;
use App\Repository\UserRepository;
use App\Service\UploadVideoService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpKernel\Attribute\MapUploadedFile;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Http\Event\LogoutEvent;

#[Route('/api/upload', name: 'api_upload_')]
class UploadVideoController extends AbstractController
{
    public function __construct(
        private UploadVideoService $uploadVideoService,
    ) {
    }

    #[Route('/video', name: 'video', methods: ['POST'])]
    public function uploadVideo(
        #[MapUploadedFile] UploadedFile $video,
        #[MapRequestPayload] ?UploadVideoConfiguration $configuration = new UploadVideoConfiguration(),
    ): JsonResponse {
        return $this->uploadVideoService->upload($video, $configuration);
    }

    #[Route('/video/url', name: 'video_url', methods: ['POST'])]
    public function uploadVideoUrl(
        #[MapRequestPayload] ?UploadVideoUrl $uploadVideoUrl = new UploadVideoUrl(),
        #[MapRequestPayload] ?UploadVideoConfiguration $configuration = new UploadVideoConfiguration(),
    ): JsonResponse {
        return $this->uploadVideoService->uploadFromUrl($uploadVideoUrl, $configuration);
    }
}
