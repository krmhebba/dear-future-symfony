<?php

namespace App\Controller\Admin;

use App\Entity\Letter;
use App\Entity\User;
use App\Entity\Product;
use App\Controller\Admin\LetterCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        return $this->redirect($adminUrlGenerator->setController(LetterCrudController::class)->generateUrl());
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('<span style="color: #7c2a2d; font-weight: bold;"><i class="fas fa-feather-alt"></i> Dear Future</span>')
            ->renderContentMaximized()
            ->disableDarkMode()
            ->setFaviconPath('images/logo.png?v=3');
    }

    public function configureAssets(): Assets
    {
        return Assets::new()->addCssFile('css/admin.css?v=' . time());
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToCrud('Lettres', 'fas fa-envelope', Letter::class);
        yield MenuItem::linkToCrud('Utilisateurs', 'fas fa-users', User::class);
        yield MenuItem::linkToCrud('Cadeaux / Stock', 'fas fa-gift', Product::class);
        yield MenuItem::section();
        yield MenuItem::linkToLogout('Se déconnecter', 'fas fa-sign-out-alt');
    }
}
