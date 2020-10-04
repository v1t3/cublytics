<?php
declare(strict_types=1);

namespace App\Service;

use App\AppRegistry;
use App\Entity\User;
use App\Repository\UserRepository;
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
     * @return bool
     */
    public function saveUser($tokenData, $userData)
    {
        if (isset($userData['id'])) {
            /**
             * @var $userAccountRepo UserRepository
             */
            $userAccountRepo = $this->entityManager->getRepository('App:User');
            $userAccount = $userAccountRepo->findOneByUserId($userData['id']);

            if (!$userAccount) {
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
                $userAccount->setToken($tokenData['access_token']);
                $userAccount->setTokenExpiredAt((int)$tokenData['expires_in']);
                $userAccount->setUpdatedAt($userData['updated_at']);

                $this->entityManager->persist($userAccount);
            }

            $this->entityManager->flush();

            return true;
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
     * @return array
     *
     * @throws Exception
     */
    public function updateSettings(Request $request, Mailer $mailer, CodeGenerator $codeGenerator)
    {
        $email = (string)$request->request->get('email');
        $password = (string)$request->request->get('password');
        $message = '';

        if ('' === $email || '' === $password) {
            return [
                'result' => 'error',
                'error'  => [
                    'message' => 'email или пароль не заполнены'
                ]
            ];
        }

        /**
         * @var $user User
         */
        $user = $this->security->getUser();

        if (
            $user->getEmail() === $email
            || $this->encoder->isPasswordValid($user, $password)
        ) {
            return [
                'result' => 'error',
                'error'  => [
                    'message' => 'email или пароль совпадают с текущим'
                ]
            ];
        }

        $user->setEmail($email);
        $user->setPassword($password);
        $user->setPassword($this->encoder->encodePassword($user, $password));

        # подтверждение почты
        if (true !== $user->getConfirmed()) {
            $user->setConfirmationCode($codeGenerator->getConfirmationCode());
            $mailerResponse = $mailer->sendConfirmationMessage($user, $email);

            if ($mailerResponse) {
                $message = 'Письмо с подтверждением отправлено на почту: ' . $email;
            } else {
                return [
                    'result' => 'error',
                    'error'  => [
                        'message' => 'Ошибка при обновлении'
                    ]
                ];
            }
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return [
            'result'  => 'success',
            'message' => 'Обновлено! ' . $message
        ];
    }
}