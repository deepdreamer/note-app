<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\RangeFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Repository\NoteRepository;
use ApiPlatform\OpenApi\Model\Operation;
use ApiPlatform\OpenApi\Model\Parameter;
use App\State\NotesByPriorityProvider;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: NoteRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(),
        new GetCollection(
            uriTemplate: '/notes/priority/{priority}',
            uriVariables: [
                'priority' => new Link(
                    fromProperty: 'priority',
                    fromClass: Note::class
                )
            ],
            openapi: new Operation(
                summary: 'Get notes by specific priority',
                description: 'Retrieves all notes matching the specified priority value',
                parameters: [
                    new Parameter(
                        name: 'priority',
                        in: 'path',
                        description: 'Priority level (1-65535)',
                        required: true,
                        schema: [
                            'type' => 'smallint',
                            'minimum' => 1,
                            'maximum' => 65535
                        ]
                    )
                ]
            ),
            name: 'get_notes_by_priority',
            provider: NotesByPriorityProvider::class
        ),
        new Post(),
        new Put(),
        new Get(),
    ],
    normalizationContext: ['groups' => ['note:read']],
    denormalizationContext: ['groups' => ['note:write']]
)]
class Note
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['product:read'])]
    private ?int $id = null;

    public function __construct(string $title)
    {
        $this->title = $title;
        $this->created = new \DateTime();
    }

    #[ORM\Column(length: 255)]
    #[Groups(['note:read', 'note:write'])]
    private string $title;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['note:read', 'note:write'])]
    private ?string $content = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    #[Groups(['note:read', 'note:write'])]
    #[ApiFilter(RangeFilter::class)]
    private ?string $priority = null;

    #[ORM\Column]
    #[Groups(['note:read'])]
    private \DateTime $created;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getPriority(): ?string
    {
        return $this->priority;
    }

    public function setPriority(?string $priority): static
    {
        $this->priority = $priority;

        return $this;
    }

    public function getCreated(): ?\DateTime
    {
        return $this->created;
    }

    public function setCreated(\DateTime $created): static
    {
        $this->created = $created;

        return $this;
    }
}
