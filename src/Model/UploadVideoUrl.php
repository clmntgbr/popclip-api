<?php

namespace App\Model;

use App\Entity\Configuration;

class UploadVideoUrl
{
    public function __construct(
        public ?string $url = null,
    ) {
        $this->url = $this->url;
    }
}
