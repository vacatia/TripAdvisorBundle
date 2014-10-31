<?php

namespace Vacatia\TripAdvisorBundle\Twig;

use Symfony\Component\DependencyInjection\ContainerInterface;

class TripAdvisorExtension extends \Twig_Extension
{
    private $container;

    public function __construct(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('tripadvisor_rating_phrase', array($this, 'getRatingPhrase')),
            new \Twig_SimpleFunction('tripadvisor_num_reviews', array($this, 'getNumReviews')),
        );
    }

    public function getRatingPhrase($rating)
    {
        $tripAdvisor = $this->container->get('vacatia_trip_advisor.trip_advisor');

        $ratingPhrase = $tripAdvisor->getRatingPhrase($rating);

        return $ratingPhrase;
    }
    
    public function getNumReviews($resortCode)
    {
        $tripAdvisor = $this->container->get('vacatia_trip_advisor.trip_advisor');

        $tAData = $tripAdvisor->getResortData($resortCode);

        return isset($tAData['num_reviews']) ? $tAData['num_reviews'] : null;
    }

    public function getName()
    {
        return 'trip_advisor_extension';
    }
}
