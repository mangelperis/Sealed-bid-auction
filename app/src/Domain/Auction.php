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

    /** Find a Winner Auction by highest bid amount
     * @return Bid|null
     */
    public function findWinner(): ?Bid
    {
        //Filter bids with an amount >= reservedPrice
        $filteredValidBids = $this->getValidBids();

        //No one bid enough amount
        if (empty($filteredValidBids)) {
            return null;
        }

        //Sort the bids by the amount, desc
        $this->sortBidsByAmountDesc($filteredValidBids);

        //First element of the array will be the bigger amount
        return reset($filteredValidBids);
    }

    /** Find a WinningPrice Auction by the highest bid amount, from a non-winning buyer above the reserve price (or the reserve price if none applies)
     * @param Bid|null $winningBid
     * @return float
     */
    public function findWinningPrice(?Bid $winningBid): float
    {
        //Remove the winner Buyer's bids from the array
        $this->removeBuyerBids($winningBid->getBuyer());

        //Filter bids with an amount >= reservedPrice
        $filteredValidBids = $this->getValidBids();

        //If there's no valid Bid then the winning price is the reserved price
        if (empty($filteredValidBids)) {
            return $this->reservePrice;
        }

        //Sort the bids by the amount, desc
        $this->sortBidsByAmountDesc($filteredValidBids);

        //First element of the array will be the bigger amount
        /** @var Bid $winningBid */
        $winningPriceBid = reset($filteredValidBids);

        return round($winningPriceBid->getAmount(), 1);
    }

    /**
     * @param array $bids
     * @return void
     */
    private function sortBidsByAmountDesc(array &$bids): void
    {
        usort(
            $bids,
            function (Bid $a, Bid $b) {
                return $b->getAmount() - $a->getAmount();
            });
    }

    /**
     * @return array
     */
    private function getValidBids(): array
    {
        return array_filter(
            $this->bids,
            function (Bid $bid) {
                return $bid->getAmount() >= $this->reservePrice;
            });
    }

    /**
     * @param Buyer $buyer
     * @return void
     */
    private function removeBuyerBids(Buyer $buyer): void
    {
        $this->bids = array_filter(
            $this->bids,
            function (Bid $bid) use ($buyer) {
                return $bid->getBuyer() !== $buyer;
            }
        );
    }
}