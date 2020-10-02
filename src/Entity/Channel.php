<?php

namespace App\Entity;

use App\Repository\ChannelRepository;
use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Exception;

/**
 * @ORM\Entity(repositoryClass=ChannelRepository::class)
 */
class Channel
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
    private $channel_id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $channel_permalink;

    /**
     * ID пользователя
     *
     * @ORM\Column(type="integer")
     */
    private $user_id;

    /**
     * флаг, отслеживать ли канал
     *
     * @ORM\Column(type="boolean")
     */
    private $is_watching;

    /**
     * является ли основным
     *
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $is_current;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $avatar;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $followers_count;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $views_count;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $recoubs_count;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $likes_count;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $stories_count;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $dislikes_count;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $reposts_count;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $remixes_count;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $kd_count;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $featured_count;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $banned_count;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $is_active;

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

    public function getChannelPermalink(): ?string
    {
        return $this->channel_permalink;
    }

    public function setChannelPermalink(string $channel_permalink): self
    {
        $this->channel_permalink = $channel_permalink;

        $this->setDateUpdate();

        return $this;
    }

    public function getUserId(): ?int
    {
        return $this->user_id;
    }

    public function setUserId(int $user_id): self
    {
        $this->user_id = $user_id;

        $this->setDateUpdate();

        return $this;
    }

    public function getIsWatching(): ?bool
    {
        return $this->is_watching;
    }

    public function setIsWatching(?bool $is_watching): self
    {
        $this->is_watching = $is_watching;

        $this->setDateUpdate();

        return $this;
    }

    public function getFollowersCount(): ?int
    {
        return $this->followers_count;
    }

    public function setFollowersCount(?int $followers_count): self
    {
        $this->followers_count = $followers_count;

        $this->setDateUpdate();

        return $this;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): self
    {
        $this->avatar = $avatar;

        $this->setDateUpdate();

        return $this;
    }

    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt($created_at): self
    {
        try {
            if ('' !== $created_at) {
                $dateObj = new DateTime($created_at);
                $this->created_at = new DateTime($dateObj->format('Y-m-d H:i:s'));

                $this->setDateUpdate();
            }
        } catch (Exception $exception) {
            trigger_error($exception);
        }

        return $this;
    }

    public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt($updated_at): self
    {
        try {
            if ('' !== $updated_at) {
                $dateObj = new DateTime($updated_at);
                $this->updated_at = new DateTime($dateObj->format('Y-m-d H:i:s'));

                $this->setDateUpdate();
            }
        } catch (Exception $exception) {
            trigger_error($exception);
        }

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = htmlspecialchars($title);

        $this->setDateUpdate();

        return $this;
    }

    public function getLikesCount(): ?int
    {
        return $this->likes_count;
    }

    public function setLikesCount(?int $likes_count): self
    {
        $this->likes_count = $likes_count;

        $this->setDateUpdate();

        return $this;
    }

    public function getStoriesCount(): ?int
    {
        return $this->stories_count;
    }

    public function setStoriesCount(?int $stories_count): self
    {
        $this->stories_count = $stories_count;

        $this->setDateUpdate();

        return $this;
    }

    public function getViewsCount(): ?int
    {
        return $this->views_count;
    }

    public function setViewsCount(?int $views_count): self
    {
        $this->views_count = $views_count;

        $this->setDateUpdate();

        return $this;
    }

    public function getIsCurrent(): ?bool
    {
        return $this->is_current;
    }

    public function setIsCurrent(?bool $is_current): self
    {
        $this->is_current = $is_current;

        $this->setDateUpdate();

        return $this;
    }

    public function getIsActive(): ?bool
    {
        return $this->is_active;
    }

    public function setIsActive(?bool $is_active): self
    {
        $this->is_active = $is_active;

        $this->setDateUpdate();

        return $this;
    }

    public function getDateCreate(): ?DateTimeInterface
    {
        return $this->date_create;
    }

    public function setDateCreate(): self
    {
        if (!$this->date_create) {
            $this->date_create = new DateTime();

            $this->setDateUpdate();
        }

        return $this;
    }

    public function getDateUpdate(): ?DateTimeInterface
    {
        return $this->date_update;
    }

    public function setDateUpdate(): self
    {
        $this->date_update = new DateTime();

        return $this;
    }

    public function getDeletedAt(): ?DateTimeInterface
    {
        return $this->deleted_at;
    }

    public function setDeletedAt(?DateTimeInterface $deleted_at): self
    {
        $this->deleted_at = $deleted_at;

        $this->setDateUpdate();

        return $this;
    }

    public function getKdCount(): ?int
    {
        return $this->kd_count;
    }

    public function setKdCount(?int $kd_count): self
    {
        $this->kd_count = $kd_count;

        return $this;
    }

    public function getFeaturedCount(): ?int
    {
        return $this->featured_count;
    }

    public function setFeaturedCount(?int $featured_count): self
    {
        $this->featured_count = $featured_count;

        return $this;
    }

    public function getBannedCount(): ?int
    {
        return $this->banned_count;
    }

    public function setBannedCount(?int $banned_count): self
    {
        $this->banned_count = $banned_count;

        return $this;
    }

    public function getDislikesCount(): ?int
    {
        return $this->dislikes_count;
    }

    public function setDislikesCount(?int $dislikes_count): self
    {
        $this->dislikes_count = $dislikes_count;

        return $this;
    }

    public function getRepostsCount(): ?int
    {
        return $this->reposts_count;
    }

    public function setRepostsCount(?int $reposts_count): self
    {
        $this->reposts_count = $reposts_count;

        return $this;
    }

    public function getRemixesCount(): ?int
    {
        return $this->remixes_count;
    }

    public function setRemixesCount(?int $remixes_count): self
    {
        $this->remixes_count = $remixes_count;

        return $this;
    }
}
