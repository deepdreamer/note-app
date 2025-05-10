<?php

declare(strict_types=1);

namespace App\Story;

use App\Factory\NoteFactory;
use Zenstruck\Foundry\Story;

final class DefaultNoteStory extends Story
{

    public function build(): void
    {
        NoteFactory::createMany(100);
    }

}
