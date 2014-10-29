<?php

namespace Vacatia\TripAdvisorBundle\Services;

class TripAdvisor
{
    private $ratingLevels = array(
        1 => 'Terrible',
        2 => 'Poor',
        3 => 'Average',
        4 => 'Very good',
        5 => 'Excellent'
    );

    public function getRatingPhrase($rating)
    {
        $rating = floor($rating);
        if ($rating == 0) {
            $rating = 1;
        }

        return $this->ratingLevels[$rating];
    }
}
