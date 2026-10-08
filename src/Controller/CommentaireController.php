<?php

namespace App\Controller;

use App\Entity\Commentaire;
use App\Entity\Restaurant;
use App\Form\CommentaireType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CommentaireController extends AbstractController
{
    #[Route('/restaurant/{id}/commentaire/add', name: 'app_commentaire_add', requirements: ['id' => '\d+'])]
    public function add(Request $request, Restaurant $restaurant, EntityManagerInterface $em): Response
    {
        $commentaire = (new Commentaire())->setRestaurant($restaurant);
        $form = $this->createForm(CommentaireType::class, $commentaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($commentaire);
            $em->flush();
            $this->addFlash('success', 'Commentaire ajouté.');

            return $this->redirectToRoute('app_restaurant_show', ['id' => $restaurant->getId()]);
        }

        return $this->render('comment/add.html.twig', ['restaurant' => $restaurant, 'form' => $form]);
    }

    #[Route('/commentaire/{id}/reply', name: 'app_commentaire_reply', requirements: ['id' => '\d+'])]
    public function reply(Request $request, Commentaire $parent, EntityManagerInterface $em): Response
    {
        $reponse = (new Commentaire())->setParent($parent)->setRestaurant($parent->getRestaurant());
        $form = $this->createForm(CommentaireType::class, $reponse, ['with_note' => false]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($reponse);
            $em->flush();
            $this->addFlash('success', 'Réponse ajoutée.');

            return $this->redirectToRoute('app_restaurant_show', ['id' => $parent->getRestaurant()->getId()]);
        }

        return $this->render('comment/reply.html.twig', ['parent' => $parent, 'form' => $form]);
    }

    #[Route('/commentaire/{id}/delete', name: 'app_commentaire_delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function delete(Request $request, Commentaire $commentaire, EntityManagerInterface $em): Response
    {
        $restaurantId = $commentaire->getRestaurant()->getId();

        if ($this->isCsrfTokenValid('delete_commentaire_' . $commentaire->getId(), $request->getPayload()->getString('_token'))) {
            $em->remove($commentaire);
            $em->flush();
            $this->addFlash('success', 'Commentaire supprimé.');
        }

        return $this->redirectToRoute('app_restaurant_show', ['id' => $restaurantId]);
    }
}
