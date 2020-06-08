<?php


namespace App\DataFixtures;

use App\Service\Slugify;
use  Faker;
use App\Entity\Actor;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ActorFixtures extends Fixture implements DependentFixtureInterface
{
const ACTORS=[
    ['name' => 'Andrew Lincoln',
        'programs' => ['program_0','program_5']
    ],
    ['name' => 'Norman Reedus',

        'programs' => ['program_0']
    ],
    ['name' => 'Lauren Cohan',

        'programs' => ['program_0']
    ],
    ['name' => 'Danai Gurira',

        'programs' => ['program_0']
    ],
];
    public function load(ObjectManager $manager)
    {
        foreach (self::ACTORS as $name => $actorData) {
            $actor = new Actor();
            $actor->setName($actorData['name']);
            foreach ($actorData['programs'] as $program) {
                $actor->addProgram($this->getReference($program));
            }
            $slugify = new Slugify();
            $slug = $slugify->generate($actor->getName());
            $actor ->setSlug($slug);
            $manager->persist($actor);
            $this->addReference('actor_' . $name, $actor);
        }
            $faker = Faker\Factory::create('en_US');
            for ($i = 5; $i < 51; $i++) {
                $actor = new Actor();
                $actor->setName($faker->name());
                $slugify = new Slugify();
                $slug = $slugify->generate($actor->getName());
                $actor ->setSlug($slug);
                $actor->addProgram($this->getReference('program_' . rand(0,5)));
                $manager->persist($actor);
            }

        $manager->flush();
    }

    public function getDependencies()
    {
        return[ProgramFixtures::class];
    }
}
