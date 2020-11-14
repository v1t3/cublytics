<?php
declare(strict_types=1);
/**
 * @usage php bin/console app:test -vvv -r/--error
 */

namespace App\Command;

use App\Service\CommandService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use function date;
use function md5;

/**
 * Class TestCommand
 *
 * @package App\Command
 */
class TestCommand extends Command
{
    use LockableTrait;

    /**
     * @var string
     */
    protected static $defaultName = 'app:test';

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
     * @var \App\Service\CommandService
     */
    private CommandService $commandService;

    /**
     * TestCommand constructor.
     *
     * @param EntityManagerInterface      $entityManager
     * @param \App\Service\CommandService $commandService
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        CommandService $commandService
    ) {
        $this->entityManager = $entityManager;
        $this->commandService = $commandService;

        parent::__construct();
    }

    /**
     * @return void
     */
    protected function configure(): void
    {
        $this
            ->setDescription('Тестовая консольная комманда')
            ->addOption(
                'error',
                'r',
                InputOption::VALUE_NONE,
                'Выбросить исключение'
            );
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->commandService->setEnvironment(self::$defaultName);

        try {
            $this->io = new SymfonyStyle($input, $output);
            $this->input = $input;
            $this->output = $output;

            $output->writeln(
                [
                    '[' . date('Y-m-d H:i:s') . '] Процесс запущен'
                ],
                OutputInterface::VERBOSITY_VERBOSE
            );

            $this->lock(self::class . md5(__DIR__), true);

            $output->writeln(
                '[' . date('Y-m-d H:i:s') . '] Включена блокировка ' . self::class,
                OutputInterface::VERBOSITY_VERBOSE
            );

            if ($input->getOption('error')) {
                throw new RuntimeException('АШИБКА!!1');
            }

            $this->commandService->writeToLog(
                [
                    'status'  => true,
                    'message' => 'Успешно выполнено',
                    'error'   => ''
                ]
            );

            $output->writeln(
                [
                    '[' . date('Y-m-d H:i:s') . '] Процесс завершен'
                ],
                OutputInterface::VERBOSITY_VERBOSE
            );

            $this->release();

            $output->writeln(
                '[' . date('Y-m-d H:i:s') . '] Отключена блокировка ' . self::class,
                OutputInterface::VERBOSITY_VERBOSE
            );
            $output->writeln(
                '[' . date('Y-m-d H:i:s') . '] Время выполнения: '
                . $this->commandService->requestTime(true, 2)
            );
        } catch (Exception $exception) {
            $output->writeln(
                '[' . date('Y-m-d H:i:s') . ']'
                . ' Ошибка во время выполнения: ' . PHP_EOL . $exception->getMessage()
            );
            $this->commandService->writeToLog(
                [
                    'error' => 'Ошибка во время выполнения. Код: ' . $exception->getCode()
                        . ' Сообщение: ' . $exception->getMessage()
                ]
            );

            return 1;
        }

        return 0;
    }
}