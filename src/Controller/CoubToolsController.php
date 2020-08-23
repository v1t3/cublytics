<?php
declare(strict_types=1);

namespace App\Controller;

use App\CoubToolsService;
use App\Entity\User;
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
        $data = '';

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
                    $data = json_encode($coubContent);
                } elseif ('' !== (string)$coubContent) {
                    $data = $coubContent;
                }
            }
        }

        return new Response($data);
    }

    /**
     * @Route("/api/coub/callback", name="coub_callback")
     */
    public function callback(Request $request)
    {
        $data = '';
        $code = $request->query->get('code');

        if ($code) {
            $coubTool = new CoubToolsService();
            $data = $coubTool->getUserToken($code);
            $data = json_decode($data, true);

            if (isset($data['access_token'])) {
                $entityManager = $this->getDoctrine()->getManager();

//                $expires_date = new \DateTime();
//                $expires_date->setTimestamp($data['expires_in']);

                $user = new User();
                $user->setToken($data['access_token']);
                $user->setRoles(['ROLE_USER']);
                //todo Настроить получение данных пользователя
                $user->setChannelId(1);
                $user->setPassword('testtest');
                //todo Разобраться с датой в timestamp
//                $user->setExpiredAt($expires_date);

                $entityManager->persist($user);
                $entityManager->flush();

//                return $this->redirectToRoute('spa');

            } elseif (isset($data['error'])) {
                throw new \Exception('Error code: ' . $data['error'] . ' description: ' . $data['error_description']);
            }

        }

        return new Response(json_encode($data));
    }
}
