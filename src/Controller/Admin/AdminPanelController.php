<?php

namespace App\Controller\Admin;

use App\Entity\AccessList;
use App\Entity\Channel;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\CrudUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class AdminPanelController extends AbstractDashboardController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        // redirect to some CRUD controller
        $routeBuilder = $this->get(CrudUrlGenerator::class)->build();

        return $this->redirect($routeBuilder->setController(AppUserCrudController::class)->generateUrl());
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Панель управления');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::section('Дашборд');
        yield MenuItem::linkToCrud('Пользователи', 'fa fa-user', User::class);
        yield MenuItem::linkToCrud('Каналы', 'fa fa-tv', Channel::class);
        yield MenuItem::linkToCrud('Список доступа', 'fa fa-id-badge', AccessList::class);
        yield MenuItem::section('Контент');
    }

    public function configureUserMenu(UserInterface $user): UserMenu
    {
        return parent::configureUserMenu($user)
            ->setMenuItems(
                [
                    MenuItem::linkToUrl('Личный кабинет', '', '/dashboard'),
                    MenuItem::linkToLogout('Выйти', 'fa fa-sign-out'),
                ]
            );
    }
}
