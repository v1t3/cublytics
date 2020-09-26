<?php

namespace App\Controller;

use App\Service\CoubService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

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
     * @throws \Exception
     */
    public function getList(Request $request, CoubService $coubService)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        try {
            $coubs = $coubService->getCoubsList($request);
        } catch (\Exception $e) {
            $response = new JsonResponse();
            $response->setData(
                [
                    'result' => 'error',
                    'error'  => [
                        'message' => $e->getMessage(),
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
     */
    public static function getCoubStatistic(Request $request, CoubService $coubService)
    {
        try {
            $coubId = (string)$request->request->get('coub_id');
            $statType = (string)$request->request->get('statistic_type') ?: 'month';

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
