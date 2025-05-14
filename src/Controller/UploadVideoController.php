<?php

namespace App\Controller;

use App\Model\UploadVideoConfiguration;
use App\Model\UploadVideoUrl;
use App\Service\UploadVideoService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpKernel\Attribute\MapUploadedFile;
use Symfony\Component\Routing\Attribute\Route;

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
