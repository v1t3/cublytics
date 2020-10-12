<?php

namespace App\Entity;

use App\Repository\CoubRepository;
use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Exception;

/**
 * @ORM\Entity(repositoryClass=CoubRepository::class)
 */
class Coub
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", unique=true)
     */
    private $coub_id;

    /**
     * @ORM\Column(type="integer")
     */
    private $channel_id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $permalink;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $title;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updated_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $deleted_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $date_create;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $date_update;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $is_kd;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $featured;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $banned;

    /**
     * @ORM\ManyToOne(targetEntity=Channel::class)
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $owner_id;

    /**
     * Coub constructor.
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
     * @return int|null
     */
    public function getCoubId(): ?int
    {
        return $this->coub_id;
    }

    /**
     * @param int $coub_id
     *
     * @return $this
     * @throws Exception
     */
    public function setCoubId(int $coub_id): self
    {
        $this->coub_id = $coub_id;

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
     * @param int $channel_id
     *
     * @return $this
     * @throws Exception
     */
    public function setChannelId(int $channel_id): self
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
    public function getPermalink(): ?string
    {
        return $this->permalink;
    }

    /**
     * @param string $permalink
     *
     * @return $this
     * @throws Exception
     */
    public function setPermalink(string $permalink): self
    {
        $this->permalink = $permalink;

        if (!$this->date_update) {
            $this->setDateUpdate();
        }

        return $this;
    }

    /**
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string|null $title
     *
     * @return $this
     * @throws Exception
     */
    public function setTitle(?string $title): self
    {
        $this->title = htmlspecialchars($title);

        if (!$this->date_update) {
            $this->setDateUpdate();
        }

        return $this;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->created_at;
    }

    /**
     * @param $created_at
     *
     * @return $this
     */
    public function setCreatedAt($created_at): self
    {
        try {
            if ('' !== $created_at) {
                $dateObj = new DateTime($created_at);
                $this->created_at = new DateTime($dateObj->format('Y-m-d H:i:s'));

                if (!$this->date_update) {
                    $this->setDateUpdate();
                }
            }
        } catch (Exception $exception) {
            trigger_error($exception);
        }

        return $this;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updated_at;
    }

    /**
     * @param $updated_at
     *
     * @return $this
     */
    public function setUpdatedAt($updated_at): self
    {
        try {
            if ('' !== $updated_at) {
                $dateObj = new DateTime($updated_at);
                $this->updated_at = new DateTime($dateObj->format('Y-m-d H:i:s'));

                if (!$this->date_update) {
                    $this->setDateUpdate();
                }
            }
        } catch (Exception $exception) {
            trigger_error($exception);
        }

        return $this;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getDeletedAt(): ?DateTimeInterface
    {
        return $this->deleted_at;
    }

    /**
     * @param DateTimeInterface|null $deleted_at
     *
     * @return $this
     * @throws Exception
     */
    public function setDeletedAt(?DateTimeInterface $deleted_at): self
    {
        $this->deleted_at = $deleted_at;

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

    /**
     * @return bool|null
     */
    public function getIsKd(): ?bool
    {
        return $this->is_kd;
    }

    /**
     * @param bool|null $is_kd
     *
     * @return $this
     * @throws Exception
     */
    public function setIsKd(?bool $is_kd): self
    {
        $this->is_kd = $is_kd;

        if (!$this->date_update) {
            $this->setDateUpdate();
        }

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getFeatured(): ?bool
    {
        return $this->featured;
    }

    /**
     * @param bool|null $featured
     *
     * @return $this
     * @throws Exception
     */
    public function setFeatured(?bool $featured): self
    {
        $this->featured = $featured;

        if (!$this->date_update) {
            $this->setDateUpdate();
        }

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getBanned(): ?bool
    {
        return $this->banned;
    }

    /**
     * @param bool|null $banned
     *
     * @return $this
     * @throws Exception
     */
    public function setBanned(?bool $banned): self
    {
        $this->banned = $banned;

        if (!$this->date_update) {
            $this->setDateUpdate();
        }

        return $this;
    }

    /**
     * @return mixed
     */
    public function getOwnerId()
    {
        return $this->owner_id;
    }

    /**
     * @param Channel $owner_id
     *
     * @return $this
     * @throws Exception
     */
    public function setOwnerId(Channel $owner_id): self
    {
        $this->owner_id = $owner_id;

        if (!$this->date_update) {
            $this->setDateUpdate();
        }

        return $this;
    }
}
