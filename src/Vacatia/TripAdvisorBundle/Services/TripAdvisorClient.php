<?php

namespace Vacatia\TripAdvisorBundle\Services;

use Doctrine\Common\Cache\CacheProvider;
use Guzzle\Http\Client;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Vacatia\TripAdvisorBundle\Validator\TripAdvisorRequestDataValidator;
use Vacatia\TripAdvisorBundle\Helper\Currency;
use Vacatia\TripAdvisorBundle\Helper\Language;
use Vacatia\TripAdvisorBundle\Helper\LengthUnit;

class TripAdvisorClient
{
    const BASE_URL = 'https://api.tripadvisor.com/api/partner/2.0/';

    /** @var Client */
    protected $client;

    /** @var CacheProvider */
    protected $cacheService;

    /** @var ContainerInterface */
    protected $container;

    protected $key;

    protected $cache_lifetime;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        $this->key = $this->container->getParameter('vacatia_trip_advisor.key');
        $this->cache_lifetime = $this->container->getParameter('vacatia_trip_advisor.cache.lifetime');
        $this->cacheService = $this->container->get('vacatia_trip_advisor.cache.service');

        $this->client = new Client(self::BASE_URL);
    }

    /**
     * @return CacheProvider
     */
    public function getCacheService()
    {
        return $this->cacheService;
    }

    /**
     * Perform the actual request
     *
     * @param $uri string
     * @param $parameters
     * @return array
     */
    protected function doRequest($uri, array $parameters)
    {
        $url = self::BASE_URL . $uri . '?' . http_build_query($parameters);

        $req = $this->client->createRequest('GET', $url, array(
            'X-TripAdvisor-API-Key' => $this->key,
        ));

        if ($this->cacheService->contains($uri)) {
            $response = $this->cacheService->fetch($uri);
        } else {
            $response = json_decode($req->send()->getBody(true), true);
            $this->cacheService->save($uri, $response, $this->cache_lifetime);
        }

        return $response;
    }

    /**
     * @param $idData
     * @return bool
     * @throws \Exception
     */
    protected function validateIdData($idData)
    {
        if (is_array($idData)) {
            foreach ($idData as $id) {
                $this->validateIdData($id);
            }
        } else {
            if (!is_numeric($idData) || $idData <= 0) {
                throw new \Exception('Invalid id data ' . $idData);
            }
        }
    }

    /**
     * Call the API with the unique ID for a hotel, restaurant, attraction or destination.
     * The response provides data such as: name, address, overall traveler rating, number of reviews,
     * link to read all reviews, link to write reviews, recent review snippets, along with additional
     * data elements.
     * Some data elements may not output if they do not apply to the particular type of location.
     *
     * Multi-get is not supported by this method!
     *
     * https://developer-tripadvisor.com/content-api/documentation/location/
     *
     * @param integer $id
     * @param string $lang
     * @param string $currency
     * @return array
     * @throws \Exception
     */
    public function getLocation($id, $lang = Language::ENGLISH, $currency = Currency::UNITED_STATES_DOLLAR)
    {
        if (!is_numeric($id) || $id <= 0) {
            throw new \Exception('Invalid id data ' . $id);
        }

        $parameters = array(
            'lang' => $lang,
            'currency' => $currency,
        );

        TripAdvisorRequestDataValidator::validateLanguage($lang);
        TripAdvisorRequestDataValidator::validateCurrency($currency);

        return $this->doRequest(sprintf('location/%d', $id), $parameters);
    }

    /**
     * Call the API with the unique ID for a destination. The response will provide a maximum
     * list of top 10 attractions along with data for each attraction in the list.
     * Alternatively, call the API with a comma-separated list of attraction location ids,
     * get data for the attractions in your list.
     *
     * https://developer-tripadvisor.com/content-api/documentation/location-attractions/
     *
     * @param $idData
     * @param array $subcategories
     * @param string $lang
     * @param string $currency
     * @return array
     */
    public function getLocationAttractions($idData, array $subcategories = array(), $lang = Language::ENGLISH, $currency = Currency::UNITED_STATES_DOLLAR)
    {
        $this->validateIdData($idData);

        $parameters = array(
            'lang' => $lang,
            'currency' => $currency,
        );

        if ($subcategories) {
            TripAdvisorRequestDataValidator::validateAttractionSubcategories($subcategories);
            $parameters['subcategory'] = implode(',', $subcategories);
        }

        TripAdvisorRequestDataValidator::validateLanguage($lang);
        TripAdvisorRequestDataValidator::validateCurrency($currency);

        if (is_array($idData)) {
            $idData = implode(',', $idData);
        }

        return $this->doRequest(sprintf('location/%s/attractions', $idData), $parameters);
    }

    /**
     * Call the API with the unique ID for a destination.
     * The response will provide a maximum list of top 10 hotels along with data for each hotel
     * in the list. Alternatively, call the API with a comma-separated list of hotel location ids,
     * get data for the hotels in your list.
     *
     * https://developer-tripadvisor.com/content-api/documentation/location-hotels/
     *
     * @param $idData
     * @param array $subcategories
     * @param string $lang
     * @param string $currency
     * @return array
     */
    public function getLocationHotels($idData, array $subcategories = array(), $lang = Language::ENGLISH, $currency = Currency::UNITED_STATES_DOLLAR)
    {
        $this->validateIdData($idData);

        $parameters = array(
            'lang' => $lang,
            'currency' => $currency,
        );

        if ($subcategories) {
            TripAdvisorRequestDataValidator::validateHotelSubcategories($subcategories);
            $parameters['subcategory'] = implode(',', $subcategories);
        }

        TripAdvisorRequestDataValidator::validateLanguage($lang);
        TripAdvisorRequestDataValidator::validateCurrency($currency);

        if (is_array($idData)) {
            $idData = implode(',', $idData);
        }

        return $this->doRequest(sprintf('location/%s/hotels', $idData), $parameters);
    }

    /**
     * Call the API with the unique ID for a destination.
     * The response will provide a maximum list of top 10 hotels along with data for each hotel
     * in the list. Alternatively, call the API with a comma-separated list of hotel location ids,
     * get data for the hotels in your list.
     *
     * https://developer-tripadvisor.com/content-api/documentation/location-restaurants/
     *
     * @param $idData
     * @param array $subcategories
     * @param array $cuisines
     * @param array $prices
     * @param string $lang
     * @param string $currency
     * @return array
     */
    public function getLocationRestaurants(
        $idData,
        array $subcategories = array(),
        array $cuisines = array(),
        array $prices = array(),
        $lang = Language::ENGLISH,
        $currency = Currency::UNITED_STATES_DOLLAR
    ) {
        $this->validateIdData($idData);

        $parameters = array(
            'lang' => $lang,
            'currency' => $currency,
        );

        if ($subcategories) {
            TripAdvisorRequestDataValidator::validateRestaurantSubcategories($subcategories);
            $parameters['subcategory'] = implode(',', $subcategories);
        }

        if ($cuisines) {
            TripAdvisorRequestDataValidator::validateCuisines($cuisines);
            $parameters['cuisines'] = implode(',', $cuisines);
        }

        if ($prices) {
            TripAdvisorRequestDataValidator::validatePrices($prices);
            $parameters['prices'] = implode(',', $prices);
        }

        TripAdvisorRequestDataValidator::validateLanguage($lang);
        TripAdvisorRequestDataValidator::validateCurrency($currency);

        if (is_array($idData)) {
            $idData = implode(',', $idData);
        }

        return $this->doRequest(sprintf('location/%s/hotels', $idData), $parameters);
    }

    /**
     * When specifying a single Lat/Long point, returns a list of 10 properties found within
     * a given distance from that point. If there are more than 10 properties within the radius
     * requested, the 10 nearest properties will be returned. When specifying two Lat/Long points,
     * returns a list of 10 properties found within the bounding box created by the two points.
     *
     * https://developer-tripadvisor.com/content-api/documentation/map/
     *
     * @param $latitude
     * @param $longitude
     * @param int $distance
     * @param string $lunit
     * @param string $lang
     * @param string $currency
     * @return array
     */
    public function getMap(
        $latitude,
        $longitude,
        $distance = 10,
        $lunit = LengthUnit::MILE,
        $lang = Language::ENGLISH,
        $currency = Currency::UNITED_STATES_DOLLAR
    ) {
        TripAdvisorRequestDataValidator::validateFloat($latitude);
        TripAdvisorRequestDataValidator::validateFloat($longitude);
        TripAdvisorRequestDataValidator::validateDistanceLengthUnit($distance, $lunit);
        TripAdvisorRequestDataValidator::validateLanguage($lang);
        TripAdvisorRequestDataValidator::validateCurrency($currency);

        $parameters = array(
            'distance' => $distance,
            'lunit' => $lunit,
            'lang' => $lang,
            'currency' => $currency,
        );

        return $this->doRequest(sprintf('map/%s,%s', $latitude, $longitude), $parameters);
    }

    /**
     * Same as /map call, restricting results to a maximum of 10 attractions.
     *
     * https://developer-tripadvisor.com/content-api/documentation/map-attractions/
     *
     * @param $latitude
     * @param $longitude
     * @param array $subcategories
     * @param int $distance
     * @param string $lunit
     * @param string $lang
     * @param string $currency
     * @return array
     */
    public function getMapAttractions(
        $latitude,
        $longitude,
        array $subcategories = array(),
        $distance = 10,
        $lunit = LengthUnit::MILE,
        $lang = Language::ENGLISH,
        $currency = Currency::UNITED_STATES_DOLLAR
    ) {
        TripAdvisorRequestDataValidator::validateFloat($latitude);
        TripAdvisorRequestDataValidator::validateFloat($longitude);
        TripAdvisorRequestDataValidator::validateDistanceLengthUnit($distance, $lunit);
        TripAdvisorRequestDataValidator::validateLanguage($lang);
        TripAdvisorRequestDataValidator::validateCurrency($currency);

        $parameters = array(
            'distance' => $distance,
            'lunit' => $lunit,
            'lang' => $lang,
            'currency' => $currency,
        );

        if ($subcategories) {
            TripAdvisorRequestDataValidator::validateAttractionSubcategories($subcategories);
            $parameters['subcategory'] = implode(',', $subcategories);
        }

        return $this->doRequest(sprintf('map/%s,%s/attractions', $latitude, $longitude), $parameters);
    }

    /**
     * Same as /map call, restricting results to a maximum of 10 hotels/accommodations.
     *
     * https://developer-tripadvisor.com/content-api/documentation/map-hotels/
     *
     * @param $latitude
     * @param $longitude
     * @param array $subcategories
     * @param int $distance
     * @param string $lunit
     * @param string $lang
     * @param string $currency
     * @return array
     */
    public function getMapHotels(
        $latitude,
        $longitude,
        array $subcategories = array(),
        $distance = 10,
        $lunit = LengthUnit::MILE,
        $lang = Language::ENGLISH,
        $currency = Currency::UNITED_STATES_DOLLAR
    ) {
        TripAdvisorRequestDataValidator::validateFloat($latitude);
        TripAdvisorRequestDataValidator::validateFloat($longitude);
        TripAdvisorRequestDataValidator::validateDistanceLengthUnit($distance, $lunit);
        TripAdvisorRequestDataValidator::validateLanguage($lang);
        TripAdvisorRequestDataValidator::validateCurrency($currency);

        $parameters = array(
            'distance' => $distance,
            'lunit' => $lunit,
            'lang' => $lang,
            'currency' => $currency,
        );

        if ($subcategories) {
            TripAdvisorRequestDataValidator::validateHotelSubcategories($subcategories);
            $parameters['subcategory'] = implode(',', $subcategories);
        }

        return $this->doRequest(sprintf('map/%s,%s/hotels', $latitude, $longitude), $parameters);
    }

    /**
     * Same as /map call, restricting results to a maximum of 10 restaurants.
     *
     * https://developer-tripadvisor.com/content-api/documentation/map-restaurants/
     *
     * @param $latitude
     * @param $longitude
     * @param array $subcategories
     * @param array $cuisines
     * @param array $prices
     * @param int $distance
     * @param string $lunit
     * @param string $lang
     * @param string $currency
     * @return array
     */
    public function getMapRestaurants(
        $latitude,
        $longitude,
        array $subcategories = array(),
        array $cuisines = array(),
        array $prices = array(),
        $distance = 10,
        $lunit = LengthUnit::MILE,
        $lang = Language::ENGLISH,
        $currency = Currency::UNITED_STATES_DOLLAR
    ) {
        TripAdvisorRequestDataValidator::validateFloat($latitude);
        TripAdvisorRequestDataValidator::validateFloat($longitude);
        TripAdvisorRequestDataValidator::validateDistanceLengthUnit($distance, $lunit);
        TripAdvisorRequestDataValidator::validateLanguage($lang);
        TripAdvisorRequestDataValidator::validateCurrency($currency);

        $parameters = array(
            'distance' => $distance,
            'lunit' => $lunit,
            'lang' => $lang,
            'currency' => $currency,
        );

        if ($subcategories) {
            TripAdvisorRequestDataValidator::validateHotelSubcategories($subcategories);
            $parameters['subcategory'] = implode(',', $subcategories);
        }

        if ($cuisines) {
            TripAdvisorRequestDataValidator::validateCuisines($cuisines);
            $parameters['cuisines'] = implode(',', $cuisines);
        }

        if ($prices) {
            TripAdvisorRequestDataValidator::validatePrices($prices);
            $parameters['prices'] = implode(',', $prices);
        }

        return $this->doRequest(sprintf('map/%s,%s/restaurants', $latitude, $longitude), $parameters);
    }
}
