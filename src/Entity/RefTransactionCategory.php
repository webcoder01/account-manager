<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RefTransactionCategoryRepository")
 * @ORM\Table(name="ref_transaction_category", schema="mo")
 */
class RefTransactionCategory
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $labelName;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\RefTransactionType", mappedBy="idRefTransactionCategory")
     */
    private $refTransactionTypes;

    public function __construct()
    {
        $this->refTransactionTypes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabelName(): ?string
    {
        return $this->labelName;
    }

    public function setLabelName(string $labelName): self
    {
        $this->labelName = $labelName;

        return $this;
    }

    /**
     * @return Collection|RefTransactionType[]
     */
    public function getRefTransactionTypes(): Collection
    {
        return $this->refTransactionTypes;
    }

    public function addRefTransactionType(RefTransactionType $refTransactionType): self
    {
        if (!$this->refTransactionTypes->contains($refTransactionType)) {
            $this->refTransactionTypes[] = $refTransactionType;
            $refTransactionType->setIdRefTransactionCategory($this);
        }

        return $this;
    }

    public function removeRefTransactionType(RefTransactionType $refTransactionType): self
    {
        if ($this->refTransactionTypes->contains($refTransactionType)) {
            $this->refTransactionTypes->removeElement($refTransactionType);
            // set the owning side to null (unless already changed)
            if ($refTransactionType->getIdRefTransactionCategory() === $this) {
                $refTransactionType->setIdRefTransactionCategory(null);
            }
        }

        return $this;
    }
}
