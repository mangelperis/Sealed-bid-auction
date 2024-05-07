<?php

namespace App\Domain;

interface AuctionInterface
{
    public function findWinner(): ?Bid;
    public function findWinningPrice(?Bid $winningBid): float;
}