<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BudgetRepository")
 * @ORM\Table(name="budget", schema="mo")
 */
class Budget
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $labelName;

    /**
     * @ORM\Column(type="decimal", precision=8, scale=2)
     */
    private $amount;

    /**
     * @ORM\Column(type="integer")
     */
    private $dateMonth;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Account", inversedBy="budgets")
     * @ORM\JoinColumn(nullable=false, name="id_account", referencedColumnName="id")
     */
    private $idAccount;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\RefTransactionType", inversedBy="budgets")
     * @ORM\JoinColumn(nullable=false, name="id_ref_transaction_type", referencedColumnName="id")
     */
    private $idRefTransactionType;

    /**
     * @ORM\Column(type="integer")
     */
    private $dateYear;
    
    public function __construct()
    {
        $now = new \DateTime();
        
        $this->isActive = true;
        $this->dateMonth = intval($now->format('n'));
        $this->dateYear = intval($now->format('Y'));
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

    public function getAmount(): ?string
    {
        return $this->amount;
    }

    public function setAmount(string $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getDateMonth(): ?int
    {
        return $this->dateMonth;
    }

    public function setDateMonth(int $dateMonth): self
    {
        $this->dateMonth = $dateMonth;

        return $this;
    }

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getIdAccount(): ?Account
    {
        return $this->idAccount;
    }

    public function setIdAccount(?Account $idAccount): self
    {
        $this->idAccount = $idAccount;

        return $this;
    }

    public function getIdRefTransactionType(): ?RefTransactionType
    {
        return $this->idRefTransactionType;
    }

    public function setIdRefTransactionType(?RefTransactionType $idRefTransactionType): self
    {
        $this->idRefTransactionType = $idRefTransactionType;

        return $this;
    }

    public function getDateYear(): ?int
    {
        return $this->dateYear;
    }

    public function setDateYear(int $dateYear): self
    {
        $this->dateYear = $dateYear;

        return $this;
    }
}
