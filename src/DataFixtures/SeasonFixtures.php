<?php

namespace App\DataFixtures;

use App\Entity\Season;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class SeasonFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('en_US');
        for ($i = 0; $i < 6; $i++) {
            $season = new Season();
            $program = $this->getReference('program_' . $i);
            $season->setProgram($program);
            $season->setDescription($faker->text(100), true);
            $season->setNumber($i);
            $season->setYear(rand(2000, 2020));
            $manager->persist($season);
            $this->addReference('season_' . $i, $season);
        }
        $seasonNum = 6;
        for ($j = 0; $j < 6; $j++) {
            $season = new Season();
            $program = $this->getReference('program_' . $j);
            $season->setProgram($program);
            $season->setNumber(2);
            $season->setYear(rand(2000, 2020));
            $season->setDescription($faker->sentence);
            $manager->persist($season);
            $this->addReference('season_' . $seasonNum, $season);
            $seasonNum++;
        }
$manager->flush();
}

public
function getDependencies()
{
    return [ProgramFixtures::class];
}
}
