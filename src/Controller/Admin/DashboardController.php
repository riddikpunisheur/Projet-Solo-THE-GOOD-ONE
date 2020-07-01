<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\Activitys;
use App\Entity\Questions;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Console\Question\Question;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Router\CrudUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

class DashboardController extends AbstractDashboardController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function admin(): Response
    {
        return parent::index();
        // redirect to some CRUD controller
        $routeBuilder = $this->get(CrudUrlGenerator::class)->build();

        return $this->redirect($routeBuilder->setController(OneOfYourCrudController::class)->generateUrl());

        // you can also redirect to different pages depending on the current user
        if ('' === $this->getUser()->getUsername()) {
            return $this->redirect('');
        }

        // you can also render some template to display a proper Dashboard
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        return $this->render('admin.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Projet The Good One')
            // you can include HTML contents too (e.g. to link to an image)
            ->setTitle('<img src="..."> ACME <span class="text-small">Corp.</span>')

            // the path defined in this method is passed to the Twig asset() function
            ->setFaviconPath('favicon.svg')

            // the domain used by default is 'messages'
            ->setTranslationDomain('my-custom-domain')

            // there's no need to define the "text direction" explicitly because
            // its default value is inferred dynamically from the user locale
            ->setTextDirection('ltr');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linktoDashboard('Dashboard', 'fa fa-home');
        // yield MenuItem::linkToCrud('The Label', 'icon class', EntityClass::class);
        return [
            MenuItem::linkToDashboard('Dashboard', 'fa-home'),

            MenuItem::section('Questions'),
            MenuItem::linkToCrud('Activitys', 'fa fa-activitys', Activitys::class),
            MenuItem::linkToCrud('Questions', 'fa fa-questions', Questions::class),
            MenuItem::linkToCrud('User', 'fa fa-user', User::class),

            MenuItem::section('User'),
            MenuItem::linkToCrud('Questions', 'fa fa-questions', Questions::class),
            MenuItem::linkToCrud('Activitys', 'fa fa-activitys', Activitys::class),
            MenuItem::linkToCrud('User', 'fa fa-user', User::class),
        ];
        
    }
}
