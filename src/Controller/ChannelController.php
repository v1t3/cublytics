<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\Log;
use App\Service\ChannelService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
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
     * @Route("/api/coub/get_channels_list", name="get_channels_list")
     *
     * @param ChannelService $channelService
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function getChannelsList(ChannelService $channelService): JsonResponse
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        try {
            $result = $channelService->getChannelsList();
        } catch (Exception $exception) {
            $this->entityManager->clear();
            $logger = new Log();
            $logger->setDate(new DateTime('now'));
            $logger->setType('get_channels_list');
            $logger->setStatus(false);
            $logger->setError('Код ' . $exception->getCode() . ' - ' . $exception->getMessage());
            $this->entityManager->persist($logger);
            $this->entityManager->flush();

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

    /**
     * @Route("/api/channel/get_channel_stat", name="get_channel_stat", methods={"POST"})
     *
     * @param Request        $request
     * @param ChannelService $channelService
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function getChannelStatistic(Request $request, ChannelService $channelService): JsonResponse
    {
        $channelName = (string)$request->request->get('channel_name');
        $statType = (string)$request->request->get('statistic_type');

        try {
            //todo проверка по времени

            $data = $channelService->getChannelStatistic($request);

            if (empty($data)) {
                $coubsOrig = $channelService->getOriginalCoubs($channelName);

                $coubsOrigSaved = $channelService->saveOriginalCoubs($coubsOrig, $channelName);

                if ($coubsOrigSaved) {
                    $data = $channelService->getChannelStatistic($request);
                }
            }

            if (!empty($data) && !empty($data['counts'])) {
                $result = [
                    'result' => 'success',
                    'data'   => $data,
                ];
            } else {
                $result = [
                    'result' => 'error',
                    'error'  => [
                        'message' => 'Данные отсутствуют (Данные начнут поступать в скором времени)',
                    ]
                ];
            }
        } catch (Exception $exception) {
            $this->entityManager->clear();
            $logger = new Log();
            $logger->setDate(new DateTime('now'));
            $logger->setType('get_channel_stat');
            $logger->setChannel($channelName);
            $logger->setStatisticType($statType);
            $logger->setStatus(false);
            $logger->setError(
                'Код ' . $exception->getCode()
                . ' - ' . $exception->getMessage()
            );
            $this->entityManager->persist($logger);
            $this->entityManager->flush();

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

    /**
     * @Route("/api/channel/update_settings", name="update_channel_settings", methods={"POST"})
     *
     * @param Request        $request
     * @param ChannelService $channelService
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function updateChannelSettings(Request $request, ChannelService $channelService): JsonResponse
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
        } catch (Exception $exception) {
            $this->entityManager->clear();
            $logger = new Log();
            $logger->setDate(new DateTime('now'));
            $logger->setType('update_channel_settings');
            $logger->setStatus(false);
            $logger->setError('Код ' . $exception->getCode() . ' - ' . $exception->getMessage());
            $this->entityManager->persist($logger);
            $this->entityManager->flush();

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
