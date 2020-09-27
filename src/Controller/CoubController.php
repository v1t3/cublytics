<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\Log;
use App\Service\CoubService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CoubController
 *
 * @package App\Controller
 */
class CoubController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * UserController constructor.
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route ("api/coub/get_list", name="get_coub_list")
     *
     * @param Request     $request
     * @param CoubService $coubService
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function getList(Request $request, CoubService $coubService)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        try {
            $coubs = $coubService->getCoubsList($request);
        } catch (Exception $exception) {
            $channelId = (int)$request->request->get('channel_id');

            $this->entityManager->clear();
            $logger = new Log();
            $logger->setDate(new DateTime('now'));
            $logger->setType('get_coub_list');
            $logger->setChannelId($channelId);
            $logger->setStatus(false);
            $logger->setError('Код ' . $exception->getCode() . ' - ' . $exception->getMessage());
            $this->entityManager->persist($logger);
            $this->entityManager->flush();

            $response = new JsonResponse();
            $response->setData(
                [
                    'result' => 'error',
                    'error'  => [
                        'message' => $exception->getMessage(),
                    ]
                ]
            );

            return $response;
        }

        $response = new JsonResponse();
        $response->setData(
            [
                'result' => 'success',
                'data'   => [
                    'coubs' => $coubs,
                ]
            ]
        );

        return $response;
    }

    /**
     * @Route("/api/coub/get_coub_stat", name="get_coub_stat", methods={"POST"})
     *
     * @param Request     $request
     * @param CoubService $coubService
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function getCoubStatistic(Request $request, CoubService $coubService)
    {
        $coubId = (string)$request->request->get('coub_id');
        $statType = (string)$request->request->get('statistic_type') ?: 'month';

        try {
            //todo проверка по времени

            $data = $coubService->getCoubStatistic($coubId, $statType);

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
        } catch (Exception $exception) {
            $this->entityManager->clear();
            $logger = new Log();
            $logger->setDate(new DateTime('now'));
            $logger->setType('get_coub_stat');
            $logger->setCoubId((int)$coubId);
            $logger->setStatisticType($statType);
            $logger->setStatus(false);
            $logger->setError('Код ' . $exception->getCode() . ' - ' . $exception->getMessage());
            $this->entityManager->persist($logger);
            $this->entityManager->flush();

            $result = [
                'result'  => 'error',
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
