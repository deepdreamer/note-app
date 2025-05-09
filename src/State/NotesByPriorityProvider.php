<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Note;
use Doctrine\ORM\EntityManagerInterface;

class NotesByPriorityProvider implements ProviderInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $priority = (int)($uriVariables['priority'] ?? 0);

        if ($priority <= 0) {
            return [];
        }

        // Query for notes with the specified priority
        return $this->entityManager->getRepository(Note::class)
            ->findBy(['priority' => $priority], ['created' => 'DESC']);
    }
}