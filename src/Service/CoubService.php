<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\Coub;
use App\Entity\CoubStat;
use App\Repository\CoubRepository;
use App\Repository\CoubStatRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;

class CoubService
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * @var Security
     */
    private Security $security;

    /**
     * @param EntityManagerInterface $entityManager
     * @param Security               $security
     */
    public function __construct(EntityManagerInterface $entityManager, Security $security)
    {
        $this->entityManager = $entityManager;
        $this->security = $security;
    }

    /**
     * @param Request $request
     *
     * @return array
     * @throws Exception
     */
    public function getCoubsList(Request $request)
    {
        $coubs = [];
        $channelId = (string)$request->request->get('channel_id');

        if (
            !$channelId
            || !is_numeric($channelId)
        ) {
            throw new Exception('Не задан или некорректный id канала ' . $channelId);
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
     * @param string $coubId
     * @param string $statType
     *
     * @return array
     * @throws Exception
     */
    public function getCoubStatistic(string $coubId, string $statType)
    {
        $result = [];

        if (0 >= (int)$coubId || '' === $statType) {
            throw new Exception('Не указано поле coub_id или type');
        }

        /**
         * @var $coubsStatRepo CoubStatRepository
         */
        $coubsStatRepo = $this->entityManager->getRepository(CoubStat::class);
        $coubsStat = $coubsStatRepo->findBy(['coub_id' => $coubId]);

        if ($coubsStat) {
            /**
             * @var $coub CoubStat
             */
            foreach ($coubsStat as $coub) {
                $result[] = [
                    'coub_id'        => $coub->getCoubId(),
                    'timestamp'      => $coub->getDateCreate(),
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