<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RefTransactionTypeRepository")
 * @ORM\Table(name="ref_transaction_type", schema="mo")
 */
class RefTransactionType
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
     * @ORM\ManyToOne(targetEntity="App\Entity\RefTransactionCategory", inversedBy="refTransactionTypes")
     * @ORM\JoinColumn(nullable=false, name="id_ref_transaction_category")
     */
    private $idRefTransactionCategory;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Transaction", mappedBy="idRefTransactionType")
     */
    private $transactions;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Budget", mappedBy="idRefTransactionType")
     */
    private $idBudget;

    public function __construct()
    {
        $this->transactions = new ArrayCollection();
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

    public function getIdRefTransactionCategory(): ?RefTransactionCategory
    {
        return $this->idRefTransactionCategory;
    }

    public function setIdRefTransactionCategory(?RefTransactionCategory $idRefTransactionCategory): self
    {
        $this->idRefTransactionCategory = $idRefTransactionCategory;

        return $this;
    }

    /**
     * @return Collection|Transaction[]
     */
    public function getTransactions(): Collection
    {
        return $this->transactions;
    }

    public function addTransaction(Transaction $transaction): self
    {
        if (!$this->transactions->contains($transaction)) {
            $this->transactions[] = $transaction;
            $transaction->setIdRefTransactionType($this);
        }

        return $this;
    }

    public function removeTransaction(Transaction $transaction): self
    {
        if ($this->transactions->contains($transaction)) {
            $this->transactions->removeElement($transaction);
            // set the owning side to null (unless already changed)
            if ($transaction->getIdRefTransactionType() === $this) {
                $transaction->setIdRefTransactionType(null);
            }
        }

        return $this;
    }

    public function getIdBudget(): ?Budget
    {
        return $this->idBudget;
    }
    
    public function setIdBudget(?Budget $idBudget): self
    {
        $this->idBudget = $idBudget;
        
        return $this;
    }
}
