<?php
declare(strict_types=1);

namespace App\Entity;

use App\Repository\CoubDislikesCountRepository;
use DateTime;
use DateTimeZone;
use Doctrine\ORM\Mapping as ORM;
use Exception;

/**
 * @ORM\Entity(repositoryClass=CoubDislikesCountRepository::class)
 */
class CoubDislikesCount
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Channel::class)
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $owner_id;

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
    private $dislikes_count;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $date_create;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $date_update;

    /**
     * CoubStat constructor.
     *
     * @throws Exception
     */
    public function __construct()
    {
        $this->setDateCreate();
    }

    public function getId(): ?int
    {
        return $this->id;
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

        $this->setDateUpdate();

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
     * @param int $coub_id
     *
     * @return $this
     * @throws Exception
     */
    public function setCoubId(int $coub_id): self
    {
        $this->coub_id = $coub_id;

        $this->setDateUpdate();

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

        $this->setDateUpdate();

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

        $this->setDateUpdate();

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

            $this->setDateUpdate();
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
}
