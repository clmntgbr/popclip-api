<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Entity\ValueObject\AccessToken;
use App\Entity\ValueObject\RefreshToken;
use App\Entity\ValueObject\SociaAccountId;
use App\Entity\ValueObject\SocialAccountType;
use App\Entity\ValueObject\Username;
use App\Repository\SocialAccountRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Embedded;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: SocialAccountRepository::class)]
#[ApiResource(
    order: ['createdAt' => 'DESC'],
    operations: [
        new GetCollection(
            normalizationContext: ['skip_null_values' => false, 'groups' => ['social_account.read']],
        ),
        new Get(
            normalizationContext: ['skip_null_values' => false, 'groups' => ['social_account.read']],
        ),
    ]
)]
class SocialAccount
{
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ApiProperty(identifier: true)]
    #[Groups(['social_account.read'])]
    private Uuid $id;

    #[Embedded(class: Username::class, columnPrefix: false)]
    #[Groups(['social_account.read'])]
    private Username $username;

    #[Embedded(class: AccessToken::class, columnPrefix: false)]
    private AccessToken $accessToken;

    #[Embedded(class: RefreshToken::class, columnPrefix: false)]
    private RefreshToken $refreshToken;

    #[Embedded(class: SociaAccountId::class, columnPrefix: false)]
    #[Groups(['social_account.read'])]
    private SociaAccountId $socialAccountId;

    #[ORM\Column(type: Types::JSON, nullable: false)]
    #[Groups(['social_account.read'])]
    private array $scope = [];

    #[Embedded(class: SocialAccountType::class, columnPrefix: false)]
    #[Groups(['social_account.read'])]
    private SocialAccountType $socialAccountType;

    #[ORM\Column(nullable: true)]
    #[Groups(['social_account.read'])]
    private ?\DateTime $expireAt = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['social_account.read'])]
    private ?\DateTime $refreshExpireAt = null;

    #[ManyToOne(targetEntity: User::class, inversedBy: 'socialAccounts')]
    #[JoinColumn(name: 'user_id', referencedColumnName: 'id')]
    private User $user;

    public function __construct()
    {
        $this->id = Uuid::v4();
    }

    public function updateToken(
        string $accessToken,
        ?string $refreshToken = null,
        ?\DateTime $expireAt = null,
        ?\DateTime $refreshExpireAt = null,
    ) {
        $this->accessToken = new AccessToken(value: $accessToken);
        $this->refreshToken = new RefreshToken(value: $refreshToken);
        $this->expireAt = $expireAt;
        $this->refreshExpireAt = $refreshExpireAt;
    }

    public function setAccessToken(): self
    {
        $this->accessToken = new AccessToken(value: $this->accessToken);

        return $this;
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getUsername(): Username
    {
        return $this->username;
    }

    public function getAccessToken(): AccessToken
    {
        return $this->accessToken;
    }

    public function getRefreshToken(): RefreshToken
    {
        return $this->refreshToken;
    }

    public function getSocialAccountId(): SociaAccountId
    {
        return $this->socialAccountId;
    }

    public function getScope(): array
    {
        return $this->scope;
    }

    public function getSocialAccountType(): SocialAccountType
    {
        return $this->socialAccountType;
    }

    public function getExpireAt(): ?\DateTime
    {
        return $this->expireAt;
    }

    public function getRefreshExpireAt(): ?\DateTime
    {
        return $this->refreshExpireAt;
    }

    public function getUser(): User
    {
        return $this->user;
    }
}
