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
use App\Entity\CoubStat;
use App\Entity\CoubViewsCount;
use App\Repository\CoubRepository;
use App\Repository\CoubStatRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

/**
 * Class StatMigrateService
 *
 * @package App\Service
 */
class StatMigrateService
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
     * @param int $startCoub
     * @param int $coubLimit
     *
     * @return array
     * @throws Exception
     */
    public function migrateStatisticToNew(int $startCoub, int $coubLimit)
    {
        $result = [];

        /** @var $coubStatRepo CoubStatRepository */
        $coubStatRepo = $this->entityManager->getRepository(CoubStat::class);
        /** @var $coubRepo CoubRepository */
        $coubRepo = $this->entityManager->getRepository(Coub::class);

        $coubs = $coubRepo->findBy(
            [],
            [
                'id' => 'ASC'
            ],
            $coubLimit,
            $startCoub
        );

        foreach ($coubs as $coub) {
            $records = $coubStatRepo->findBy(
                [
                    'coub_id' => $coub->getCoubId()
                ],
                [
                    'id' => 'ASC'
                ]
            );

            $temp = [
                'view'     => null,
                'like'     => null,
                'repost'   => null,
                'remix'    => null,
                'dislike'  => null,
                'kd'       => null,
                'featured' => null,
                'banned'   => null,
            ];
            $data = [
                'views'    => [],
                'likes'    => [],
                'reposts'  => [],
                'remixes'  => [],
                'dislikes' => [],
                'kd'       => [],
                'featured' => [],
                'banned'   => [],
            ];

            foreach ($records as $key => $record) {
                $mainInfo = [
                    'owner_id'    => $record->getOwnerId(),
                    'coub_id'     => $record->getCoubId(),
                    'channel_id'  => $record->getChannelId(),
                    'date_create' => $record->getDateCreate(),
                    'date_update' => $record->getDateUpdate()
                ];

                if (0 === $key) {
                    $temp['view'] = $mainInfo;
                    $temp['view']['views_count'] = $record->getViewsCount();
                    $temp['like'] = $mainInfo;
                    $temp['like']['like_count'] = $record->getLikeCount();
                    $temp['repost'] = $mainInfo;
                    $temp['repost']['repost_count'] = $record->getRepostCount();
                    $temp['remix'] = $mainInfo;
                    $temp['remix']['remixes_count'] = $record->getRemixesCount();
                    $temp['dislike'] = $mainInfo;
                    $temp['dislike']['dislikes_count'] = $record->getRemixesCount();
                    $temp['kd'] = $mainInfo;
                    $temp['kd']['is_kd'] = $record->getIsKd();
                    $temp['featured'] = $mainInfo;
                    $temp['featured']['featured'] = $record->getFeatured();
                    $temp['banned'] = $mainInfo;
                    $temp['banned']['banned'] = $record->getBanned();

                    continue;
                }

                if ($record->getViewsCount() === $temp['view']['views_count']) {
                    $temp['view']['date_update'] = $record->getDateUpdate();
                } else {
                    $data['views'][] = $temp['view'];

                    $temp['view'] = $mainInfo;
                    $temp['view']['views_count'] = $record->getViewsCount();
                }

                if ($record->getLikeCount() === $temp['like']['like_count']) {
                    $temp['like']['date_update'] = $record->getDateUpdate();
                } else {
                    $data['likes'][] = $temp['like'];

                    $temp['like'] = $mainInfo;
                    $temp['like']['like_count'] = $record->getLikeCount();
                }

                if ($record->getRepostCount() === $temp['repost']['repost_count']) {
                    $temp['repost']['date_update'] = $record->getDateUpdate();
                } else {
                    $data['reposts'][] = $temp['repost'];

                    $temp['repost'] = $mainInfo;
                    $temp['repost']['repost_count'] = $record->getRepostCount();
                }

                if ($record->getRemixesCount() === $temp['remix']['remixes_count']) {
                    $temp['remix']['date_update'] = $record->getDateUpdate();
                } else {
                    $data['remixes'][] = $temp['remix'];

                    $temp['remix'] = $mainInfo;
                    $temp['remix']['remixes_count'] = $record->getRemixesCount();
                }

                if ($record->getDislikesCount() === $temp['dislike']['dislikes_count']) {
                    $temp['dislike']['date_update'] = $record->getDateUpdate();
                } else {
                    $data['dislikes'][] = $temp['dislike'];

                    $temp['dislike'] = $mainInfo;
                    $temp['dislike']['dislikes_count'] = $record->getDislikesCount();
                }

                if ($record->getIsKd() === $temp['kd']['is_kd']) {
                    $temp['kd']['date_update'] = $record->getDateUpdate();
                } else {
                    $data['kd'][] = $temp['kd'];

                    $temp['kd'] = $mainInfo;
                    $temp['kd']['is_kd'] = $record->getIsKd();
                }

                if ($record->getFeatured() === $temp['featured']['featured']) {
                    $temp['featured']['date_update'] = $record->getDateUpdate();
                } else {
                    $data['featured'][] = $temp['featured'];

                    $temp['featured'] = $mainInfo;
                    $temp['featured']['featured'] = $record->getFeatured();
                }

                if ($record->getBanned() === $temp['banned']['banned']) {
                    $temp['banned']['date_update'] = $record->getDateUpdate();
                } else {
                    $data['banned'][] = $temp['banned'];

                    $temp['banned'] = $mainInfo;
                    $temp['banned']['banned'] = $record->getBanned();
                }
            }

            $data['views'][] = $temp['view'];
            $data['likes'][] = $temp['like'];
            $data['reposts'][] = $temp['repost'];
            $data['remixes'][] = $temp['remix'];
            $data['dislikes'][] = $temp['dislike'];
            $data['kd'][] = $temp['kd'];
            $data['featured'][] = $temp['featured'];
            $data['banned'][] = $temp['banned'];

            foreach ($data['views'] as $item) {
                $record = new CoubViewsCount();
                $record->setOwnerId($item['owner_id']);
                $record->setCoubId($item['coub_id']);
                $record->setChannelId($item['channel_id']);
                $record->setViewsCount($item['views_count']);
                $record->setDateCreate($item['date_create']);
                $record->setDateUpdate($item['date_update']);
                $this->entityManager->persist($record);
            }
            foreach ($data['likes'] as $item) {
                $record = new CoubLikeCount();
                $record->setOwnerId($item['owner_id']);
                $record->setCoubId($item['coub_id']);
                $record->setChannelId($item['channel_id']);
                $record->setLikeCount($item['like_count']);
                $record->setDateCreate($item['date_create']);
                $record->setDateUpdate($item['date_update']);
                $this->entityManager->persist($record);
            }
            foreach ($data['reposts'] as $item) {
                $record = new CoubRepostCount();
                $record->setOwnerId($item['owner_id']);
                $record->setCoubId($item['coub_id']);
                $record->setChannelId($item['channel_id']);
                $record->setRepostCount($item['repost_count']);
                $record->setDateCreate($item['date_create']);
                $record->setDateUpdate($item['date_update']);
                $this->entityManager->persist($record);
            }
            foreach ($data['remixes'] as $item) {
                $record = new CoubRemixesCount();
                $record->setOwnerId($item['owner_id']);
                $record->setCoubId($item['coub_id']);
                $record->setChannelId($item['channel_id']);
                $record->setRemixesCount($item['remixes_count']);
                $record->setDateCreate($item['date_create']);
                $record->setDateUpdate($item['date_update']);
                $this->entityManager->persist($record);
            }
            foreach ($data['dislikes'] as $item) {
                $record = new CoubDislikesCount();
                $record->setOwnerId($item['owner_id']);
                $record->setCoubId($item['coub_id']);
                $record->setChannelId($item['channel_id']);
                $record->setDislikesCount($item['dislikes_count']);
                $record->setDateCreate($item['date_create']);
                $record->setDateUpdate($item['date_update']);
                $this->entityManager->persist($record);
            }
            foreach ($data['kd'] as $item) {
                $record = new CoubKdCount();
                $record->setOwnerId($item['owner_id']);
                $record->setCoubId($item['coub_id']);
                $record->setChannelId($item['channel_id']);
                $record->setIsKd($item['is_kd']);
                $record->setDateCreate($item['date_create']);
                $record->setDateUpdate($item['date_update']);
                $this->entityManager->persist($record);
            }
            foreach ($data['featured'] as $item) {
                $record = new CoubFeaturedCount();
                $record->setOwnerId($item['owner_id']);
                $record->setCoubId($item['coub_id']);
                $record->setChannelId($item['channel_id']);
                $record->setFeatured($item['featured']);
                $record->setDateCreate($item['date_create']);
                $record->setDateUpdate($item['date_update']);
                $this->entityManager->persist($record);
            }
            foreach ($data['banned'] as $item) {
                $record = new CoubBannedCount();
                $record->setOwnerId($item['owner_id']);
                $record->setCoubId($item['coub_id']);
                $record->setChannelId($item['channel_id']);
                $record->setBanned($item['banned']);
                $record->setDateCreate($item['date_create']);
                $record->setDateUpdate($item['date_update']);
                $this->entityManager->persist($record);
            }

            $this->entityManager->flush();

            if (is_array($coubs)) {
                if (!empty($result['records_count'])) {
                    $result['records_count'] += count($records);
                } else {
                    $result['records_count'] = count($records);
                }
            }
        }

        if (is_array($coubs)) {
            $result['coubs_count'] = count($coubs);
            $result['coub_last_id'] = $coubs[array_key_last($coubs)]->getId();
        }

        return $result;
    }
}