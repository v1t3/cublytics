<?php


namespace App;


use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class ChannelService
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function saveUserChannels($channels)
    {

        return false;
    }

    public function updateUserChannels($channels)
    {

        return false;
    }
}