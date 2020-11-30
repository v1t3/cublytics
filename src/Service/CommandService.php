<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\CommandLog;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

/**
 * Class CommandService
 *
 * @package App\Service
 */
class CommandService
{
    /**
     * @var string
     */
    private string $command;
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
     * @param string $command
     */
    public function setEnvironment(string $command): void
    {
        $this->command = $command;
    }

    /**
     * Получить время выполнения скрипта
     *
     * @param bool     $humanize
     * @param int|null $decimals
     *
     * @return string
     * @throws Exception
     */
    public function requestTime(bool $humanize = null, int $decimals = null): string
    {
        $time = microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'];

        return $humanize
            ? $this->getHumanReadableTime((int)$time)
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
    private function getHumanReadableTime(int $time): string
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

    /**
     * @param array $data
     *
     * @return bool
     * @throws \Exception
     */
    public function writeToLog(array $data): bool
    {
        if (empty($data)) {
            return false;
        }
        if (!$this->entityManager->isOpen()) {
            $this->entityManager = $this->entityManager->create(
                $this->entityManager->getConnection(),
                $this->entityManager->getConfiguration()
            );
        }

        $this->entityManager->clear();

        $logger = new CommandLog();

        $logger->setCommand($this->command);

        if (!empty($data['date'])) {
            $logger->setDate($data['date']);
        } else {
            $logger->setDate(new DateTime());
        }

        if (!empty($data['status']) && empty($data['error'])) {
            $logger->setStatus($data['status']);
        } else {
            $logger->setStatus(false);
        }

        if (!empty($data['message'])) {
            $logger->setMessage($data['message']);
        }

        if (!empty($data['error'])) {
            $logger->setError($data['error']);
        }

        $this->entityManager->persist($logger);
        $this->entityManager->flush();

        return true;
    }
}