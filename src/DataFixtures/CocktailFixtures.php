<?php

namespace App\DataFixtures;

use App\Entity\Cocktail;
use App\Entity\CocktailIngredient;
use App\Entity\Ingredient;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CocktailFixtures extends Fixture implements DependentFixtureInterface
{
    public function getDependencies()
    {
        return [
            IngredientFixtures::class,
            UserFixtures::class,
        ];
    }

    public function load(ObjectManager $manager): void
    {
        $cocktails = [
            [
                'name' => 'Ti\'punch',
                'hasAlcohol' => true,
                'origin' => 'antilles',
                'price' => 12,
                'user' => 'carlos@shauri.fr',
                'ingredients' => [
                    [
                        'name' => 'rhum',
                        'quantity' => 6,
                        'quantityType' => 'cl',
                    ],
                    [
                        'name' => 'sucre de cannes',
                        'quantity' => 2,
                        'quantityType' => 'cl',
                    ],
                    [
                        'name' => 'citron vert',
                        'quantity' => 1,
                        'quantityType' => 'tranche',
                    ],
                ]
            ],
        ];
        foreach ($cocktails as $cocktailArray) {
            $cocktail = new Cocktail();
            $name = $cocktailArray['name'];
            $cocktail->setName($name);
            $hasAlcohol = $cocktailArray['hasAlcohol'];
            $cocktail->setHasAlcohol($hasAlcohol);
            $origin = $cocktailArray['origin'];
            $cocktail->setOrigin($origin);
            $price = $cocktailArray['price'];
            $cocktail->setPrice($price);
            /** @var User $user */
            $user = $this->getReference($cocktailArray['user']);
            $cocktail->setUser($user);
            foreach ($cocktailArray['ingredients'] as $ingredientArray) {
                $cocktailIngredient = new CocktailIngredient();
                $cocktailIngredient->setCocktail($cocktail);
                /** @var Ingredient $ingredient */
                $ingredient = $this->getReference($ingredientArray['name']);
                $cocktailIngredient->setIngredient($ingredient);
                $quantity = $ingredientArray['quantity'];
                $cocktailIngredient->setQuantity($quantity);
                $quantityType = $ingredientArray['quantityType'];
                $cocktailIngredient->setQuantityType($quantityType);
                $cocktail->addCocktailIngredient($cocktailIngredient);
            }
            $manager->persist($cocktail);
        }

        $manager->flush();
    }
}
