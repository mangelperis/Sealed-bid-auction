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
    #[Route('/auction/task', name: 'task_auction', methods: ['GET'])]
    public function runSealedBidAuction(): Response
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

            //TODO Add responseHandler
            if ($result['winnerName']) {
                return new Response(sprintf("The buyer %s wins the auction at the price of %d euros.", $result['winnerName'], $result['winningPrice']));
            }

            return new Response(sprintf("There were no bids above %d euros, so no one won the auction", $reservedPrice));
        } catch (Exception $e) {
            $this->logger->error("[API] Run SealedBidAuction error: {$e->getMessage()}");
            return new Response("Something went wrong");
        }

    }

    /**
     * @throws Exception
     */
    #[Route('/auction/demo', name: 'demo_auction', methods: ['GET'])]
    public function runSealedBidAuctionDemo(): Response
    {
        try {
            $reservedPrice = 500;
            $this->logger->info("Running Demo Auction for {$reservedPrice} euros!");

            $buyerA = new Buyer(1, 'A');
            $buyerB = new Buyer(2, 'B');
            $buyerC = new Buyer(3, 'C');
            $buyerD = new Buyer(4, 'D');
            $buyerE = new Buyer(5, 'E');
            //Expected Winner
            $buyerF = new Buyer(6, 'E');

            $bids = [
                new Bid($buyerA, 400),
                new Bid($buyerA, 499.99),
                new Bid($buyerB, 0),
                new Bid($buyerC, 0),
                new Bid($buyerD, 501),
                new Bid($buyerD, 505),
                new Bid($buyerD, 510),
                //Expected winningPrice
                new Bid($buyerE, 600),
                new Bid($buyerE, 350),
                new Bid($buyerE, 550),
                new Bid($buyerF, 610),
            ];

            $result = $this->auctionService->runAuction($reservedPrice, $bids);

            //TODO Add responseHandler
            if ($result['winnerName']) {
                return new Response(sprintf("The buyer %s wins the auction at the price of %d euros.", $result['winnerName'], $result['winningPrice']));
            }

            return new Response(sprintf("There were no bids above %d euros, so no one won the auction", $reservedPrice));
        } catch (Exception $e) {
            $this->logger->error("[API] Run SealedBidAuctionDemo error: {$e->getMessage()}");
            return new Response("Something went wrong");
        }

    }
}