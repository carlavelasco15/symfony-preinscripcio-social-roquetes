<?php

namespace App\Entity;

use App\Repository\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=EntityRepository::class)
 */
class Entity
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
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $location;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $phone;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $web;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $created_at;

    /**
     * @ORM\OneToMany(targetEntity=Activity::class, mappedBy="entity")
     */
    private $activities;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $schedule;

    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     */
    private $picture;

    /**
     * @ORM\OneToMany(targetEntity=UserEntity::class, mappedBy="entity")
     */
    private $userEntities;

    /**
     * @ORM\OneToMany(targetEntity=Participant::class, mappedBy="entity")
     */
    private $participants;

    public function __construct()
    {
        $this->activities = new ArrayCollection();
        $this->userEntities = new ArrayCollection();
        $this->participants = new ArrayCollection();
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

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(?string $location): self
    {
        $this->location = $location;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getWeb(): ?string
    {
        return $this->web;
    }

    public function setWeb(?string $web): self
    {
        $this->web = $web;

        return $this;
    }

    /**
     * @return Collection<int, Activity>
     */
    public function getActivities(): Collection
    {
        return $this->activities;
    }

    public function addActivity(Activity $activity): self
    {
        if (!$this->activities->contains($activity)) {
            $this->activities[] = $activity;
            $activity->setEntity($this);
        }

        return $this;
    }

    public function removeActivity(Activity $activity): self
    {
        if ($this->activities->removeElement($activity)) {
            // set the owning side to null (unless already changed)
            if ($activity->getEntity() === $this) {
                $activity->setEntity(null);
            }
        }

        return $this;
    }

    public function getSchedule(): ?string
    {
        return $this->schedule;
    }

    public function setSchedule(?string $schedule): self
    {
        $this->schedule = $schedule;

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

    /**
     * @return Collection<int, UserEntity>
     */
    public function getUserEntities(): Collection
    {
        return $this->userEntities;
    }

    public function addUserEntity(UserEntity $userEntity): self
    {
        if (!$this->userEntities->contains($userEntity)) {
            $this->userEntities[] = $userEntity;
            $userEntity->setEntity($this);
        }

        return $this;
    }

    public function removeUserEntity(UserEntity $userEntity): self
    {
        if ($this->userEntities->removeElement($userEntity)) {
            // set the owning side to null (unless already changed)
            if ($userEntity->getEntity() === $this) {
                $userEntity->setEntity(null);
            }
        }

        return $this;
    }
/* 
    public function __toString() {
        return $this->id;
    }
 */

/**
 * @return Collection<int, Participant>
 */
public function getParticipants(): Collection
{
    return $this->participants;
}

public function addParticipant(Participant $participant): self
{
    if (!$this->participants->contains($participant)) {
        $this->participants[] = $participant;
        $participant->setEntity($this);
    }

    return $this;
}

public function removeParticipant(Participant $participant): self
{
    if ($this->participants->removeElement($participant)) {
        // set the owning side to null (unless already changed)
        if ($participant->getEntity() === $this) {
            $participant->setEntity(null);
        }
    }

    return $this;
}
}
