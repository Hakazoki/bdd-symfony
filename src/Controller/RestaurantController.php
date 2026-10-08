<?php

namespace App\Controller;

use App\Entity\Restaurant;
use App\Form\RestaurantType;
use App\Repository\RestaurantRepository;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class RestaurantController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function home(): Response
    {
        return $this->redirectToRoute('app_restaurant_list');
    }

    #[Route('/restaurant', name: 'app_restaurant_list')]
    public function list(Request $request, RestaurantRepository $restaurantRepository, VilleRepository $villeRepository): Response
    {
        $villeId = $request->query->filter('ville', null, \FILTER_VALIDATE_INT, \FILTER_NULL_ON_FAILURE);
        $criteria = $villeId ? ['ville' => $villeId] : [];

        return $this->render('restaurant/list.html.twig', [
            'restaurants' => $restaurantRepository->findBy($criteria, ['nom' => 'ASC']),
            'villes' => $villeRepository->findBy([], ['nom' => 'ASC']),
            'villeId' => $villeId,
        ]);
    }

    #[Route('/restaurant/add', name: 'app_restaurant_add')]
    public function add(Request $request, EntityManagerInterface $em): Response
    {
        $restaurant = new Restaurant();
        $form = $this->createForm(RestaurantType::class, $restaurant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($restaurant);
            $em->flush();
            $this->addFlash('success', 'Restaurant créé.');

            return $this->redirectToRoute('app_restaurant_show', ['id' => $restaurant->getId()]);
        }

        return $this->render('restaurant/add.html.twig', ['form' => $form]);
    }

    #[Route('/restaurant/{id}', name: 'app_restaurant_show', requirements: ['id' => '\d+'])]
    public function show(Restaurant $restaurant): Response
    {
        return $this->render('restaurant/show.html.twig', ['restaurant' => $restaurant]);
    }

    #[Route('/restaurant/{id}/edit', name: 'app_restaurant_edit', requirements: ['id' => '\d+'])]
    public function edit(Request $request, Restaurant $restaurant, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(RestaurantType::class, $restaurant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Restaurant modifié.');

            return $this->redirectToRoute('app_restaurant_show', ['id' => $restaurant->getId()]);
        }

        return $this->render('restaurant/edit.html.twig', ['restaurant' => $restaurant, 'form' => $form]);
    }

    #[Route('/restaurant/{id}/delete', name: 'app_restaurant_delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function delete(Request $request, Restaurant $restaurant, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete_restaurant_' . $restaurant->getId(), $request->getPayload()->getString('_token'))) {
            $em->remove($restaurant);
            $em->flush();
            $this->addFlash('success', 'Restaurant supprimé.');
        }

        return $this->redirectToRoute('app_restaurant_list');
    }
}
