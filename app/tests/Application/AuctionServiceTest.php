<?php

namespace App\Tests\Application;

use App\Application\AuctionService;
use App\Domain\Bid;
use App\Domain\Buyer;
use Exception;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class AuctionServiceTest extends TestCase
{

    private LoggerInterface $logger;

    public function setUp(): void
    {
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->auctionService = new AuctionService($this->logger);

    }

    /**
     * @throws Exception
     */
    public function testRunAuction(): void
    {
        //Sample data
        $reservePrice = 100;

        $buyerA = new Buyer(1, 'A');
        $buyerB = new Buyer(2, 'B');
        $buyerC = new Buyer(3, 'C');
        $buyerD = new Buyer(4, 'D');
        $buyerE = new Buyer(5, 'E');

        $bids = [
            new Bid($buyerA, 110),
            new Bid($buyerA, 130),
            new Bid($buyerB, 0),
            new Bid($buyerC, 125),
            new Bid($buyerD, 105),
            new Bid($buyerD, 115),
            new Bid($buyerD, 90),
            new Bid($buyerE, 132),
            new Bid($buyerE, 135),
            new Bid($buyerE, 140),
        ];

        //Run the auction with the sample Data
        $result = $this->auctionService->runAuction($reservePrice, $bids);

        //Expected result
        $this->assertSame('E', $result['winnerName']);
        $this->assertSame(130, $result['winningPrice']);
    }
}
