<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\Coub;
use App\Entity\CoubBannedCount;
use App\Entity\CoubDislikesCount;
use App\Entity\CoubFeaturedCount;
use App\Entity\CoubKdCount;
use App\Entity\CoubLikeCount;
use App\Entity\CoubRemixesCount;
use App\Entity\CoubRepostCount;
use App\Entity\CoubViewsCount;
use App\Repository\CoubBannedCountRepository;
use App\Repository\CoubDislikesCountRepository;
use App\Repository\CoubFeaturedCountRepository;
use App\Repository\CoubKdCountRepository;
use App\Repository\CoubLikeCountRepository;
use App\Repository\CoubRemixesCountRepository;
use App\Repository\CoubRepository;
use App\Repository\CoubRepostCountRepository;
use App\Repository\CoubViewsCountRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use RuntimeException;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class CoubService
 *
 * @package App\Service
 */
class CoubService
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;
    /**
     * @var array
     */
    private $formats;
    /**
     * @var
     */
    private $timezone;
    /**
     * @var CoubViewsCountRepository
     */
    private $coubsViewsRepo;
    /**
     * @var CoubLikeCountRepository
     */
    private $coubsLikeRepo;
    /**
     * @var CoubRepostCountRepository
     */
    private $coubsRepostRepo;
    /**
     * @var CoubRemixesCountRepository
     */
    private $coubsRemixesRepo;
    /**
     * @var CoubDislikesCountRepository
     */
    private $coubsDislikesRepo;
    /**
     * @var CoubKdCountRepository
     */
    private $coubsKdRepo;
    /**
     * @var CoubFeaturedCountRepository
     */
    private $coubsFeaturedRepo;
    /**
     * @var CoubBannedCountRepository
     */
    private $coubsBannedRepo;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @return void
     */
    private function initializeRepositories(): void
    {
        if (!$this->coubsViewsRepo) {
            $this->coubsViewsRepo = $this->entityManager->getRepository(CoubViewsCount::class);
        }
        if (!$this->coubsLikeRepo) {
            $this->coubsLikeRepo = $this->entityManager->getRepository(CoubLikeCount::class);
        }
        if (!$this->coubsRepostRepo) {
            $this->coubsRepostRepo = $this->entityManager->getRepository(CoubRepostCount::class);
        }
        if (!$this->coubsRemixesRepo) {
            $this->coubsRemixesRepo = $this->entityManager->getRepository(CoubRemixesCount::class);
        }
        if (!$this->coubsDislikesRepo) {
            $this->coubsDislikesRepo = $this->entityManager->getRepository(CoubDislikesCount::class);
        }
        if (!$this->coubsKdRepo) {
            $this->coubsKdRepo = $this->entityManager->getRepository(CoubKdCount::class);
        }
        if (!$this->coubsFeaturedRepo) {
            $this->coubsFeaturedRepo = $this->entityManager->getRepository(CoubFeaturedCount::class);
        }
        if (!$this->coubsBannedRepo) {
            $this->coubsBannedRepo = $this->entityManager->getRepository(CoubBannedCount::class);
        }
    }

    /**
     * @param Request $request
     *
     * @return array
     * @throws Exception
     */
    public function getCoubsList(Request $request): array
    {
        $coubs = [];
        $channelId = (string)$request->request->get('channel_id');

        if (
            !$channelId
            || !is_numeric($channelId)
        ) {
            throw new RuntimeException('Не задан или некорректный id канала ' . $channelId);
        }
        /**
         * @var $channelCoubsRepo CoubRepository
         */
        $channelCoubsRepo = $this->entityManager->getRepository(Coub::class);
        $channelCoubs = $channelCoubsRepo->findBy(['channel_id' => $channelId]);

        if ($channelCoubs) {
            /**
             * @var $coub Coub
             */
            foreach ($channelCoubs as $coub) {
                $coubs[] = [
                    'coub_id'    => $coub->getCoubId(),
                    'channel_id' => $coub->getChannelId(),
                    'permalink'  => $coub->getPermalink(),
                    'title'      => $coub->getTitle(),
                ];
            }
        }

        return $coubs;
    }

    /**
     * @param Request $request
     *
     * @return array
     * @throws Exception
     */
    public function getCoubStatistic(Request $request): array
    {
        $coubId = (string)$request->request->get('coub_id');
        $statType = (string)$request->request->get('statistic_type');
        $this->timezone = (string)$request->request->get('timezone');
        $result = [];

        if (0 >= (int)$coubId || '' === $statType) {
            throw new RuntimeException('Не указано поле coub_id или type');
        }

        # Получить форматы
        $this->formats = $this->getStatisticFormat($statType);
        # Иничиализировать репозитории для статистики
        $this->initializeRepositories();

        $dateStart = new DateTime("{$this->formats['ymd_start']} 00:00:00");
        $dateEnd = new DateTime("{$this->formats['ymd_end']} 23:59.59");
        if (0 < $this->timezone) {
            $dateStart->modify('-' . $this->timezone . ' hour');
            $dateEnd->modify('-' . $this->timezone . ' hour');
        } elseif (0 > $this->timezone) {
            $dateStart->modify($this->timezone . ' hour');
            $dateEnd->modify($this->timezone . ' hour');
        }

        $coubViews = $this->coubsViewsRepo->findByPeriodCoub($coubId, $dateStart, $dateEnd);
        $this->addStatisticCounts($coubViews, 'views_count', $result);

        $coubLike = $this->coubsLikeRepo->findByPeriodCoub($coubId, $dateStart, $dateEnd);
        $this->addStatisticCounts($coubLike, 'like_count', $result);

        $coubRepost = $this->coubsRepostRepo->findByPeriodCoub($coubId, $dateStart, $dateEnd);
        $this->addStatisticCounts($coubRepost, 'repost_count', $result);

        $coubRemixes = $this->coubsRemixesRepo->findByPeriodCoub($coubId, $dateStart, $dateEnd);
        $this->addStatisticCounts($coubRemixes, 'remixes_count', $result);

        $coubDislikes = $this->coubsDislikesRepo->findByPeriodCoub($coubId, $dateStart, $dateEnd);
        $this->addStatisticCounts($coubDislikes, 'dislikes_count', $result);

        return $result;
    }

    /**
     * @param $data
     * @param $type
     * @param $parentResult
     */
    private function addStatisticCounts($data, $type, &$parentResult)
    {
        foreach ($data as $record) {
            switch ($type) {
                case 'views_count':
                    /** @var CoubViewsCount $record */
                    $tempCount = $record->getViewsCount();
                    break;
                case 'like_count':
                    /** @var CoubLikeCount $record */
                    $tempCount = $record->getLikeCount();
                    break;
                case 'repost_count':
                    /** @var CoubRepostCount $record */
                    $tempCount = $record->getRepostCount();
                    break;
                case 'remixes_count':
                    /** @var CoubRemixesCount $record */
                    $tempCount = $record->getRemixesCount();
                    break;
                case 'dislikes_count':
                    /** @var CoubDislikesCount $record */
                    $tempCount = $record->getDislikesCount();
                    break;
                default:
                    $tempCount = 0;
                    break;
            }

            $dateCreate = $record->getDateCreate();
            $dateUpdate = $record->getDateUpdate();

            if ($dateCreate && $dateUpdate) {
                # Скорректируем таймзону обратно к локальной
                if (0 < $this->timezone) {
                    $dateCreate->modify($this->timezone . ' hour');
                    $dateUpdate->modify($this->timezone . ' hour');
                } elseif (0 > $this->timezone) {
                    $dateCreate->modify('-' . $this->timezone . ' hour');
                    $dateUpdate->modify('-' . $this->timezone . ' hour');
                }

                if (0 < (int)$tempCount) {
                    $tsDiff = $dateUpdate->getTimestamp() - $dateCreate->getTimestamp();

                    if (0 < $tsDiff) {
                        $tsDiff = round($tsDiff / 3600);
                    }

                    for ($i = 0; $i <= $tsDiff; $i++) {
                        $time = $dateCreate->format($this->formats['date_format']);

                        $parentResult[$type][$time] = $tempCount;

                        $dateCreate->modify('1 hour');
                    }
                }
            }
        }
    }

    private function getStatisticFormat($type)
    {
        $result = [
            'date_format'   => '',
            'ymd_start'     => date('Y-m-d'),
            'ymd_end'       => date('Y-m-d')
        ];

        switch ($type) {
            case 'day':
                $result['date_format'] = 'H:00';
                break;
            case 'week':
                $result['date_format'] = 'd.m';
                $result['ymd_start'] = date('Y-m-d', strtotime('-7 day'));
                break;
            case 'month1':
                $result['date_format'] = 'd.m';
                $result['ymd_start'] = date('Y-m-d', strtotime('-1 month'));
                break;
            case 'month6':
                $result['date_format'] = 'm.Y';
                // получить первый день месяца
                $result['ymd_start'] = date('Y-m-0', strtotime('-6 month'));
                // получить последний день месяца
                $result['ymd_end'] = date('Y-m-t');
                break;
            case 'year':
                $result['date_format'] = 'm.Y';
                // получить первый день месяца
                $result['ymd_start'] = date('Y-m-0', strtotime('-1 year'));
                // получить последний день месяца
                $result['ymd_end'] = date('Y-m-t');
                break;
            case 'all':
                $result['date_format'] = 'm.Y';
                $result['ymd_start'] = '2010-01-01';
                break;
        }

        return $result;
    }
}