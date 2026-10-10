<?php

namespace App\MessageHandler;

use App\Entity\Commentaire;
use App\Message\NouveauCommentaire;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class NouveauCommentaireHandler
{
    public function __construct(
        private EntityManagerInterface $em,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(NouveauCommentaire $message): void
    {
        $debut = microtime(true);

        $commentaire = $this->em->getRepository(Commentaire::class)->find($message->getCommentaireId());

        if (!$commentaire) {
            $this->logger->warning('Commentaire introuvable', ['id' => $message->getCommentaireId()]);
            return;
        }

        sleep(5);

        $this->logger->info('Notification envoyée pour le commentaire', [
            'id' => $commentaire->getId(),
            'restaurant' => $commentaire->getRestaurant()->getNom(),
            'auteur' => $commentaire->getAuteur()?->getUserIdentifier(),
        ]);
    }
}