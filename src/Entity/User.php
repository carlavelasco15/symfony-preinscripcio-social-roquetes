<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;
    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $username;
    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];
    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;
    /**
     * @ORM\ManyToMany(targetEntity=Entity::class, inversedBy="users")
     */
    private $equipment;
    /**
     * @ORM\Column(type="string", length=180, nullable=true)
     */
    private $email;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isVerified = false;

    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     */
    private $picture;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $email_ent_new_prescription;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $email_ent_waiting_list;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $email_ent_finished_activity;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $email_ent_attendance_form;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $email_ent_participant_rating;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $email_ser_new_activity;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $email_ser_attendance_form;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $email_ser_deleted_activity;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $email_ser_from_waiting_to_open;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $email_ser_activity_ended;
    
    public function __construct()
    {
        $this->equipment = new ArrayCollection();
    }
    public function getId(): ?int
    {
        return $this->id;
    }
    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->username;
    }
    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }
    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }
    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }
    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }
    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }
    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }
    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }
    /**
     * @return Collection<int, Entity>
     */
    public function getEquipment(): Collection
    {
        return $this->equipment;
    }
    public function addEquipment(Entity $equipment): self
    {
        if (!$this->equipment->contains($equipment)) {
            $this->equipment[] = $equipment;
        }

        return $this;
    }
    public function removeEquipment(Entity $equipment): self
    {
        $this->equipment->removeElement($equipment);

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

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

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

    public function isEmailEntNewPrescription(): ?bool
    {
        return $this->email_ent_new_prescription;
    }

    public function setEmailEntNewPrescription(?bool $email_ent_new_prescription): self
    {
        $this->email_ent_new_prescription = $email_ent_new_prescription;

        return $this;
    }

    public function isEmailEntWaitingList(): ?bool
    {
        return $this->email_ent_waiting_list;
    }

    public function setEmailEntWaitingList(?bool $email_ent_waiting_list): self
    {
        $this->email_ent_waiting_list = $email_ent_waiting_list;

        return $this;
    }

    public function isEmailEntFinishedActivity(): ?bool
    {
        return $this->email_ent_finished_activity;
    }

    public function setEmailEntFinishedActivity(?bool $email_ent_finished_activity): self
    {
        $this->email_ent_finished_activity = $email_ent_finished_activity;

        return $this;
    }

    public function isEmailEntAttendanceForm(): ?bool
    {
        return $this->email_ent_attendance_form;
    }

    public function setEmailEntAttendanceForm(?bool $email_ent_attendance_form): self
    {
        $this->email_ent_attendance_form = $email_ent_attendance_form;

        return $this;
    }

    public function isEmailEntParticipantRating(): ?bool
    {
        return $this->email_ent_participant_rating;
    }

    public function setEmailEntParticipantRating(?bool $email_ent_participant_rating): self
    {
        $this->email_ent_participant_rating = $email_ent_participant_rating;

        return $this;
    }

    public function isEmailSerNewActivity(): ?bool
    {
        return $this->email_ser_new_activity;
    }

    public function setEmailSerNewActivity(?bool $email_ser_new_activity): self
    {
        $this->email_ser_new_activity = $email_ser_new_activity;

        return $this;
    }

    public function isEmailSerAttendanceForm(): ?bool
    {
        return $this->email_ser_attendance_form;
    }

    public function setEmailSerAttendanceForm(?bool $email_ser_attendance_form): self
    {
        $this->email_ser_attendance_form = $email_ser_attendance_form;

        return $this;
    }

    public function isEmailSerDeletedActivity(): ?bool
    {
        return $this->email_ser_deleted_activity;
    }

    public function setEmailSerDeletedActivity(?bool $email_ser_deleted_activity): self
    {
        $this->email_ser_deleted_activity = $email_ser_deleted_activity;

        return $this;
    }

    public function isEmailSerFromWaitingToOpen(): ?bool
    {
        return $this->email_ser_from_waiting_to_open;
    }

    public function setEmailSerFromWaitingToOpen(?bool $email_ser_from_waiting_to_open): self
    {
        $this->email_ser_from_waiting_to_open = $email_ser_from_waiting_to_open;

        return $this;
    }

    public function isEmailSerActivityEnded(): ?bool
    {
        return $this->email_ser_activity_ended;
    }

    public function setEmailSerActivityEnded(?bool $email_ser_activity_ended): self
    {
        $this->email_ser_activity_ended = $email_ser_activity_ended;

        return $this;
    }

}
