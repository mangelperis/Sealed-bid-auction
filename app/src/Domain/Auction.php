<?php
declare(strict_types=1);


namespace App\Domain;

class Auction implements AuctionInterface
{
    private float $reservePrice;
    private array $bids;

    public function __construct(float $reservePrice)
    {
        $this->reservePrice = $reservePrice;
        $this->bids = [];
    }

    public function addBid(Bid $bid): void
    {
        $this->bids[] = $bid;
    }

    /**
     * @return Bid|null
     */
    public function findWinner(): ?Bid
    {
        return null;
    }

    /**
     * @param Bid|null $winningBid
     * @return float
     */
    public function findWinningPrice(?Bid $winningBid): float
    {
        return 1;
    }
}