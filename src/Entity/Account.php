<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AccountRepository")
 * @ORM\Table(name="account", schema="mo")
 */
class Account
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $labelName;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Usersite", inversedBy="accounts")
     * @ORM\JoinColumn(nullable=false, name="id_usersite", referencedColumnName="id")
     */
    private $idUsersite;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isFavorite;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Transaction", mappedBy="idAccount")
     */
    private $transactions;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Income", mappedBy="idAccount")
     */
    private $incomes;

    public function __construct()
    {
        $this->transactions = new ArrayCollection();
        $this->incomes = new ArrayCollection();
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

    public function getIdUsersite(): ?Usersite
    {
        return $this->idUsersite;
    }

    public function setIdUsersite(?Usersite $idUsersite): self
    {
        $this->idUsersite = $idUsersite;

        return $this;
    }

    public function getIsFavorite(): ?bool
    {
        return $this->isFavorite;
    }

    public function setIsFavorite(bool $isFavorite): self
    {
        $this->isFavorite = $isFavorite;

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
            $transaction->setIdAccount($this);
        }

        return $this;
    }

    public function removeTransaction(Transaction $transaction): self
    {
        if ($this->transactions->contains($transaction)) {
            $this->transactions->removeElement($transaction);
            // set the owning side to null (unless already changed)
            if ($transaction->getIdAccount() === $this) {
                $transaction->setIdAccount(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Income[]
     */
    public function getIncomes(): Collection
    {
        return $this->incomes;
    }

    public function addIncome(Income $income): self
    {
        if (!$this->incomes->contains($income)) {
            $this->incomes[] = $income;
            $income->setIdAccount($this);
        }

        return $this;
    }

    public function removeIncome(Income $income): self
    {
        if ($this->incomes->contains($income)) {
            $this->incomes->removeElement($income);
            // set the owning side to null (unless already changed)
            if ($income->getIdAccount() === $this) {
                $income->setIdAccount(null);
            }
        }

        return $this;
    }
}
