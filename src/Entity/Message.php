<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Cache;
use App\Entity\PieceJointe;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\MessageRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity(repositoryClass: MessageRepository::class)]
/**
 * @ORM\Cache(usage="READ_ONLY", region="my_region")
 */
//#[Cache(usage: 'READ_ONLY')]
#[ORM\HasLifecycleCallbacks]
//#[Cache(usage: 'READ_ONLY')]
class Message
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private $id;

    #[ORM\Column(type: "string", length: 255)]
    private $titre;

    #[ORM\Column(type: "text")]
    private $corps;

    #[ORM\Column(type: "datetime")]
    private $created_at;

    #[ORM\OneToMany(targetEntity: UsersMessage::class, mappedBy: "message")]
    private $usersMessages;

    #[ORM\OneToMany(targetEntity: PieceJointe::class, mappedBy: "message", orphanRemoval: true, cascade: ["persist", "remove"])]
    private $pieceJointes;

    private $lettreTransmissions;

    /*# [ORM\OneToOne(targetEntity: PieceJointe::class, mappedBy: "message", cascade: ["persist", "remove"], fetch: "EAGER")]*/
    /*#[ORM\JoinColumn(nullable: true)]*/
    #[ORM\OneToOne(inversedBy: 'messageLt', cascade: ['persist', 'remove'])]
    private ?PieceJointe $lettreTransmission=null;

   /* # [ORM\OneToOne(inversedBy: 'messlt', cascade: ['persist', 'remove'])]
    private ?PieceJointe $lt = null;*/


    public function __construct()
    {
        $this->created_at = new \DateTime();
        //   $this->is_read= false;
        //$this->lettreTransmission= new PieceJointe();
        //$this->lettreTransmission->setLibelle("Lettre de Transmission");
        //$this->lettreTransmission->setType("Lettre de Transmission");
        $this->usersMessages = new ArrayCollection();
        $this->pieceJointes = new ArrayCollection();
    }

    #[ORM\PrePersist]
    public function setPrepersistValue(): void
    {
        if ($this->created_at === null) {
            $this->created_at = new \DateTime();
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = $titre;

        return $this;
    }

    public function getCorps(): ?string
    {
        return $this->corps;
    }

    public function setCorps(string $corps): self
    {
        $this->corps = $corps;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

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

    public function getLettreTransmissions(): ?PieceJointe
    {
        return $this->lettreTransmissions;
    }

    public function setLettreTransmissions(PieceJointe $lettreTransmissions): self
    {
        $this->lettreTransmissions = $lettreTransmissions;

        return $this;
    }


    /**
     * @return Collection|UsersMessage[]
     */
    public function getUsersMessages(): Collection
    {
        return $this->usersMessages;
    }

    public function addUsersMessage(UsersMessage $usersMessage): self
    {
        if (!$this->usersMessages->contains($usersMessage)) {
            $this->usersMessages[] = $usersMessage;
            $usersMessage->setMessage($this);
        }

        return $this;
    }

    public function removeUsersMessage(UsersMessage $usersMessage): self
    {
        if ($this->usersMessages->removeElement($usersMessage)) {
            // set the owning side to null (unless already changed)
            if ($usersMessage->getMessage() === $this) {
                $usersMessage->setMessage(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|PieceJointe[]
     */
    public function getPieceJointes(): Collection
    {
        return $this->pieceJointes;
    }

    public function addPieceJointe(PieceJointe $pieceJointe): self
    {
        if (!$this->pieceJointes->contains($pieceJointe)) {
            $this->pieceJointes[] = $pieceJointe;
            $pieceJointe->setMessage($this);
        }

        return $this;
    }

    public function removePieceJointe(PieceJointe $pieceJointe): self
    {
        if ($this->pieceJointes->removeElement($pieceJointe)) {
            // set the owning side to null (unless already changed)
            if ($pieceJointe->getMessage() === $this) {
                $pieceJointe->setMessage(null);
            }
        }

        return $this;
    }

    public function getLettreTransmission(): ?PieceJointe
    {
        return $this->lettreTransmission;
    }

   /* public function setLettreTransmission(PieceJointe $lettreTransmission): self
    {
        $this->lettreTransmission = $lettreTransmission;

        return $this;
    }*/

    public function setLettreTransmission(?PieceJointe $lettreTransmission): static
    {
        $this->lettreTransmission = $lettreTransmission;

        return $this;
    }
    public function setLettreTransmissionToNull(): self
    {
        $this->lettreTransmission = null;

        return $this;
    }
    

   /* public function getLt(): ?PieceJointe
    {
        return $this->lt;
    }

    public function setLt(?PieceJointe $lt): static
    {
        $this->lt = $lt;

        return $this;
    }*/

}
