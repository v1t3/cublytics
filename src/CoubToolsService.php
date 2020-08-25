<?php
declare(strict_types=1);

namespace App;

use DateInterval;
use DateTime;
use Exception;
use Throwable;

/**
 * Class CoubToolsService
 *
 * @package App\Utility
 */
class CoubToolsService
{
    /**
     *
     */
    public const API_COUB_SINGLE_LINK = 'https://coub.com/api/v2/coubs/';

    /**
     *
     */
    public const API_COUB_USER_LINK = 'https://coub.com/api/v2/channels/';

    /**
     *
     */
    public const API_COUB_TIMELINE_LINK = 'https://coub.com/api/v2/timeline/channel/';

    /**
     *
     */
    public const TIMELINE_PER_PAGE = 20;

    /**
     *
     */
    public const TIMELINE_ORDER_BY = 'newest_popular';

    /**
     *
     */
    public const REQUEST_AUTHORIZE_APP = 'http://coub.com/oauth/authorize';

    /**
     *
     */
    public const REQUEST_REVOKE_APP = 'http://coub.com/oauth/revoke';

    /**
     *
     */
    public const REDIRECT_CALLBACK = 'https://d4aabb60b869.ngrok.io/api/coub/callback';

    /**
     *
     */
    public const REQUEST_ACCESS_TOKEN = 'http://coub.com/oauth/token';

    /**
     *
     */
    public const REQUEST_USER_INFO = 'https://coub.com/api/v2/users/me';

    /**
     * @param string $coubId
     *
     * @return bool|string
     */
    public function getCoubData(string $coubId)
    {
        $result = '';

        try {
            if ((string)$coubId === '') {
                return $result;
            }

            if (strpos($coubId, 'https://coub.com/view/') !== false) {
                $coubId = str_replace('https://coub.com/view/', '', $coubId);
            }

            $urlApi = self::API_COUB_SINGLE_LINK . $coubId;

            $data = $this->getInfo($urlApi);

            if (
                '' !== (string)$data
                && false === strpos((string)$data, '<!DOCTYPE html>')
            ) {
                $result = $data;
            }
        } catch (Exception $e) {
            trigger_error($e);
        }

        return $result;
    }

    /**
     * @param string $channelName
     *
     * @return bool|string
     */
    public function getUserData(string $channelName)
    {
        $result = '';

        try {
            if ((string)$channelName === '') {
                return $result;
            }

            if (strpos($channelName, 'https://coub.com/') !== false) {
                $channelName = str_replace('https://coub.com/', '', $channelName);
            }

            $urlApi = self::API_COUB_USER_LINK . $channelName;

            $data = $this->getInfo($urlApi);

            if (
                '' !== (string)$data
                && false === strpos((string)$data, '<!DOCTYPE html>')
            ) {
                $result = $data;
            }
        } catch (Exception $e) {
            trigger_error($e);
        }

        return $result;
    }


    /**
     * @param string $channelName
     *
     * @return array|string
     */
    public function getChannelPerf(string $channelName)
    {
        $result = '';

        try {
            if ((string)$channelName === '') {
                return $result;
            }

            if (strpos($channelName, 'https://coub.com/') !== false) {
                $channelName = str_replace('https://coub.com/', '', $channelName);
            }

            $urlTale = '&per_page=' . self::TIMELINE_PER_PAGE . '&order_by=' . self::TIMELINE_ORDER_BY;

            $data = $this->getInfo(self::API_COUB_TIMELINE_LINK . $channelName . '?page=1' . $urlTale);

            // проверим, что вернулся не html
            if (false !== strpos((string)$data, '<!DOCTYPE html>')) {
                return $result;
            }

            if ('' !== (string)$data) {
                $result = [];
                $decodeData = json_decode(html_entity_decode($data), true);

                if ($decodeData['total_pages'] > 1) {
                    $urls = [];
                    # сохраним уже полученную 1ю страницу
                    $encodeData[] = $data;

                    # получим грязный список страниц всех коубов
                    for ($i = 2; $i <= $decodeData['total_pages']; $i++) {
                        $urls[] = self::API_COUB_TIMELINE_LINK . $channelName . '?page=' . $i . $urlTale;
                    }

                    $others = $this->getUrls($urls);

                    if (is_array($others)) {
                        $encodeData = array_merge($encodeData, $others);
                    }

                    # получаем коубы постранично и объединяем в общий массив
                    $res = [];
                    foreach ($encodeData as $item) {
                        $decodeTemp = json_decode(html_entity_decode($item), true);
                        if (is_array($decodeTemp['coubs'])) {
                            $res = array_merge($res, $decodeTemp['coubs']);
                        }
                    }

                    # уберём дубликаты коубов
                    $result['coubs'] = $this->arrayUniqueKey($res, 'id');
                } elseif ($decodeData['total_pages'] === 1) {
                    $result['coubs'] = $decodeData['coubs'];
                }


                $arCountDatesTotal = [];
                $arCountDatesSelf = [];
                $arCountDatesReposts = [];
                $createdDateMin = 'now';
                $createdDateMax = 0;

                # получим данные коубов
                if (!empty($result['coubs'])) {
                    $result['total_coubs'] = count($result['coubs']);

                    foreach ($result['coubs'] as $coub) {
                        $date = strtotime($coub['created_at']);
                        $monthDate = date('m.Y', $date);

                        if (!$coub['recoub_to']) {  # свои коубы
                            if (array_key_exists('self_coubs', $result)) {
                                $result['self_coubs']++;
                            } else {
                                $result['self_coubs'] = 1;
                            }

                            if (array_key_exists($monthDate, $arCountDatesSelf)) {
                                $arCountDatesSelf[$monthDate] += $arCountDatesSelf[$monthDate];
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

                        if ($coub['banned'] === true) {
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
                            'date' => $dateMonth,
                            'count' => (array_key_exists($dateMonth, $arCountDatesTotal))
                                ? (int)$arCountDatesTotal[$dateMonth]
                                : 0
                        ];

                        $result['self_points_month'][$i] = [
                            'date' => $dateMonth,
                            'count' => (array_key_exists($dateMonth, $arCountDatesSelf))
                                ? (int)$arCountDatesSelf[$dateMonth]
                                : 0
                        ];

                        $result['reposts_points_month'][$i] = [
                            'date' => $dateMonth,
                            'count' => (array_key_exists($dateMonth, $arCountDatesReposts))
                                ? (int)$arCountDatesReposts[$dateMonth]
                                : 0
                        ];
                    }
                }

                if (
                    $result['total_points_month']
                    && (bool)array_filter($result['total_points_month'])
                ) {
                    $result['total_points_month'] = array_reverse($result['total_points_month']);
                } else {
                    unset($result['total_points_month']);
                }

                if (
                    $result['self_points_month']
                    && (bool)array_filter($result['self_points_month'])
                ) {
                    $result['self_points_month'] = array_reverse($result['self_points_month']);
                } else {
                    unset($result['self_points_month']);
                }

                if (
                    $result['reposts_points_month']
                    && (bool)array_filter($result['reposts_points_month'])
                ) {
                    $result['reposts_points_month'] = array_reverse($result['reposts_points_month']);
                } else {
                    unset($result['reposts_points_month']);
                }

                unset($result['coubs']);
            }
        } catch (Exception $e) {
            trigger_error($e);
        }

        return $result;
    }

    /**
     * Возвращает количество месяцев
     * с даты публикации по сегодняшний день
     *
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
     * @param $min
     * @param $max
     *
     * @return array
     */
    private function fillByMonth($min, $max)
    {
        $res = [];

        try {
            $diff = $this->getMonthDiff($min, $max);

            // минимальное количество месяцев = 12
            $diff = ($diff > 12) ? $diff : 12;

            for ($i = 0; $i < $diff; $i++) {
                $nowTemp = new DateTime();
                $dateMonth = $nowTemp->sub(new DateInterval('P' . $i . 'M'));

                $dateMonth = $dateMonth->format('m.Y');

                $res[] = $dateMonth;
            }
        } catch (Exception $e) {
            trigger_error($e);
        }

        return $res;
    }

    /**
     * @param $array
     * @param $key
     *
     * @return array
     */
    private function arrayUniqueKey($array, $key)
    {
        try {
            $tmp = [];
            $key_array = [];
            $i = 0;

            if (count($array) > 0) {
                foreach ($array as $val) {
                    if (!in_array($val[$key], $key_array)) {
                        $key_array[$i] = $val[$key];
                        $tmp[$i] = $val;
                    }
                    $i++;
                }
            }
        } catch (Exception $e) {
            trigger_error($e);
        }

        return $tmp;
    }

    /**
     * @param $url
     *
     * @return bool|string
     */
    private function getInfo(string $url)
    {
        $data = '';

        try {
            if ((string)$url === '') {
                return $data;
            }

            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $data = curl_exec($ch);

            if (curl_errno($ch)) {
                $data = curl_error($ch);
            }

            curl_close($ch);
        } catch (Throwable $e) {
            trigger_error($e);
        }

        return $data;
    }

    /**
     * @param array $urls
     *
     * @return array
     */
    private function getUrls(array $urls)
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
        } catch (Exception $e) {
            trigger_error($e);
        }

        return $result;
    }

    /**
     * Паралельная загрузка страниц при количестве более одной
     *
     * @param $urls
     *
     * @return array
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
        } catch (Throwable $e) {
            trigger_error($e);
        }

        return $result;
    }

    /**
     * @param string $code
     *
     * @return bool|string
     * @throws Exception
     */
    public function getUserToken(string $code)
    {
        if (
            (string)$_ENV['COUB_KEY'] !== ''
            && (string)$_ENV['COUB_SECRET'] !== ''
            && $code !== ''
        ) {
            $postfields = 'grant_type=authorization_code'
                . '&redirect_uri=' . self::REDIRECT_CALLBACK
                . '&client_id=' . $_ENV['COUB_KEY']
                . '&client_secret=' . $_ENV['COUB_SECRET']
                . '&code=' . $code;

            //todo Сделать на response
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, self::REQUEST_ACCESS_TOKEN);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $postfields);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            $out = curl_exec($curl);

            curl_close($curl);
        } else {
            throw new \Exception('env or code empty');
        }

        return $out;
    }

    /**
     * @param string $token
     *
     * @return array|mixed
     */
    public function getUserInfo(string $token)
    {
        $data = [];

        if ('' !== $token) {
            $temp = $this->getInfo(self::REQUEST_USER_INFO . '?access_token=' . $token);

            if ('' !== (string)$temp) {
                $data = json_decode($temp, true);
            }
        }

        return $data;
    }
}
