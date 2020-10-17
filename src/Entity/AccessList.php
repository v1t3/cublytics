<?php

namespace App\Entity;

use App\Repository\AccessListRepository;
use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Exception;

/**
 * @ORM\Entity(repositoryClass=AccessListRepository::class)
 */
class AccessList
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
    private $user;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $active;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $is_registered;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $date_create;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $date_update;

    /**
     * Channel constructor.
     *
     * @throws Exception
     */
    public function __construct()
    {
        $this->setDateCreate();
        $this->setRequestedAt();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getUser(): ?string
    {
        return $this->user;
    }

    /**
     * @param string $user
     *
     * @return $this
     * @throws Exception
     */
    public function setUser(string $user): self
    {
        $this->user = $user;

        if (!$this->date_update) {
            $this->setDateUpdate();
        }

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getActive(): ?bool
    {
        return $this->active;
    }

    /**
     * @param bool|null $active
     *
     * @return $this
     * @throws Exception
     */
    public function setActive(?bool $active): self
    {
        $this->active = $active;

        if (!$this->date_update) {
            $this->setDateUpdate();
        }

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getIsRegistered(): ?bool
    {
        return $this->is_registered;
    }

    /**
     * @param bool|null $is_registered
     *
     * @return $this
     * @throws Exception
     */
    public function setIsRegistered(?bool $is_registered): self
    {
        $this->is_registered = $is_registered;

        if (!$this->date_update) {
            $this->setDateUpdate();
        }

        return $this;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getDateCreate(): ?DateTimeInterface
    {
        return $this->date_create;
    }

    /**
     * @return $this
     * @throws Exception
     */
    public function setDateCreate(): self
    {
        if (!$this->date_create) {
            $this->date_create = new DateTime();

            if (!$this->date_update) {
                $this->setDateUpdate();
            }
        }

        return $this;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getDateUpdate(): ?DateTimeInterface
    {
        return $this->date_update;
    }

    /**
     * @return $this
     * @throws Exception
     */
    public function setDateUpdate(): self
    {
        $this->date_update = new DateTime();

        return $this;
    }
}
