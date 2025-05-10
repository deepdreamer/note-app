<?php

declare(strict_types=1);

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Note;
use Doctrine\ORM\EntityManagerInterface;

final class NotesByPriorityProvider implements ProviderInterface
{

    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    /**
     * @param array<mixed> $uriVariables
     * @param array<mixed> $context
     * @return array<Note>
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): array
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
