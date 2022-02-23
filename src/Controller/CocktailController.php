<?php

namespace App\Controller;

use App\Entity\Cocktail;
use App\Entity\CocktailIngredient;
use App\Repository\CocktailIngredientRepository;
use App\Repository\CocktailRepository;
use App\Repository\IngredientRepository;
use App\Serializer\CocktailNormalizer;
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
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/api/cocktails", name="cocktails_")
 */
class CocktailController extends AbstractController
{
    /**
     * @Route("", name="get", methods={"GET"})
     */
    public function getAll(
        CocktailRepository $cocktailRepository,
        SerializerInterface $serializer
    ): Response
    {
        (new ObjectNormalizer())->setSerializer($serializer);
        // On va récupérer en BDD TOUS les cocktails
        $cocktails = $cocktailRepository->findAll();
        return new JsonResponse($serializer->normalize($cocktails, null, ['groups' => 'cocktail:read']));
    }

    /**
     * @Route("/{id}", name="get_one", methods={"GET"})
     */
    public function getOne(
        $id,
        CocktailRepository $cocktailRepository,
        SerializerInterface $serializer
    ) {
        (new ObjectNormalizer())->setSerializer($serializer);
        // On va récupérer en BDD TOUS les cocktails
        $cocktail = $cocktailRepository->find($id);

        if (!$cocktail) {
            throw $this->createNotFoundException(sprintf("%s is not a known cocktail", $id));
        }

        return new JsonResponse($serializer->normalize($cocktail, null, ['groups' => 'cocktail:read']));
    }

    /**
     * @Route("", name="post", methods={"POST"}, format="json")
     */
    public function post(
        IngredientRepository $ingredientRepository,
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer,
        Request $request,
        ValidatorInterface $validator
    ): Response
    {
        // normalement, on doit recevoir une resource Cocktail au format JSON
        $content = $request->getContent();

        // Transformation JSON en instance de Cocktail
        (new CocktailNormalizer($ingredientRepository))->setSerializer($serializer);
        /** @var Cocktail $cocktail */
        $cocktail = $serializer->deserialize(
            $content,
            Cocktail::class,
            'json'
        );

        $errors = $validator->validate($cocktail);
        if (count($errors) > 0) { // signifie qu'il y a des erreurs de validation
            $errorMessages = [];
            foreach ($errors as $error) {
                /** @var ConstraintViolationInterface $error */
                $errorMessages[$error->getPropertyPath()] = $error->getMessage();
            }

            return new JsonResponse($errorMessages, Response::HTTP_BAD_REQUEST);
        }

        // Enregistrement de $cocktail en BDD
        // permet à l'EntityManager de connaitre la nouvelle instance de Cocktail
        $entityManager->persist($cocktail);
        // l'EntityManager synchronise (Créer / Modifie / Supprime) la BDD avec les modifs qu'il a répertorié
        $entityManager->flush();

        // je renvoie au format JSON le cocktail que je viens de créer
        return new JsonResponse($serializer->normalize($cocktail, null, ['groups' => 'cocktail:read']), Response::HTTP_CREATED);
    }

    /**
     * @Route("/{id}", name="patch", methods={"PATCH", "PUT"}, format="json")
     */
    public function patch(
        $id,
        CocktailRepository $cocktailRepository,
        EntityManagerInterface $entityManager,
        Request $request,
        SerializerInterface $serializer
    ): Response
    {
        // Récupération depuis la BDD du Cocktail qui a pour identifiant $id
        $cocktail = $cocktailRepository->find($id);
        // Si aucun cocktail n'est trouvé, alors on renvoie une erreur.
        if (!$cocktail) {
            throw $this->createNotFoundException(
                sprintf('%s is an unknown Cocktail', $id)
            );
        }

        // normalement, on doit recevoir une resource Cocktail au format JSON
        $content = $request->getContent();
        // hydratation des données du JSON dans le $cocktail
        (new ObjectNormalizer())->setSerializer($serializer);
        $serializer->deserialize(
            $content,
            Cocktail::class,
            'json',
            [
                AbstractNormalizer::OBJECT_TO_POPULATE => $cocktail,
                AbstractObjectNormalizer::DEEP_OBJECT_TO_POPULATE => false,
            ]
        );

        // mettre à jour la BDD
        $entityManager->flush();

        // je renvoie au format JSON le cocktail que je viens de modifier
        return new JsonResponse($serializer->normalize($cocktail, null, ['groups' => 'cocktail:read']));
    }

    /**
     * @Route("/{id}/ingredients/{ingredientId}", name="patch_ingredient", methods={"PATCH", "PUT"}, format="json")
     */
    public function patchIngredient(
        $id,
        $ingredientId,
        CocktailIngredientRepository $cocktailIngredientRepository,
        EntityManagerInterface $entityManager,
        Request $request,
        SerializerInterface $serializer
    ): Response
    {
        // Récupération depuis la BDD du CocktailIngredient qui a pour identifiant $id
        $cocktailIngredient = $cocktailIngredientRepository->findOneBy(['cocktail' => $id, 'ingredient' => $ingredientId]);
        // Si aucun CocktailIngredient n'est trouvé, alors on renvoie une erreur.
        if (!$cocktailIngredient) {
            throw $this->createNotFoundException(
                sprintf('%s is an unknown ingredient for cocktail %s', $ingredientId, $id)
            );
        }

        // normalement, on doit recevoir une resource Cocktail au format JSON
        $content = $request->getContent();
        // hydratation des données du JSON dans le $cocktail
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

        // je renvoie au format JSON le cocktail que je viens de modifier
        return new JsonResponse($serializer->normalize($cocktailIngredient, null, ['groups' => 'cocktail:read']));
    }

    /**
     * @Route("/{id}", name="delete", methods={"DELETE"})
     */
    public function delete(
        $id,
        CocktailRepository $cocktailRepository,
        EntityManagerInterface $entityManager
    ): Response
    {
        // Récupération depuis la BDD du Cocktail qui a pour identifiant $id
        $cocktail = $cocktailRepository->find($id);
        // Si aucun cocktail n'est trouvé, alors on renvoie une erreur.
        if (!$cocktail) {
            throw $this->createNotFoundException(
                sprintf('%s is an unknown Cocktail', $id)
            );
        }

        $entityManager->remove($cocktail);
        $entityManager->flush();

        return new Response();
    }

    /**
     * @Route("/{id}/ingredients/{ingredientId}", name="delete_ingredient", methods={"DELETE"})
     */
    public function deleteIngredient(
        $id,
        $ingredientId,
        CocktailIngredientRepository $cocktailIngredientRepository,
        EntityManagerInterface $entityManager
    ): Response
    {
        // Récupération depuis la BDD du CocktailIngredient qui a pour identifiant $id
        $cocktailIngredient = $cocktailIngredientRepository->findOneBy(['cocktail' => $id, 'ingredient' => $ingredientId]);
        // Si aucun CocktailIngredient n'est trouvé, alors on renvoie une erreur.
        if (!$cocktailIngredient) {
            throw $this->createNotFoundException(
                sprintf('%s is an unknown ingredient for cocktail %s', $ingredientId, $id)
            );
        }

        $entityManager->remove($cocktailIngredient);
        $entityManager->flush();

        return new Response();
    }
}
