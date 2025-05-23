<?php

declare(strict_types=1);

namespace App\Entity\ValueObject;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Webmozart\Assert\Assert;

#[ORM\Embeddable]
class RefreshToken implements \Stringable
{
    #[ORM\Column(name: 'refresh_token', type: Types::STRING, nullable: true)]
    private ?string $value;

    public function __construct(?string $value)
    {
        if (null !== $value) {
            Assert::notEmpty($value);
            Assert::notWhitespaceOnly($value);
            Assert::string($value);
        }

        $this->value = $value;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value ?? '';
    }
}
