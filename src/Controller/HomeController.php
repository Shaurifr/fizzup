<?php

namespace App\Controller;

use App\Entity\Cocktail;
use App\Entity\CocktailIngredient;
use App\Entity\User;
use App\Form\CocktailType;
use App\Repository\CocktailIngredientRepository;
use App\Repository\CocktailRepository;
use App\Repository\IngredientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(
        CocktailRepository $cocktailRepository,
        IngredientRepository $ingredientRepository
    ): Response {
        // Récupérer tous les cocktails en BDD
        $cocktails = $cocktailRepository->findAll();
        // Récupérer tous les ingrédients en BDD
        $ingredients = $ingredientRepository->findAll();

        return $this->render(
            'home/index.html.twig',
            [
                'cocktails' => $cocktails,
                'ingredients' => $ingredients,
            ]
        );
    }

    /**
     * @Route("/cocktail/{id}", name="cocktail", requirements={"id"="\d+"})
     */
    public function cocktail(
        $id,
        CocktailRepository $cocktailRepository,
        EntityManagerInterface $entityManager,
        SluggerInterface $slugger,
        Request $request
    ) {
        $cocktail = $cocktailRepository->find($id);
        if (!$cocktail) {
            return $this->createNotFoundException('cocktail.not.found');
        }

        // je récupère l'action à effectuer
        $action = $request->get('action', 'view');
        if ('edit' === $action) {
            $cocktailForm = $this->createForm(CocktailType::class, $cocktail, ['isNew' => false]);

            // gestion de l'enregistrement
            $cocktailForm->handleRequest($request);
            if ($cocktailForm->isSubmitted() && $cocktailForm->isValid()) {
                // gestion de l'upload d'image
                /** @var UploadedFile $coverFile */
                $coverFile = $cocktailForm->get('cover')->getData();
                if ($coverFile) {
                    $originalFilename = pathinfo($coverFile->getClientOriginalName(), PATHINFO_FILENAME);
                    // this is needed to safely include the file name as part of the URL
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename.'-'.uniqid().'.'.$coverFile->guessExtension();

                    // Move the file to the directory where brochures are stored
                    $coverDirectory = 'images';
                    try {
                        $coverFile->move(
                            $this->getParameter('public_directory').'/'.$coverDirectory,
                            $newFilename
                        );
                    } catch (FileException $e) {
                        // ... handle exception if something happens during file upload
                    }

                    $cocktail->setCoverFilename($coverDirectory.'/'.$newFilename);
                }

                // enregistrement des modifications en BDD
                $entityManager->flush();

                return $this->redirectToRoute('cocktail', ['id' => $cocktail->getId()]);
            }
            return $this->renderForm(
                'home/cocktailAdd.html.twig',
                [
                    'cocktailForm' => $cocktailForm,
                ]
            );
        }

        return $this->render(
            'home/cocktail.html.twig',
            [
                'cocktail' => $cocktail,
            ]
        );
    }

    /**
     * @Route("/cocktail", name="cocktail_add")
     */
    public function cocktailAdd(
        UserInterface $user,
        EntityManagerInterface $entityManager,
        IngredientRepository $ingredientRepository,
        Request $request
    ) {
//        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');
//        if (!$this->isGranted('ROLE_SUPER_ADMIN')) {
//            throw $this->createAccessDeniedException('vous ne passerez pas');
//        }
        /** @var User $user */
        $name = 'rhum';
        if ($user) {
            $name = $user->getFirstName();
        }
        $cocktail = new Cocktail();

        // condition qui fait qu'on remplie que des cocktails avec du rhum
        $cocktail->setName($name);
        $cocktailIngredient = new CocktailIngredient();
        $rhum = $ingredientRepository->findOneBy(['name' => 'rhum']);
        $cocktailIngredient->setIngredient($rhum);
        $cocktailIngredient->setQuantityType('cl');
        $cocktail->addCocktailIngredient($cocktailIngredient);


        $cocktailForm = $this->createForm(
            CocktailType::class,
            $cocktail,
            [
                'isNew' => true,
            ]
        );

        // si données submit ALORS $cocktail est hydraté avec les données submit
        $cocktailForm->handleRequest($request);

        if ($cocktailForm->isSubmitted()) {
            //  on valide que la donnée est bonne
            if ($cocktailForm->isValid()) {
                // Enregistrer en BDD les nouvelles données
                $entityManager->persist($cocktail);
                $entityManager->flush();

                return $this->redirectToRoute('home');
            }
        }

        return $this->renderForm(
            'home/cocktailAdd.html.twig',
            [
                'cocktailForm' => $cocktailForm,
            ]
        );
    }


    /**
     * @Route("/ingredient/{id}", name="ingredient", requirements={"id"="\d+"})
     */
    public function ingredient(
        $id,
        IngredientRepository $ingredientRepository
    ) {
        $ingredient = $ingredientRepository->find($id);
        if (!$ingredient) {
            return $this->createNotFoundException('ingredient.not.found');
        }

        return $this->render(
            'home/ingredient.html.twig',
            [
                'ingredient' => $ingredient,
            ]
        );
    }
}






