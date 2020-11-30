<?php
declare(strict_types=1);
/**
 * @usage php bin/console app:stat:migrate -vvv --start=START_ID --limit=LIMIT_NUM --max=MAX
 */

namespace App\Command;

use App\Service\CommandService;
use App\Service\StatMigrateService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use function date;
use function md5;

/**
 * Class MigrateStatCommand
 *
 * @package App\Command
 */
class MigrateStatCommand extends Command
{
    use LockableTrait;

    /**
     * @var string
     */
    protected static $defaultName = 'app:stat:migrate';

    /**
     * @var InputInterface Интерфейс ввода данных из консоли
     */
    private InputInterface $input;

    /**
     * @var OutputInterface Интерфейс вывода данных в консоль
     */
    private OutputInterface $output;

    /**
     * @var SymfonyStyle
     */
    private SymfonyStyle $io;

    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * @var CommandService
     */
    private CommandService $commandService;

    /**
     * @var StatMigrateService
     */
    private StatMigrateService $migrateService;

    /**
     * @param EntityManagerInterface $entityManager
     * @param CommandService         $commandService
     * @param StatMigrateService     $migrateService
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        CommandService $commandService,
        StatMigrateService $migrateService
    )
    {
        parent::__construct();

        $this->entityManager = $entityManager;
        $this->commandService = $commandService;
        $this->migrateService = $migrateService;
    }

    /**
     * @return void
     */
    protected function configure(): void
    {
        $this
            ->setDescription('Миграция данных статистики')
            ->addOption(
                'start',
                null,
                InputOption::VALUE_REQUIRED,
                'id coub-а с которой начать обработку'
            )
            ->addOption(
                'limit',
                null,
                InputOption::VALUE_REQUIRED,
                'Максимальное количество coub-ов обрабатываемых за раз'
            )
            ->addOption(
                'max',
                null,
                InputOption::VALUE_REQUIRED,
                'Общее максимальное количество coub-ов'
            );
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->commandService->setEnvironment(self::$defaultName);

        try {
            $this->io = new SymfonyStyle($input, $output);
            $this->input = $input;
            $this->output = $output;
            $startCoub = 0;
            $coubLimit = 0;
            $max = 0;

            if ($this->input->getOption('start')) {
                $startCoub = (int)$this->input->getOption('start');
            }
            if ($this->input->getOption('limit')) {
                $coubLimit = (int)$this->input->getOption('limit');
            }
            if ($this->input->getOption('max')) {
                $max = (int)$this->input->getOption('max');
            }

            $this->output->writeln(
                [
                    '[' . date('Y-m-d H:i:s') . '] Процесс запущен'
                ],
                OutputInterface::VERBOSITY_VERBOSE
            );

            $this->lock(self::class . md5(__DIR__), true);

            $this->output->writeln(
                '[' . date('Y-m-d H:i:s') . '] Включена блокировка ' . self::class,
                OutputInterface::VERBOSITY_VERBOSE
            );

            # Выполнить обработку
            $this->process($startCoub, $coubLimit, $max);

            $this->output->writeln(
                [
                    '[' . date('Y-m-d H:i:s') . '] Процесс завершен'
                ],
                OutputInterface::VERBOSITY_VERBOSE
            );

            $this->release();

            $this->output->writeln(
                '[' . date('Y-m-d H:i:s') . '] Отключена блокировка ' . self::class,
                OutputInterface::VERBOSITY_VERBOSE
            );
            $requestTime = $this->commandService->requestTime(true, 2);
            $this->output->writeln(
                '[' . date('Y-m-d H:i:s') . '] Время выполнения: ' . $requestTime
            );
        } catch (Exception $exception) {
            $this->output->writeln(
                '[' . date('Y-m-d H:i:s') . ']'
                . ' Ошибка во время выполнения: ' . PHP_EOL . $exception->getMessage()
                . ' - ' . $exception->getTraceAsString()
            );

            return 1;
        }

        return 0;
    }

    /**
     * @param int $startCoub
     * @param int $coubLimit
     *
     * @return void
     * @throws Exception
     */
    private function process(int $startCoub = 0, int $coubLimit = 0, int $max = 0)
    {
        try {
            $coubLimit = $coubLimit > 0 ? $coubLimit : 50;
            if (0 < $max && $max < $coubLimit) {
                $coubLimit = $max;
            }
            $totalCount = 0;
            $totalRecCount = 0;
            $active = true;

            while ($active) {
                $data = $this->migrateService->migrateStatisticToNew($startCoub, $coubLimit);

                if (
                    array_key_exists('coubs_count', $data)
                    && array_key_exists('coub_last_id', $data)
                ) {
                    if ($data['coubs_count']) {
                        $totalCount += (int)$data['coubs_count'];
                    }
                    if ($data['records_count']) {
                        $totalRecCount += (int)$data['records_count'];
                    }

                    if (
                        $data['coubs_count'] < $coubLimit
                        || (0 < $max && $max <= $totalCount)
                    ) {
                        $active = false;
                    }

                    if (0 < $data['coub_last_id']) {
                        $startCoub = $data['coub_last_id'] + 1;
                    }

                    $this->output->writeln(
                        [
                            '[' . date('Y-m-d H:i:s') . ']'
                            . ' Обработано коубов:' . $data['coubs_count']
                            . ', id последнего coub:' . $data['coub_last_id']
                            . ', Всего обработано коубов:' . $totalCount
                            . ', Всего обработано записей:' . $totalRecCount
                        ],
                        OutputInterface::VERBOSITY_VERBOSE
                    );
                } else {
                    $active = false;
                }
            }
        } catch (Exception $exception) {
            $this->output->writeln(
                [
                    '[' . date('Y-m-d H:i:s') . ']'
                    . ' Ошибка при обновлении: ' . PHP_EOL . $exception->getMessage()
                    . ' - Trace:' . $exception->getTraceAsString()
                    . ' - Line:' . $exception->getLine()
                ],
                OutputInterface::VERBOSITY_VERBOSE
            );
        }
    }
}
