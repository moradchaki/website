<?php

namespace App\Entity;

use App\Repository\FlashDealRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: FlashDealRepository::class)]
#[ORM\HasLifecycleCallbacks]
class FlashDeal
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotNull]
    #[Assert\Range(min: 0, max: 100)]
    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 2)]
    private ?float $discountPercent = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $startAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $endAt = null;

    #[ORM\Column]
    private ?bool $isActive = true;

    #[Assert\NotNull]
    #[Assert\PositiveOrZero]
    #[ORM\Column]
    private ?int $quantitySold = 0;

    #[Assert\Positive]
    #[ORM\Column(nullable: true)]
    private ?int $maxQuantity = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\OneToOne(inversedBy: 'flashDeal')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Product $product = null;

    public function __construct()
    {
    }

    #[ORM\PrePersist]
    public function setCreatedAtOnCreate(): void
    {
        $this->createdAt ??= new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDiscountPercent(): ?float
    {
        return $this->discountPercent;
    }

    public function setDiscountPercent(float $discountPercent): static
    {
        $this->discountPercent = $discountPercent;
        return $this;
    }

    public function getStartAt(): ?\DateTimeImmutable
    {
        return $this->startAt;
    }

    public function setStartAt(\DateTimeImmutable $startAt): static
    {
        $this->startAt = $startAt;
        return $this;
    }

    public function getEndAt(): ?\DateTimeImmutable
    {
        return $this->endAt;
    }

    public function setEndAt(\DateTimeImmutable $endAt): static
    {
        $this->endAt = $endAt;
        return $this;
    }

    public function isActive(): bool
    {
        $now = new \DateTimeImmutable();
        return $this->isActive
            && $this->startAt <= $now
            && $this->endAt >= $now
            && ($this->maxQuantity === null || $this->quantitySold < $this->maxQuantity);
    }

    public function setActive(bool $isActive): static
    {
        $this->isActive = $isActive;
        return $this;
    }

    public function getQuantitySold(): ?int
    {
        return $this->quantitySold;
    }

    public function setQuantitySold(int $quantitySold): static
    {
        $this->quantitySold = $quantitySold;
        return $this;
    }

    public function getMaxQuantity(): ?int
    {
        return $this->maxQuantity;
    }

    public function setMaxQuantity(?int $maxQuantity): static
    {
        $this->maxQuantity = $maxQuantity;
        return $this;
    }

    public function getRemainingStock(): ?int
    {
        if ($this->maxQuantity === null) {
            return null;
        }
        return $this->maxQuantity - $this->quantitySold;
    }

    public function isExpired(): bool
    {
        return $this->endAt < new \DateTimeImmutable();
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): static
    {
        $this->product = $product;
        return $this;
    }
}
