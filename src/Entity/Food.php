<?php

namespace App\Entity;

use App\Repository\FoodRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation\DeletedCheck;

/**
 * @ORM\Entity(repositoryClass=FoodRepository::class)
 * @ORM\HasLifecycleCallbacks()
 * @DeletedCheck(deletedAtFieldName="deleted_at")
 */
class Food implements \JsonSerializable
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     * @Assert\Length(max=255)
     */
    private $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=0, options={"default" : 0})
     * @Assert\PositiveOrZero
     */
    private $price;

    /**
     * @ORM\Column(type="smallint")
     */
    private $serving_per_person;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $image_url;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updated_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $deleted_at;

    /**
     * @ORM\ManyToMany(targetEntity="Category", inversedBy="foods")
     * @ORM\JoinTable(name="foods_categories")
     */
    private $categories;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getServingPerPerson(): ?int
    {
        return $this->serving_per_person;
    }

    public function setServingPerPerson(int $serving_per_person): self
    {
        $this->serving_per_person = $serving_per_person;

        return $this;
    }

    public function getImageUrl(): ?string
    {
        if( $this->image_url )
            return $_ENV["SITE_URL"] . $this->image_url;
        else
            return null;
    }

    public function setImageUrl(?string $image_url): self
    {
        $this->image_url = $image_url;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(?\DateTimeInterface $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    public function getDeletedAt(): ?\DateTimeInterface
    {
        return $this->deleted_at;
    }

    public function setDeletedAt(?\DateTimeInterface $deleted_at): self
    {
        $this->deleted_at = $deleted_at;

        return $this;
    }

    /**
     * Get the value of categories
     */ 
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    /**
     * @param Category $category
     */
    public function addToCategory(Category $category): void
    {
        // First we check if we already have this category in our collection
        if ($this->categories->contains($category)){
            // Do nothing if its already part of our collection
            return;
        }
        // Add category to our array collection
        $this->categories->add($category);
    }

    /**
     * Detach all Categories
     */
    public function detachAllCategories() 
    {
        $this->categories = new ArrayCollection();
    }

    /**
     * @throws \Exception
     * @ORM\PrePersist()
     */
    public function beforeSave()
    {
        $this->created_at = new \DateTime('now');
    }

    /**
     * Gets triggered every time on update

     * @ORM\PreUpdate
     */
    public function onPreUpdate()
    {
        $this->updated_at = new \DateTime("now");
    }

    /**
   * Specify data which should be serialized to JSON
   * @link https://php.net/manual/en/jsonserializable.jsonserialize.php
   * @return mixed data which can be serialized by <b>json_encode</b>,
   * which is a value of any type other than a resource.
   * @since 5.4.0
   */
    public function jsonSerialize()
    {
        return [
            "name"          => $this->getName(),
            "description"   => $this->getDescription(),
            "price"         => $this->getPrice(),
            "serving_per_person" => $this->getServingPerPerson(),
            "image_url"     => $this->getImageUrl(),
            'categories'    => $this->getCategories()->toArray(),
            "created_at"    => $this->getCreatedAt(),
        ];
    }
}
