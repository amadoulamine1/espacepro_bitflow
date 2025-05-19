<?php

namespace App\Entity;

use Doctrine\ORM\Mapping\Cache;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UsersMessageRepository;

#[ORM\Entity(repositoryClass: UsersMessageRepository::class)]
//#[Cache(usage: 'READ_WRITE', region: 'users_message')]

class UsersMessage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private $id;

    #[ORM\ManyToOne(targetEntity: Users::class, inversedBy: "sentMessages", cascade: ["persist"])]
    #[ORM\JoinColumn(nullable: false)]
    private $sender;

    #[ORM\ManyToOne(targetEntity: Message::class, inversedBy: "usersMessages", cascade: ["persist"], fetch: "EAGER")]
    #[ORM\JoinColumn(nullable: false)]
    private $message;

    #[ORM\ManyToOne(targetEntity: Users::class, inversedBy: "receivedMessages", cascade: ["persist"])]
    #[ORM\JoinColumn(nullable: false)]
    private $recipient;

    #[ORM\Column(type: "boolean")]
    private $is_read;

    #[ORM\Column(type: "datetime", nullable: true)]
    private $readAt;

    #[ORM\Column(type: "boolean", options: ["default" => false], nullable: true)]
    private $is_visibleBCEAO;

    private $receipters = [];

    private $sfds = [];

    private $choixProfil = [];

    /**
     * Aide a verifier si le message a ete accepté par le bureau courrier pour etre affiché ou pas
     * Tout message envoyé par un agent drs est par defaut accepté
      */
    #[ORM\Column(type: "boolean", nullable: true)]
    private $is_accepted=null;

    #[ORM\Column(type: "text", nullable: true)]
    private $motifRejet;



    //Ajouter agent qui a fait rejet et date rejet pour le suivi



    public function __construct()
    {
        $this->is_read = false;
        $this->is_visibleBCEAO = false;
      //  $this->message = new Message();
        //   $this->message->setLettreTransmission(new PieceJointe());
        //  $this->message->getLettreTransmission()->setLibelle("Lettre de Transmission");
        // $this->message->getLettreTransmission()->setType("Lettre de Transmission");

    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSender(): ?Users
    {
        return $this->sender;
    }

    public function setSender(?Users $sender): self
    {
        $this->sender = $sender;

        return $this;
    }

    public function getMessage(): ?Message
    {
        return $this->message;
    }

    public function setMessage(?Message $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getRecipient(): ?Users
    {
        return $this->recipient;
    }

    public function setRecipient(?Users $recipient): self
    {
        $this->recipient = $recipient;

        return $this;
    }

    public function getIsRead(): ?bool
    {
        return $this->is_read;
    }

    public function setIsRead(bool $is_read): self
    {
        $this->is_read = $is_read;

        return $this;
    }

    public function getReadAt(): ?\DateTimeInterface
    {
        return $this->readAt;
    }

    public function setReadAt(?\DateTimeInterface $readAt): self
    {
        $this->readAt = $readAt;

        return $this;
    }

    public function getReceipters(): ?array
    {
        return $this->receipters;
    }

    public function setReceipters(?array $receipters): self
    {
        $this->receipters = $receipters;

        return $this;
    }


    /**
     * Get the value of sfds
     */
    public function getSfds(): ?array
    {
        return $this->sfds;
    }

    /**
     * Set the value of sfds
     *
     * @return  self
     */
    public function setSfds(?array $sfds): self
    {
        $this->sfds = $sfds;

        return $this;
    }

    /**
     * Get the value of sfds
     */
    public function getChoixProfil(): ?array
    {
        return $this->choixProfil;
    }

    /**
     * Set the value of sfds
     *
     * @return  self
     */
    public function setChoixProfil(?array $choixProfil): self
    {
        $this->choixProfil = $choixProfil;

        return $this;
    }
    public function getIsAccepted(): ?bool
    {
        return $this->is_accepted;
    }

    public function setIsAccepted(?bool $is_accepted): self
    {
        $this->is_accepted = $is_accepted;

        return $this;
    }


    public function getIsVisibleBCEAO(): ?bool
    {
        return $this->is_visibleBCEAO;
    }

    public function setIsVisibleBCEAO(bool $is_visibleBCEAO): self
    {
        $this->is_visibleBCEAO = $is_visibleBCEAO;

        return $this;
    }

    public function getMotifRejet(): ?string
    {
        return $this->motifRejet;
    }

    public function setMotifRejet(string $motifRejet): self
    {
        $this->motifRejet = $motifRejet;

        return $this;
    }

}
