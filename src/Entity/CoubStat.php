<?php

namespace App\Entity;

use App\Repository\CoubStatRepository;
use DateTime;
use DateTimeZone;
use Doctrine\ORM\Mapping as ORM;
use Exception;

/**
 * @ORM\Entity(repositoryClass=CoubStatRepository::class)
 */
class CoubStat
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
    private $channel_id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $like_count;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $repost_count;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $remixes_count;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $views_count;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $dislikes_count;

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
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $date_create;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $date_update;

    /**
     * @ORM\ManyToOne(targetEntity=Channel::class)
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $owner_id;

    /**
     * CoubStat constructor.
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
     * @return int|null
     */
    public function getLikeCount(): ?int
    {
        return $this->like_count;
    }

    /**
     * @param int|null $like_count
     *
     * @return $this
     * @throws Exception
     */
    public function setLikeCount(?int $like_count): self
    {
        $this->like_count = $like_count;

        if (!$this->date_update) {
            $this->setDateUpdate();
        }

        return $this;
    }

    /**
     * @return int|null
     */
    public function getRepostCount(): ?int
    {
        return $this->repost_count;
    }

    /**
     * @param int|null $repost_count
     *
     * @return $this
     * @throws Exception
     */
    public function setRepostCount(?int $repost_count): self
    {
        $this->repost_count = $repost_count;

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
     * @return DateTime|null
     */
    public function getDateCreate(): ?DateTime
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
            $this->date_create = new DateTime('now', new DateTimeZone('Europe/London'));

            if (!$this->date_update) {
                $this->setDateUpdate();
            }
        }

        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getDateUpdate(): ?DateTime
    {
        return $this->date_update;
    }

    /**
     * @return $this
     * @throws Exception
     */
    public function setDateUpdate(): self
    {
        $this->date_update = new DateTime('now', new DateTimeZone('Europe/London'));

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
