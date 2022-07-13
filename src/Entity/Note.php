<?php

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints;

/**
 * @ORM\Entity
 */
class Note
{

    /**
     * @var int
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer",nullable=false)
     */
    private int $id;

    /**
     * @var string|null
     * @ORM\Column(type="text",nullable=true)
     * @Constraints\NotBlank
     */
    private ?string $title = null;

    /**
     * @var string|null
     * @ORM\Column(type="text",nullable=true)
     */
    private ?string $description = null;

    /**
     * @var DateTime
     * @ORM\Column(type="datetime",nullable=false)
     */
    private DateTime $createdAt;

    /**
     * @var Collection
     * @ORM\ManyToMany(targetEntity="Tag",cascade={"persist","merge","refresh"})
     * @ORM\JoinTable(name="note_tag",
     *     joinColumns={@ORM\JoinColumn(name="note_id", referencedColumnName="id", onDelete="CASCADE")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="tag_id", referencedColumnName="id", onDelete="CASCADE")}
     * )
     */
    private Collection $tags;

    public function __construct()
    {
        $this->createdAt = new DateTime();
        $this->tags = new ArrayCollection();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        if (! isset($this->id)) {
            return null;
        }
        return $this->id;
    }

    /**
     * @param int|null $id
     * @return self
     */
    public function setId(?int $id): static
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string|null $title
     * @return self
     */
    public function setTitle(?string $title): static
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     * @return self
     */
    public function setDescription(?string $description): static
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    /**
     * @return array|Tag[]
     */
    public function getTags(): array
    {
        return $this->tags->getValues();
    }

    /**
     * @param array|Collection|Tag[] $tags
     * @return Note
     */
    public function setTags(array|Collection $tags): static
    {
        $this->tags = ($tags instanceof Collection) ? $tags : new ArrayCollection($tags);
        return $this;
    }

    /**
     * @param Tag $tag
     * @return bool
     */
    public function hasTag(Tag $tag): bool
    {
        return $this->tags->contains($tag);
    }

    /**
     * @param Tag $tag
     * @return $this
     */
    public function addTag(Tag $tag): static
    {
        if (!$this->hasTag($tag)) {
            $this->tags->add($tag);
        }

        return $this;
    }

    /**
     * @param Tag $tag
     * @return $this
     */
    public function removeTag(Tag $tag): static
    {
        if ($this->hasTag($tag)) {
            $this->tags->removeElement($tag);
        }

        return $this;
    }
}
