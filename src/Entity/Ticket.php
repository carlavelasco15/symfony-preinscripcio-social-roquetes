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
}
