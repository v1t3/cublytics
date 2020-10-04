<?php

namespace App\DataFixtures;

use App\Entity\Channel;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private UserPasswordEncoderInterface $encoder;

    private EntityManagerInterface $entityManager;

    public function __construct(UserPasswordEncoderInterface $encoder, EntityManagerInterface $entityManager)
    {
        $this->encoder = $encoder;
        $this->entityManager = $entityManager;
    }

    public function load(ObjectManager $manager)
    {
        $usersData = [
            [
                'user_id'  => 123456,
                'title'    => 'test1',
                'email'    => 'test1@test.test',
                'role'     => ['ROLE_USER'],
                'password' => '1qaz2wsx',
                'channels' => [
                    [
                        'channel_id'        => 000001,
                        'channel_permalink' => 'test1',
                        'user_id'           => 123456,
                        'title'             => 'test1',
                        'avatar'            => 'test1',
                        'is_watching'       => false,
                        'is_current'        => false,
                    ],
                ],
            ],
            [
                'user_id'  => 123457,
                'title'    => 'test2',
                'email'    => 'test2@test.test',
                'role'     => ['ROLE_ADMIN'],
                'password' => '1qaz2wsx',
                'channels' => [
                    [
                        'channel_id'        => 000002,
                        'channel_permalink' => 'test2',
                        'user_id'           => 123457,
                        'title'             => 'test2',
                        'avatar'            => 'test2',
                        'is_watching'       => false,
                        'is_current'        => false,
                    ],
                ],
            ],
        ];

        foreach ($usersData as $user) {
            $newUser = new User();
            $newUser->setUserId($user['user_id']);
            $newUser->setEmail($user['email']);
            $newUser->setPassword($this->encoder->encodePassword($newUser, $user['password']));
            $newUser->setRoles($user['role']);
            $this->entityManager->persist($newUser);

            if (!empty($user['channels'])) {
                foreach ($user['channels'] as $channel) {
                    $newCh = new Channel();
                    $newCh->setChannelId($channel['channel_id']);
                    $newCh->setChannelPermalink($channel['channel_permalink']);
                    $newCh->setUserId($channel['user_id']);
                    $newCh->setTitle($channel['title']);
                    $newCh->setAvatar($channel['avatar']);
                    $newCh->setIsWatching($channel['is_watching']);
                    $newCh->setIsCurrent($channel['is_current']);
                    $this->entityManager->persist($newCh);
                }
            }
        }

        $this->entityManager->flush();
    }
}
