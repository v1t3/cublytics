<?php


namespace App;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class UserService
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

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

                //todo Добавить таблицу для каналов, добавить сохранение каналов юзера

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