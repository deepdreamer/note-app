<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\RangeFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\OpenApi\Model\Operation;
use ApiPlatform\OpenApi\Model\Parameter;
use App\Repository\NoteRepository;
use App\State\NotesByPriorityProvider;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: NoteRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(
            openapi: new Operation(
                summary: 'Get all notes',
                description: 'Retrieves the collection of all notes, with optional filtering capabilities',
                parameters: [
                    new Parameter(
                        name: 'priority[between]',
                        in: 'query',
                        description: 'Filter by priority range (e.g. 1..5)',
                        required: false,
                        schema: ['type' => 'string']
                    ),
                    new Parameter(
                        name: 'priority[gt]',
                        in: 'query',
                        description: 'Filter notes with priority greater than value',
                        required: false,
                        schema: ['type' => 'integer']
                    ),
                    new Parameter(
                        name: 'priority[gte]',
                        in: 'query',
                        description: 'Filter notes with priority greater than or equal to value',
                        required: false,
                        schema: ['type' => 'integer']
                    ),
                    new Parameter(
                        name: 'priority[lt]',
                        in: 'query',
                        description: 'Filter notes with priority less than value',
                        required: false,
                        schema: ['type' => 'integer']
                    ),
                    new Parameter(
                        name: 'priority[lte]',
                        in: 'query',
                        description: 'Filter notes with priority less than or equal to value',
                        required: false,
                        schema: ['type' => 'integer']
                    ),
                ]
            ),
            description: 'Retrieves the collection of Note resources'
        ),
        new GetCollection(
            uriTemplate: '/notes/priority/{priority}',
            uriVariables: [
                'priority' => new Link(
                    fromProperty: 'priority',
                    fromClass: Note::class
                ),
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
                            'type' => 'integer',
                            'minimum' => 1,
                            'maximum' => 65535,
                        ]
                    ),
                ]
            ),
            name: 'get_notes_by_priority',
            provider: NotesByPriorityProvider::class
        ),
        new Post(
            openapi: new Operation(
                summary: 'Create a new note',
                description: 'Creates a new note with the provided data'
            ),
            description: 'Creates a Note resource'
        ),
        new Put(
            openapi: new Operation(
                summary: 'Replace a note',
                description: 'Fully updates an existing note'
            ),
            description: 'Replaces the Note resource'
        ),
        new Patch(
            openapi: new Operation(
                summary: 'Update a note partially',
                description: 'Partially updates an existing note with the provided data'
            ),
            description: 'Updates the Note resource'
        ),
        new Get(
            openapi: new Operation(
                summary: 'Get a single note',
                description: 'Retrieves a specific note by ID'
            ),
            description: 'Retrieves a Note resource'
        ),
    ],
    normalizationContext: ['groups' => ['note:read']],
    denormalizationContext: ['groups' => ['note:write']]
)]
class Note
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['note:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['note:read', 'note:write'])]
    #[Assert\NotBlank]
    private string $title;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['note:read', 'note:write'])]
    #[Assert\NotBlank]
    private ?string $content = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: false)]
    #[Groups(['note:read', 'note:write'])]
    #[ApiFilter(RangeFilter::class)]
    #[Assert\NotBlank]
    #[Assert\Range(min: 1, max: 65535)]
    private int $priority;

    #[ORM\Column]
    #[Groups(['note:read'])]
    private \DateTime $created;

    public function __construct(string $title)
    {
        $this->title = $title;
        $this->created = new \DateTime();
    }

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

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function setPriority(int $priority): static
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
