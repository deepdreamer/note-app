<?php
// src/DataFixtures/NoteFixtures.php
namespace App\DataFixtures;

use App\Entity\Note;
use App\Story\DefaultNoteStory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class NoteFixtures extends Fixture
{
    /**
     * @throws \DateMalformedStringException
     */
    public function load(ObjectManager $manager): void
    {
        DefaultNoteStory::load();
    }
}