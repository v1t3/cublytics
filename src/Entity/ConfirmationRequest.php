<?php

namespace App\Entity;

use App\Repository\ConfirmationRequestRepository;
use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Exception;

/**
 * @ORM\Entity(repositoryClass=ConfirmationRequestRepository::class)
 */
class ConfirmationRequest
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $owner_id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $code;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $confirmed;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $requested_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $expires_at;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getOwnerId()
    {
        return $this->owner_id;
    }

    /**
     * @param User $owner_id
     *
     * @return $this
     */
    public function setOwnerId(User $owner_id): self
    {
        $this->owner_id = $owner_id;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCode(): ?string
    {
        return $this->code;
    }

    /**
     * @param string|null $code
     *
     * @return $this
     */
    public function setCode(?string $code): self
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getConfirmed(): ?bool
    {
        return $this->confirmed;
    }

    /**
     * @param bool|null $confirmed
     *
     * @return $this
     */
    public function setConfirmed(?bool $confirmed): self
    {
        $this->confirmed = $confirmed;

        return $this;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getRequestedAt(): ?DateTimeInterface
    {
        return $this->requested_at;
    }

    /**
     * @param DateTimeInterface|null $requested_at
     *
     * @return $this
     * @throws Exception
     */
    public function setRequestedAt(?DateTimeInterface $requested_at = null): self
    {
        if (!$requested_at) {
            $requested_at = new DateTime();
        }

        $this->requested_at = $requested_at;

        return $this;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getExpiresAt(): ?DateTimeInterface
    {
        return $this->expires_at;
    }

    /**
     * @param DateTimeInterface|null $expires_at
     *
     * @return $this
     */
    public function setExpiresAt(?DateTimeInterface $expires_at): self
    {
        $this->expires_at = $expires_at;

        return $this;
    }
}
