<?php

// src/Entity/Gift2Games/Categories.php

namespace App\Entity\Gift2Games;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="category")
 */
class Categories
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", name="category_id", unique=true)
     */
    private $categoryId;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="string", name="short_title", length=255)
     */
    private $shortTitle;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $image;

    /**
     * @ORM\ManyToOne(targetEntity="Categories", inversedBy="childs")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     */
    private $parent;

    /**
     * @ORM\OneToMany(targetEntity="Categories", mappedBy="parent")
     */
    private $childs;

    /**
     * @ORM\Column(type="integer",nullable=true)
     */
    private $type;

    public function __construct()
    {
        $this->childs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getShortTitle(): ?string
    {
        return $this->shortTitle;
    }

    public function setShortTitle(string $shortTitle): self
    {
        $this->shortTitle = $shortTitle;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getType(): ?int
    {
        return $this->type;
    }

    public function setType(int $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getCategoryId(): ?int
    {
        return $this->categoryId;
    }

    public function setCategoryId(int $categoryId): self
    {
        $this->categoryId = $categoryId;

        return $this;
    }

    public function getParent(): ?Categories
    {
        return $this->parent;
    }

    public function setParent(?Categories $parent): self
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return Collection|Categories[]
     */
    public function getChilds(): Collection
    {
        return $this->childs;
    }

    public function addChild(Categories $child): self
    {
        if (!$this->childs->contains($child)) {
            $this->childs[] = $child;
            $child->setParent($this);
        }

        return $this;
    }

    public function removeChild(Categories $child): self
    {
        if ($this->childs->removeElement($child)) {
            if ($child->getParent() === $this) {
                $child->setParent(null);
            }
        }

        return $this;
    }
    public function toArray(int $depth = 1): array
    {
        if ($depth <= 0) {
            return [];
        }

        $children = [];
        foreach ($this->childs as $child) {
            $children[] = $child->toArray($depth - 1);
        }

        return [
            'id' => $this->id,
            'categoryId' => $this->categoryId,
            'title' => $this->title,
            'shortTitle' => $this->shortTitle,
            'image' => $this->image,
            'type' => $this->type,
            'children' => $children,
        ];
    }

    /**
     * Get child categories for the given category ID.
     *
     * @param int $categoryId
     * @param int $depth
     * @return array
     */
    public function getChildCategories(int $categoryId, int $depth = 1): array
    {
        $category = $this->mr->getRepository(Categories::class)->find($categoryId);

        if (!$category) {
            return []; // Category not found
        }

        $children = [];
        foreach ($category->getChilds() as $child) {
            $children[] = $child->toArray($depth - 1);
        }

        return $children;
    }
}
