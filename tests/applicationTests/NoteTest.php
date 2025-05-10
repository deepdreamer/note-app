<?php

namespace applicationTests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Symfony\Bundle\Test\Client;
use App\Entity\Note;
use App\Factory\NoteFactory;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class NoteTest extends ApiTestCase
{
    use ResetDatabase, Factories;

    private Client $client;

    public function setUp(): void
    {
        parent::setUp();

        static::$alwaysBootKernel = false;

        $this->client = static::createClient();
    }

    public function testDatabaseConfigurationDebug(): void
    {
        $container = self::getContainer();
        $doctrine = $container->get('doctrine');
        $connection = $doctrine->getConnection();
        $params = $connection->getParams();

        echo "\n\nUsing database: {$params['dbname']} with environment: " . $_SERVER['APP_ENV'] . "\n\n";

        $this->assertTrue($params['dbname'] === 'symfony_api_test');
    }

    /**
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     */
    public function testGetCollection()
    {
        NoteFactory::createMany(5, [ 'priority' => 1 ]);
        NoteFactory::createMany(5, [ 'priority' => 2 ]);
        NoteFactory::createMany(50, [ 'priority' => 3 ]);
        NoteFactory::createMany(40, [ 'priority' => 4 ]);

        self::bootKernel();
        $response = $this->client->request('GET', '/api/notes');

        $this->assertResponseIsSuccessful();

        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertJsonContains([
            '@context' => '/api/contexts/Note',
            '@id' => '/api/notes',
            '@type' => 'Collection',
            'totalItems' => 100,
            'view' => [
                '@id' => '/api/notes?page=1',
                '@type' => 'PartialCollectionView',
                'first' => '/api/notes?page=1',
                'last' => '/api/notes?page=4',
                'next' => '/api/notes?page=2',
            ],
        ]);

        $this->assertCount(30, $response->toArray()['member']);

        $this->assertMatchesResourceCollectionJsonSchema(Note::class);

        $response = $this->client->request('GET', '/api/notes/priority/1');

        $this->assertCount(5, $response->toArray()['member']);

        $this->client->request('GET', '/api/notes?priority[gte]=4');

        $this->assertJsonContains([
            '@context' => '/api/contexts/Note',
            '@id' => '/api/notes',
            '@type' => 'Collection',
            'totalItems' => 40,
            'view' => [
                '@id' => '/api/notes?priority%5Bgte%5D=4&page=1',
                '@type' => 'PartialCollectionView',
                'first' => '/api/notes?priority%5Bgte%5D=4&page=1',
                'last' => '/api/notes?priority%5Bgte%5D=4&page=2',
                'next' => '/api/notes?priority%5Bgte%5D=4&page=2',
            ],
        ]);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function testCreateNote(): void
    {
        self::bootKernel();

        $this->client->request('POST', '/api/notes', ['json' => [
            'title' => 'Ukol 1',
            'content' => 'Koukat na netflix',
            'priority' => 1,
        ]]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertJsonContains([
            '@context' => '/api/contexts/Note',
            '@type' => 'Note',
            'title' => 'Ukol 1',
            'content' => 'Koukat na netflix',
            'priority' => 1,
        ]);
        $this->assertMatchesResourceItemJsonSchema(Note::class);
    }

    /**
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     */
    public function testCreateInvalidNote(): void
    {
        self::bootKernel();

        $this->client->request('POST', '/api/notes', ['json' => [
            'title' => 'Ukol 2',
            'content' => 'Uklizet',
            'priority' => -1,
        ]]);


        $this->assertResponseStatusCodeSame(422);
        $this->assertResponseHeaderSame('content-type', 'application/problem+json; charset=utf-8');
        $this->assertJsonContains([
            '@context' => '/api/contexts/ConstraintViolation',
            '@type' => 'ConstraintViolation',
            'title' => 'An error occurred',
            'description' => 'priority: This value should be between 1 and 65535.',
        ]);

        $this->client->request('POST', '/api/notes', ['json' => ['title' => 'Ukol 3', 'priority' => 1,]]);
        $this->assertResponseStatusCodeSame(422);
        $this->assertResponseHeaderSame('content-type', 'application/problem+json; charset=utf-8');
        $this->assertJsonContains([
            '@context' => '/api/contexts/ConstraintViolation',
            '@type' => 'ConstraintViolation',
            'title' => 'An error occurred',
            'description' => 'content: This value should not be blank.',
        ]);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function testUpdateNote(): void
    {
        NoteFactory::createOne(['title' => 'Nejlepsi ukol']);

        $iri = $this->findIriBy(Note::class, ['title' => 'Nejlepsi ukol']);

        $this->client->request('PATCH', $iri, [
            'json' => [
                'title' => 'updated title',
            ],
            'headers' => [
                'Content-Type' => 'application/merge-patch+json',
            ]
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@id' => $iri,
            'title' => 'updated title',
        ]);
    }
}