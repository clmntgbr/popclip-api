<?php

namespace App\Application\Command;

use App\Model\UploadVideoConfiguration;
use Symfony\Component\Uid\Uuid;

final class CreateClipFromVideo
{
    public function __construct(
        public readonly Uuid $clipId,
        public readonly Uuid $userId,
        public readonly UploadVideoConfiguration $uploadVideoConfiguration,
        public readonly string $originalName,
        public readonly string $name,
        public readonly string $mimeType,
        public readonly string $size,
    ) {
    }
}
