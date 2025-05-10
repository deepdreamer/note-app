<?php

declare(strict_types=1);

// src/DataFixtures/NoteFixtures.php

namespace App\DataFixtures;

use App\Story\DefaultNoteStory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class NoteFixtures extends Fixture
{

    /** @throws \DateMalformedStringException */
    public function load(ObjectManager $manager): void
    {
        DefaultNoteStory::load();
    }

}
