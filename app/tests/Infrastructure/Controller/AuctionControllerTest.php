<?php
declare(strict_types=1);

namespace App\Tests\Infrastructure\Controller;

use App\Application\AuctionService;
use App\Domain\Bid;
use App\Domain\Buyer;
use Exception;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Infrastructure\Controller\AuctionController;

class AuctionControllerTest extends TestCase
{
    private AuctionController $auctionController;
    private LoggerInterface $logger;
    private AuctionService $auctionService;

    protected function setUp(): void
    {
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->auctionService = $this->createMock(AuctionService::class);
        $this->auctionController = new AuctionController($this->logger, $this->auctionService);
    }

    /**
     * @throws Exception
     */
    public function testRunSealedBidAuctionWithWinner(): void
    {
        $this->auctionService->expects($this->once())
            ->method('runAuction')
            ->willReturn([
                'winnerName' => 'E',
                'winningPrice' => 130,
            ]);

        $response = $this->auctionController->runSealedBidAuction();

        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame('The buyer E wins the auction at the price of 130 euros.', $response->getContent());
        $this->assertSame(200, $response->getStatusCode());
    }


    /**
     * @throws Exception
     */
    public function testRunSealedBidAuctionWithoutWinner(): void
    {
        $this->auctionService->expects($this->once())
            ->method('runAuction')
            ->willReturn([
                'winnerName' => null,
                'winningPrice' => 100,
            ]);

        $response = $this->auctionController->runSealedBidAuction();

        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame('There were no bids above 100 euros, so no one won the auction', $response->getContent());
        $this->assertSame(200, $response->getStatusCode());
    }

}
