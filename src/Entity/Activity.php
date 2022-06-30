<?php

namespace App\Entity;

use App\Repository\ActivityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ActivityRepository::class)
 */
class Activity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $is_free;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $picture;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $worker;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $places_total;

    /**
     * @ORM\Column(type="integer", options={"default" : 0}, nullable=true)
     */
    private $places_taken;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $schedule;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $start_date;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $end_date;

    /**
     * @ORM\Column(type="boolean", options={"default" : 1}, nullable=true)
     */
    private $is_visible;

    /**
     * @ORM\Column(type="boolean", options={"default" : 0}, nullable=true)
     */
    private $is_deleted;

    /**
     * @ORM\ManyToOne(targetEntity=Entity::class, inversedBy="activities")
     * @ORM\JoinColumn(nullable=true)
     */
    private $entity;

    /**
     * @ORM\ManyToOne(targetEntity=AgeRange::class, inversedBy="activity")
     */
    private $ageRange;

    /**
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="activity")
     */
    private $category;

    /**
     * @ORM\OneToMany(targetEntity=Ticket::class, mappedBy="activity", orphanRemoval=true)
     */
    private $tickets;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $is_morinig;

    public function __construct()
    {
        $this->tickets = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get the value of is_free
     */ 
    public function getIsFree()
    {
        return $this->is_free;
    }

    /**
     * Set the value of is_free
     *
     * @return  self
     */ 
    public function setIsFree($is_free)
    {
        $this->is_free = $is_free;

        return $this;
    }

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(?string $picture): self
    {
        $this->picture = $picture;

        return $this;
    }

    public function getWorker(): ?string
    {
        return $this->worker;
    }

    public function setWorker(?string $worker): self
    {
        $this->worker = $worker;

        return $this;
    }

        /**
     * Get the value of places_taken
     */ 
    public function getPlacesTaken()
    {
        return $this->places_taken;
    }

    /**
     * Set the value of places_taken
     *
     * @return  self
     */ 
    public function setPlacesTaken($places_taken)
    {
        $this->places_taken = $places_taken;

        return $this;
    }

    /**
     * Get the value of places_total
     */ 
    public function getPlacesTotal()
    {
        return $this->places_total;
    }

    /**
     * Set the value of places_total
     *
     * @return  self
     */ 
    public function setPlacesTotal($places_total)
    {
        $this->places_total = $places_total;

        return $this;
    }

    public function getSchedule(): ?string
    {
        return $this->schedule;
    }

    public function setSchedule(string $schedule): self
    {
        $this->schedule = $schedule;

        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->start_date;
    }

    public function setStartDate(\DateTimeInterface $start_date): self
    {
        $this->start_date = $start_date;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->end_date;
    }

    public function setEndDate(\DateTimeInterface $end_date): self
    {
        $this->end_date = $end_date;

        return $this;
    }

    public function isIsVisible(): ?bool
    {
        return $this->is_visible;
    }

    public function setIsVisible(bool $is_visible): self
    {
        $this->is_visible = $is_visible;

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

    public function getEntity(): ?Entity
    {
        return $this->entity;
    }

    public function setEntity(?Entity $entity): self
    {
        $this->entity = $entity;

        return $this;
    }

    public function getAgeRange(): ?AgeRange
    {
        return $this->ageRange;
    }

    public function setAgeRange(?AgeRange $ageRange): self
    {
        $this->ageRange = $ageRange;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection<int, Ticket>
     */
    public function getTickets(): Collection
    {
        return $this->tickets;
    }

    public function addTicket(Ticket $ticket): self
    {
        if (!$this->tickets->contains($ticket)) {
            $this->tickets[] = $ticket;
            $ticket->setActivity($this);
        }

        return $this;
    }

    public function removeTicket(Ticket $ticket): self
    {
        if ($this->tickets->removeElement($ticket)) {
            // set the owning side to null (unless already changed)
            if ($ticket->getActivity() === $this) {
                $ticket->setActivity(null);
            }
        }

        return $this;
    }

    public function isIsMorinig(): ?bool
    {
        return $this->is_morinig;
    }

    public function setIsMorinig(?bool $is_morinig): self
    {
        $this->is_morinig = $is_morinig;

        return $this;
    }

}
