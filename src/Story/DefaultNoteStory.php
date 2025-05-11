<?php

declare(strict_types=1);

namespace App\Story;

use App\Factory\NoteFactory;
use Zenstruck\Foundry\Story;

final class DefaultNoteStory extends Story
{

    public function build(): void
    {
        NoteFactory::createMany(10, ['priority' => 1]);;
        NoteFactory::createMany(10, ['priority' => 2]);;
        NoteFactory::createMany(10, ['priority' => 3]);;
        NoteFactory::createMany(10, ['priority' => 4]);;
        NoteFactory::createMany(10, ['priority' => 5]);;
    }

}
