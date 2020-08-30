<?php
declare(strict_types=1);


namespace App\Service;


use App\Entity\Channel;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;


/**
 * Class ChannelService
 *
 * @package App
 */
class ChannelService
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
     * @param EntityManagerInterface $entityManager
     * @param Security               $security
     */
    public function __construct(EntityManagerInterface $entityManager, Security $security)
    {
        $this->entityManager = $entityManager;
        $this->security = $security;
    }

    /**
     * @param $data
     *
     * @return bool
     */
    public function saveUserChannels($data)
    {
        if (
            empty($data)
            || !isset($data['id'])
            || !isset($data['channels'])
        ) {
            return false;
        }

        $userId = $data['id'];
        $channels = $data['channels'];
        $current = $data['current_channel']['id'];

        //todo Добавить проверку активности канала

        if (!empty($channels)) {
            $repo = $this->entityManager->getRepository('App:Channel');

            foreach ($channels as $channel) {
                $channelStored = $repo->findOneByChannelId($channel['id']);

                if (!$channelStored) {
                    $ch = new Channel();
                    $ch->setChannelId($channel['id']);
                    $ch->setChannelPermalink($channel['permalink']);
                    $ch->setUserId($userId);
                    $ch->setIsWatching(false);
                    $ch->setIsCurrent((int)$channel['id'] === (int)$current);
                    $ch->setTitle($channel['title']);
                    $ch->setCreatedAt($channel['created_at']);
                    $ch->setUpdatedAt($channel['updated_at']);
                    $ch->setFollowersCount($channel['followers_count']);
                    $ch->setRecoubsCount($channel['recoubs_count']);
                    $ch->setStoriesCount($channel['stories_count']);
                    //todo Скачивание изображения
                    $ch->setAvatar($channel['avatar_versions']['template']);

                    $this->entityManager->persist($ch);
                    $this->entityManager->flush();
                }
            }

            return true;
        }

        return false;
    }

    /**
     * @param $channels
     *
     * @return bool
     */
    public function updateUserChannels($channels)
    {
        return false;
    }

    /**
     * @return array
     */
    public function getChannelsList()
    {
        $channels = [];
        $user = $this->security->getUser();

        if (!$user) {
            return [
                'result'   => 'error',
                'message'  => 'Пользователь не найден',
                'channels' => $channels
            ];
        }

        $userId = $user->getUserId();

        $userChannels = $this->entityManager
            ->getRepository(Channel::class)
            ->findBy(['user_id' => $userId]);

        if ($userChannels) {
            foreach ($userChannels as $userChannel) {
                $channels[] = [
                    'avatar'      => $userChannel->getAvatar(),
                    'name'        => $userChannel->getChannelPermalink(),
                    'is_watching' => $userChannel->getIsWatching(),
                ];
            }

            $data = [
                'result'   => 'success',
                'message'  => '',
                'channels' => $channels,
            ];
        } else {
            $data = [
                'result'   => 'error',
                'message'  => 'Каналы не найдены',
                'channels' => $channels,
            ];
        }

        return $data;
    }
}