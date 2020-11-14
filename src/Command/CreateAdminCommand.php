<?php
declare(strict_types=1);
/**
 * @usage php bin/console app:admin:create -vvv
 */

namespace App\Command;

use App\Entity\ConfirmationRequest;
use App\Entity\User;
use App\Repository\ConfirmationRequestRepository;
use App\Repository\UserRepository;
use App\Service\CommandService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

use function date;
use function md5;

/**
 * Class CreateAdminCommand
 *
 * @package App\Command
 */
class CreateAdminCommand extends Command
{
    use LockableTrait;

    /**
     * @var string
     */
    protected static $defaultName = 'app:admin:create';

    /**
     *
     */
    private const DEFAULT_USER_ID = 1;

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
     * @var UserPasswordEncoderInterface
     */
    private UserPasswordEncoderInterface $encoder;
    /**
     * @var \App\Service\CommandService
     */
    private CommandService $commandService;

    /**
     * ExportToFileCommand constructor.
     *
     * @param EntityManagerInterface       $entityManager
     * @param UserPasswordEncoderInterface $encoder
     * @param \App\Service\CommandService  $commandService
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        UserPasswordEncoderInterface $encoder,
        CommandService $commandService
    ) {
        parent::__construct();

        $this->entityManager = $entityManager;
        $this->encoder = $encoder;
        $this->commandService = $commandService;
    }

    /**
     * @return void
     */
    protected function configure(): void
    {
        $this
            ->setDescription('Добавление пользователя')
            ->addArgument(
                'name',
                InputArgument::REQUIRED
            )
            ->addArgument(
                'password',
                InputArgument::REQUIRED
            )
            ->addArgument(
                'email',
                InputArgument::REQUIRED
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
        $userName = '';
        $email = '';
        $password = '';

        try {
            $this->io = new SymfonyStyle($input, $output);
            $this->input = $input;
            $this->output = $output;

            if ($input->getArgument('name')) {
                $userName = $input->getArgument('name');
            }
            if ($input->getArgument('password')) {
                $password = $input->getArgument('password');
            }
            if ($input->getArgument('email')) {
                $email = $input->getArgument('email');
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

            $this->process($userName, $password, $email);

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
                . ' Ошибка: ' . PHP_EOL . $exception->getMessage()
            );
            $this->commandService->writeToLog(
                [
                    'error' => 'Данные:' . $userName . ', ' . $email
                        . ' Код: ' . $exception->getCode()
                        . ' Сообщение: ' . $exception->getMessage()
                ]
            );

            return 1;
        }

        return 0;
    }

    /**
     * @param string $userName
     * @param string $password
     * @param string $email
     *
     * @return bool
     * @throws Exception
     */
    private function process(string $userName, string $password, string $email): bool
    {
        if ('' === $userName || '' === $password || '' === $email) {
            $this->output->writeln(
                [
                    '[' . date('Y-m-d H:i:s') . '] Переданы неверные данные'
                ],
                OutputInterface::VERBOSITY_VERBOSE
            );
            $this->commandService->writeToLog(
                [
                    'error' => 'Переданы неверные данные: ' . $userName . ', ' . $email
                ]
            );

            return false;
        }

        /**
         * @var $userRepo UserRepository
         */
        $userRepo = $this->entityManager->getRepository(User::class);
        $user = $userRepo->findOneByUserId(self::DEFAULT_USER_ID);

        if ($user) {
            $user->setUsername($userName);
            $user->setPassword($this->encoder->encodePassword($user, $password));
            $user->setEmail($email);
        } else {
            $user = new User();
            $user->setUserId(self::DEFAULT_USER_ID);
            $user->setUsername($userName);
            $user->setPassword($this->encoder->encodePassword($user, $password));
            $user->setEmail($email);
            $user->setRoles(['ROLE_ADMIN']);
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $user = $userRepo->findOneByUserId(self::DEFAULT_USER_ID);

        /**
         * @var $confRepo ConfirmationRequestRepository
         */
        $confRepo = $this->entityManager->getRepository(ConfirmationRequest::class);
        $confirm = $confRepo->findOneBy(['owner_id' => $user->getId()]);

        if (!$confirm) {
            $confirm = new ConfirmationRequest();
            $confirm->setOwnerId($user);
            $confirm->setConfirmed(true);

            $this->entityManager->persist($confirm);
            $this->entityManager->flush();
        }

        $this->output->writeln(
            [
                '[' . date('Y-m-d H:i:s') . '] Пользователь создан'
            ],
            OutputInterface::VERBOSITY_VERBOSE
        );
        $this->commandService->writeToLog(
            [
                'status'  => true,
                'message' => 'Пользователь создан: ' . $userName . ', ' . $email
            ]
        );

        return true;
    }
}
