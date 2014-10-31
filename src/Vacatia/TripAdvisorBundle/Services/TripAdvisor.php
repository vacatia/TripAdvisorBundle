<?php

namespace Vacatia\TripAdvisorBundle\Services;

use Vacatia\TripAdvisorBundle\Services\TripAdvisorClient;

class TripAdvisor
{
    protected $tripAdvisorClient;

    private $ratingLevels = array(
        1 => 'Terrible',
        2 => 'Poor',
        3 => 'Average',
        4 => 'Very good',
        5 => 'Excellent'
    );

    public function __construct(TripAdvisorClient $tripAdvisorClient)
    {
        $this->tripAdvisorClient = $tripAdvisorClient;
    }

    public function getRatingPhrase($rating)
    {
        $rating = floor($rating);
        if ($rating == 0) {
            $rating = 1;
        }

        return $this->ratingLevels[$rating];
    }

    public function getResortData($resortCode)
    {
        $propertyTAData = null;

        $TAClient = $this->tripAdvisorClient;
        $TAClientCacheService = $TAClient->getCacheService();
        
        if ($TAClientCacheService->contains(sprintf('location/%d', $resortCode))) {
            $propertyTAData = $TAClientCacheService->fetch(sprintf('location/%d', $resortCode));
        } else {
            $propertyTAData = $TAClient->getLocation($resortCode);
        }

        return $propertyTAData;
    }
}
