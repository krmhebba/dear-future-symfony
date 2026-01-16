<?php

namespace App\Controller\Admin;

use App\Entity\Letter;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class LetterCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Letter::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)


            ->disable(Action::NEW, Action::EDIT)

            ->update(Crud::PAGE_INDEX, Action::DETAIL, function (Action $action) {
                return $action->setIcon('fa fa-eye')->setLabel('Voir l\'adresse')->setCssClass('text-info');
            })
            ->update(Crud::PAGE_INDEX, Action::DELETE, function (Action $action) {
                return $action->setIcon('fa fa-trash')->setLabel('Supprimer')->setCssClass('text-danger');
            });
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle('index', 'Suivi des Expéditions')
            ->setPageTitle('detail', 'Informations de Livraison')
            ->setEntityLabelInSingular('Lettre')
            ->setEntityLabelInPlural('Lettres')
            ->setDefaultSort(['sendDate' => 'ASC']);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),

            TextField::new('title', 'Lettre'),

            DateField::new('sendDate', 'Date de délivrance'),


            AssociationField::new('product', 'Cadeau à préparer'),
            TextField::new('city', 'Ville'),
            TextField::new('phoneNumber', 'Téléphone'),

            TextField::new('deliveryAddress', 'Adresse précise')->onlyOnDetail(),

            BooleanField::new('isSent', 'Colis Expédié ?')
                ->renderAsSwitch(false),
        ];
    }
}
