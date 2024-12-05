<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Domain
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: 'string', length: 50)]
    private ?string $type = null;

    #[ORM\Column(type: 'string', length: 20, options: ['default' => 'pending'])]
    private ?string $status = 'pending';

    #[ORM\Column(type: 'datetime')]
    private ?\DateTime $created_at = null;

    public function __construct()
    {
        // Initialise la date de création lors de la création de l'entité
        $this->created_at = new \DateTime();
    }

    // Getter et setter pour `created_at`
    public function getCreatedAt(): ?\DateTime
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTime $created_at): self
    {
        $this->created_at = $created_at;
        return $this;
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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }    

    public function getStatus(): ?string
    {
        return $this->status;
    }    

    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }   
}

