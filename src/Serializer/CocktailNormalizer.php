<?php

namespace App\Serializer;


use App\Entity\Cocktail;
use App\Entity\CocktailIngredient;
use App\Repository\IngredientRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerAwareTrait;
use Symfony\Component\Serializer\SerializerInterface;

class CocktailNormalizer implements DenormalizerInterface
{
    use SerializerAwareTrait;

    /**
     * @var IngredientRepository
     */
    private $ingredientRepository;
    public function __construct(IngredientRepository $ingredientRepository)
    {
        $this->ingredientRepository = $ingredientRepository;
    }
    public function supportsDenormalization($data, string $type, string $format = null)
    {
        return Cocktail::class === $type && is_array($data) && !array_key_exists('id', $data);
    }
    public function denormalize($data, string $type, string $format = null, array $context = [])
    {
        $serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);
        $normalizedCocktailIngredients = $data['cocktailIngredients'] ?? [];
        unset($data['cocktailIngredients']);
        /** @var Cocktail $cocktail */
        $cocktail = $serializer->deserialize(
            json_encode($data),
            Cocktail::class,
            'json'
        );
        foreach ($normalizedCocktailIngredients as $normalizedCocktailIngredient) {
            // transformation de $normalizedCocktailIngredient en object CocktailIngredient
            $ingredient = $this->ingredientRepository->find($normalizedCocktailIngredient['ingredient']);
            unset($normalizedCocktailIngredient['ingredient']);
            /** @var CocktailIngredient $cocktailIngredient */
            $cocktailIngredient = $serializer->deserialize(
                json_encode($normalizedCocktailIngredient),
                CocktailIngredient::class,
                'json'
            );
            $cocktailIngredient->setIngredient($ingredient);
            $cocktail->addCocktailIngredient($cocktailIngredient);
        }

        return $cocktail;
    }
}
