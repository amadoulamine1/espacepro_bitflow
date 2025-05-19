<?php

namespace App\Entity;

use App\Entity\Sfd;
use App\Entity\UsersMessage;
use Doctrine\ORM\Mapping\Cache;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UsersRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: UsersRepository::class)]
#[UniqueEntity(fields: ["email"], message: "Il existe deja un compte avec cet email")]
#[UniqueEntity(fields: ["username"], message: "Il existe deja un compte avec ce nom d'utilisateur")]
//#[Cache(usage: 'READ_WRITE', region: 'users')]
/**
 * @ORM\Cache(usage="READ_ONLY", region="my_region")
 */
class Users implements  UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private $id;

    #[ORM\Column(type: "string", length: 12)]
    private $nom;

    #[ORM\Column(type: "string", length: 180)]
    private $prenom;

    #[ORM\Column(type: "string", length: 15, nullable: true)]
    private $telephone;
    
    #[ORM\Column(type: "string", length: 180, unique: true)]
    private $email;

    #[ORM\Column(type: "json")]
    private $roles = [];
    

    #[ORM\Column(type: "string")]
    private $password;

    #[ORM\ManyToOne(targetEntity: Sfd::class, inversedBy: "users", cascade: ["persist"])]
    private $sfd;
    
    #[ORM\Column(type: "string", length: 10, nullable: true)]
    private $fonction;

    #[ORM\OneToMany(targetEntity: UsersMessage::class, mappedBy: "sender")]
    private $sentMessages;

    #[ORM\OneToMany(targetEntity: UsersMessage::class, mappedBy: "recipient")]
    private $receivedMessages;

    #[ORM\Column(type: "boolean", options: ["default" => true])]
    private $is_actif;

    const ROLES = array(
        'roles.sfd' => 'ROLE_SFD',
        'roles.guichet' => 'ROLE_GUICHET',
        'roles.user' => 'ROLE_USER',
        'roles.admin' => 'ROLE_ADMIN'

    );

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private $confirmation_token;

    #[ORM\Column(type: "datetime", nullable: true)]
    private $lastLoginAt;

    #[ORM\Column(type: "datetime", nullable: true)]
    private $lastResetPassword;

    #[ORM\Column(type: "datetime", nullable: true)]
    private $lastActivity;

    #[ORM\Column(type: "datetime", nullable: true)]
    private $createdAt;

    #[ORM\Column(type: "datetime", nullable: true)]
    private $updatedAt;

    #[ORM\ManyToOne(inversedBy: 'users')]
    #[ORM\JoinColumn(nullable: true)]
    private ?FonctionSfd $fonctionSfd = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $username = null;
    
    public function __construct()
    {
        $this->sentMessages = new ArrayCollection();
        $this->receivedMessages = new ArrayCollection();
    }

    public function __toString()
    {
        return ucfirst($this->getPrenom()) . " " . mb_strtoupper($this->getNom());
    }
    
    #[ORM\PrePersist]
    public function setCreated()
    {
        $this->setCreatedAt(new \DateTime());
        $this->setUpdatedAt(new \DateTime());
    }

    #[ORM\PreUpdate]
    public function setUpdated()
    {
        $this->setUpdatedAt(new \DateTime());
    }
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        //return (string) $this->email;
        return (string) $this->username;
    }
    public function getUsernameOrEmail(string $identifier): string
    {
        return filter_var($identifier, FILTER_VALIDATE_EMAIL) ? $this->email : $this->username;
    }
     /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
       // return (string) $this->email;
        return (string) $this->email;
    }

    
    public function hasRole($role): bool
    {
        return in_array(strtoupper($role), $this->getRoles(), true);
    }
    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getSfd(): ?Sfd
    {
        return $this->sfd;
    }

    public function setSfd(?Sfd $sfd): self
    {
        $this->sfd = $sfd;

        return $this;
    }

    public function getFonction(): ?string
    {
        return $this->fonction;
    }

    public function setFonction(?string $fonction): self
    {
        $this->fonction = $fonction;

        return $this;
    }

    /**
     * @return Collection|UsersMessage[]
     */
    public function getSentMessages(): Collection
    {
        return $this->sentMessages;
    }

    public function addSentMessage(UsersMessage $sentMessage): self
    {
        if (!$this->sentMessages->contains($sentMessage)) {
            $this->sentMessages[] = $sentMessage;
            $sentMessage->setSender($this);
        }

        return $this;
    }

    public function removeSentMessage(UsersMessage $sentMessage): self
    {
        if ($this->sentMessages->removeElement($sentMessage)) {
            // set the owning side to null (unless already changed)
            if ($sentMessage->getSender() === $this) {
                $sentMessage->setSender(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|UsersMessage[]
     */
    public function getReceivedMessages(): Collection
    {
        return $this->receivedMessages;
    }

    public function addReceivedMessage(UsersMessage $receivedMessage): self
    {
        if (!$this->receivedMessages->contains($receivedMessage)) {
            $this->receivedMessages[] = $receivedMessage;
            $receivedMessage->setRecipient($this);
        }

        return $this;
    }

    public function removeReceivedMessage(UsersMessage $receivedMessage): self
    {
        if ($this->receivedMessages->removeElement($receivedMessage)) {
            // set the owning side to null (unless already changed)
            if ($receivedMessage->getRecipient() === $this) {
                $receivedMessage->setRecipient(null);
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

     public function getConfirmationToken(): ?string
    {
        return $this->confirmation_token;
    }

    public function setConfirmationToken(string $confirmation_token): self
    {
        $this->confirmation_token = $confirmation_token;

        return $this;
    }

    public function getLastLoginAt(): ?\DateTimeInterface
    {
        return $this->lastLoginAt;
    }

    public function setLastLoginAt(\DateTimeInterface $lastLoginAt): self
    {
        $this->lastLoginAt = $lastLoginAt;

        return $this;
    }

    public function getLastActivity(): ?\DateTimeInterface
    {
        return $this->lastActivity;
    }

    public function setLastActivity(\DateTimeInterface $lastActivity): self
    {
        $this->lastActivity = $lastActivity;

        return $this;
    }

    public function getLastResetPassword(): ?\DateTimeInterface
    {
        return $this->lastResetPassword;
    }

    public function setLastResetPassword(\DateTimeInterface $lastResetPassword): self
    {
        $this->lastResetPassword = $lastResetPassword;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getFonctionSfd(): ?FonctionSfd
    {
        return $this->fonctionSfd;
    }

    public function setFonctionSfd(?FonctionSfd $fonctionSfd): static
    {
        $this->fonctionSfd = $fonctionSfd;

        return $this;
    }

    public function setUsername(?string $username): static
    {
        $this->username = $username;

        return $this;
    }

}
