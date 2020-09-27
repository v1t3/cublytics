<?php

namespace App\Entity;

use App\Repository\LogRepository;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=LogRepository::class)
 */
class Log
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $date;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $type;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $status;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $error;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $user;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $reg_code;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $token;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $channel;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $coub;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $statistic_type;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $date_create;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $date_update;

    /**
     * Log constructor.
     *
     * @throws \Exception
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
     * @return DateTimeInterface|null
     */
    public function getDate(): ?DateTimeInterface
    {
        return $this->date;
    }

    /**
     * @param DateTimeInterface|null $date
     *
     * @return $this
     */
    public function setDate(?DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @param string|null $type
     *
     * @return $this
     */
    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getStatus(): ?string
    {
        return $this->status;
    }

    /**
     * @param bool|null $status
     *
     * @return $this
     */
    public function setStatus(?bool $status): self
    {
        $this->status = $status;

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
     */
    public function setUser(string $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getRegCode(): ?string
    {
        return $this->reg_code;
    }

    public function setRegCode(?string $reg_code): self
    {
        $this->reg_code = $reg_code;

        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(?string $token): self
    {
        $this->token = $token;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getChannel(): ?string
    {
        return $this->channel;
    }

    /**
     * @param string|null $channel
     *
     * @return $this
     */
    public function setChannel(?string $channel): self
    {
        $this->channel = $channel;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCoub(): ?string
    {
        return $this->coub;
    }

    /**
     * @param string|null $coub
     *
     * @return $this
     */
    public function setCoub(?string $coub): self
    {
        $this->coub = $coub;

        return $this;
    }

    public function getStatisticType(): ?string
    {
        return $this->statistic_type;
    }

    public function setStatisticType(?string $statistic_type): self
    {
        $this->statistic_type = $statistic_type;

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
     * @throws \Exception
     */
    public function setDateCreate(): self
    {
        if (!$this->date_create) {
            $this->date_create = new \DateTime();

            $this->setDateUpdate();
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
     * @throws \Exception
     */
    public function setDateUpdate(): self
    {
        $this->date_update = new \DateTime();

        return $this;
    }
}
