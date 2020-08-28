<?php
declare(strict_types=1);

namespace App;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

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
     * UserService constructor.
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
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
        } catch (\Exception $e) {
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
            $userAccount = $this->entityManager
                ->getRepository('App:User')
                ->findOneByUserId($userData['id']);

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
}