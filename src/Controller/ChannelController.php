<?php

namespace App\Controller;

use App\Service\ChannelService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ChannelController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/api/channel/get_channel_stat", name="get_channel_stat", methods={"POST"})
     *
     * @param Request        $request
     * @param ChannelService $channelService
     *
     * @return JsonResponse
     */
    public static function getChannelStatistic(Request $request, ChannelService $channelService)
    {
        try {
            $result = $channelService->getChannelStatistic($request);
        } catch (\Exception $exception) {
            $result = [
                'result'  => 'error',
                'message' => $exception,
            ];

            $response = new JsonResponse();
            $response->setData($result);

            return $response;
        }

        $response = new JsonResponse();
        $response->setData($result);

        return $response;
    }
}
