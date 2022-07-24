<?php

namespace App\Service;

use App\Entity\Course;
use App\Entity\Transaction;
use App\Entity\User;
use DateInterval;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;

class PayService
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $em)
    {
        $this->entityManager = $em;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function pay(User $user, Course $course): Transaction{
        $this->entityManager->getConnection()->beginTransaction();
        try {
            if ($user->getBalance() < $course->getCost()) {
                throw new \RuntimeException( 'На счету недостаточно средств', Response::HTTP_NOT_ACCEPTABLE);
            }
            $transaction = new Transaction();

            $transaction->setBillingUser($user);
            $transaction->setType(0);
            $transaction->setValue($course->getCost());
            $transaction->setCourse($course);

            if ($course->getType() === 'rent') {
                $expiresAt = (new \DateTimeImmutable())->add(new DateInterval('P2W'));
                $transaction->setEndDatetime($expiresAt);
            }

            $user->setBalance($user->getBalance() - $course->getCost());

            $this->entityManager->persist($transaction);
            $this->entityManager->flush();
            $this->entityManager->getConnection()->commit();
        } catch (\Exception $exception) {
            $this->entityManager->getConnection()->rollBack();
            throw new \RuntimeException($exception->getMessage(), $exception->getCode());
        }
        return $transaction;
    }

    public function deposit(User $user, float $amount)
    {
        $this->entityManager->getConnection()->beginTransaction();
        try {
            $transaction = new Transaction();

            $transaction->setBillingUser($user);
            $transaction->setType(1);
            $transaction->setValue($amount);

            $user->setBalance($user->getBalance() + $amount);

            $this->entityManager->persist($transaction);
            $this->entityManager->flush();
            $this->entityManager->getConnection()->commit();
        } catch (\Exception $exception) {
            $this->entityManager->getConnection()->rollBack();
            throw new \RuntimeException($exception->getMessage(), $exception->getCode());
        }
    }
}