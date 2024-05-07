<?php
declare(strict_types=1);


namespace App\Domain;

class Bid
{
    private Buyer $buyer;

    private float $amount;

    public function __construct(Buyer $buyer, float $amount)
    {
        $this->buyer = $buyer;
        $this->amount = $amount;
    }

    public function getBuyer(): Buyer
    {
        return $this->buyer;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }
}