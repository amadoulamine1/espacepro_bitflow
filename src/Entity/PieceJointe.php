<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\PieceJointeRepository;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\HttpFoundation\File\File;

use Doctrine\Common\Collections\ArrayCollection;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\UploadedFile;

#[ORM\Entity(repositoryClass: PieceJointeRepository::class)]
/**
 * @ORM\Cache(usage="READ_ONLY", region="my_region")
 */
#[Vich\Uploadable]
class PieceJointe
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private $id;

    #[ORM\ManyToOne(targetEntity: Message::class, inversedBy: "pieceJointes", cascade: ["persist"])]
    #[ORM\JoinColumn(nullable: false)]
    private $message;

    /* # [ORM\OneToOne(targetEntity: Message::class, inversedBy: "lettreTransmission", cascade: ["persist"])]
    # [ORM\JoinColumn(nullable: true)]*/
    #[ORM\OneToOne(mappedBy: 'lettreTransmission', cascade: ['persist', 'remove'])]
    private  ?Message $messageLt=null;

    #[ORM\Column(type: "string", length: 200)]
    private $path;

    #[ORM\Column(type: "string", length: 200, nullable: true)]
    private $libelle;

    #[ORM\Column(type: "string", length: 200, nullable: true)]
    private $type;

    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     * @var File|null
     */
    #[Vich\UploadableField(mapping: "piece_jointe", fileNameProperty: "path")]
    private $file;

    private $fileName;

    #[ORM\Column(type: "string", length: 255)]
    private $originalFileName;

     /**
      * Annee de stockage
      */
    #[ORM\Column(type: "integer", nullable: true)]
    private $year;

    /**
     * Mois de stockage
     */
    #[ORM\Column(type: "integer", nullable: true)]
    private $month;
    /**
    * leprefixe du fichier numAgrement ou guichet
      */
    #[ORM\Column(type: "string", length: 50, nullable: true)]
    private $prefix;

 /*   #[ORM\OneToOne(mappedBy: 'lt', cascade: ['persist', 'remove'])]
    private ?Message $messlt = null;*/

    // / * *
    //  * @ ORM\OneToOne(targetEntity=Message::class, mappedBy="lettreTransmission", cascade={"persist", "remove"})
    //  * /
    // private $messageLettreTransmission;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMessageLt(): ?Message
    {
        return $this->messageLt;
    }

   /* public function setMessageLt(?Message $messageLt): self
    {
        $this->messageLt = $messageLt;

        return $this;
    }*/

     public function setMessageLt(?Message $messageLt): static
    {
        // unset the owning side of the relation if necessary
        if ($messageLt === null && $this->messageLt !== null) {
            $this->messageLt->setLettreTransmission(null);
        }

        // set the owning side of the relation if necessary
        if ($messageLt !== null && $messageLt->getLettreTransmission() !== $this) {
            $messageLt->setLettreTransmission($this);
        }

        $this->messageLt = $messageLt;

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

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(?string $libelle): self
    {
        $this->libelle = $libelle;

        return $this;
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



    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile|null $imageFile
     */
    public function setFile(?File $file = null)
    {
        $this->file = $file;
    }

    /**
     * Get file.
     *
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }

    public function getAbsolutePath()
    {
        return null === $this->path
            ? null
            : $this->getUploadRootDir() . '/' . $this->path;
    }

    public function getWebPath()
    {
        return null === $this->path
            ? null
            : $this->getUploadDir() . '/' . $this->path;
    }

    protected function getUploadRootDir()
    {
        // the absolute directory path where uploaded
        // documents should be saved
        return __DIR__ . '/../../../../web/' . $this->getUploadDir();
    }

    protected function getUploadDir()
    {
        // get rid of the __DIR__ so it doesn't screw up
        // when displaying uploaded doc/image in the view.
        return 'uploads/documents';
    }

    public function getFileName(): ?string
    {
        return $this->fileName;
    }

    public function setFileName(?string $fileName): self
    {
        $this->fileName = $fileName;

        return $this;
    }

    public function getOriginalFileName(): ?string
    {
        return $this->originalFileName;
    }

    public function setOriginalFileName(string $originalFileName): self
    {
        $this->originalFileName = $originalFileName;

        return $this;
    }

     public function getYear(): ?int
    {
        return $this->year;
    }

    public function setYear(int $year): self
    {
        $this->year = $year;

        return $this;
    }

    public function getMonth(): ?int
    {
        return $this->month;
    }

    public function setMonth(int $month): self
    {
        $this->month = $month;

        return $this;
    }

     public function getPrefix(): ?string
    {
        return $this->prefix;
    }

    public function setPrefix(string $prefix): self
    {
        $this->prefix = $prefix;

        return $this;
    }

    // public function getMessageLettreTransmission(): ?Message
    // {
    //     return $this->messageLettreTransmission;
    // }

    // public function setMessageLettreTransmission(Message $messageLettreTransmission): self
    // {
    //     // set the owning side of the relation if necessary
    //     if ($messageLettreTransmission->getLettreTransmission() !== $this) {
    //         $messageLettreTransmission->setLettreTransmission($this);
    //     }

    //     $this->messageLettreTransmission = $messageLettreTransmission;

    //     return $this;
    // }

    /*public function getMesslt(): ?Message
    {
        return $this->messlt;
    }

    public function setMesslt(?Message $messlt): static
    {
        // unset the owning side of the relation if necessary
        if ($messlt === null && $this->messlt !== null) {
            $this->messlt->setLt(null);
        }

        // set the owning side of the relation if necessary
        if ($messlt !== null && $messlt->getLt() !== $this) {
            $messlt->setLt($this);
        }

        $this->messlt = $messlt;

        return $this;
    }*/

}
