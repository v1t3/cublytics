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

    /**
     * Channel constructor.
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
    public function getChannelPermalink(): ?string
    {
        return $this->channel_permalink;
    }

    /**
     * @param string $channel_permalink
     *
     * @return $this
     * @throws Exception
     */
    public function setChannelPermalink(string $channel_permalink): self
    {
        $this->channel_permalink = $channel_permalink;

        if (!$this->date_update) {
            $this->setDateUpdate();
        }

        return $this;
    }

    /**
     * @return int|null
     */
    public function getUserId(): ?int
    {
        return $this->user_id;
    }

    /**
     * @param int $user_id
     *
     * @return $this
     * @throws Exception
     */
    public function setUserId(int $user_id): self
    {
        $this->user_id = $user_id;

        if (!$this->date_update) {
            $this->setDateUpdate();
        }

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getIsWatching(): ?bool
    {
        return $this->is_watching;
    }

    /**
     * @param bool|null $is_watching
     *
     * @return $this
     * @throws Exception
     */
    public function setIsWatching(?bool $is_watching): self
    {
        $this->is_watching = $is_watching;

        if (!$this->date_update) {
            $this->setDateUpdate();
        }

        return $this;
    }

    /**
     * @return int|null
     */
    public function getFollowersCount(): ?int
    {
        return $this->followers_count;
    }

    /**
     * @param int|null $followers_count
     *
     * @return $this
     * @throws Exception
     */
    public function setFollowersCount(?int $followers_count): self
    {
        $this->followers_count = $followers_count;

        if (!$this->date_update) {
            $this->setDateUpdate();
        }

        return $this;
    }

    /**
     * @return string|null
     */
    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    /**
     * @param string|null $avatar
     *
     * @return $this
     * @throws Exception
     */
    public function setAvatar(?string $avatar): self
    {
        $this->avatar = $avatar;

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
     * @return int|null
     */
    public function getLikesCount(): ?int
    {
        return $this->likes_count;
    }

    /**
     * @param int|null $likes_count
     *
     * @return $this
     * @throws Exception
     */
    public function setLikesCount(?int $likes_count): self
    {
        $this->likes_count = $likes_count;

        if (!$this->date_update) {
            $this->setDateUpdate();
        }

        return $this;
    }

    /**
     * @return int|null
     */
    public function getStoriesCount(): ?int
    {
        return $this->stories_count;
    }

    /**
     * @param int|null $stories_count
     *
     * @return $this
     * @throws Exception
     */
    public function setStoriesCount(?int $stories_count): self
    {
        $this->stories_count = $stories_count;

        if (!$this->date_update) {
            $this->setDateUpdate();
        }

        return $this;
    }

    /**
     * @return int|null
     */
    public function getViewsCount(): ?int
    {
        return $this->views_count;
    }

    /**
     * @param int|null $views_count
     *
     * @return $this
     * @throws Exception
     */
    public function setViewsCount(?int $views_count): self
    {
        $this->views_count = $views_count;

        if (!$this->date_update) {
            $this->setDateUpdate();
        }

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getIsCurrent(): ?bool
    {
        return $this->is_current;
    }

    /**
     * @param bool|null $is_current
     *
     * @return $this
     * @throws Exception
     */
    public function setIsCurrent(?bool $is_current): self
    {
        $this->is_current = $is_current;

        if (!$this->date_update) {
            $this->setDateUpdate();
        }

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getIsActive(): ?bool
    {
        return $this->is_active;
    }

    /**
     * @param bool|null $is_active
     *
     * @return $this
     * @throws Exception
     */
    public function setIsActive(?bool $is_active): self
    {
        $this->is_active = $is_active;

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
     * @return int|null
     */
    public function getKdCount(): ?int
    {
        return $this->kd_count;
    }

    /**
     * @param int|null $kd_count
     *
     * @return $this
     * @throws Exception
     */
    public function setKdCount(?int $kd_count): self
    {
        $this->kd_count = $kd_count;

        if (!$this->date_update) {
            $this->setDateUpdate();
        }

        return $this;
    }

    /**
     * @return int|null
     */
    public function getFeaturedCount(): ?int
    {
        return $this->featured_count;
    }

    /**
     * @param int|null $featured_count
     *
     * @return $this
     * @throws Exception
     */
    public function setFeaturedCount(?int $featured_count): self
    {
        $this->featured_count = $featured_count;

        if (!$this->date_update) {
            $this->setDateUpdate();
        }

        return $this;
    }

    /**
     * @return int|null
     */
    public function getBannedCount(): ?int
    {
        return $this->banned_count;
    }

    /**
     * @param int|null $banned_count
     *
     * @return $this
     * @throws Exception
     */
    public function setBannedCount(?int $banned_count): self
    {
        $this->banned_count = $banned_count;

        if (!$this->date_update) {
            $this->setDateUpdate();
        }

        return $this;
    }

    /**
     * @return int|null
     */
    public function getDislikesCount(): ?int
    {
        return $this->dislikes_count;
    }

    /**
     * @param int|null $dislikes_count
     *
     * @return $this
     * @throws Exception
     */
    public function setDislikesCount(?int $dislikes_count): self
    {
        $this->dislikes_count = $dislikes_count;

        if (!$this->date_update) {
            $this->setDateUpdate();
        }

        return $this;
    }

    /**
     * @return int|null
     */
    public function getRepostsCount(): ?int
    {
        return $this->reposts_count;
    }

    /**
     * @param int|null $reposts_count
     *
     * @return $this
     * @throws Exception
     */
    public function setRepostsCount(?int $reposts_count): self
    {
        $this->reposts_count = $reposts_count;

        if (!$this->date_update) {
            $this->setDateUpdate();
        }

        return $this;
    }

    /**
     * @return int|null
     */
    public function getRemixesCount(): ?int
    {
        return $this->remixes_count;
    }

    /**
     * @param int|null $remixes_count
     *
     * @return $this
     * @throws Exception
     */
    public function setRemixesCount(?int $remixes_count): self
    {
        $this->remixes_count = $remixes_count;

        if (!$this->date_update) {
            $this->setDateUpdate();
        }

        return $this;
    }
}
