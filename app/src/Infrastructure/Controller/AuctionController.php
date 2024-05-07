<?php
declare(strict_types=1);


namespace App\Infrastructure\Controller;

use App\Application\AuctionService;
use App\Domain\Bid;
use App\Domain\Buyer;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AuctionController
{

    private LoggerInterface $logger;
    private AuctionService $auctionService;

    public function __construct(
        LoggerInterface $logger,
        AuctionService  $auctionService,
    )
    {
        $this->logger = $logger;
        $this->auctionService = $auctionService;
    }

    /**
     * @throws Exception
     */
    #[Route('/auction/demo', name: 'index', methods: ['GET'])]
    public function runSealedBidAuction(Request $request): Response
    {
        try {
            $reservedPrice = 100;
            $this->logger->info("Running Demo Auction for {$reservedPrice} euros!");

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

            $result = $this->auctionService->runAuction($reservedPrice, $bids);

            if ($result['winnerName']) {
                return new Response(sprintf("The buyer %s wins the auction at the price of %f euros.", $result['winnerName'], $result['winningPrice']));
            }

            return new Response(sprintf("There were no bids above %f euros, so no one won the auction", $reservedPrice));
        } catch (Exception $e) {
            $this->logger->error("[API] Run SealedBidAuction error: {$e->getMessage()}");
            return new Response("Something went wrong");
        }

    }
}