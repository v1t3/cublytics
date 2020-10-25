<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class AppUserCrudController
 *
 * @package App\Controller\Admin
 */
class AppUserCrudController extends AbstractCrudController
{
    /** @var UserPasswordEncoderInterface */
    private $passwordEncoder;

    /**
     * @return string
     */
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    /**
     * @param string $pageName
     *
     * @return iterable
     */
    public function configureFields(string $pageName): iterable
    {
        $roles = ChoiceField::new('roles', 'Роли')
            ->setChoices(
                [
                    'Пользователь'  => 'ROLE_USER',
                    'Администратор' => 'ROLE_ADMIN',
                ]
            )
            ->setFormTypeOption('multiple', true);

        yield IntegerField::new('user_id', 'ID пользователя')
            ->onlyOnIndex();
        yield TextField::new('username', 'Имя');
        yield EmailField::new('email', 'Почта');

        yield Field::new('plainPassword', 'New password')->onlyOnForms()
            ->setFormType(RepeatedType::class)
            ->setFormTypeOptions(
                [
                    'type'           => PasswordType::class,
                    'first_options'  => ['label' => 'Новый пароль'],
                    'second_options' => ['label' => 'Пароль ещё раз'],
                ]
            );

        yield $roles;
        yield DateTimeField::new('created_at', 'Дата создания')
            ->onlyOnIndex()
            ->setFormat('dd-M-yy hh:mm');
        yield DateTimeField::new('updated_at', 'Дата обновления')
            ->onlyOnIndex()
            ->setFormat('dd-M-yy hh:mm');
        yield BooleanField::new('blocked', 'Заблокирован');
    }

    /**
     * @param EntityDto     $entityDto
     * @param KeyValueStore $formOptions
     * @param AdminContext  $context
     *
     * @return FormBuilderInterface
     */
    public function createEditFormBuilder(
        EntityDto $entityDto,
        KeyValueStore $formOptions,
        AdminContext $context
    ): FormBuilderInterface
    {
        $formBuilder = parent::createEditFormBuilder($entityDto, $formOptions, $context);

        $this->addEncodePasswordEventListener($formBuilder);

        return $formBuilder;
    }

    /**
     * @param EntityDto     $entityDto
     * @param KeyValueStore $formOptions
     * @param AdminContext  $context
     *
     * @return FormBuilderInterface
     */
    public function createNewFormBuilder(
        EntityDto $entityDto,
        KeyValueStore $formOptions,
        AdminContext $context
    ): FormBuilderInterface
    {
        $formBuilder = parent::createNewFormBuilder($entityDto, $formOptions, $context);

        $this->addEncodePasswordEventListener($formBuilder);

        return $formBuilder;
    }

    /**
     * @required
     *
     * @param UserPasswordEncoderInterface $passwordEncoder
     */
    public function setEncoder(UserPasswordEncoderInterface $passwordEncoder): void
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @param FormBuilderInterface $formBuilder
     */
    protected function addEncodePasswordEventListener(FormBuilderInterface $formBuilder)
    {
        $formBuilder->addEventListener(
            FormEvents::SUBMIT, function(FormEvent $event) {
            /** @var User $user */
            $user = $event->getData();
            if ($user->getPlainPassword()) {
                $user->setPassword($this->passwordEncoder->encodePassword($user, $user->getPlainPassword()));
            }
        }
        );
    }
}
