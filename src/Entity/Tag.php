<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineConstraints;
use Symfony\Component\Validator\Constraints;

/**
 * @ORM\Entity
 * @DoctrineConstraints\UniqueEntity(fields="name", message="Tag already exists.")
 */
class Tag
{

    /**
     * @var int
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @var string
     * @ORM\Column(type="text")
     * @Constraints\NotBlank
     */
    private string $name;

    /**
     * @param string|null $name
     */
    public function __construct(?string $name)
    {
        if (isset($name)) {
            $this->name = $name;
        }
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
    public function getName(): ?string
    {
        if (! isset($this->name)) {
            return null;
        }
        return $this->name;
    }

    /**
     * @param string|null $name
     * @return self
     */
    public function setName(?string $name): static
    {
        $this->name = $name;
        return $this;
    }
}
