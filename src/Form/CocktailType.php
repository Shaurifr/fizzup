<?php

namespace App\Form;

use App\Entity\Cocktail;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;

use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class CocktailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $isNew = $options['isNew'];
        $builder
            ->add(
                'cover',
                FileType::class,
                [
                    'label' => 'image',
                    'mapped' => false,
                    'required' => false,
                    'constraints' => [
                        new File([
                            'maxSize' => '2048k',
                            'mimeTypes' => [
                                'image/jpeg',
                                'image/jpg',
                                'image/png',
                            ],
                            'mimeTypesMessage' => 'Veuillez téléverser une image au format jpeg ou png',
                        ])
                    ],
                ]
            )
            ->add(
                'name',
                TextType::class,
                [
                    'attr' => [
                        'class' => 'name',
                        'placeholder' => 'mojito',
                    ],
                    'label' => 'Nom',
                ]
            )
            ->add(
                'price',
                NumberType::class,
                [
                    'attr' => [
                        'placeholder' => '16',
                    ],
                    'label' => 'Prix',
                ]
            )
            ->add('hasAlcohol', null, [
                'label' => 'Contient de l\'alcool',
                'required' => false,
            ])
            ->add('origin', null, [
                'attr' => [
                    'placeholder' => 'Cuba',
                ],
                'label' => 'Origine',
            ])
            ->add($isNew ? 'Ajouter' : 'Enregistrer', SubmitType::class, [
                'attr' => [
                    'class' => 'mt-5 btn-primary',
                ],
            ])
            ->add('cocktailIngredients', CollectionType::class, [
                'entry_type' => CocktailIngredientType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'label' => 'Ingrédients',
                'by_reference' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Cocktail::class,
            'isNew' => null,
        ]);
    }
}
