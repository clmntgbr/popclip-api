<?php

declare(strict_types=1);

namespace App\Entity\ValueObject;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Webmozart\Assert\Assert;

#[ORM\Embeddable]
class Status implements \Stringable
{
    #[ORM\Column(name: 'status', type: Types::STRING, length: 180, nullable: false)]
    private string $value;

    public function __construct(string $value)
    {
        Assert::notEmpty($value);
        Assert::notWhitespaceOnly($value);
        Assert::string($value);
        $this->value = $value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
