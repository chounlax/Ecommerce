<?php
namespace App\Form;

use App\Entity\Fichier;
use App\Entity\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class FichierType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
    ->add('fichier', FileType::class, [
        'label' => 'Choisir une image',
        'mapped' => false,
        'attr' => ['class' => 'hidden'], // On cache l'input moche
        'label_attr' => ['class' => 'cursor-pointer flex flex-col items-center justify-center w-full'],
        'constraints' => [
            new File([
                'maxSize' => '20000k',
                'mimeTypes' => [
                    'image/jpeg', 'image/png', 'image/webp', 'image/gif', 
                    'image/svg+xml', 'image/avif', 'image/heic'
                ],
                'mimeTypesMessage' => 'Format d\'image non supporté',
            ]),
        ],
    ])
    ->add('user', EntityType::class, [
        'class' => User::class,
        'label' => 'Attribuer à l\'utilisateur',
        'placeholder' => 'Sélectionnez un membre',
        'choice_label' => function ($user) {
            return strtoupper($user->getNom()) . ' ' . $user->getPrenom();
        },
        'query_builder' => function (EntityRepository $er) {
            return $er->createQueryBuilder('u')
                ->orderBy('u.nom', 'ASC');
        },
    ]);
    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Fichier::class,
        ]);
    }
}
