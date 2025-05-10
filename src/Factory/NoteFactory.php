<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Note;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/** @extends \Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory<\App\Entity\Note> */
final class NoteFactory extends PersistentProxyObjectFactory
{

    public static function class(): string
    {
        return Note::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     * @return array<string, mixed>
     */
    protected function defaults(): array
    {
        return [
            'content' => self::faker()->text(255),
            'created' => self::faker()->dateTime(),
            'priority' => 1,
            'title' => self::faker()->sentence(3),
        ];
    }

    /** @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization */
    protected function initialize(): static
    {
        return $this;
    }

}
