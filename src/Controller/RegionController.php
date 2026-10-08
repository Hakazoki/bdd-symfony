<?php

namespace App\Controller;

use App\Entity\Region;
use App\Form\RegionType;
use App\Repository\RegionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class RegionController extends AbstractController
{
    #[Route('/region', name: 'app_region_list')]
    public function list(RegionRepository $regionRepository): Response
    {
        return $this->render('region/list.html.twig', [
            'regions' => $regionRepository->findBy([], ['nom' => 'ASC']),
        ]);
    }

    #[Route('/region/add', name: 'app_region_add')]
    public function add(Request $request, EntityManagerInterface $em): Response
    {
        $region = new Region();
        $form = $this->createForm(RegionType::class, $region);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($region);
            $em->flush();
            $this->addFlash('success', 'Région créée.');

            return $this->redirectToRoute('app_region_list');
        }

        return $this->render('region/add.html.twig', ['form' => $form]);
    }

    #[Route('/region/{id}/delete', name: 'app_region_delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function delete(Request $request, Region $region, EntityManagerInterface $em): Response
    {
        if (!$this->isCsrfTokenValid('delete_region_' . $region->getId(), $request->getPayload()->getString('_token'))) {
            return $this->redirectToRoute('app_region_list');
        }

        if (!$region->getVilles()->isEmpty()) {
            $this->addFlash('error', sprintf('la région « %s » contient encore %d ville(s), supprimez-les d\'abord.', $region->getNom(), $region->getVilles()->count()));

            return $this->redirectToRoute('app_region_list');
        }

        $em->remove($region);
        $em->flush();
        $this->addFlash('success', 'Région supprimée.');

        return $this->redirectToRoute('app_region_list');
    }
}
