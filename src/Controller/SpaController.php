<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\Channel;
use App\Repository\ChannelRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class SpaController
 *
 * @package App\Controller
 */
class SpaController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * SpaController constructor.
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/dashboard/{page}", name="spa")
     *
     * @param string $page
     *
     * @return Response
     */
    public function index($page = '')
    {
        /**
         * @var $channelRepo ChannelRepository
         */
        $channelRepo = $this->entityManager->getRepository(Channel::class);
        $channel = $channelRepo->findOneBy(['is_current' => true, 'user_id' => $this->getUser()->getUserId()]);

        if ($channel) {
            $params = [
                'title'     => $channel->getTitle(),
                'permalink' => $channel->getChannelPermalink(),
                'avatar'    => $channel->getAvatar(),
            ];
        } else {
            $params = [
                'title'     => $this->getUser()->getUsername(),
                'permalink' => '',
                'avatar'    => '',
            ];
        }

        return $this->render(
            'spa/index.html.twig',
            [
                'controller_name' => 'SpaController',
                'channel'         => $params,
            ]
        );
    }
}
