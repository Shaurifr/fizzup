<?php

namespace App\DataFixtures;

use App\Entity\Ingredient;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class IngredientFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $ingredients = [
            'sucre de cannes' => 'sucre',
            'rhum' => 'alcool',
            'vodka' => 'alcool',
            'whisky' => 'alcool',
            'citron vert' => 'fruit',
            // ...
        ];
        foreach ($ingredients as $name => $type) {
            $ingredient = new Ingredient();
            $ingredient->setName($name);
            $ingredient->setType($type);
            $manager->persist($ingredient);
            $this->addReference($name, $ingredient);
        }

        $manager->flush();
    }
}
