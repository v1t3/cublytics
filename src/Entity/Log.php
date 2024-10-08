<?php

namespace App\Entity;

use App\Repository\LogRepository;
use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Exception;

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
     * @ORM\Column(type="integer", nullable=true)
     */
    private $channel_id;

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
     * @ORM\Column(type="integer", nullable=true)
     */
    private $coub_id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * Log constructor.
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
     * @throws Exception
     */
    public function setDate(?DateTimeInterface $date = null): self
    {
        if (!$date) {
            $date = new DateTime('now', new \DateTimeZone('Europe/London'));
        }

        $this->date = $date;

        if (!$this->date_update) {
            $this->setDateUpdate();
        }

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
     * @throws Exception
     */
    public function setType(?string $type): self
    {
        $this->type = $type;

        if (!$this->date_update) {
            $this->setDateUpdate();
        }

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
     * @param bool|null $status
     *
     * @return $this
     * @throws Exception
     */
    public function setStatus(?bool $status): self
    {
        $this->status = $status;

        if (!$this->date_update) {
            $this->setDateUpdate();
        }

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
     * @throws Exception
     */
    public function setError(?string $error): self
    {
        $this->error = $error;

        if (!$this->date_update) {
            $this->setDateUpdate();
        }

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
     * @return string|null
     */
    public function getRegCode(): ?string
    {
        return $this->reg_code;
    }

    /**
     * @param string|null $reg_code
     *
     * @return $this
     * @throws Exception
     */
    public function setRegCode(?string $reg_code): self
    {
        $this->reg_code = $reg_code;

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
     * @throws Exception
     */
    public function setChannel(?string $channel): self
    {
        $this->channel = $channel;

        if (!$this->date_update) {
            $this->setDateUpdate();
        }

        return $this;
    }

    /**
     * @return int|null
     */
    public function getChannelId(): ?int
    {
        return $this->channel_id;
    }

    /**
     * @param int|null $channel_id
     *
     * @return $this
     * @throws Exception
     */
    public function setChannelId(?int $channel_id): self
    {
        $this->channel_id = $channel_id;

        if (!$this->date_update) {
            $this->setDateUpdate();
        }

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
     * @throws Exception
     */
    public function setCoub(?string $coub): self
    {
        $this->coub = $coub;

        if (!$this->date_update) {
            $this->setDateUpdate();
        }

        return $this;
    }

    /**
     * @return string|null
     */
    public function getStatisticType(): ?string
    {
        return $this->statistic_type;
    }

    /**
     * @param string|null $statistic_type
     *
     * @return $this
     * @throws Exception
     */
    public function setStatisticType(?string $statistic_type): self
    {
        $this->statistic_type = $statistic_type;

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
            $this->date_create = new DateTime('now', new \DateTimeZone('Europe/London'));

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
        $this->date_update = new DateTime('now', new \DateTimeZone('Europe/London'));

        return $this;
    }

    /**
     * @return int|null
     */
    public function getCoubId(): ?int
    {
        return $this->coub_id;
    }

    /**
     * @param int|null $coub_id
     *
     * @return $this
     * @throws Exception
     */
    public function setCoubId(?int $coub_id): self
    {
        $this->coub_id = $coub_id;

        if (!$this->date_update) {
            $this->setDateUpdate();
        }

        return $this;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string|null $email
     *
     * @return $this
     * @throws Exception
     */
    public function setEmail(?string $email): self
    {
        $this->email = $email;

        if (!$this->date_update) {
            $this->setDateUpdate();
        }

        return $this;
    }
}
