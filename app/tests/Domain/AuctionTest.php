<?php
declare(strict_types=1);

namespace App\Tests\Domain;

use App\Domain\Auction;
use App\Domain\Bid;
use App\Domain\Buyer;
use PHPUnit\Framework\TestCase;

class AuctionTest extends TestCase
{
    public function testFindWinnerWithValidBids(): void
    {
        $auction = new Auction(100);

        $buyer1 = new Buyer(1, 'A');
        $buyer2 = new Buyer(2, 'B');

        $auction->addBid(new Bid($buyer1, 110));
        $auction->addBid(new Bid($buyer2, 120));

        $winner = $auction->findWinner();

        $this->assertInstanceOf(Bid::class, $winner);
        $this->assertSame($buyer2, $winner->getBuyer());
        $this->assertEquals(120, $winner->getAmount());
    }

    public function testFindWinnerWithNoValidBids(): void
    {
        $auction = new Auction(100);

        $buyer1 = new Buyer(1, 'A');
        $buyer2 = new Buyer(2, 'B');

        $auction->addBid(new Bid($buyer1, 90.0));
        $auction->addBid(new Bid($buyer2, 95.0));

        $winner = $auction->findWinner();

        $this->assertNull($winner);
    }

    public function testFindWinningPriceWithValidBids(): void
    {
        $auction = new Auction(100);

        $buyer1 = new Buyer(1, 'A');
        $buyer2 = new Buyer(2, 'B');
        $buyer3 = new Buyer(3, 'C');

        $auction->addBid(new Bid($buyer1, 110));
        $auction->addBid(new Bid($buyer2, 120));
        $auction->addBid(new Bid($buyer3, 130));

        $winner = $auction->findWinner();
        $winningPrice = $auction->findWinningPrice($winner);

        $this->assertEquals(120, $winningPrice);
    }

    public function testFindWinningPriceWithNoValidBids(): void
    {
        $auction = new Auction(100);

        $buyer1 = new Buyer(1, 'A');
        $buyer2 = new Buyer(2, 'B');

        $auction->addBid(new Bid($buyer1, 90));
        $auction->addBid(new Bid($buyer2, 95));

        $winner = $auction->findWinner();
        $winningPrice = $auction->findWinningPrice($winner);

        $this->assertEquals(100, $winningPrice);
    }

    public function testFindWinningPriceWithOnlyWinnerBids(): void
    {
        $auction = new Auction(100);

        $buyer1 = new Buyer(1, 'A');

        $auction->addBid(new Bid($buyer1, 110.0));
        $auction->addBid(new Bid($buyer1, 120.0));

        $winner = $auction->findWinner();
        $winningPrice = $auction->findWinningPrice($winner);

        $this->assertSame(100.0, $winningPrice);
    }
}
