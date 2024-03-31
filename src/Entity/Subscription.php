<?php

namespace App\Entity;

use App\Repository\SubscriptionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SubscriptionRepository::class)]
#[ORM\Table('subscriptions')]
class Subscription
{
    private static $planDataNames = ['free', 'pro', 'enterprise'];

    private static $planDataPrices = [
        'free' => 0,
        'pro' => 15,
        'enterprise' => 29,
    ];

    public static function getPlanDataNameByIndex(int $index): string
    {
        return self::$planDataNames[$index];
    }

    public static function getPlanDataPriceByName(string $name): int
    {
        return self::$planDataPrices[$name];
    }

    public static function getPlanDataNames(): array
    {
        return self::$planDataNames;
    }

    public static function getPlanDataPrices(): array
    {
        return self::$planDataPrices;
    }

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $plan = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $valid_to = null;

    #[ORM\Column(length: 45, nullable: true)]
    private ?string $payment_status = null;

    #[ORM\Column]
    private ?bool $free_plan_used = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPlan(): ?string
    {
        return $this->plan;
    }

    public function setPlan(string $plan): static
    {
        $this->plan = $plan;

        return $this;
    }

    public function getValidTo(): ?\DateTimeInterface
    {
        return $this->valid_to;
    }

    public function setValidTo(\DateTimeInterface $valid_to): static
    {
        $this->valid_to = $valid_to;

        return $this;
    }

    public function getPaymentStatus(): ?string
    {
        return $this->payment_status;
    }

    public function setPaymentStatus(?string $payment_status): static
    {
        $this->payment_status = $payment_status;

        return $this;
    }

    public function isFreePlanUsed(): ?bool
    {
        return $this->free_plan_used;
    }

    public function setFreePlanUsed(bool $free_plan_used): static
    {
        $this->free_plan_used = $free_plan_used;

        return $this;
    }
}
