<?php
declare(strict_types=1);

namespace App\Service;

use App\AppRegistry;
use DateInterval;
use DateTime;
use Exception;
use Throwable;

/**
 * @deprecated
 * Class CoubToolsService
 *
 * @package App\Utility
 */
class CoubToolsService
{
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

            $urlApi = AppRegistry::API_COUB_SINGLE_LINK . $coubId;

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

            $urlApi = AppRegistry::API_COUB_USER_LINK . $channelName;

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

}
