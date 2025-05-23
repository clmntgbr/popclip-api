<?php

namespace App\Service;

use App\Entity\Clip;
use App\Entity\Configuration;
use App\Entity\Video;
use App\Protobuf\Clip as ProtobufClip;
use App\Protobuf\Configuration as ProtobufConfiguration;
use App\Protobuf\Video as ProtobufVideo;
use App\Repository\ClipRepository;
use Symfony\Component\Uid\Uuid;

class ProtobufService
{
    public function __construct(
        private ClipRepository $clipRepository,
    ) {
    }

    public function getProtobufClip(Clip $clip): ProtobufClip
    {
        $protobufClip = $this->transformClipToProtobuf($clip);

        $protobufConfiguration = $this->transformConfigurationToProtobuf($clip->getConfiguration());
        $protobufClip->setConfiguration($protobufConfiguration);

        if ($clip->getOriginalVideo()) {
            $protobufOriginalVideo = $this->transformVideoToProtobuf($clip->getOriginalVideo());
            $protobufClip->setOriginalVideo($protobufOriginalVideo);
        }

        if ($clip->getProcessedVideo()) {
            $protobufProcessedVideo = $this->transformVideoToProtobuf($clip->getProcessedVideo());
            $protobufClip->setProcessedVideo($protobufProcessedVideo);
        }

        return $protobufClip;
    }

    public function getClip(ProtobufClip $protobuf): Clip
    {
        $clip = $this->transformProtobufToClip($protobuf);

        $originalVideo = $this->transformProtobufToVideo($clip->getOriginalVideo(), $protobuf->getOriginalVideo());
        $configuration = $this->transformProtobufToConfiguration($clip->getConfiguration(), $protobuf->getConfiguration());

        if (null !== $protobuf->getProcessedVideo()) {
            $processedVideo = $this->transformProtobufToVideo($clip->getProcessedVideo(), $protobuf->getProcessedVideo());
            $clip->setProcessedVideo($processedVideo);
        }

        $clip->setOriginalVideo($originalVideo);
        $clip->setConfiguration($configuration);

        return $clip;
    }

    private function transformClipToProtobuf(Clip $clip): ProtobufClip
    {
        $protobuf = new ProtobufClip();

        $protobuf->setId($clip->getId()->toString());
        $protobuf->setUserId($clip->getUser()->getId()->toString());
        $protobuf->setStatus($clip->getStatus());

        if ($clip->getCover()) {
            $protobuf->setCover($clip->getCover());
        }

        if ($clip->getUrl()) {
            $protobuf->setUrl($clip->getUrl());
        }

        return $protobuf;
    }

    private function transformProtobufToClip(ProtobufClip $protobufClip): Clip
    {
        /** @var ?Clip $clip */
        $clip = $this->clipRepository->findOneBy(['id' => $protobufClip->getId()]);

        if (null === $clip) {
            throw new \RuntimeException('User not found');
        }

        if ($protobufClip->getCover()) {
            $clip->setCover($protobufClip->getCover());
        }

        if ($protobufClip->getUrl()) {
            $clip->setUrl($protobufClip->getUrl());
        }

        return $clip;
    }

    private function transformProtobufToVideo(?Video $video, ProtobufVideo $protobufVideo): Video
    {
        if (null === $video) {
            $video = new Video(
                videoId: Uuid::fromString($protobufVideo->getId()),
                originalName: $protobufVideo->getOriginalName(),
                name: $protobufVideo->getName(),
                mimeType: $protobufVideo->getMimeType(),
                size: $protobufVideo->getSize(),
            );
        }

        if ($protobufVideo->getId()) {
            $video->setId($protobufVideo->getId());
        }

        if ($protobufVideo->getName()) {
            $video->setName($protobufVideo->getName());
        }

        if ($protobufVideo->getMimeType()) {
            $video->setMimeType($protobufVideo->getMimeType());
        }

        if ($protobufVideo->getAss()) {
            $video->setAss($protobufVideo->getAss());
        }

        if ($protobufVideo->getLength()) {
            $video->setLength($protobufVideo->getLength());
        }

        if ($protobufVideo->getSize()) {
            $video->setSize($protobufVideo->getSize());
        }

        if ($protobufVideo->getSubtitle()) {
            $video->setSubtitle($protobufVideo->getSubtitle());
        }

        if ($protobufVideo->getOriginalName()) {
            $video->setOriginalName($protobufVideo->getOriginalName());
        }

        if ($protobufVideo->getSubtitles()) {
            $video->setSubtitles([]);
            foreach ($protobufVideo->getSubtitles()->getIterator() as $iterator) {
                $video->addSubtitles($iterator);
            }
        }

        if ($protobufVideo->getAudios()) {
            $video->setAudios([]);
            foreach ($protobufVideo->getAudios()->getIterator() as $iterator) {
                $video->addAudios($iterator);
            }
        }

        return $video;
    }

    private function transformVideoToProtobuf(Video $video): ProtobufVideo
    {
        $protobuf = new ProtobufVideo();

        if ($video->getName()) {
            $protobuf->setName($video->getName());
        }

        if ($video->getId()) {
            $protobuf->setId($video->getId()->toString());
        }

        if ($video->getOriginalName()) {
            $protobuf->setOriginalName($video->getOriginalName());
        }

        if ($video->getMimeType()) {
            $protobuf->setMimeType($video->getMimeType());
        }

        if ($video->getSize()) {
            $protobuf->setSize($video->getSize());
        }

        if ($video->getLength()) {
            $protobuf->setLength($video->getLength());
        }

        if ($video->getAss()) {
            $protobuf->setAss($video->getAss());
        }

        if ($video->getSubtitle()) {
            $protobuf->setSubtitle($video->getSubtitle());
        }

        if ($video->getSubtitles()) {
            $protobuf->setSubtitles($video->getSubtitles());
        }

        if ($video->getAudios()) {
            $protobuf->setAudios($video->getAudios());
        }

        return $protobuf;
    }

    private function transformProtobufToConfiguration(?Configuration $configuration, ProtobufConfiguration $protobufConfiguration): Configuration
    {
        if (null === $configuration) {
            $configuration = new Configuration();
        }

        if ($protobufConfiguration->getId()) {
            $configuration->setId($protobufConfiguration->getId());
        }

        if ($protobufConfiguration->getFormat()) {
            $configuration->setFormat($protobufConfiguration->getFormat());
        }

        if ($protobufConfiguration->getSplit()) {
            $configuration->setSplit($protobufConfiguration->getSplit());
        }

        if ($protobufConfiguration->getPrivacyOptions()) {
            $configuration->setPrivacyOptions($protobufConfiguration->getPrivacyOptions());
        }

        if ($protobufConfiguration->getSubtitleBold()) {
            $configuration->setSubtitleBold($protobufConfiguration->getSubtitleBold());
        }

        if ($protobufConfiguration->getSubtitleColor()) {
            $configuration->setSubtitleColor($protobufConfiguration->getSubtitleColor());
        }

        if ($protobufConfiguration->getSubtitleFont()) {
            $configuration->setSubtitleFont($protobufConfiguration->getSubtitleFont());
        }

        if ($protobufConfiguration->getMarginV()) {
            $configuration->setMarginV($protobufConfiguration->getMarginV());
        }

        if ($protobufConfiguration->getSubtitleItalic()) {
            $configuration->setSubtitleItalic($protobufConfiguration->getSubtitleItalic());
        }

        if ($protobufConfiguration->getSubtitleOutlineColor()) {
            $configuration->setSubtitleOutlineColor($protobufConfiguration->getSubtitleOutlineColor());
        }

        if ($protobufConfiguration->getSubtitleOutlineThickness()) {
            $configuration->setSubtitleOutlineThickness($protobufConfiguration->getSubtitleOutlineThickness());
        }

        if ($protobufConfiguration->getSubtitleShadow()) {
            $configuration->setSubtitleShadow($protobufConfiguration->getSubtitleShadow());
        }

        if ($protobufConfiguration->getSubtitleShadowColor()) {
            $configuration->setSubtitleShadowColor($protobufConfiguration->getSubtitleShadowColor());
        }

        if ($protobufConfiguration->getSubtitleSize()) {
            $configuration->setSubtitleSize($protobufConfiguration->getSubtitleSize());
        }

        if ($protobufConfiguration->getSubtitleUnderline()) {
            $configuration->setSubtitleUnderline($protobufConfiguration->getSubtitleUnderline());
        }

        return $configuration;
    }

    private function transformConfigurationToProtobuf(Configuration $configuration): ProtobufConfiguration
    {
        $protobuf = new ProtobufConfiguration();

        $protobuf->setId($configuration->getId()->toString());
        $protobuf->setSubtitleFont($configuration->getSubtitleFont());
        $protobuf->setSubtitleSize($configuration->getSubtitleSize());
        $protobuf->setFormat($configuration->getFormat());
        $protobuf->setMarginV($configuration->getMarginV());
        $protobuf->setSplit($configuration->getSplit());
        $protobuf->setSubtitleBold($configuration->getSubtitleBold());
        $protobuf->setSubtitleColor($configuration->getSubtitleColor());
        $protobuf->setSubtitleItalic($configuration->getSubtitleItalic());
        $protobuf->setSubtitleOutlineColor($configuration->getSubtitleOutlineColor());
        $protobuf->setSubtitleOutlineThickness($configuration->getSubtitleOutlineThickness());
        $protobuf->setSubtitleShadow($configuration->getSubtitleShadow());
        $protobuf->setSubtitleShadowColor($configuration->getSubtitleShadowColor());
        $protobuf->setSubtitleUnderline($configuration->getSubtitleUnderline());
        $protobuf->setPrivacyOptions($configuration->getPrivacyOptions());

        return $protobuf;
    }
}
