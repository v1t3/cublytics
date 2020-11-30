<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\Coub;
use App\Entity\CoubStat;
use App\Repository\CoubRepository;
use App\Repository\CoubStatRepository;
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
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
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
        $timezone = (string)$request->request->get('timezone');
        $result = [];

        if (0 >= (int)$coubId || '' === $statType) {
            throw new RuntimeException('Не указано поле coub_id или type');
        }

        $dateFormat = '';
        $ymdStart = date('Y-m-d');
        $ymdEnd = date('Y-m-d');

        switch ($statType) {
            case 'day':
                $dateFormat = 'H:00';
                break;
            case 'week':
                $dateFormat = 'd.m';
                $ymdStart = date('Y-m-d', strtotime('-7 day'));
                break;
            case 'month1':
                $dateFormat = 'd.m';
                $ymdStart = date('Y-m-d', strtotime('-1 month'));
                break;
            case 'month6':
                $dateFormat = 'd.m.Y';
                $ymdStart = date('Y-m-d', strtotime('-6 month'));
                break;
            case 'year':
                $dateFormat = 'm.Y';
                $ymdStart = date('Y-m-d', strtotime('-1 year'));
                break;
            case 'all':
                $dateFormat = 'm.Y';
                $ymdStart = '2010-01-01';
                break;
        }

        $dateStart = new DateTime("{$ymdStart} 00:00:00");
        $dateEnd = new DateTime("{$ymdEnd} 23:59.59");

        /**
         * @var $coubsStatRepo CoubStatRepository
         */
        $coubsStatRepo = $this->entityManager->getRepository(CoubStat::class);
        $coubsStat = $coubsStatRepo->findByPeriodCoub($coubId, $dateStart, $dateEnd);

        if ($coubsStat) {
            /**
             * @var $coub CoubStat
             */
            foreach ($coubsStat as $coub) {
                $timestamp = $coub->getDateCreate();
                if ($timestamp) {
                    $timestamp->modify($timezone . ' hour');
                }
                $result[] = [
                    'coub_id'        => $coub->getCoubId(),
                    'timestamp'      => $timestamp->format($dateFormat),
                    'like_count'     => $coub->getLikeCount(),
                    'repost_count'   => $coub->getRepostCount(),
                    'remixes_count'  => $coub->getRemixesCount(),
                    'views_count'    => $coub->getViewsCount(),
                    'dislikes_count' => $coub->getDislikesCount(),
                    'is_kd'          => $coub->getIsKd(),
                    'featured'       => $coub->getFeatured(),
                    'banned'         => $coub->getBanned(),
                ];
            }
        }

        return $result;
    }
}