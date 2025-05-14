<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Protobuf\ClipStatus;
use App\Repository\ClipRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: ClipRepository::class)]
#[ApiResource(
    order: ['createdAt' => 'DESC'],
    operations: [
        new GetCollection(
            normalizationContext: ['skip_null_values' => false, 'groups' => ['clip.read', 'video.read', 'social_account.read']],
        ),
        new Get(
            normalizationContext: ['skip_null_values' => false, 'groups' => ['clip.read', 'video.read', 'social_account.read']],
        ),
    ]
)]
class Clip
{
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ApiProperty(identifier: true)]
    #[Groups(['clip.read'])]
    private Uuid $id;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private User $user;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    #[Groups(['clip.read'])]
    private ?string $name = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    #[Groups(['clip.read'])]
    private ?string $cover = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    #[Groups(['clip.read'])]
    private ?string $url = null;

    #[ORM\OneToOne(targetEntity: Configuration::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(name: 'configuration_id', referencedColumnName: 'id', nullable: false)]
    #[Groups(['clip.read'])]
    private Configuration $configuration;

    #[ORM\OneToOne(targetEntity: Video::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(name: 'orginal_video_id', referencedColumnName: 'id', nullable: true)]
    #[Groups(['clip.read'])]
    private ?Video $originalVideo;

    #[ORM\OneToOne(targetEntity: Video::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(name: 'processed_video_id', referencedColumnName: 'id', nullable: true)]
    #[Groups(['clip.read'])]
    private ?Video $processedVideo = null;

    #[ORM\Column(type: Types::STRING, nullable: false)]
    #[Groups(['clip.read'])]
    private string $status;

    #[ORM\Column(type: Types::JSON, nullable: false)]
    #[Groups(['clip.read'])]
    private array $statuses = [];

    #[ORM\Column(type: Types::STRING, nullable: true)]
    private string $message;

    public function __construct(
        User $user,
        Uuid $clipId,
        Configuration $configuration,
        ?Video $originalVideo = null,
        ?string $url = null,
    ) {
        $this->id = $clipId;
        $this->user = $user;
        $this->url = $url;
        $this->originalVideo = $originalVideo;
        $this->configuration = $configuration;
        $this->status = ClipStatus::name(ClipStatus::UPLOADED);
        $this->statuses = [ClipStatus::name(ClipStatus::UPLOADED)];
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;
        $this->statuses[] = $status;

        return $this;
    }

    public function setStatuses(array $statuses): self
    {
        $this->statuses = $statuses;

        return $this;
    }

    public function setCover(string $cover): self
    {
        $this->cover = $cover;

        return $this;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function setConfiguration(Configuration $configuration): self
    {
        $this->configuration = $configuration;

        return $this;
    }

    public function setOriginalVideo(Video $video): self
    {
        $this->originalVideo = $video;

        return $this;
    }

    public function setProcessedVideo(?Video $video): self
    {
        $this->processedVideo = $video;

        return $this;
    }

    public function getOriginalVideo(): ?Video
    {
        return $this->originalVideo;
    }

    public function getConfiguration(): Configuration
    {
        return $this->configuration;
    }

    public function getCover(): ?string
    {
        return $this->cover;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getProcessedVideo(): ?Video
    {
        return $this->processedVideo;
    }

    public function getStatuses(): array
    {
        return $this->statuses;
    }

    #[Groups(['clip.read'])]
    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    #[Groups(['clip.read'])]
    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function updateStatusError(?string $errorMessage = null)
    {
        $this->message = $errorMessage ?? 'Unknown error';
        $this->status = ClipStatus::name(ClipStatus::STATUS_ERROR);
    }

    public function updateStatusUploading()
    {
        $this->status = ClipStatus::name(ClipStatus::CLIP_UPLOADING);
    }
}
