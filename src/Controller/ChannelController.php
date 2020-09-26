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
     * @throws \Exception
     */
    public static function getChannelStatistic(Request $request, ChannelService $channelService)
    {
        try {
            $channelName = (string)$request->request->get('channel_name');
            $statType = (string)$request->request->get('statistic_type') ?: 'month';

            //todo проверка по времени

            $data = $channelService->getChannelStatistic($channelName, $statType);

            if (empty($data)) {
                $coubsOrig = $channelService->getOriginalCoubs($channelName);

                $coubsOrigSaved = $channelService->saveOriginalCoubs($coubsOrig, $channelName);

                if ($coubsOrigSaved) {
                    $data = $channelService->getChannelStatistic($channelName, $statType);
                }
            }

            if (!empty($data)) {
                $result = [
                    'result'  => 'success',
                    'message' => '',
                    'data'    => $data,
                ];
            } else {
                $result = [
                    'result'  => 'error',
                    'message' => 'Данные отсутствуют',
                    'data'    => $data
                ];
            }
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

    /**
     * @Route("/api/channel/update_settings", name="update_channel_settings", methods={"POST"})
     *
     * @param Request        $request
     * @param ChannelService $channelService
     *
     * @return JsonResponse
     */
    public function updateChannelSettings(Request $request, ChannelService $channelService)
    {
        try {
            $type = (string)$request->request->get('type');

            $data = $channelService->updateChannelSettings($request);

            if (
                array_key_exists('success', $data)
                && true === $data['success']
            ) {
                $result = [
                    'result' => 'success',
                    'data'   => [
                        $type => $data[$type]
                    ],
                ];
            } else {
                $result = [
                    'result' => 'error',
                    'error'  => [
                        'message' => 'Данные не обновлены',
                    ]
                ];
            }
        } catch (\Exception $exception) {
            $result = [
                'result' => 'error',
                'error'  => [
                    'message' => $exception->getMessage(),
                ]
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
