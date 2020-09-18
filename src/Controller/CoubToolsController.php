<?php
declare(strict_types=1);

namespace App\Controller;

use App\Service\CoubToolsService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/***
 * @deprecated
 * Class CoubToolsController
 * @package App\Controller
 */
class CoubToolsController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * CoubToolsController constructor.
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/api/coub/getdata", name="getData")
     *
     * @param Request $request
     *
     * @return string|Response
     */
    public static function getData(Request $request)
    {
        $data = '';

        if ($request->isMethod('post')) {
            $params = $request->request->get('params');

            $params = json_decode(html_entity_decode($params), true);

            if (empty($params['url'])) {
                $response = new JsonResponse();
                $response->setData(
                    [
                        'result'  => 'error',
                        'message' => 'Отсутствует поле url',
                    ]
                );

                return $response;
            }

            $coub = new CoubToolsService();

            if ($coub) {
                if ((string)$params['type'] === 'coubdata') {
                    $coubContent = $coub->getCoubData($params['url']);
                } else {
                    $coubContent = '';
                }

                if (is_array($coubContent) && !empty($coubContent)) {
                    $data = json_encode($coubContent);
                } elseif (is_string($coubContent) && !empty($coubContent)) {
                    $data = $coubContent;
                }
            }
        }

        return new JsonResponse($data);
    }
}
