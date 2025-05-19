<?php

namespace App\Entity;

use App\Entity\Users;
use Doctrine\ORM\Mapping\Cache;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\SfdRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity(repositoryClass: SfdRepository::class)]
//#[Cache(usage: 'READ_WRITE', region: 'sfd')]
class Sfd
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private $id;

    #[ORM\Column(type: "string", length: 12, unique: true)]
    private $numAgrement;

    #[ORM\Column(type: "string", length: 255)]
    private $sigle;

    #[ORM\Column(type: "string", length: 255)]
    private $nomDeveloppe;

    #[ORM\Column(type: "string", length: 100, nullable: true)]
    private $email;

    #[ORM\Column(type: "string", length: 150, nullable: true)]
    private $telephone;

    #[ORM\OneToMany(targetEntity: Users::class, mappedBy: "sfd")]
    private $users;

    #[ORM\Column(type: "boolean")]
    private $is_actif;

    #[ORM\Column(type: "string", length: 100, nullable: true)]
    private $type;

    #[ORM\Column(type: "string", length: 100, nullable: true)]
    private $decisionAgrement;

    #[ORM\Column(type: "string", length: 20, nullable: true)]
    private $region;

    #[ORM\Column(type: "string", length: 20, nullable: true)]
    private $reseau;

    #[ORM\Column(type: "boolean", options: ["default" => false])]
    private $is_article44 = false;

    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->numAgrement . " | " . $this->sigle;
        //. " | " . $this->nomDeveloppe;
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumAgrement(): ?string
    {
        return $this->numAgrement;
    }

    public function setNumAgrement(string $numAgrement): self
    {
        $this->numAgrement = $numAgrement;

        return $this;
    }

    public function getSigle(): ?string
    {
        return $this->sigle;
    }

    public function setSigle(string $sigle): self
    {
        $this->sigle = $sigle;

        return $this;
    }

    public function getNomDeveloppe(): ?string
    {
        return $this->nomDeveloppe;
    }

    public function setNomDeveloppe(string $nomDeveloppe): self
    {
        $this->nomDeveloppe = $nomDeveloppe;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(?string $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

    /**
     * @return Collection|Users[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(Users $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->setSfd($this);
        }

        return $this;
    }

    public function removeUser(Users $user): self
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getSfd() === $this) {
                $user->setSfd(null);
            }
        }

        return $this;
    }

    public function getIsActif(): ?bool
    {
        return $this->is_actif;
    }

    public function setIsActif(bool $is_actif): self
    {
        $this->is_actif = $is_actif;

        return $this;
    }

    // Les getters doivent correspondre exactement
    public function getIs_actif(): ?bool
    {
        return $this->is_actif;
    }


    public function getType(): ?string
    {
        return $this->type;
    }

    

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getDecisionAgrement(): ?string
    {
        return $this->decisionAgrement;
    }

    public function setDecisionAgrement(?string $decisionAgrement): self
    {
        $this->decisionAgrement = $decisionAgrement;

        return $this;
    }

    public function getRegion(): ?string
    {
        return $this->region;
    }

    public function setRegion(?string $region): self
    {
        $this->region = $region;

        return $this;
    }

    public function getReseau(): ?string
    {
        return $this->reseau;
    }

    public function setReseau(?string $reseau): self
    {
        $this->reseau = $reseau;

        return $this;
    }

    public function getIsArticle44(): ?bool
    {
        return $this->is_article44;
    }

    public function setIsArticle44(bool $is_article44): self
    {
        $this->is_article44 = $is_article44;

        return $this;
    }

    public function getIs_Article44(): ?bool
    {
        return $this->is_article44;
    }

}
