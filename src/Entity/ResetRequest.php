<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ResetRequestRepository")
 * @ORM\Table(name="reset_request", schema="mo")
 */
class ResetRequest
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Usersite")
     * @ORM\JoinColumn(nullable=false, name="id_usersite", referencedColumnName="id")
     */
    private $idUsersite;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $tokenRequest;

    /**
     * @ORM\Column(type="datetime")
     */
    private $requestDate;

    public function __construct()
    {
        $this->requestDate = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdUsersite(): ?Usersite
    {
        return $this->idUsersite;
    }

    public function setIdUsersite(?Usersite $idUsersite): self
    {
        $this->idUsersite = $idUsersite;

        return $this;
    }

    public function getTokenRequest(): ?string
    {
        return $this->tokenRequest;
    }

    public function setTokenRequest(string $tokenRequest): self
    {
        $this->tokenRequest = $tokenRequest;

        return $this;
    }

    public function getRequestDate(): ?\DateTimeInterface
    {
        return $this->requestDate;
    }

    public function setRequestDate(\DateTimeInterface $requestDate): self
    {
        $this->requestDate = $requestDate;

        return $this;
    }
}
