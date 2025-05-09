<?php
// src/DataFixtures/NoteFixtures.php
namespace App\DataFixtures;

use App\Entity\Note;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class NoteFixtures extends Fixture
{
    /**
     * @throws \DateMalformedStringException
     */
    public function load(ObjectManager $manager): void
    {
        // Create an array of note data with different priorities
        $notesData = [
            // Priority 1 (Low priority) notes
            [
                'title' => 'Read an article',
                'content' => 'Find an interesting tech article to read during lunch break',
                'priority' => 1,
                'daysAgo' => 10
            ],
            [
                'title' => 'Buy groceries',
                'content' => 'Milk, bread, eggs, vegetables',
                'priority' => 1,
                'daysAgo' => 2
            ],
            [
                'title' => 'Weekly reflection',
                'content' => 'Review progress on personal goals',
                'priority' => 1,
                'daysAgo' => 5
            ],

            // Priority 2 notes
            [
                'title' => 'Prepare presentation slides',
                'content' => 'Create slides for the next team meeting',
                'priority' => 2,
                'daysAgo' => 3
            ],
            [
                'title' => 'Research new technologies',
                'content' => 'Look into API Platform and GraphQL',
                'priority' => 2,
                'daysAgo' => 14
            ],
            [
                'title' => 'Schedule dentist appointment',
                'content' => 'Call dental office for a check-up',
                'priority' => 2,
                'daysAgo' => 7
            ],

            // Priority 3 (Medium priority) notes
            [
                'title' => 'Complete code review',
                'content' => 'Review pull request for the authentication feature',
                'priority' => 3,
                'daysAgo' => 1
            ],
            [
                'title' => 'Update documentation',
                'content' => 'Add examples to the API documentation',
                'priority' => 3,
                'daysAgo' => 4
            ],
            [
                'title' => 'Weekly team meeting',
                'content' => 'Discuss progress and blockers',
                'priority' => 3,
                'daysAgo' => 6
            ],

            // Priority 4 notes
            [
                'title' => 'Fix critical bug',
                'content' => 'Address the issue with user registration',
                'priority' => 4,
                'daysAgo' => 0 // Today
            ],
            [
                'title' => 'Client presentation',
                'content' => 'Prepare and rehearse for the client presentation',
                'priority' => 4,
                'daysAgo' => 2
            ],
            [
                'title' => 'Submit quarterly report',
                'content' => 'Compile and submit the Q3 performance report',
                'priority' => 4,
                'daysAgo' => 3
            ],

            // Priority 5 notes
            [
                'title' => 'Deploy production release',
                'content' => 'Finalize and deploy v2.0 to production',
                'priority' => 5,
                'daysAgo' => 0 // Today
            ],
            [
                'title' => 'Security vulnerability patch',
                'content' => 'Apply patch for the recently discovered security vulnerability',
                'priority' => 5,
                'daysAgo' => 1
            ],
            [
                'title' => 'Finalize contract',
                'content' => 'Review and sign the contract with the new client',
                'priority' => 5,
                'daysAgo' => 2
            ],
        ];

        // Create the notes
        foreach ($notesData as $index => $noteData) {
            $note = new Note($noteData['title']);
            $note->setContent($noteData['content']);
            $note->setPriority($noteData['priority']);

            // Set created date
            $created = new \DateTime();
            $created->modify('-' . $noteData['daysAgo'] . ' days');
            // Add some random hours to make timestamps more realistic
            $created->modify('-' . rand(0, 23) . ' hours');
            $created->modify('-' . rand(0, 59) . ' minutes');
            $note->setCreated($created);

            $manager->persist($note);

            // Set a reference for potential future fixtures that might need these notes
            $this->addReference('note_' . $index, $note);
        }

        $manager->flush();
    }
}