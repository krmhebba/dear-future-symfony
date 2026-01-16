<?php

namespace App\Entity;

use App\Repository\LetterRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[ORM\Entity(repositoryClass: LetterRepository::class)]
class Letter
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le titre est obligatoire.")]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: "Vous devez écrire un message pour votre futur vous !")]
    private ?string $content = null;

    #[ORM\Column]
    #[Assert\NotNull(message: "La date est obligatoire.")]
    #[Assert\GreaterThan("today", message: "La date d'ouverture doit être dans le futur !")]
    private ?\DateTimeImmutable $sendDate = null;

    #[ORM\Column]
    private ?bool $isSent = null;

    #[ORM\ManyToOne(inversedBy: 'letters')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $author = null;

    #[ORM\ManyToOne]
    private ?Product $product = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $deliveryAddress = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $city = null;

    #[ORM\Column(length: 20, nullable: true)]
    #[Assert\Regex(pattern: "/^[0-9\s\+]+$/", message: "Le numéro de téléphone n'est pas valide.")]
    private ?string $phoneNumber = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): static
    {
        $this->title = $title;
        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): static
    {
        $this->content = $content;
        return $this;
    }

    public function getSendDate(): ?\DateTimeImmutable
    {
        return $this->sendDate;
    }

    public function setSendDate(?\DateTimeImmutable $sendDate): static
    {
        $this->sendDate = $sendDate;
        return $this;
    }

    public function isSent(): ?bool
    {
        return $this->isSent;
    }

    public function setIsSent(bool $isSent): static
    {
        $this->isSent = $isSent;
        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): static
    {
        $this->author = $author;
        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): static
    {
        $this->product = $product;
        return $this;
    }

    public function getDeliveryAddress(): ?string
    {
        return $this->deliveryAddress;
    }

    public function setDeliveryAddress(?string $deliveryAddress): static
    {
        $this->deliveryAddress = $deliveryAddress;
        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): static
    {
        $this->city = $city;
        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(?string $phoneNumber): static
    {
        $this->phoneNumber = $phoneNumber;
        return $this;
    }

    public function getSendDateString(): string
    {
        if ($this->sendDate === null) {
            return '';
        }
        return $this->sendDate->format('d/m/Y');
    }

    
    #[Assert\Callback]
    public function validateDeliveryDetails(ExecutionContextInterface $context): void
    {
        if ($this->getProduct() === null) {
            return;
        }

        if (empty($this->getDeliveryAddress())) {
            $context->buildViolation('Puisque vous avez choisi un cadeau, l\'adresse est obligatoire !')
                ->atPath('deliveryAddress')
                ->addViolation();
        }

        if (empty($this->getCity())) {
            $context->buildViolation('La ville est obligatoire pour la livraison.')
                ->atPath('city')
                ->addViolation();
        }

        if (empty($this->getPhoneNumber())) {
            $context->buildViolation('Le téléphone est obligatoire pour le livreur.')
                ->atPath('phoneNumber')
                ->addViolation();
        }
    }
}

