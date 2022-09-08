<?php

namespace App\Entity;

use App\Repository\TicketRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TicketRepository::class)
 */
class Ticket
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Activity::class, inversedBy="tickets")
     * @ORM\JoinColumn(nullable=true)
     */
    private $activity;

    /**
     * @ORM\ManyToOne(targetEntity=Participant::class, inversedBy="tickets")
     * @ORM\JoinColumn(nullable=true)
     */
    private $participant;

    /**
     * @ORM\ManyToOne(targetEntity=TicketStatus::class, inversedBy="ticket")
     * @ORM\JoinColumn(nullable=true)
     */
    private $ticketStatus;

    /**
     * @ORM\Column(type="boolean")
     */
    private $is_deleted;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $is_waiting_list;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="tickets")
     */
    private $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getActivity(): ?Activity
    {
        return $this->activity;
    }

    public function setActivity(?Activity $activity): self
    {
        $this->activity = $activity;

        return $this;
    }

    public function getParticipant(): ?Participant
    {
        return $this->participant;
    }

    public function setParticipant(?Participant $participant): self
    {
        $this->participant = $participant;

        return $this;
    }

    public function getTicketStatus(): ?TicketStatus
    {
        return $this->ticketStatus;
    }

    public function setTicketStatus(?TicketStatus $ticketStatus): self
    {
        $this->ticketStatus = $ticketStatus;

        return $this;
    }

    public function isIsDeleted(): ?bool
    {
        return $this->is_deleted;
    }

    public function setIsDeleted(bool $is_deleted): self
    {
        $this->is_deleted = $is_deleted;

        return $this;
    }

    public function isIsWaitingList(): ?bool
    {
        return $this->is_waiting_list;
    }

    public function setIsWaitingList(?bool $is_waiting_list): self
    {
        $this->is_waiting_list = $is_waiting_list;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
