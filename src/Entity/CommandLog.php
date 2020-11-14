<?php

namespace App\Entity;

use App\Repository\CommandLogRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CommandLogRepository::class)
 */
class CommandLog
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?DateTime $date;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $command;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $status;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $message;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $error;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?DateTime $date_create;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?DateTime $date_update;

    /**
     * CommandLog constructor.
     *
     * @throws \Exception
     */
    public function __construct()
    {
        $this->setDateCreate(new DateTime());
        $this->setDateUpdate(new DateTime());
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return \DateTime|null
     */
    public function getDate(): ?DateTime
    {
        return $this->date;
    }

    /**
     * @param \DateTime|null $date
     *
     * @return $this
     * @throws \Exception
     */
    public function setDate(?DateTime $date = null): self
    {
        if (!$date) {
            $date = new DateTime();
        }
        $this->date = $date;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCommand(): ?string
    {
        return $this->command;
    }

    /**
     * @param string $command
     *
     * @return $this
     */
    public function setCommand(string $command): self
    {
        $this->command = $command;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getStatus(): ?bool
    {
        return $this->status;
    }

    /**
     * @param bool $status
     *
     * @return $this
     */
    public function setStatus(bool $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getMessage(): ?string
    {
        return $this->message;
    }

    /**
     * @param string|null $message
     *
     * @return $this
     */
    public function setMessage(?string $message): self
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getError(): ?string
    {
        return $this->error;
    }

    /**
     * @param string|null $error
     *
     * @return $this
     */
    public function setError(?string $error): self
    {
        $this->error = $error;

        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getDateCreate(): ?DateTime
    {
        return $this->date_create;
    }

    /**
     * @param \DateTime|null $date_create
     *
     * @return $this
     */
    public function setDateCreate(?DateTime $date_create): self
    {
        $this->date_create = $date_create;

        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getDateUpdate(): ?DateTime
    {
        return $this->date_update;
    }

    /**
     * @param \DateTime|null $date_update
     *
     * @return $this
     */
    public function setDateUpdate(?DateTime $date_update): self
    {
        $this->date_update = $date_update;

        return $this;
    }
}
