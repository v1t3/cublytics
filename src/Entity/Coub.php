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
     * @ORM\Column(type="integer")
     */
    private $coub_id;

    /**
     * @ORM\Column(type="integer")
     */
    private $user_id;

    /**
     * @ORM\Column(type="integer")
     */
    private $channel_id;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $is_kd;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $featured;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $like_count;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $repost_count;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $date_create;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $timestamp;

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

        return $this;
    }

    public function getUserId(): ?int
    {
        return $this->user_id;
    }

    public function setUserId(int $user_id): self
    {
        $this->user_id = $user_id;

        return $this;
    }

    public function getChannelId(): ?int
    {
        return $this->channel_id;
    }

    public function setChannelId(int $channel_id): self
    {
        $this->channel_id = $channel_id;

        return $this;
    }

    public function getIsKd(): ?bool
    {
        return $this->is_kd;
    }

    public function setIsKd(?bool $is_kd): self
    {
        $this->is_kd = $is_kd;

        return $this;
    }

    public function getFeatured(): ?bool
    {
        return $this->featured;
    }

    public function setFeatured(?bool $featured): self
    {
        $this->featured = $featured;

        return $this;
    }

    public function getLikeCount(): ?int
    {
        return $this->like_count;
    }

    public function setLikeCount(?int $like_count): self
    {
        $this->like_count = $like_count;

        return $this;
    }

    public function getRepostCount(): ?int
    {
        return $this->repost_count;
    }

    public function setRepostCount(?int $repost_count): self
    {
        $this->repost_count = $repost_count;

        return $this;
    }

    public function getDateCreate(): ?\DateTimeInterface
    {
        return $this->date_create;
    }

    public function setDateCreate(\DateTimeInterface $date_create): self
    {
        $this->date_create = $date_create;

        return $this;
    }

    public function getTimestamp(): ?\DateTimeInterface
    {
        return $this->timestamp;
    }

    public function setTimestamp(?\DateTimeInterface $timestamp): self
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(?\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(?\DateTimeInterface $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    public function getDeletedAt(): ?\DateTimeInterface
    {
        return $this->deleted_at;
    }

    public function setDeletedAt(?\DateTimeInterface $deleted_at): self
    {
        $this->deleted_at = $deleted_at;

        return $this;
    }
}
