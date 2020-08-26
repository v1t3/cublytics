<?php

namespace App\Entity;

use App\Repository\ChannelRepository;
use Doctrine\ORM\Mapping as ORM;

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
     * @ORM\Column(type="string", length=255)
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
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updated_at;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $followers_count;

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
    private $views_count;

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

        return $this;
    }

    public function getChannelPermalink(): ?string
    {
        return $this->channel_permalink;
    }

    public function setChannelPermalink(string $channel_permalink): self
    {
        $this->channel_permalink = $channel_permalink;

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

    public function getIsWatching(): ?bool
    {
        return $this->is_watching;
    }

    public function setIsWatching(?bool $is_watching): self
    {
        $this->is_watching = $is_watching;

        return $this;
    }

    public function getFollowersCount(): ?int
    {
        return $this->followers_count;
    }

    public function setFollowersCount(?int $followers_count): self
    {
        $this->followers_count = $followers_count;

        return $this;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): self
    {
        $this->avatar = $avatar;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(string $created_at): self
    {
        try {
            if ('' !== $created_at) {
                $dateObj = new \DateTime($created_at);
                $this->created_at = new \DateTime($dateObj->format('Y-m-d H:i:s'));
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

    public function setUpdatedAt(string $updated_at): self
    {
        try {
            if ('' !== $updated_at) {
                $dateObj = new \DateTime($updated_at);
                $this->updated_at = new \DateTime($dateObj->format('Y-m-d H:i:s'));
            }
        } catch (\Exception $exception) {
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
        $this->title = $title;

        return $this;
    }

    public function getRecoubsCount(): ?int
    {
        return $this->recoubs_count;
    }

    public function setRecoubsCount(?int $recoubs_count): self
    {
        $this->recoubs_count = $recoubs_count;

        return $this;
    }

    public function getLikesCount(): ?int
    {
        return $this->likes_count;
    }

    public function setLikesCount(?int $likes_count): self
    {
        $this->likes_count = $likes_count;

        return $this;
    }

    public function getStoriesCount(): ?int
    {
        return $this->stories_count;
    }

    public function setStoriesCount(?int $stories_count): self
    {
        $this->stories_count = $stories_count;

        return $this;
    }

    public function getViewsCount(): ?int
    {
        return $this->views_count;
    }

    public function setViewsCount(?int $views_count): self
    {
        $this->views_count = $views_count;

        return $this;
    }

    public function getIsCurrent(): ?bool
    {
        return $this->is_current;
    }

    public function setIsCurrent(?bool $is_current): self
    {
        $this->is_current = $is_current;

        return $this;
    }
}
