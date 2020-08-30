<?php

namespace App\Controller;

use App\Service\ChannelService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class StatisticController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/api/stat/getChannelsList", name="api_statistic")
     *
     * @param ChannelService $channelService
     *
     * @return JsonResponse
     */
    public function getChannelsList(ChannelService $channelService)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        try {
            $result = $channelService->getChannelsList();
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
