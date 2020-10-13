<?php

namespace App\Controller\Admin;

use App\Entity\Channel;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AvatarField;
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
        yield AvatarField::new('avatar');
        yield IdField::new('channel_id');
        yield TextField::new('channel_permalink');
        yield IntegerField::new('user_id');
        yield TextField::new('title');
        yield BooleanField::new('is_watching');
        yield BooleanField::new('is_active');
        yield DateTimeField::new('created_at')
            ->setFormat('dd-M-yy hh:mm');
        yield DateTimeField::new('updated_at')
            ->setFormat('dd-M-yy hh:mm');
    }
}
