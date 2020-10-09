<?php

namespace App\Controller\Admin;

use App\Entity\Channel;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ChannelCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Channel::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('channel_id'),
            TextField::new('channel_permalink'),
            IntegerField::new('user_id'),
            TextField::new('title'),
            BooleanField::new('is_watching'),
            BooleanField::new('is_active'),
            DateTimeField::new('created_at'),
            DateTimeField::new('updated_at'),
        ];
    }
}
