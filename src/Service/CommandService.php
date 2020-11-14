<?php
declare(strict_types=1);

namespace App\Service;

use DateTime;
use Exception;

/**
 * Class CommandService
 *
 * @package App\Service
 */
class CommandService
{
    /**
     * Получить время выполнения скрипта
     *
     * @param bool     $humanize
     * @param int|null $decimals
     *
     * @return string
     * @throws Exception
     */
    public static function requestTime(bool $humanize = null, int $decimals = null): string
    {
        $time = microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'];

        return $humanize
            ? self::getHumanReadableTime((int)$time)
            : number_format($time, $decimals ?? 3);
    }

    /**
     * Преобразовать в человекочитаемый формат время из микросекунд
     *
     * @param int $time
     *
     * @return string
     * @throws Exception
     */
    public static function getHumanReadableTime(int $time): string
    {
        $timeFrom = new DateTime('@0');
        $timeTo = new DateTime("@$time");
        $days = $timeFrom->diff($timeTo)->format('%a');
        $hours = $timeFrom->diff($timeTo)->format('%h');
        $minutes = $timeFrom->diff($timeTo)->format('%i');

        $result = $timeFrom->diff($timeTo)->format('%s seconds');

        if ($days > 0) {
            $result = $timeFrom->diff($timeTo)->format('%a days %h hours %i minutes %s seconds');
        } elseif ($hours > 0) {
            $result = $timeFrom->diff($timeTo)->format('%h hours, %i minutes %s seconds');
        } elseif ($minutes > 0) {
            $result = $timeFrom->diff($timeTo)->format(' %i minutes %s seconds');
        }

        return $result;
    }
}