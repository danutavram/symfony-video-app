<?php

namespace App\Controller\Traits;

use App\Entity\Subscription;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

trait SaveSubscription
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    private function saveSubscription($plan, $user)
    {
        $date = new DateTime();
        $date->modify('+1 month');
        $subscription = $user->getSubscription();

        if (null === $subscription) {
            $subscription = new Subscription;
        }

        if ($subscription->getFreePlanUsed() && $plan == Subscription::getPlanDataNameByIndex(0)) {
            return;
        }
        $subscription->setValidTo($date);
        $subscription->setPlan($plan);

        if ($plan == Subscription::getPlanDataNameByIndex(0)) {
            $subscription->setFreePlanUsed(true);
            $subscription->setPaymentStatus('paid');
        }

        $subscription->setPaymentStatus('paid'); // tmp

        $user->setSubscription($subscription);

        $this->em->persist($user);
        $this->em->flush();
    }
}
