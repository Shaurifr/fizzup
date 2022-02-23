<?php

namespace App\Serializer;


use App\Entity\Cocktail;
use App\Entity\CocktailIngredient;
use App\Entity\Ingredient;
use App\Repository\CocktailRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerAwareTrait;
use Symfony\Component\Serializer\SerializerInterface;

class IngredientNormalizer implements DenormalizerInterface
{
    use SerializerAwareTrait;

    /**
     * @var CocktailRepository
     */
    private $cocktailRepository;

    public function __construct(CocktailRepository $cocktailRepository)
    {
        $this->cocktailRepository = $cocktailRepository;
    }

    public function supportsDenormalization($data, string $type, string $format = null)
    {
        return Ingredient::class === $type && is_array($data) && !array_key_exists('id', $data);
    }

    public function denormalize($data, string $type, string $format = null, array $context = [])
    {
        $normalizedCocktailIngredients = $data['cocktailIngredients'] ?? [];
        unset($data['cocktailIngredients']);
        $serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);
        /** @var Ingredient $ingredient */
        $ingredient = $serializer->deserialize(
            json_encode($data),
            Ingredient::class,
            'json'
        );
        foreach ($normalizedCocktailIngredients as $normalizedCocktailIngredient) {
            // transformation de $normalizedCocktailIngredient en object CocktailIngredient
            $cocktail = $this->cocktailRepository->find($normalizedCocktailIngredient['cocktail']);
            unset($normalizedCocktailIngredient['cocktail']);
            /** @var CocktailIngredient $cocktailIngredient */
            $cocktailIngredient = $serializer->deserialize(
                json_encode($normalizedCocktailIngredient),
                CocktailIngredient::class,
                'json'
            );
            $cocktailIngredient->setCocktail($cocktail);
            $ingredient->addCocktailIngredient($cocktailIngredient);
        }

        return $ingredient;
    }
}
