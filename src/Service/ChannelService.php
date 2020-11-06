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
use App\Repository\UserRepository;
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
     * @throws Exception
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

        /**
         * Получим пользователя для установки связи
         *
         * @var $userRepo UserRepository
         */
        $userRepo = $this->entityManager->getRepository(User::class);
        $user = $userRepo->findOneBy(['user_id' => $userId]);

        if ($user && !empty($channels)) {
            /**
             * @var $repo ChannelRepository
             */
            $repo = $this->entityManager->getRepository(Channel::class);

            foreach ($channels as $channel) {
                if ($this->isChannelExist($channel['permalink'])) {
                    $channelStored = $repo->findOneByChannelId($channel['id']);

                    $avatar = str_replace(
                        '%{version}',
                        'profile_pic_big',
                        $channel['avatar_versions']['template']
                    );

                    if (!$channelStored) {
                        $ch = new Channel();
                        $ch->setOwnerId($user);
                        $ch->setChannelId($channel['id']);
                        $ch->setChannelPermalink($channel['permalink']);
                        $ch->setUserId($userId);
                        $ch->setIsWatching(true);
                        $ch->setIsActive(true);
                        $ch->setIsCurrent((int)$channel['id'] === (int)$current);
                        $ch->setTitle($channel['title']);
                        $ch->setCreatedAt($channel['created_at']);
                        $ch->setUpdatedAt($channel['updated_at']);
                        $ch->setFollowersCount($channel['followers_count']);
                        $ch->setStoriesCount($channel['stories_count']);
                        $ch->setAvatar($avatar);

                        $this->entityManager->persist($ch);
                        $this->entityManager->flush();
                    } else {
                        $channelStored->setChannelPermalink($channel['permalink']);
                        $channelStored->setTitle($channel['title']);
                        $channelStored->setUpdatedAt($channel['updated_at']);
                        $channelStored->setFollowersCount($channel['followers_count']);
                        $channelStored->setStoriesCount($channel['stories_count']);
                        $channelStored->setAvatar($avatar);

                        $this->entityManager->persist($channelStored);
                        $this->entityManager->flush();
                    }
                }
            }

            return true;
        }

        return false;
    }

    /**
     * Проверка существования канала
     *
     * @param $channelName
     *
     * @return bool
     */
    public function isChannelExist($channelName)
    {
        if ('' !== (string)$channelName) {
            $result = get_headers(AppRegistry::HTTPS_COUB . $channelName);

            if ($result && strpos($result[0], '200')) {
                return true;
            }
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

        # Переведём значение в "настоящий" boolean
        $newVal = ($newVal === 'true');

        /**
         * @var $channelRepo ChannelRepository
         * @var $channel     Channel
         */
        $channelRepo = $this->entityManager->getRepository(Channel::class);
        $channel = $channelRepo->findOneBy(['channel_permalink' => $channelPermalink]);

        if (!$channel) {
            throw new Exception('Канал не найден');
        }

        if ('is_active' === $type) {
            $channel->setIsActive((bool)$newVal);

            # Если канал отмечается неактивным,
            # то также отключается наблюдение
            if (false === $newVal) {
                $channel->setIsWatching(false);
            }

            $this->entityManager->persist($channel);
            $this->entityManager->flush();
        } elseif (
            'is_watching' === $type
            && true === $channel->getIsActive()
        ) {
            $channel->setIsWatching((bool)$newVal);

            $this->entityManager->persist($channel);
            $this->entityManager->flush();
        }

        $result = [
            'is_active'   => $channel->getIsActive(),
            'is_watching' => $channel->getIsWatching()
        ];

        return [
            'success' => $newVal === $result,
            'result'  => $result
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
         * @var $userChannels     Channel
         * @var $userChannel      Channel
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
                    'dislikes_count'  => $userChannel->getDislikesCount(),
                    'followers_count' => $userChannel->getFollowersCount(),
                    'reposts_count'   => $userChannel->getRepostsCount(),
                    'recoubs_count'   => $userChannel->getRemixesCount(),
                    'stories_count'   => $userChannel->getStoriesCount(),
                    'kd_count'        => $userChannel->getKdCount(),
                    'featured_count'  => $userChannel->getFeaturedCount(),
                    'banned_count'    => $userChannel->getBannedCount(),
                ];
            }
        }

        return $channels;
    }

    /**
     * @param Request $request
     *
     * @return array
     * @throws Exception
     */
    public function getChannelStatistic(Request $request)
    {
        $channelName = (string)$request->request->get('channel_name');
        $statType = (string)$request->request->get('statistic_type');
        $timezone = (string)$request->request->get('timezone');

        $result = [];

        if ('' === $channelName || '' === $statType) {
            throw new Exception('Не указано поле channel_name или type');
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
         * @var $channelRepo ChannelRepository
         * @var $channel     Channel
         */
        $channelRepo = $this->entityManager->getRepository(Channel::class);
        $channel = $channelRepo->findOneBy(['channel_permalink' => $channelName]);

        if ($channel && 0 < (int)$channel->getChannelId()) {
            $channelId = $channel->getChannelId();

            $result['total'] = [
                'followers_count' => $channel->getFollowersCount(),
                'views_count'     => $channel->getViewsCount(),
                'likes_count'     => $channel->getLikesCount(),
                'dislikes_count'  => $channel->getDislikesCount(),
                'repost_count'    => $channel->getRepostsCount(),
                'remixes_count'   => $channel->getRemixesCount(),
                'stories_count'   => $channel->getStoriesCount(),
                'kd_count'        => $channel->getKdCount(),
                'featured_count'  => $channel->getFeaturedCount(),
                'banned_count'    => $channel->getBannedCount(),
            ];

            /**
             * @var $coubsStatRepo CoubStatRepository
             * @var $coubsStat     CoubStat
             */
            $coubsStatRepo = $this->entityManager->getRepository(CoubStat::class);
            $coubsStat = $coubsStatRepo->findByPeriodChannel($channelId, $dateStart, $dateEnd);

            if (!empty($coubsStat)) {
                /**
                 * @var $coub CoubStat
                 */
                foreach ($coubsStat as $coub) {
                    $coubId = $coub->getCoubId();

                    $result['counts'][] = [
                        'coub_id'        => $coubId,
                        'timestamp'      => $coub->getDateCreate()
                            ->modify($timezone . ' hour')
                            ->format($dateFormat),
                        'like_count'     => $coub->getLikeCount(),
                        'repost_count'   => $coub->getRepostCount(),
                        'recoubs_count'  => $coub->getRemixesCount(),
                        'views_count'    => $coub->getViewsCount(),
                        'dislikes_count' => $coub->getDislikesCount(),
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

        if (
            is_array($decodeData)
            || array_key_exists('total_pages', $decodeData)
        ) {
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
                        $allCoubs[] = $decodeTemp['coubs'];
                    }
                }
                # сольём массив
                $result = array_merge([], ...$allCoubs);

                # уберём дубликаты коубов
                $result = $this->arrayUniqueKey($result, 'id');
            } elseif (1 === $decodeData['total_pages']) {
                $result = $decodeData['coubs'];
            }
        }

        return $result;
    }

    /**
     * @param      $data
     * @param      $channelName
     * @param null $channel
     *
     * @return bool
     * @throws Exception
     */
    public function saveOriginalCoubs($data, $channelName, $channel = null)
    {
        if (!$data) {
            return false;
        }

        if (!$channel) {
            /**
             * @var $channelRepo ChannelRepository
             * @var $channel     Channel
             */
            $channelRepo = $this->entityManager->getRepository(Channel::class);
            $channel = $channelRepo->findOneBy(['channel_permalink' => $channelName]);
        }

        if ($channel && 0 < (int)$channel->getChannelId()) {
            $channelId = $channel->getChannelId();
            /**
             * @var $coubRepo CoubRepository
             */
            $coubRepo = $this->entityManager->getRepository(Coub::class);

            $tempChannel = [
                'views_count'    => 0,
                'likes_count'    => 0,
                'reposts_count'  => 0,
                'remixes_count'  => 0,
                'dislikes_count' => 0,
                'kd_count'       => 0,
                'featured_count' => 0,
                'banned_count'   => 0,
            ];

            foreach ($data as $coub) {
                /**
                 * Проверить существует ли текущий coub
                 *
                 * @var $coubItem Coub
                 */
                $coubItem = $coubRepo->findOneBy(['coub_id' => $coub['id']]);

                if ($coubItem) {
                    if ($coubItem->getUpdatedAt() !== (new DateTime($coub['updated_at']))) {
                        $coubItem->setChannelId($channelId);
                        $coubItem->setTitle($coub['title']);
                        $coubItem->setUpdatedAt($coub['updated_at']);
                        $coubItem->setIsKd($coub['cotd']);
                        $coubItem->setFeatured($coub['featured']);
                        $coubItem->setBanned($coub['banned']);

                        $this->entityManager->persist($coubItem);
                    }
                } else {
                    # Если recoub_to не существует, то coub свой
                    if (empty($coub['recoub_to'])) {
                        $coubItem = new Coub();
                        $coubItem->setOwnerId($channel);
                        $coubItem->setCoubId($coub['id']);
                        $coubItem->setChannelId($channelId);
                        $coubItem->setPermalink($coub['permalink']);
                        $coubItem->setTitle($coub['title']);
                        $coubItem->setCreatedAt($coub['created_at']);
                        $coubItem->setUpdatedAt($coub['updated_at']);
                        $coubItem->setIsKd($coub['cotd']);
                        $coubItem->setFeatured($coub['featured']);
                        $coubItem->setBanned($coub['banned']);
                        $this->entityManager->persist($coubItem);
                    }
                }

                # Если recoub_to не существует, то coub свой
                if (empty($coub['recoub_to'])) {
                    $coubStatItem = new CoubStat();
                    $coubStatItem->setOwnerId($channel);
                    $coubStatItem->setCoubId($coub['id']);
                    $coubStatItem->setChannelId($channelId);
                    $coubStatItem->setViewsCount($coub['views_count']);
                    $coubStatItem->setLikeCount($coub['likes_count']);
                    $coubStatItem->setRepostCount($coub['recoubs_count']);
                    $coubStatItem->setRemixesCount($coub['remixes_count']);
                    $coubStatItem->setDislikesCount($coub['dislikes_count']);
                    $coubStatItem->setIsKd($coub['cotd']);
                    $coubStatItem->setFeatured($coub['featured']);
                    $coubStatItem->setBanned($coub['banned']);
                    $this->entityManager->persist($coubStatItem);

                    // подготовить данные для канала
                    $tempChannel['views_count'] = (int)$tempChannel['views_count'] + (int)$coub['views_count'];
                    $tempChannel['likes_count'] = (int)$tempChannel['likes_count'] + (int)$coub['likes_count'];
                    $tempChannel['reposts_count'] = (int)$tempChannel['reposts_count'] + (int)$coub['recoubs_count'];
                    $tempChannel['remixes_count'] = (int)$tempChannel['remixes_count'] + (int)$coub['remixes_count'];
                    $tempChannel['dislikes_count'] = (int)$tempChannel['dislikes_count'] + (int)$coub['dislikes_count'];
                    if (true === (bool)$coub['cotd']) {
                        $tempChannel['kd_count']++;
                    }
                    if (true === (bool)$coub['featured']) {
                        $tempChannel['featured_count']++;
                    }
                    if (true === (bool)$coub['banned']) {
                        $tempChannel['banned_count']++;
                    }
                }
            }

            // обновить channel
            $channel->setViewsCount($tempChannel['views_count']);
            $channel->setLikesCount($tempChannel['likes_count']);
            $channel->setRepostsCount($tempChannel['reposts_count']);
            $channel->setRemixesCount($tempChannel['remixes_count']);
            $channel->setDislikesCount($tempChannel['dislikes_count']);
            $channel->setKdCount($tempChannel['kd_count']);
            $channel->setFeaturedCount($tempChannel['featured_count']);
            $channel->setBannedCount($tempChannel['banned_count']);

            $this->entityManager->persist($channel);

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
    public function getInfo(string $url)
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
    public function getInfoByUrls(array $urls): array
    {
        $result = [];

        try {
            $resultTemp = [];

            if (count($urls) > 50) {
                $urlsMulti = array_chunk($urls, 50);

                foreach ($urlsMulti as $url) {
                    $temp = $this->getInfoMulti($url);

                    if (is_array($temp)) {
                        $resultTemp = (array)$temp;
                    }
                }

                # сольём массив
                if (is_array($resultTemp)) {
                    $result = array_merge([], ...(array)$resultTemp);
                }
            } else {
                $result = $this->getInfoMulti($urls);
            }
        } catch (Exception $exception) {
            throw new Exception($exception->getMessage());
        }

        if (!is_array($result)) {
            $result = [];
        }

        return $result;
    }

    /**
     * @param $urls
     *
     * @return array
     * @throws Exception
     */
    private function getInfoMulti($urls): array
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
                    curl_setopt($curl_array[$i], CURLOPT_TIMEOUT, 20000);
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
    public function arrayUniqueKey($array, $key): array
    {
        $result = [];
        $key_array = [];

        if (count($array) > 0) {
            $i = 0;
            foreach ($array as $val) {
                if (
                    array_key_exists($key, $val)
                    && !in_array($val[$key], $key_array)
                ) {
                    $key_array[$i] = $val[$key];

                    $result[$i] = $val;
                }
                $i++;
            }
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
    public function checkDeletedCoubs($data, $channelId): bool
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