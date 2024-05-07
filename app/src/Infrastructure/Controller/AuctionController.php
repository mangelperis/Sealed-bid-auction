<?php
declare(strict_types=1);


namespace App\Infrastructure\Controller;

use App\Application\AuctionService;
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

    #[Route('/auction/demo', name: 'index', methods: ['GET'])]
    public function runSealedBidAuction(Request $request): Response
    {
        $this->logger->info("Running Demo Auction!");

        //TODO run service auction

        return new Response("result");
    }
}