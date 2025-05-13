<?php

namespace App\Application\Command;

use App\Model\UploadVideoConfiguration;
use App\Model\UploadVideoUrl;
use Symfony\Component\Uid\Uuid;

final class CreateClipUrl
{
    public function __construct(
        public readonly Uuid $clipId,
        public readonly Uuid $userId,
        public readonly UploadVideoUrl $uploadVideoUrl,
        public readonly UploadVideoConfiguration $uploadVideoConfiguration,
    ) {
    }
}
