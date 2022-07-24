<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Course;

class CourseData extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $course1 = new Course();
        $course1->setCharCode('mathematics-and-python');
        $course1->setType(0);
        $course1->setCost(0);
        $manager->persist($course1);

        $course2 = new Course();
        $course2->setCharCode('supervised-learning');
        $course2->setType(1);
        $course2->setCost(10);
        $manager->persist($course2);

        $course3 = new Course();
        $course3->setCharCode('unsupervised-learning');
        $course3->setType(2);
        $course3->setCost(60);
        $manager->persist($course3);

        $course4 = new Course();
        $course4->setCharCode('stats-for-data-analysis');
        $course4->setType(1);
        $course4->setCost(60);
        $manager->persist($course4);

        $course5 = new Course();
        $course5->setCharCode('data-analysis-applications');
        $course5->setType(2);
        $course5->setCost(100);
        $manager->persist($course5);

        $manager->flush();
    }
}
