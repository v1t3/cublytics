<?php
declare(strict_types=1);

namespace App\Service;

use App\AppRegistry;
use App\Entity\ConfirmationRequest;
use App\Entity\User;
use App\Repository\ConfirmationRequestRepository;
use App\Repository\UserRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;

/**
 * Class UserService
 *
 * @package App
 */
class UserService
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * @var Security
     */
    private Security $security;

    /**
     * @var UserPasswordEncoderInterface
     */
    private UserPasswordEncoderInterface $encoder;

    /**
     * @param EntityManagerInterface       $entityManager
     * @param Security                     $security
     * @param UserPasswordEncoderInterface $encoder
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        Security $security,
        UserPasswordEncoderInterface $encoder
    )
    {
        $this->entityManager = $entityManager;
        $this->security = $security;
        $this->encoder = $encoder;
    }

    /**
     * @param string $token
     *
     * @return array|mixed
     */
    public function getInfo(string $token)
    {
        $data = [];
        $temp = '';

        try {
            if ('' === (string)$token) {
                return $data;
            }
            $url = AppRegistry::REQUEST_USER_INFO . '?access_token=' . $token;

            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $temp = curl_exec($ch);

            if (curl_errno($ch)) {
                $temp = curl_error($ch);
            }

            curl_close($ch);
        } catch (Exception $e) {
            trigger_error($e);
        }

        if ('' !== (string)$temp) {
            $data = json_decode($temp, true);
        }

        return $data;
    }

    /**
     * @param $tokenData
     * @param $userData
     *
     * @return bool|int|null
     * @throws Exception
     */
    public function saveUser($tokenData, $userData)
    {
        if (isset($userData['id'])) {
            /**
             * @var $userAccountRepo UserRepository
             */
            $userAccountRepo = $this->entityManager->getRepository('App:User');
            $user = $userAccountRepo->findOneByUserId($userData['id']);

            if (!$user) {
                $user = new User();
                $user->setToken($tokenData['access_token']);
                $user->setTokenExpiredAt((int)$tokenData['expires_in'] + (int)$tokenData['created_at']);
                $user->setRoles(['ROLE_USER']);
                $user->setUserId($userData['id']);
                $user->setUsername($userData['name']);
                $user->setCreatedAt($userData['created_at']);
                $user->setUpdatedAt($userData['updated_at']);

                $this->entityManager->persist($user);
            } else {
                $user->setToken($tokenData['access_token']);
                $user->setTokenExpiredAt((int)$tokenData['expires_in']);
                $user->setUpdatedAt($userData['updated_at']);

                $this->entityManager->persist($user);
            }

            $this->entityManager->flush();

            # Получить id записанного пользователя
            $userNew = $userAccountRepo->findOneByUserId($userData['id']);

            return $userNew ? $userNew->getId() : false;
        }

        return false;
    }

    /**
     * @param $user
     *
     * @return array
     */
    public function getSettings(User $user)
    {
        $result = [];

        if ($user) {
            $result = [
                'username' => $user->getUsername(),
                'email'    => $user->getEmail(),
            ];
        }

        return $result;
    }

    /**
     * @param Request       $request
     * @param Mailer        $mailer
     * @param CodeGenerator $codeGenerator
     *
     * @return string
     * @throws Exception
     */
    public function updateSettings(Request $request, Mailer $mailer, CodeGenerator $codeGenerator)
    {
        $email = (string)$request->request->get('email');
        $password = (string)$request->request->get('password');
        $newPassword = (string)$request->request->get('newPassword');
        $message = '';

        /**
         * @var $user User
         */
        $user = $this->security->getUser();

        if (!$user) {
            return $message;
        }

        if (
            (!$user->getEmail() && '' === $email)
            || ($user->getPassword() && '' === $password)
            || '' === $newPassword
        ) {
            throw new Exception('Email или пароль не заполнены');
        }

        if (
            $user->getPassword()
            && !$this->encoder->isPasswordValid($user, $password)
        ) {
            throw new Exception('Неправильный пароль');
        }

        if ($this->encoder->isPasswordValid($user, $newPassword)) {
            throw new Exception('Новый пароль совпадает с текущим');
        }

        if ('' !== $email) {
            $user->setEmail($email);
        }
        $user->setPassword($this->encoder->encodePassword($user, $newPassword));

        /**
         * @var $confirmRepo      ConfirmationRequestRepository
         * @var $confirmation     ConfirmationRequest
         */
        $confirmRepo = $this->entityManager->getRepository(ConfirmationRequest::class);
        $confirmation = $confirmRepo->findOneBy(['owner_id' => $user->getId()]);

        # подтверждение почты
        if ($confirmation) {
            if (true !== $confirmation->getConfirmed()) {
                $code = $codeGenerator->getConfirmationCode();
                $confirmation->setCode($code);
                $confirmation->setRequestedAt();
                $dateExpired = new DateTime();
                $dateExpired->modify('24 hours');
                $confirmation->setExpiresAt($dateExpired);
                $this->entityManager->persist($confirmation);

                $mailerResponse = $mailer->sendConfirmationMessage($user, $email, $code);
                if (!$mailerResponse) {
                    throw new Exception('Ошибка при отправке письма');
                }

                $message = 'Письмо с подтверждением отправлено на почту: ' . $email;
            }
        } else {
            $code = $codeGenerator->getConfirmationCode();
            $confirmation = new ConfirmationRequest();
            $confirmation->setOwnerId($user);
            $confirmation->setCode($code);
            $confirmation->setConfirmed(false);
            $confirmation->setRequestedAt();
            $dateExpired = new DateTime();
            $dateExpired->modify('24 hours');
            $confirmation->setExpiresAt($dateExpired);
            $this->entityManager->persist($confirmation);

            $mailerResponse = $mailer->sendConfirmationMessage($user, $email, $code);
            if (!$mailerResponse) {
                throw new Exception('Ошибка при отправке письма');
            }

            $message = 'Письмо с подтверждением отправлено на почту: ' . $email;
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $message;
    }

    /**
     * @param Mailer        $mailer
     * @param CodeGenerator $codeGenerator
     *
     * @return string
     * @throws Exception
     */
    public function resendConfirmation(Mailer $mailer, CodeGenerator $codeGenerator)
    {
        /**
         * @var $user User
         */
        $user = $this->security->getUser();
        $message = '';

        if (!$user) {
            throw new Exception('Пользователь не найден');
        }

        /**
         * @var $confirmRepo  ConfirmationRequestRepository
         * @var $confirmation ConfirmationRequest
         */
        $confirmRepo = $this->entityManager->getRepository(ConfirmationRequest::class);
        $confirmation = $confirmRepo->findOneBy(['owner_id' => $user->getId()]);

        if ($confirmation) {
            if (true === $confirmation->getConfirmed()) {
                throw new Exception('Email уже подтверждён');
            }

            $email = $user->getEmail();
            if ('' === (string)$email) {
                throw new Exception('Не задан email');
            }

            $confCode = $codeGenerator->getConfirmationCode();
            $confirmation->setCode($confCode);
            $confirmation->setRequestedAt();
            $dateExpired = new DateTime();
            $dateExpired->modify('24 hours');
            $confirmation->setExpiresAt($dateExpired);

            $mailerResponse = $mailer->sendConfirmationMessage($user, $email, $confCode);

            if (!$mailerResponse) {
                throw new Exception('Ошибка при отправке письма');
            }

            $message = 'Письмо с подтверждением отправлено на почту: ' . $email;

            $this->entityManager->persist($confirmation);
            $this->entityManager->flush();
        }

        return $message;
    }
}