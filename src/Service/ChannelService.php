<?php
declare(strict_types=1);


namespace App\Service;


use App\AppRegistry;
use App\Entity\Channel;
use Doctrine\ORM\EntityManagerInterface;
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
    public function saveUserChannels($data)
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
            $repo = $this->entityManager->getRepository('App:Channel');

            foreach ($channels as $channel) {
                $channelStored = $repo->findOneByChannelId($channel['id']);

                if (!$channelStored) {
                    $ch = new Channel();
                    $ch->setChannelId($channel['id']);
                    $ch->setChannelPermalink($channel['permalink']);
                    $ch->setUserId($userId);
                    $ch->setIsWatching(false);
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
     * @param $channels
     *
     * @return bool
     */
    public function updateUserChannels($channels)
    {
        return false;
    }

    /**
     * @return array
     */
    public function getChannelsList()
    {
        $channels = [];
        $user = $this->security->getUser();

        if (!$user) {
            return [
                'result'   => 'error',
                'message'  => 'Пользователь не найден',
                'channels' => $channels
            ];
        }

        $userId = $user->getUserId();

        $userChannels = $this->entityManager
            ->getRepository(Channel::class)
            ->findBy(['user_id' => $userId]);

        if ($userChannels) {
            foreach ($userChannels as $userChannel) {
                $channels[] = [
                    'avatar'      => $userChannel->getAvatar(),
                    'name'        => $userChannel->getChannelPermalink(),
                    'is_watching' => $userChannel->getIsWatching(),
                ];
            }

            $data = [
                'result'   => 'success',
                'message'  => '',
                'channels' => $channels,
            ];
        } else {
            $data = [
                'result'   => 'error',
                'message'  => 'Каналы не найдены',
                'channels' => $channels,
            ];
        }

        return $data;
    }

    /**
     * @param Request $request
     *
     * @return array
     * @throws \Exception
     */
    public function getChannelStatistic(Request $request)
    {
        $result = [];
        $channelName = (string)$request->request->get('channel_name');

        if ('' === $channelName) {
            throw new \Exception('Не корректно или не заполнено поле channel_name');
        }

        $urlTale = '&per_page=' . AppRegistry::TIMELINE_PER_PAGE . '&order_by=' . AppRegistry::TIMELINE_ORDER_BY;
        //получим основные данные канала
        $data = $this->getInfo(AppRegistry::API_COUB_TIMELINE_LINK . $channelName . '?page=1' . $urlTale);

        // проверим, что вернулся не html
        if (false !== strpos((string)$data, '<!DOCTYPE html>')) {
            throw new \Exception('Некорректный ответ от сервиса');
        }
        if ('' === (string)$data) {
            throw new \Exception('Возвращён пустой ответ');
        }

        $decodeData = json_decode(html_entity_decode($data), true);

        if (
            is_array($decodeData)
            && array_key_exists('total_pages', $decodeData)
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
                        $allCoubs = array_merge($allCoubs, $decodeTemp['coubs']);
                    }
                }

                # уберём дубликаты коубов
                $result['coubs'] = $this->arrayUniqueKey($allCoubs, 'id');
            } elseif (1 === $decodeData['total_pages']) {
                $result['coubs'] = $decodeData['coubs'];
            }
        }

        $arCountDatesTotal = [];
        $arCountDatesSelf = [];
        $arCountDatesReposts = [];

        # получим данные коубов
        if (
            !empty($result['coubs'])
            && is_array($result['coubs'])
        ) {
            $result['total_coubs'] = count($result['coubs']);
            // начальные значения мин. и макс. дат
            $createdDateMin = 'now';
            $createdDateMax = 0;

            foreach ($result['coubs'] as $coub) {
                $date = strtotime($coub['created_at']);
                $monthDate = date('m.Y', $date);

                # Если recoub_to не существует, то coub свой
                if (empty($coub['recoub_to'])) {
                    if (array_key_exists('self_coubs', $result)) {
                        $result['self_coubs']++;
                    } else {
                        $result['self_coubs'] = 1;
                    }

                    if (array_key_exists($monthDate, $arCountDatesSelf)) {
                        $arCountDatesSelf[$monthDate] ++;
                    } else {
                        $arCountDatesSelf[$monthDate] = 1;
                    }

                    if (array_key_exists('total_likes', $result)) {
                        $result['total_likes'] += $coub['likes_count'];
                    } else {
                        $result['total_likes'] = $coub['likes_count'];
                    }
                } else {
                    if (array_key_exists('reposted', $result)) {
                        $result['reposted']++;
                    } else {
                        $result['reposted'] = 1;
                    }

                    if (array_key_exists($monthDate, $arCountDatesReposts)) {
                        $arCountDatesReposts[$monthDate]++;
                    } else {
                        $arCountDatesReposts[$monthDate] = 1;
                    }
                }

                if (
                    array_key_exists('banned', $coub)
                    && true === $coub['banned']
                ) {
                    $result['banned']++;
                }

                // получим самую маленькую и большую даты
                if ($date) {
                    if ((int)strtotime($createdDateMin) > (int)$date) {
                        $createdDateMin = date('d.m.Y', $date);
                    }

                    if (
                        (int)$createdDateMax === 0
                        || ((int)strtotime($createdDateMax) < (int)$date)
                    ) {
                        $createdDateMax = date('d.m.Y', $date);
                    }
                }

                // массив вида [дата => количество элементов]
                if (array_key_exists($monthDate, $arCountDatesTotal)) {
                    $arCountDatesTotal[$monthDate]++;
                } else {
                    $arCountDatesTotal[$monthDate] = 1;
                }
            }

            // пустой помесячный массив
            $arCoubsByMonth = $this->fillByMonth($createdDateMin, $createdDateMax);

            for ($i = 0, $count = count($arCoubsByMonth); $i < $count; $i++) {
                $dateMonth = $arCoubsByMonth[$i];

                $result['total_points_month'][$i] = [
                    'date'  => $dateMonth,
                    'count' => (array_key_exists($dateMonth, $arCountDatesTotal))
                        ? (int)$arCountDatesTotal[$dateMonth]
                        : 0
                ];

                $result['self_points_month'][$i] = [
                    'date'  => $dateMonth,
                    'count' => (array_key_exists($dateMonth, $arCountDatesSelf))
                        ? (int)$arCountDatesSelf[$dateMonth]
                        : 0

                ];

                $result['reposts_points_month'][$i] = [
                    'date'  => $dateMonth,
                    'count' => (array_key_exists($dateMonth, $arCountDatesReposts))
                        ? (int)$arCountDatesReposts[$dateMonth]
                        : 0
                ];
            }
        }

        //развернуть массивы для привильной последовательности дат
        if (
            array_key_exists('total_points_month', $result)
            && !empty($result['total_points_month'])
        ) {
            $result['total_points_month'] = array_reverse($result['total_points_month']);
        }
        if (
            array_key_exists('self_points_month', $result)
            && !empty($result['self_points_month'])
        ) {
            $result['self_points_month'] = array_reverse($result['self_points_month']);

        }
        if (
            array_key_exists('reposts_points_month', $result)
            && !empty($result['reposts_points_month'])
        ) {
            $result['reposts_points_month'] = array_reverse($result['reposts_points_month']);
        }

        unset($result['coubs']);

        return $result;
    }

    /**
     * @param string $url
     *
     * @return bool|string
     * @throws \Exception
     */
    private function getInfo(string $url)
    {
        try {
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $data = curl_exec($ch);

            if (curl_errno($ch)) {
                $data = curl_error($ch);
            }

            curl_close($ch);
        } catch (\Exception $exception) {
            throw new \Exception($exception);
        }

        return $data;
    }

    /**
     * @param array $urls
     *
     * @return array
     * @throws \Exception
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
        } catch (\Exception $exception) {
            throw new \Exception($exception);
        }

        return $result;
    }

    /**
     * @param $urls
     *
     * @return array
     * @throws \Exception
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
                    curl_setopt($curl_array[$i], CURLOPT_TIMEOUT, 6000);
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
        } catch (\Exception $exception) {
            throw new \Exception($exception);
        }

        return $result;
    }

    /**
     * @param $min
     * @param $max
     *
     * @return array
     * @throws \Exception
     */
    private function fillByMonth($min, $max)
    {
        try {
            $result = [];
            $diff = $this->getMonthDiff($min, $max);

            // минимальное количество месяцев = 12
            $diff = ($diff > 12) ? $diff : 12;

            for ($i = 0; $i < $diff; $i++) {
                $nowTemp = new \DateTime();
                $dateMonth = $nowTemp->sub(new \DateInterval('P' . $i . 'M'));

                $dateMonth = $dateMonth->format('m.Y');

                $result[] = $dateMonth;
            }
        } catch (\Exception $exception) {
            throw new \Exception($exception);
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
            $min = new \DateTime($min);
            $max = new \DateTime($max);

            $diff = $max->diff($min);

            return ($diff->m + 12 * $diff->y);
        } catch (\Exception $e) {
        }

        return 0;
    }

    /**
     * @param $array
     * @param $key
     *
     * @return array
     * @throws \Exception
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
        } catch (\Exception $exception) {
            throw new \Exception($exception);
        }

        return $result;
    }
}