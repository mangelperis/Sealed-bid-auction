<?php
declare(strict_types=1);


namespace App\Application;

use App\Domain\Auction;
use App\Domain\Bid;
use Exception;
use Psr\Log\LoggerInterface;

class AuctionService
{
    private LoggerInterface $logger;

    public function __construct(
        LoggerInterface $logger,
    )
    {
        $this->logger = $logger;

    }

    /**
     * @param float $reservePrice
     * @param array $bids
     * @return array
     * @throws Exception
     */
    public function runAuction(float $reservePrice, array $bids): array
    {
        try {
            $auction = new Auction($reservePrice);

            /** @var Bid $bid */
            foreach ($bids as $bid) {
                $auction->addBid($bid);
            }

            $winningBid = $auction->findWinner();
            $winningPrice = $auction->findWinningPrice($winningBid);

            return [
                'winnerName' => $winningBid ? $winningBid->getBuyer()->getName() : null,
                'winningPrice' => $winningPrice,
            ];

        } catch (Exception $e) {
            $this->logger->error(sprintf("SERVICE runAuction fail: %s", $e->getMessage()));
            throw new Exception('Error while running Auction');
        }
    }
}