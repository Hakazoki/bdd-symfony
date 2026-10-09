<?php

namespace App\Controller;

use App\Entity\Ville;
use App\Form\VilleType;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class VilleController extends AbstractController
{
    #[Route('/ville', name: 'app_ville_list')]
    public function list(VilleRepository $villeRepository): Response
    {
        return $this->render('ville/list.html.twig', [
            'villes' => $villeRepository->findBy([], ['nom' => 'ASC']),
        ]);
    }

    #[Route('/ville/{id}', name: 'app_ville_show', requirements: ['id' => '\d+'])]
    public function show(Ville $ville, VilleRepository $villeRepository): Response
    {
        return $this->render('restaurant/list.html.twig', [
            'restaurants' => $ville->getRestaurants(),
            'villes' => $villeRepository->findBy([], ['nom' => 'ASC']),
            'villeId' => $ville->getId(),
        ]);
    }

    #[Route('/ville/add', name: 'app_ville_add')]
    #[IsGranted('ROLE_ADMIN')]
    public function add(Request $request, EntityManagerInterface $em): Response
    {
        $ville = new Ville();
        $form = $this->createForm(VilleType::class, $ville);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($ville);
            $em->flush();
            $this->addFlash('success', 'Ville créée.');

            return $this->redirectToRoute('app_ville_list');
        }

        return $this->render('ville/add.html.twig', ['form' => $form]);
    }

    #[Route('/ville/{id}/delete', name: 'app_ville_delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Request $request, Ville $ville, EntityManagerInterface $em): Response
    {
        if (!$this->isCsrfTokenValid('delete_ville_' . $ville->getId(), $request->getPayload()->getString('_token'))) {
            return $this->redirectToRoute('app_ville_list');
        }

        if (!$ville->getRestaurants()->isEmpty()) {
            $this->addFlash('error', sprintf('la ville « %s » contient encore %d restaurant(s), supprimez-les d\'abord.', $ville->getNom(), $ville->getRestaurants()->count()));

            return $this->redirectToRoute('app_ville_list');
        }

        $em->remove($ville);
        $em->flush();
        $this->addFlash('success', 'Ville supprimée.');

        return $this->redirectToRoute('app_ville_list');
    }
}
