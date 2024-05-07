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
        //Filter bids with an amount >= reservedPrice
        $filteredValidBids = array_filter(
            $this->bids,
            function (Bid $bid) {
                return $bid->getAmount() >= $this->reservePrice;
            });

        //No one bid enough amount
        if (empty($filteredValidBids)) {
            return null;
        }


        //Sort the bids by the amount, desc
        usort(
            $filteredValidBids,
            function (Bid $a, Bid $b) {
                return $b->getAmount() > $a->getAmount();
            });

        //First element of the array will be the bigger amount
        return reset($filteredValidBids);
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