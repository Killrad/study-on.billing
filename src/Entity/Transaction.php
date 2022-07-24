<?php

namespace App\Entity;

use App\Repository\TransactionRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TransactionRepository::class)
 */
class Transaction
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="transactions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $billing_user;

    /**
     * @ORM\ManyToOne(targetEntity=Course::class, inversedBy="transactions")
     */
    private $Course;

    /**
     * @ORM\Column(type="smallint")
     */
    private $Type;

    /**
     * @ORM\Column(type="float")
     */
    private $Value;

    /**
     * @ORM\Column(type="datetime")
     */
    private $Datetime_transaction;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $End_datetime;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBillingUser(): ?User
    {
        return $this->billing_user;
    }

    public function setBillingUser(?User $billing_user): self
    {
        $this->billing_user = $billing_user;

        return $this;
    }

    public function getCourse(): ?Course
    {
        return $this->Course;
    }

    public function setCourse(?Course $Course): self
    {
        $this->Course = $Course;

        return $this;
    }

    public function getType(): ?string
    {
        $ans = 'payment';
        if ($this->Type == 1) $ans = 'deposit';
        return $ans;
    }

    public function setType(int $Type): self
    {
        $this->Type = $Type;

        return $this;
    }

    public function getValue(): ?float
    {
        return $this->Value;
    }

    public function setValue(float $Value): self
    {
        $this->Value = $Value;

        return $this;
    }

    public function getDatetimeTransaction(): ?\DateTimeInterface
    {
        return $this->Datetime_transaction;
    }

    public function setDatetimeTransaction(\DateTimeInterface $Datetime_transaction): self
    {
        $this->Datetime_transaction = $Datetime_transaction;

        return $this;
    }

    public function getEndDatetime(): ?\DateTimeInterface
    {
        return $this->End_datetime;
    }

    public function setEndDatetime(?\DateTimeInterface $End_datetime): self
    {
        $this->End_datetime = $End_datetime;

        return $this;
    }

}
