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
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use function date;
use function md5;
use function microtime;
use function number_format;

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
     * ExportToFileCommand constructor.
     *
     * @param EntityManagerInterface       $entityManager
     * @param UserPasswordEncoderInterface $encoder
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        UserPasswordEncoderInterface $encoder
    )
    {
        $this->entityManager = $entityManager;
        $this->encoder = $encoder;

        parent::__construct();
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
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        //todo Добавить логирование
        try {
            $this->io = new SymfonyStyle($input, $output);
            $this->input = $input;
            $this->output = $output;
            $userName = '';
            $password = '';
            $email = '';

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
            $output->writeln(
                '[' . date('Y-m-d H:i:s') . '] Время выполнения: '
                . $this->requestTime(true, 2)
            );
        } catch (Exception $exception) {
            throw new RuntimeException((string)$exception);
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
    private function process(string $userName, string $password, string $email)
    {
        if (
            '' === (string)$userName
            || '' === (string)$password
            || '' === (string)$email
        ) {
            $this->output->writeln(
                [
                    '[' . date('Y-m-d H:i:s') . '] Переданы неверные данные'
                ],
                OutputInterface::VERBOSITY_VERBOSE
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

        return true;
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
    private function requestTime(bool $humanize = null, int $decimals = null): string
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
}
