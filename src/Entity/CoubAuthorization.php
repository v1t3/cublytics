<?php

namespace App\Entity;

use App\Repository\CoubAuthorizationRepository;
use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Exception;

/**
 * @ORM\Entity(repositoryClass=CoubAuthorizationRepository::class)
 */
class CoubAuthorization
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $last_username;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $token;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $date_create;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $date_update;

    /**
     * CoubAuthorization constructor.
     *
     * @throws Exception
     */
    public function __construct()
    {
        $this->setDateCreate();
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
    public function getLastUsername(): ?string
    {
        return $this->last_username;
    }

    /**
     * @param string $last_username
     *
     * @return $this
     * @throws Exception
     */
    public function setLastUsername(string $last_username): self
    {
        $this->last_username = $last_username;

        if (!$this->date_update) {
            $this->setDateUpdate();
        }

        return $this;
    }

    /**
     * @return string|null
     */
    public function getToken(): ?string
    {
        return $this->token;
    }

    /**
     * @param string|null $token
     *
     * @return $this
     * @throws Exception
     */
    public function setToken(?string $token): self
    {
        $this->token = $token;

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
