<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Auth\Core\User\Domain\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use Symfony\Component\Routing\Annotation\Route;


class UserCrudController extends AbstractCrudController
{
    #[Route('/user', name: '_user')]
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('...')
            ->setDateFormat('...')
            // ...
            ;
    }
}
