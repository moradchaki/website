<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    #[ORM\Column(length: 255, unique: true)]
    private ?string $slug = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 500, nullable: true)]
    private ?string $shortDescription = null;

    #[Assert\NotBlank]
    #[Assert\Positive]
    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?float $price = null;

    #[Assert\Positive]
    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    private ?float $salePrice = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $specs = null;

    #[ORM\Column(length: 500, nullable: true)]
    private ?string $imageUrl = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $galleryImages = null;

    #[Assert\NotNull]
    #[Assert\PositiveOrZero]
    #[ORM\Column]
    private ?int $stock = 0;

    #[Assert\NotBlank]
    #[Assert\Length(max: 100)]
    #[ORM\Column(length: 100, unique: true)]
    private ?string $sku = null;

    #[ORM\Column]
    private ?bool $isFeatured = false;

    #[ORM\Column]
    private ?bool $isNewArrival = false;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToMany(targetEntity: Category::class, inversedBy: 'products')]
    private Collection $categories;

    #[ORM\OneToOne(mappedBy: 'product', targetEntity: FlashDeal::class, cascade: ['persist', 'remove'])]
    private ?FlashDeal $flashDeal = null;

    #[ORM\OneToMany(targetEntity: Review::class, mappedBy: 'product')]
    private Collection $reviews;

    #[ORM\OneToMany(targetEntity: OrderItem::class, mappedBy: 'product')]
    private Collection $orderItems;

    #[ORM\OneToMany(targetEntity: ProductLike::class, mappedBy: 'product', cascade: ['remove'])]
    private Collection $likes;

    #[ORM\Column]
    private ?int $likesCount = 0;

    #[ORM\Column]
    private ?int $viewsCount = 0;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
        $this->reviews = new ArrayCollection();
        $this->orderItems = new ArrayCollection();
        $this->likes = new ArrayCollection();
    }

    #[ORM\PrePersist]
    public function setTimestampsOnCreate(): void
    {
        $this->createdAt ??= new \DateTimeImmutable();
        $this->updatedAt ??= new \DateTimeImmutable();
    }

    #[ORM\PreUpdate]
    public function setTimestampsOnUpdate(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getShortDescription(): ?string
    {
        return $this->shortDescription;
    }

    public function setShortDescription(?string $shortDescription): static
    {
        $this->shortDescription = $shortDescription;
        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;
        return $this;
    }

    public function getSalePrice(): ?float
    {
        return $this->salePrice;
    }

    public function setSalePrice(?float $salePrice): static
    {
        $this->salePrice = $salePrice;
        return $this;
    }

    public function getEffectivePrice(): float
    {
        if ($this->flashDeal && $this->flashDeal->isActive()) {
            $discounted = $this->price * (1 - $this->flashDeal->getDiscountPercent() / 100);
            return round($discounted, 2);
        }
        return $this->salePrice ?? $this->price;
    }

    public function getSpecs(): ?array
    {
        return $this->specs;
    }

    public function setSpecs(?array $specs): static
    {
        $this->specs = $specs;
        return $this;
    }

    public function getImageUrl(): ?string
    {
        return $this->imageUrl;
    }

    public function setImageUrl(?string $imageUrl): static
    {
        $this->imageUrl = $imageUrl;
        return $this;
    }

    public function getGalleryImages(): ?array
    {
        return $this->galleryImages;
    }

    public function setGalleryImages(?array $galleryImages): static
    {
        $this->galleryImages = $galleryImages;
        return $this;
    }

    public function getStock(): ?int
    {
        return $this->stock;
    }

    public function setStock(int $stock): static
    {
        $this->stock = $stock;
        return $this;
    }

    public function getSku(): ?string
    {
        return $this->sku;
    }

    public function setSku(string $sku): static
    {
        $this->sku = $sku;
        return $this;
    }

    public function isFeatured(): ?bool
    {
        return $this->isFeatured;
    }

    public function setFeatured(bool $isFeatured): static
    {
        $this->isFeatured = $isFeatured;
        return $this;
    }

    public function isNewArrival(): ?bool
    {
        return $this->isNewArrival;
    }

    public function setNewArrival(bool $isNewArrival): static
    {
        $this->isNewArrival = $isNewArrival;
        return $this;
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

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): static
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
        }
        return $this;
    }

    public function removeCategory(Category $category): static
    {
        $this->categories->removeElement($category);
        return $this;
    }

    public function getFlashDeal(): ?FlashDeal
    {
        return $this->flashDeal;
    }

    public function setFlashDeal(?FlashDeal $flashDeal): static
    {
        if ($flashDeal === null && $this->flashDeal !== null) {
            $this->flashDeal->setProduct(null);
        }
        if ($flashDeal !== null && $flashDeal->getProduct() !== $this) {
            $flashDeal->setProduct($this);
        }
        $this->flashDeal = $flashDeal;
        return $this;
    }

    public function getReviews(): Collection
    {
        return $this->reviews;
    }

    public function addReview(Review $review): static
    {
        if (!$this->reviews->contains($review)) {
            $this->reviews->add($review);
            $review->setProduct($this);
        }
        return $this;
    }

    public function removeReview(Review $review): static
    {
        if ($this->reviews->removeElement($review)) {
            if ($review->getProduct() === $this) {
                $review->setProduct(null);
            }
        }
        return $this;
    }

    public function getOrderItems(): Collection
    {
        return $this->orderItems;
    }

    public function addOrderItem(OrderItem $orderItem): static
    {
        if (!$this->orderItems->contains($orderItem)) {
            $this->orderItems->add($orderItem);
            $orderItem->setProduct($this);
        }
        return $this;
    }

    public function removeOrderItem(OrderItem $orderItem): static
    {
        if ($this->orderItems->removeElement($orderItem)) {
            if ($orderItem->getProduct() === $this) {
                $orderItem->setProduct(null);
            }
        }
        return $this;
    }

    public function getLikes(): Collection
    {
        return $this->likes;
    }

    public function addLike(ProductLike $like): static
    {
        if (!$this->likes->contains($like)) {
            $this->likes->add($like);
            $like->setProduct($this);
            $this->likesCount = $this->likes->count();
        }
        return $this;
    }

    public function removeLike(ProductLike $like): static
    {
        if ($this->likes->removeElement($like)) {
            if ($like->getProduct() === $this) {
                $like->setProduct(null);
            }
            $this->likesCount = $this->likes->count();
        }
        return $this;
    }

    public function getLikesCount(): ?int
    {
        return $this->likesCount;
    }

    public function setLikesCount(int $likesCount): static
    {
        $this->likesCount = $likesCount;
        return $this;
    }

    public function isLikedByUser(?User $user): bool
    {
        if (!$user) {
            return false;
        }
        foreach ($this->likes as $like) {
            if ($like->getUser() === $user) {
                return true;
            }
        }
        return false;
    }

    public function getViewsCount(): ?int
    {
        return $this->viewsCount;
    }

    public function setViewsCount(int $viewsCount): static
    {
        $this->viewsCount = $viewsCount;
        return $this;
    }

    public function getAverageRating(): ?float
    {
        if ($this->reviews->isEmpty()) {
            return null;
        }
        $total = 0;
        $count = 0;
        foreach ($this->reviews as $review) {
            if ($review->isApproved()) {
                $total += $review->getRating();
                $count++;
            }
        }
        return $count > 0 ? round($total / $count, 1) : null;
    }
}
