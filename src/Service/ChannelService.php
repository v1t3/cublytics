<?php
declare(strict_types=1);


namespace App\Service;


use App\AppRegistry;
use App\Entity\Channel;
use App\Entity\Coub;
use App\Entity\CoubStat;
use App\Entity\User;
use App\Repository\ChannelRepository;
use App\Repository\CoubRepository;
use App\Repository\CoubStatRepository;
use DateInterval;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;


/**
 * Class ChannelService
 *
 * @package App
 */
class ChannelService
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
     * @param $data
     *
     * @return bool
     */
    public function saveUserChannelsList($data)
    {
        if (
            empty($data)
            || !isset($data['id'])
            || !isset($data['channels'])
        ) {
            return false;
        }

        $userId = $data['id'];
        $channels = $data['channels'];
        $current = $data['current_channel']['id'];

        //todo Добавить проверку активности канала

        if (!empty($channels)) {
            /**
             * @var $repo ChannelRepository
             */
            $repo = $this->entityManager->getRepository('App:Channel');

            foreach ($channels as $channel) {
                $channelStored = $repo->findOneByChannelId($channel['id']);

                if (!$channelStored) {
                    $ch = new Channel();
                    $ch->setChannelId($channel['id']);
                    $ch->setChannelPermalink($channel['permalink']);
                    $ch->setUserId($userId);
                    $ch->setIsWatching(false);
                    $ch->setIsActive(true);
                    $ch->setIsCurrent((int)$channel['id'] === (int)$current);
                    $ch->setTitle($channel['title']);
                    $ch->setCreatedAt($channel['created_at']);
                    $ch->setUpdatedAt($channel['updated_at']);
                    $ch->setFollowersCount($channel['followers_count']);
                    $ch->setRecoubsCount($channel['recoubs_count']);
                    $ch->setStoriesCount($channel['stories_count']);
                    //todo Скачивание изображения
                    $ch->setAvatar($channel['avatar_versions']['template']);

                    $this->entityManager->persist($ch);
                    $this->entityManager->flush();
                }
            }

            return true;
        }

        return false;
    }

    /**
     * @param Request $request
     *
     * @return array
     * @throws Exception
     */
    public function updateChannelSettings(Request $request)
    {
        $result = null;

        $channelPermalink = (string)$request->request->get('channel_permalink');
        $type = (string)$request->request->get('type');
        $newVal = $request->request->get('new_val');

        if (
            '' === $channelPermalink
            || '' === $type
            || null === $newVal
        ) {
            throw new Exception('Не заданы все необходимые параметры');
        }

        $newVal = ($newVal === 'true');

        /**
         * @var $channelRepo ChannelRepository
         * @var $channel Channel
         */
        $channelRepo = $this->entityManager->getRepository(Channel::class);
        $channel = $channelRepo->findOneBy(['channel_permalink' => $channelPermalink]);

        if (!$channel) {
            throw new Exception('Канал не найден');
        }

        if ('is_active' === $type) {
            $channel->setIsActive((bool)$newVal);

            $this->entityManager->persist($channel);
            $this->entityManager->flush();

            $result = $channel->getIsActive();
        }
        if ('is_watching' === $type) {
            $channel->setIsWatching((bool)$newVal);

            $this->entityManager->persist($channel);
            $this->entityManager->flush();

            $result = $channel->getIsWatching();
        }

        return [
            'success' => $newVal === $result,
            $type     => $result
        ];
    }

    /**
     * @return array
     * @throws Exception
     */
    public function getChannelsList()
    {
        $channels = [];
        /**
         * @var $user User
         */
        $user = $this->security->getUser();

        if (!$user) {
            throw new Exception('Пользователь не найден');
        }

        $userId = $user->getUserId();

        /**
         * @var $userChannelsRepo ChannelRepository
         * @var $userChannels Channel
         * @var $userChannel  Channel
         */
        $userChannelsRepo = $this->entityManager->getRepository(Channel::class);
        $userChannels = $userChannelsRepo->findBy(
            [
                'user_id' => $userId
            ],
            [
                'is_current' => 'DESC'
            ]
        );

        if ($userChannels) {
            foreach ($userChannels as $userChannel) {
                $channels[] = [
                    'channel_id'      => $userChannel->getChannelId(),
                    'avatar'          => $userChannel->getAvatar(),
                    'name'            => $userChannel->getChannelPermalink(),
                    'title'           => $userChannel->getTitle(),
                    'is_active'       => $userChannel->getIsActive(),
                    'is_watching'     => $userChannel->getIsWatching(),
                    'is_current'      => $userChannel->getIsCurrent(),
                    'views_count'     => $userChannel->getViewsCount(),
                    'likes_count'     => $userChannel->getLikesCount(),
                    'followers_count' => $userChannel->getFollowersCount(),
                    'recoubs_count'   => $userChannel->getRecoubsCount(),
                    'stories_count'   => $userChannel->getStoriesCount()
                ];
            }
        }

        return $channels;
    }

    /**
     * @param string $channelName
     * @param string $statType
     *
     * @return array
     * @throws Exception
     */
    public function getChannelStatistic(string $channelName, string $statType)
    {
        $result = [];

        if ('' === $channelName || '' === $statType) {
            throw new Exception('Не указано поле channel_name или type');
        }

        /**
         * @var $channelRepo ChannelRepository
         * @var $channel     Channel
         */
        $channelRepo = $this->entityManager->getRepository(Channel::class);
        $channel = $channelRepo->findOneBy(['channel_permalink' => $channelName]);

        if ($channel && 0 < (int)$channel->getChannelId()) {
            $channelId = $channel->getChannelId();
            /**
             * @var $coubsStatRepo CoubStatRepository
             * @var $coubsStat     CoubStat
             */
            $coubsStatRepo = $this->entityManager->getRepository(CoubStat::class);
            $coubsStat = $coubsStatRepo->findBy(['channel_id' => $channelId]);

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
        }

        return $result;
    }

    /**
     *
     * @param string $channelName
     *
     * @return array
     * @throws Exception
     */
    public function getOriginalCoubs(string $channelName)
    {
        $result = [];

        if ('' === $channelName) {
            throw new Exception('Не корректно или не заполнено поле channel_name');
        }

        $urlTale = '&per_page=' . AppRegistry::TIMELINE_PER_PAGE . '&order_by=' . AppRegistry::TIMELINE_ORDER_BY;
        //получим основные данные канала
        $data = $this->getInfo(AppRegistry::API_COUB_TIMELINE_LINK . $channelName . '?page=1' . $urlTale);

        if ('' === (string)$data) {
            throw new Exception('Возвращён пустой ответ');
        }
        // проверим, что вернулся не html
        if (false !== strpos((string)$data, '<!DOCTYPE html>')) {
            throw new Exception('Некорректный ответ от сервиса');
        }

        $decodeData = json_decode(html_entity_decode($data), true);

        if (
            !is_array($decodeData)
            || !array_key_exists('total_pages', $decodeData)
        ) {
            throw new Exception('Ошибка при получении данных data: ' . json_encode($data));
        }

        if (array_key_exists('total_pages', $decodeData)) {
            if (1 < (int)$decodeData['total_pages']) {
                $urls = [];
                $allCoubs = [];
                # сохраним уже полученную 1ю страницу
                $encodeData[] = $data;

                # получим грязный список страниц всех коубов
                for ($i = 2; $i <= $decodeData['total_pages']; $i++) {
                    $urls[] = AppRegistry::API_COUB_TIMELINE_LINK . $channelName . '?page=' . $i . $urlTale;
                }

                $others = $this->getInfoByUrls($urls);

                if (is_array($others) && !empty($others)) {
                    $encodeData = array_merge($encodeData, $others);
                }

                # получаем коубы постранично и объединяем в общий массив
                foreach ($encodeData as $item) {
                    $decodeTemp = json_decode(html_entity_decode($item), true);
                    if (is_array($decodeTemp['coubs'])) {
                        $allCoubs = array_merge($allCoubs, $decodeTemp['coubs']);
                    }
                }

                # уберём дубликаты коубов
                $result = $this->arrayUniqueKey($allCoubs, 'id');
            } elseif (1 === $decodeData['total_pages']) {
                $result = $decodeData['coubs'];
            }
        }

        return $result;
    }

    /**
     * @param $data
     * @param $channelName
     *
     * @return bool
     * @throws Exception
     */
    public function saveOriginalCoubs($data, $channelName)
    {
        if (!$data) {
            return false;
        }

        /**
         * @var $channelRepo ChannelRepository
         * @var $channel     Channel
         */
        $channelRepo = $this->entityManager->getRepository(Channel::class);
        $channel = $channelRepo->findOneBy(['channel_permalink' => $channelName]);

        if ($channel && 0 < (int)$channel->getChannelId()) {
            $channelId = $channel->getChannelId();
            /**
             * @var $coubRepo CoubRepository
             */
            $coubRepo = $this->entityManager->getRepository(Coub::class);

            foreach ($data as $coub) {
                /**
                 * @var $coubItem Coub
                 */
                $coubItem = $coubRepo->findOneBy(['channel_id' => $channelId]);

                if ($coubItem) {
                    if ($coubItem->getUpdatedAt() !== (new DateTime($coub['updated_at']))) {
                        $coubItem->setChannelId($channelId);
                        $coubItem->setTitle($coub['title']);
                        $coubItem->setUpdatedAt($coub['updated_at']);
                        //todo Реализовать проверку существования coub'a при выполнении задания cron

                        //добавим coub к сохранению
                        $this->entityManager->persist($coubItem);
                    }
                } elseif (empty($coub['recoub_to'])) {
                    $coubItem = new Coub();
                    $coubItem->setCoubId($coub['id']);
                    $coubItem->setChannelId($channelId);
                    $coubItem->setPermalink($coub['permalink']);
                    $coubItem->setTitle($coub['title']);
                    $coubItem->setCreatedAt($coub['created_at']);
                    $coubItem->setUpdatedAt($coub['updated_at']);

                    //добавим coub к сохранению
                    $this->entityManager->persist($coubItem);
                }

                # Если recoub_to не существует, то coub свой
                if (empty($coub['recoub_to'])) {
                    $coubStatItem = new CoubStat();
                    $coubStatItem->setCoubId($coub['id']);
                    $coubStatItem->setChannelId($channelId);
                    $coubStatItem->setLikeCount($coub['likes_count']);
                    $coubStatItem->setRepostCount($coub['recoubs_count']);
                    $coubStatItem->setRemixesCount($coub['remixes_count']);
                    $coubStatItem->setDislikesCount($coub['dislikes_count']);
                    $coubStatItem->setViewsCount($coub['views_count']);
                    $coubStatItem->setFeatured($coub['featured']);
                    $coubStatItem->setIsKd($coub['cotd']);
                    $coubStatItem->setBanned($coub['banned']);

                    //добавим данные coub'a к сохранению
                    $this->entityManager->persist($coubStatItem);
                }
            }

            $this->entityManager->flush();

            return true;
        }

        return false;
    }

    /**
     * @param string $url
     *
     * @return bool|string
     * @throws Exception
     */
    private function getInfo(string $url)
    {
        try {
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10); //timeout in seconds
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $data = curl_exec($ch);

            if (curl_errno($ch)) {
                $data = curl_error($ch);
            }

            curl_close($ch);
        } catch (Exception $exception) {
            throw new Exception($exception);
        }

        return $data;
    }

    /**
     * @param array $urls
     *
     * @return array
     * @throws Exception
     */
    private function getInfoByUrls(array $urls)
    {
        $result = [];

        try {
            if (count($urls) > 50) {
                $urlsMulti = array_chunk($urls, 50);

                foreach ($urlsMulti as $url) {
                    $temp = $this->getInfoMulti($url);

                    $result = array_merge($result, $temp);
                }
            } else {
                $result = $this->getInfoMulti($urls);
            }
        } catch (Exception $exception) {
            throw new Exception($exception);
        }

        return $result;
    }

    /**
     * @param $urls
     *
     * @return array
     * @throws Exception
     */
    private function getInfoMulti($urls)
    {
        try {
            $result = [];
            $curl_array = [];

            if (count($urls) > 0) {
                $mh = curl_multi_init();

                foreach ($urls as $i => $url) {
                    $curl_array[$i] = curl_init($url);
                    curl_setopt($curl_array[$i], CURLOPT_HEADER, 0);
                    curl_setopt($curl_array[$i], CURLOPT_NOBODY, 0);
                    curl_setopt($curl_array[$i], CURLOPT_CONNECTTIMEOUT, 10);
                    curl_setopt($curl_array[$i], CURLOPT_TIMEOUT, 10000);
                    curl_setopt($curl_array[$i], CURLOPT_RETURNTRANSFER, true);

                    curl_multi_add_handle($mh, $curl_array[$i]);
                }

                $running = null;
                do {
                    curl_multi_exec($mh, $running);
                } while ($running > 0);

                foreach ($urls as $i => $url) {
                    $result[] = curl_multi_getcontent($curl_array[$i]);
                }

                foreach ($urls as $i => $url) {
                    curl_multi_remove_handle($mh, $curl_array[$i]);
                }

                curl_multi_close($mh);
            }
        } catch (Exception $exception) {
            throw new Exception($exception);
        }

        return $result;
    }

    /**
     * @param $min
     * @param $max
     *
     * @return array
     * @throws Exception
     */
    private function fillByMonth($min, $max)
    {
        try {
            $result = [];
            $diff = $this->getMonthDiff($min, $max);

            // минимальное количество месяцев = 12
            $diff = ($diff > 12) ? $diff : 12;

            for ($i = 0; $i < $diff; $i++) {
                $nowTemp = new DateTime();
                $dateMonth = $nowTemp->sub(new DateInterval('P' . $i . 'M'));

                $dateMonth = $dateMonth->format('m.Y');

                $result[] = $dateMonth;
            }
        } catch (Exception $exception) {
            throw new Exception($exception);
        }

        return $result;
    }

    /**
     * @param $min
     * @param $max
     *
     * @return float|int
     */
    private function getMonthDiff($min, $max)
    {
        try {
            $min = new DateTime($min);
            $max = new DateTime($max);

            $diff = $max->diff($min);

            return ($diff->m + 12 * $diff->y);
        } catch (Exception $e) {
        }

        return 0;
    }

    /**
     * @param $array
     * @param $key
     *
     * @return array
     * @throws Exception
     */
    private function arrayUniqueKey($array, $key)
    {
        try {
            $result = [];
            $key_array = [];

            if (count($array) > 0) {
                $i = 0;
                foreach ($array as $val) {
                    if (!in_array($val[$key], $key_array)) {
                        $key_array[$i] = $val[$key];

                        $result[$i] = $val;
                    }
                    $i++;
                }
            }
        } catch (Exception $exception) {
            throw new Exception($exception);
        }

        return $result;
    }

    /**
     * @param $data
     * @param $channelId
     *
     * @return bool
     * @throws Exception
     */
    public function checkDeletedCoubs($data, $channelId)
    {
        $allCoubsIds = [];
        $allDataIds = [];
        /**
         * @var $coubRepo CoubRepository
         */
        $coubRepo = $this->entityManager->getRepository(Coub::class);

        if (!empty($data)) {
            $allCoubs = $coubRepo->findEnabledByChannelId($channelId);

            /**
             * @var $coub Coub
             */
            foreach ($allCoubs as $coub) {
                $allCoubsIds[] = $coub->getCoubId();
            }

            foreach ($data as $coub) {
                $allDataIds[] = $coub['id'];
            }

            $diff = array_diff($allCoubsIds, $allDataIds);

            if (!empty($diff)) {
                /**
                 * @var $item Coub
                 */
                foreach ($diff as $coubId) {
                    $coub = $coubRepo->findOneBy(['coub_id' => $coubId]);

                    $coub->setDeletedAt(new DateTime());

                    $this->entityManager->persist($coub);
                }

                $this->entityManager->flush();

                return true;
            }
        }

        return false;
    }
}