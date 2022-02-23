<?php

namespace App\Controller;

use App\Entity\Ingredient;
use App\Entity\CocktailIngredient;
use App\Repository\CocktailIngredientRepository;
use App\Repository\CocktailRepository;
use App\Repository\IngredientRepository;
use App\Serializer\IngredientNormalizer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/api/ingredients", name="ingredients_")
 */
class IngredientController extends AbstractController
{
    /**
     * @Route("", name="get", methods={"GET"})
     */
    public function getAll(
        IngredientRepository $ingredientRepository,
        SerializerInterface $serializer
    ): Response {
        (new ObjectNormalizer())->setSerializer($serializer);
        // On va récupérer en BDD TOUS les ingredients
        $ingredients = $ingredientRepository->findAll();

        return new JsonResponse($serializer->normalize($ingredients, null, ['groups' => 'ingredient:read']));
    }

    /**
     * @Route("/{id}", name="get_one", methods={"GET"}, requirements={"id"="\d+"})
     */
    public function getOne(
        $id,
        IngredientRepository $ingredientRepository,
        SerializerInterface $serializer
    ) {
        (new ObjectNormalizer())->setSerializer($serializer);
        // On va récupérer en BDD TOUS les ingredients
        $ingredient = $ingredientRepository->find($id);

        if (!$ingredient) {
            throw $this->createNotFoundException(sprintf("%s is not a known ingredient", $id));
        }

        return new JsonResponse($serializer->normalize($ingredient, null, ['groups' => 'ingredient:read']));
    }

    /**
     * @Route("", name="post", methods={"POST"}, format="json")
     */
    public function post(
        CocktailRepository $cocktailRepository,
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer,
        Request $request
    ): Response {
        // normalement, on doit recevoir une resource Ingredient au format JSON
        $content = $request->getContent();

        // Transformation JSON en instance de Ingredient
        (new IngredientNormalizer($cocktailRepository))->setSerializer($serializer);
        /** @var Ingredient $ingredient */
        $ingredient = $serializer->deserialize(
            $content,
            Ingredient::class,
            'json'
        );

        // Enregistrement de $ingredient en BDD
        // permet à l'EntityManager de connaitre la nouvelle instance de Ingredient
        $entityManager->persist($ingredient);
        // l'EntityManager synchronise (Créer / Modifie / Supprime) la BDD avec les modifs qu'il a répertorié
        $entityManager->flush();

        // je renvoie au format JSON le ingredient que je viens de créer
        return new JsonResponse(
            $serializer->normalize($ingredient, null, ['groups' => 'ingredient:read']),
            Response::HTTP_CREATED
        );
    }

    /**
     * @Route("/{id}", name="patch", methods={"PATCH", "PUT"}, format="json")
     */
    public function patch(
        $id,
        IngredientRepository $ingredientRepository,
        EntityManagerInterface $entityManager,
        Request $request,
        SerializerInterface $serializer
    ): Response {
        // Récupération depuis la BDD du Ingredient qui a pour identifiant $id
        $ingredient = $ingredientRepository->find($id);
        // Si aucun ingredient n'est trouvé, alors on renvoie une erreur.
        if (!$ingredient) {
            throw $this->createNotFoundException(
                sprintf('%s is an unknown Ingredient', $id)
            );
        }

        // normalement, on doit recevoir une resource Ingredient au format JSON
        $content = $request->getContent();
        // hydratation des données du JSON dans le $ingredient
        (new ObjectNormalizer())->setSerializer($serializer);
        $serializer->deserialize(
            $content,
            Ingredient::class,
            'json',
            [
                AbstractNormalizer::OBJECT_TO_POPULATE => $ingredient,
                AbstractObjectNormalizer::DEEP_OBJECT_TO_POPULATE => false,
            ]
        );

        // mettre à jour la BDD
        $entityManager->flush();

        // je renvoie au format JSON le ingredient que je viens de modifier
        return new JsonResponse($serializer->normalize($ingredient, null, ['groups' => 'ingredient:read']));
    }

    /**
     * @Route("/{id}/cocktails/{cocktailId}", name="patch_cocktail", methods={"PATCH", "PUT"}, format="json")
     */
    public function patchCocktail(
        $id,
        $cocktailId,
        CocktailIngredientRepository $ingredientIngredientRepository,
        EntityManagerInterface $entityManager,
        Request $request,
        SerializerInterface $serializer
    ): Response {
        // Récupération depuis la BDD du IngredientIngredient qui a pour identifiant $id
        $cocktailIngredient = $ingredientIngredientRepository->findOneBy(
            ['ingredient' => $id, 'cocktail' => $cocktailId]
        );
        // Si aucun IngredientIngredient n'est trouvé, alors on renvoie une erreur.
        if (!$cocktailIngredient) {
            throw $this->createNotFoundException(
                sprintf('%s is an unknown ingredient for ingredient %s', $id, $cocktailId)
            );
        }

        // normalement, on doit recevoir une resource Ingredient au format JSON
        $content = $request->getContent();
        // hydratation des données du JSON dans le $ingredient
        (new ObjectNormalizer())->setSerializer($serializer);
        $serializer->deserialize(
            $content,
            CocktailIngredient::class,
            'json',
            [
                AbstractNormalizer::OBJECT_TO_POPULATE => $cocktailIngredient,
                AbstractObjectNormalizer::DEEP_OBJECT_TO_POPULATE => false,
            ]
        );

        // mettre à jour la BDD
        $entityManager->flush();

        // je renvoie au format JSON le ingredient que je viens de modifier
        return new JsonResponse($serializer->normalize($cocktailIngredient, null, ['groups' => 'ingredient:read']));
    }

    /**
     * @Route("/{id}", name="delete", methods={"DELETE"})
     */
    public function delete(
        $id,
        IngredientRepository $ingredientRepository,
        EntityManagerInterface $entityManager
    ): Response {
        // Récupération depuis la BDD du Ingredient qui a pour identifiant $id
        $ingredient = $ingredientRepository->find($id);
        // Si aucun ingredient n'est trouvé, alors on renvoie une erreur.
        if (!$ingredient) {
            throw $this->createNotFoundException(
                sprintf('%s is an unknown Ingredient', $id)
            );
        }

        $entityManager->remove($ingredient);
        $entityManager->flush();

        return new Response();
    }

    /**
     * @Route("/{id}/cocktails/{cocktailId}", name="delete_ingredient", methods={"DELETE"})
     */
    public function deleteIngredient(
        $id,
        $cocktailId,
        CocktailIngredientRepository $cocktailIngredientRepository,
        EntityManagerInterface $entityManager
    ): Response {
        // Récupération depuis la BDD du IngredientIngredient qui a pour identifiant $id
        $cocktailIngredient = $cocktailIngredientRepository->findOneBy(['ingredient' => $id, 'cocktail' => $cocktailId]
        );
        // Si aucun IngredientIngredient n'est trouvé, alors on renvoie une erreur.
        if (!$cocktailIngredient) {
            throw $this->createNotFoundException(
                sprintf('%s is an unknown ingredient for ingredient %s', $id, $cocktailId)
            );
        }

        $entityManager->remove($cocktailIngredient);
        $entityManager->flush();

        return new Response();
    }
}
