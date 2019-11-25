<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\IncomeRepository")
 * @ORM\Table(name="income", schema="mo")
 */
class Income
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
     * @ORM\Column(type="date")
     */
    private $actionDate;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Account", inversedBy="incomes")
     * @ORM\JoinColumn(nullable=false, name="id_account", referencedColumnName="id")
     */
    private $idAccount;

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

    public function getActionDate($expectDate = false)
    {
        if(null === $this->actionDate) {
            return null;
        }

        $date = $expectDate ? $this->actionDate : $this->actionDate->format('Y-m-d');

        return $date;
    }

    public function setActionDate($actionDate): self
    {
        if(is_string($actionDate)) {
            $actionDate = \DateTime::createFromFormat('Y-m-d', $actionDate);
        }

        $this->actionDate = $actionDate instanceof \DateTime ? $actionDate : new \DateTime();

        return $this;
    }

    public function getIdAccount(): ?Account
    {
        return $this->idAccount;
    }

    public function setIdAccount(Account $idAccount): self
    {
        $this->idAccount = $idAccount;

        return $this;
    }
}
