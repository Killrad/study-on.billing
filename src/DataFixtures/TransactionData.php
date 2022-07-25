<?php

namespace App\DataFixtures;

use App\Entity\Course;
use App\Entity\Transaction;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

use App\Repository\UserRepository;
use App\Repository\CourseRepository;
class TransactionData extends Fixture
{

    public function load(ObjectManager $manager): void
    {
        $courseRepository = $manager->getRepository(Course::class);
        $userRepository = $manager->getRepository(User::class);
        // Пользователь
        $user = $userRepository->findOneBy(['email' => 'admin@example.test']);
        // Получаем существующие курсы
        $freeCourses = $courseRepository->findBy(['Type' => 0]);
        $buyCourses = $courseRepository->findBy(['Type' => 1]);
        $rentCourses = $courseRepository->findBy(['Type' => 2]);
        $transaction1 = new Transaction();
        $transaction1->setBillingUser($user);
        $transaction1->setCourse($freeCourses[0]);
        $transaction1->setType(0);
        $transaction1->setValue(0);
        $transaction1->setDatetimeTransaction(new \DateTimeImmutable('2022-07-20 00:00:00'));

        $manager->persist($transaction1);

        $transaction2 = new Transaction();
        $transaction2->setBillingUser($user);
        $transaction2->setCourse($buyCourses[0]);
        $transaction2->setType(0);
        $transaction2->setValue($buyCourses[0]->getCost());
        $transaction2->setDatetimeTransaction(new \DateTimeImmutable('2022-06-20 00:00:00'));

        $manager->persist($transaction2);

        $transaction3 = new Transaction();
        $transaction3->setBillingUser($user);
        $transaction3->setCourse($rentCourses[0]);
        $transaction3->setType(0);
        $transaction3->setValue($rentCourses[0]->getCost());
        $transaction3->setDatetimeTransaction(new \DateTimeImmutable('2022-06-20 00:00:00'));
        $transaction3->setEndDatetime(new \DateTimeImmutable('2022-07-30 00:00:00'));

        $manager->persist($transaction3);

        $transaction4 = new Transaction();
        $transaction4->setBillingUser($user);
        $transaction4->setType(1);
        $transaction4->setValue(200);
        $transaction4->setDatetimeTransaction(new \DateTimeImmutable('2022-01-20 00:00:00'));

        $manager->persist($transaction4);

        $user2 = $userRepository->findOneBy(['email' => 'common.user@example.test']);

        $manager->persist($transaction2);

        $transaction32 = new Transaction();
        $transaction32->setBillingUser($user2);
        $transaction32->setCourse($buyCourses[1]);
        $transaction32->setType(0);
        $transaction32->setValue($buyCourses[1]->getCost());
        $transaction32->setDatetimeTransaction(new \DateTimeImmutable('2022-06-20 00:00:00'));

        $manager->persist($transaction32);

        $transaction42 = new Transaction();
        $transaction42->setBillingUser($user2);
        $transaction42->setType(1);
        $transaction42->setValue(50);
        $transaction42->setDatetimeTransaction(new \DateTimeImmutable('2022-01-20 00:00:00'));

        $manager->persist($transaction42);

        $manager->flush();

    }
}
