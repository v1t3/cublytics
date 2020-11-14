<?php
declare(strict_types=1);

namespace App\Schedule;

use Zenstruck\ScheduleBundle\Schedule;
use Zenstruck\ScheduleBundle\Schedule\ScheduleBuilder;

class AppScheduleBuilder implements ScheduleBuilder
{
    public function buildSchedule(Schedule $schedule): void
    {
        $schedule
            ->timezone('UTC')
//            ->environments('prod')
        ;

        $schedule->addCommand('app:test')
            ->description('Test console')
            ->everyMinute()
        ;

    }
}