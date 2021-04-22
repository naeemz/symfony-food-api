<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
//use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=CategoryRepository::class)
 * @ORM\Table(name="category")
 * @ORM\HasLifecycleCallbacks()
 */
class Category implements \JsonSerializable
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

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
     * @ORM\ManyToMany(targetEntity="Food", mappedBy="categories", fetch="EAGER")
     */
    private $foods;

    public function __construct()
    {
        $this->foods = new ArrayCollection();
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

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
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

    public function addFood(Food $food): void
    {
        $this->foods[] = $food;
    }

    /**
     * @return Collection
     */
    public function getFoods(): Collection
    {
        return $this->foods;
    }

    /**
     * @param Category $category
     */
    public function removeCategory(Category $category): void
    {
        // If the category does not exist in the collection, then we don't need to do anything
        if (!$this->categories->contains($category)) {
            return;
        }

        // Remove category from the collection
        $this->categories->removeElement($category);

        // Also remove this from the blog post collection of the category
        $category->removeFood($this);
    }

    /**
     * @param Food $food
     */
    public function removeFood(Food $food): void
    {
        // If the blog post does not exist in the collection, then we don't need to do anything
        if (!$this->foods->contains($food)) {
            return;
        }

        // Remove blog post from the collection
        $this->foods->removeElement($food);

        // Also remove this from the category collection of the blog post
        $food->removeCategory($this);
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
            "id"        => $this->getId(),
            "name"      => $this->getName(),
            "created_at"=> $this->getCreatedAt(),
            //'foods' => $this->getFoods()->toArray(),
        ];
    }
}
