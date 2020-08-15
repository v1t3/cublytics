<?php
declare(strict_types=1);

namespace App\Controller;

use App\CoubToolsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CoubToolsController extends AbstractController
{
    /**
     * @Route("/api/coub/getdata", name="getData")
     *
     * @param Request $request
     *
     * @return string|Response
     */
    public static function getData(Request $request)
    {
        $data = [];

        if ($request->isMethod('post')) {
            $params = $request->request->get('params');

            $params = json_decode(html_entity_decode($params), true);

            if ((string)$params['url'] === '') {
                return '';
            }

            $coub = new CoubToolsService();

            if ($coub) {
                if ((string)$params['type'] === 'userdata') {
                    $coubContent = $coub->getUserData($params['url']);
                } elseif ((string)$params['type'] === 'coubdata') {
                    $coubContent = $coub->getCoubData($params['url']);
                } elseif ((string)$params['type'] === 'performance') {
                    $coubContent = $coub->getChannelPerf($params['url']);
                } else {
                    $coubContent = '';
                }

                if (is_array($coubContent) && count($coubContent) > 0) {
                    $data = $coubContent;
                } elseif ('' !== (string)$coubContent) {
                    $data = $coubContent;
                }
            }
        }

        return new Response($data);
    }
}
