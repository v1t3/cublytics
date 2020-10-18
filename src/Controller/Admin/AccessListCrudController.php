<?php

namespace App\Controller\Admin;

use App\Entity\AccessList;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

/**
 * Class AccessListCrudController
 *
 * @package App\Controller\Admin
 */
class AccessListCrudController extends AbstractCrudController
{
    /**
     * @return string
     */
    public static function getEntityFqcn(): string
    {
        return AccessList::class;
    }

    /**
     * @param string $pageName
     *
     * @return iterable
     */
    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('user');
        yield BooleanField::new('active');
        yield BooleanField::new('is_registered');
        yield DateTimeField::new('date_create')
            ->onlyOnIndex()
            ->setFormat('dd-M-yy hh:mm');
        yield DateTimeField::new('date_update')
            ->onlyOnIndex()
            ->setFormat('dd-M-yy hh:mm');
    }
}
