<?php

namespace App\Form;

use App\Entity\Cocktail;
use App\Entity\CocktailIngredient;
use App\Entity\Ingredient;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CocktailIngredientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('quantity')
            ->add('quantityType')
/*
            ->add('cocktail', EntityType::class,
            [
                'class' => Cocktail::class,
                'choice_label' => 'name',
            ])
*/
            ->add('ingredient', EntityType::class,
            [
                'class' => Ingredient::class,
                'choice_label' => 'name',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CocktailIngredient::class,
        ]);
    }
}
