<?php

namespace App\Entity;

use App\Repository\CoubRepository;
use Doctrine\ORM\Mapping as ORM;

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

    public function __construct()
    {
        $this->setDateCreate();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCoubId(): ?int
    {
        return $this->coub_id;
    }

    public function setCoubId(int $coub_id): self
    {
        $this->coub_id = $coub_id;

        $this->setDateUpdate();

        return $this;
    }

    public function getChannelId(): ?int
    {
        return $this->channel_id;
    }

    public function setChannelId(int $channel_id): self
    {
        $this->channel_id = $channel_id;

        $this->setDateUpdate();

        return $this;
    }

    public function getPermalink(): ?string
    {
        return $this->permalink;
    }

    public function setPermalink(string $permalink): self
    {
        $this->permalink = $permalink;

        $this->setDateUpdate();

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        $this->setDateUpdate();

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt($created_at): self
    {
        try {
            if ('' !== $created_at) {
                $dateObj = new \DateTime($created_at);
                $this->created_at = new \DateTime($dateObj->format('Y-m-d H:i:s'));

                $this->setDateUpdate();
            }
        } catch (\Exception $exception) {
            trigger_error($exception);
        }

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt($updated_at): self
    {
        try {
            if ('' !== $updated_at) {
                $dateObj = new \DateTime($updated_at);
                $this->updated_at = new \DateTime($dateObj->format('Y-m-d H:i:s'));

                $this->setDateUpdate();
            }
        } catch (\Exception $exception) {
            trigger_error($exception);
        }

        return $this;
    }

    public function getDeletedAt(): ?\DateTimeInterface
    {
        return $this->deleted_at;
    }

    public function setDeletedAt(?\DateTimeInterface $deleted_at): self
    {
        $this->deleted_at = $deleted_at;

        $this->setDateUpdate();

        return $this;
    }

    public function getDateCreate(): ?\DateTimeInterface
    {
        return $this->date_create;
    }

    public function setDateCreate(): self
    {
        if (!$this->date_create) {
            $this->date_create = new \DateTime();

            $this->setDateUpdate();
        }

        return $this;
    }

    public function getDateUpdate(): ?\DateTimeInterface
    {
        return $this->date_update;
    }

    public function setDateUpdate(): self
    {
        $this->date_update = new \DateTime();

        return $this;
    }
}
