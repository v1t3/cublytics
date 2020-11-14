<?php
declare(strict_types=1);
/**
 * @usage php bin/console app:coub-update -vvv --channel=channel_permalink
 */

namespace App\Command;

use App\Entity\Channel;
use App\Repository\ChannelRepository;
use App\Service\ChannelService;
use App\Service\CommandService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use Throwable;

use function date;
use function md5;

/**
 * Class CoubUpdateCommand
 *
 * Класс для загрузки данных по coub'ам в заданный период
 *
 * @package App\Command
 */
class CoubUpdateCommand extends Command
{
    use LockableTrait;

    /**
     * @var string
     */
    protected static $defaultName = 'app:coub-update';

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
     * @var ChannelService
     */
    private ChannelService $channelService;

    /**
     * @var \App\Service\CommandService
     */
    private CommandService $commandService;

    /**
     * ExportToFileCommand constructor.
     *
     * @param EntityManagerInterface      $entityManager
     * @param ChannelService              $channelService
     * @param \App\Service\CommandService $commandService
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        ChannelService $channelService,
        CommandService $commandService
    ) {
        parent::__construct();

        $this->entityManager = $entityManager;
        $this->channelService = $channelService;
        $this->commandService = $commandService;
    }

    /**
     * @return void
     */
    protected function configure(): void
    {
        $this
            ->setDescription('Загрузка данных по коубам')
            ->addOption(
                'channel',
                null,
                InputOption::VALUE_REQUIRED,
                'Загрузка данных по отдельному каналу'
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
            $singleChannel = '';

            if ($input->getOption('channel')) {
                $singleChannel = $input->getOption('channel');
            }

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

            //получить данные
            $result = $this->process($singleChannel);

            if (!$result) {
                $output->writeln(
                    '[' . date('Y-m-d H:i:s') . '] Данные для сохранения отсутствуют',
                    OutputInterface::VERBOSITY_VERBOSE
                );
                $this->commandService->writeToLog(
                    [
                        'message' => 'Данные для сохранения отсутствуют'
                    ]
                );
            }

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
            $requestTime = $this->commandService->requestTime(true, 2);
            $output->writeln(
                '[' . date('Y-m-d H:i:s') . '] Время выполнения: ' . $requestTime
            );
            $this->commandService->writeToLog(
                [
                    'status'  => true,
                    'message' => 'Время выполнения: ' . $requestTime
                ]
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

    /**
     * Подготовить данные для экспорта
     *
     * @param string $singleChannel
     *
     * @return bool
     * @throws Exception
     */
    private function process($singleChannel = ''): bool
    {
        try {
            /**
             * @var $channelRepo ChannelRepository
             */
            $channelRepo = $this->entityManager->getRepository(Channel::class);

            $params = [
                'is_active'   => true,
                'is_watching' => true
            ];

            if ('' !== (string)$singleChannel) {
                $params['channel_permalink'] = $singleChannel;
            }

            $channels = $channelRepo->findBy($params);

            if (!$channels) {
                $this->output->writeln(
                    [
                        '[' . date('Y-m-d H:i:s') . '] Отсутствуют активные каналы для слежения'
                    ],
                    OutputInterface::VERBOSITY_VERBOSE
                );

                return false;
            }

            if (is_array($channels)) {
                # посчитаем количесво обновлённых каналов
                $updated = 0;
                foreach ($channels as $channel) {
                    $permalink = $channel->getChannelPermalink();
                    $channelId = $channel->getChannelId();

                    $this->output->writeln(
                        [
                            '[' . date('Y-m-d H:i:s') . '] Обработка канала: ' . $permalink
                        ],
                        OutputInterface::VERBOSITY_VERBOSE
                    );

                    try {
                        # получим данные
                        $data = $this->channelService->getOriginalCoubs($permalink);

                        if (!empty($data)) {
                            $saveRes = $this->channelService->saveOriginalCoubs($data, $permalink, $channel);

                            if ($saveRes) {
                                $updated++;
                                $this->output->writeln(
                                    [
                                        '[' . date('Y-m-d H:i:s') . '] Данные канала обновлены'
                                    ],
                                    OutputInterface::VERBOSITY_VERBOSE
                                );
                            }

                            $checkRes = $this->channelService->checkDeletedCoubs($data, $channelId);

                            if ($checkRes) {
                                $this->output->writeln(
                                    [
                                        '[' . date('Y-m-d H:i:s') . '] Помечены удалённые коубы'
                                    ],
                                    OutputInterface::VERBOSITY_VERBOSE
                                );
                            }
                        } else {
                            $this->output->writeln(
                                [
                                    '[' . date('Y-m-d H:i:s') . '] коубы не найдены'
                                ],
                                OutputInterface::VERBOSITY_VERBOSE
                            );
                        }
                    } catch (Throwable $exception) {
                        $this->output->writeln(
                            [
                                '[' . date('Y-m-d H:i:s') . ']'
                                . ' Ошибка при обновлении канала: ' . PHP_EOL . $exception->getMessage()
                            ],
                            OutputInterface::VERBOSITY_VERBOSE
                        );
                        $this->commandService->writeToLog(
                            [
                                'error' => 'Канал: ' . $permalink . ' Код: ' . $exception->getCode()
                                    . ' Сообщение: ' . $exception->getMessage(),
                            ]
                        );

                        continue;
                    }
                }

                $this->entityManager->flush();

                $this->output->writeln(
                    [
                        '[' . date('Y-m-d H:i:s') . '] Обновлено каналов: ' . $updated
                    ]
                );
                $this->commandService->writeToLog(
                    [
                        'status'  => true,
                        'message' => 'Обновлено каналов: ' . $updated,
                    ]
                );

                return true;
            }
        } catch (Exception $exception) {
            $this->output->writeln(
                [
                    '[' . date('Y-m-d H:i:s') . ']'
                    . ' Ошибка при обновлении: ' . PHP_EOL . $exception->getMessage()
                ],
                OutputInterface::VERBOSITY_VERBOSE
            );
            $this->commandService->writeToLog(
                [
                    'error' => 'Ошибка при обновлении. Код: ' . $exception->getCode()
                        . ' Сообщение: ' . $exception->getMessage(),
                ]
            );
        }

        return false;
    }
}
