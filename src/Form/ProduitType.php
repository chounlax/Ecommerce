<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Fichier;
use Doctrine\ORM\EntityRepository;

class ProduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
		->add('nom', TextType::class)
            ->add('marque', TextType::class)
            ->add('categorie', TextType::class)
            ->add('description', TextareaType::class)
            ->add('prix', NumberType::class)
            ->add('stock', IntegerType::class)
            
            ->add('image', EntityType::class, [
                'attr' => ['class' => 'form-select'], 'label_attr' => ['class' => 'fw-bold'],
                'class' => Fichier::class,
                'choice_label' => function ($fichier) {
                    return $fichier->getNomOriginal();
                },
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->orderBy('u.nomOriginal', 'ASC');
                },

            ])
            ->add('envoyer', SubmitType::class)
        ;

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
