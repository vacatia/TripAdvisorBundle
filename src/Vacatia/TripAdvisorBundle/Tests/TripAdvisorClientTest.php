<?php

namespace Vacatia\TripAdvisorBundle\Tests;

use Vacatia\TripAdvisorBundle\Services\TripAdvisorClient;

class TripAdvisorClientTest extends KernelAwareTest
{
    /** @var TripAdvisorClient */
    protected $client;

    public function setUp()
    {
        parent::setUp();
        $this->client = $this->container->get('vacatia_trip_advisor.services.client');
    }

    public function testLocation()
    {
        $location = $this->client->getLocation(89575);
        $this->assertTrue(is_array($location));
        $this->assertArrayHasKey('see_all_photos', $location);
        $this->assertArrayHasKey('reviews', $location);
        $this->assertArrayHasKey('address_obj', $location);
    }

    public function testLocationAttractions()
    {
        $locations = $this->client->getLocationAttractions(60745);
        $this->assertTrue(is_array($locations));
        $this->assertArrayHasKey('data', $locations);
        $this->assertGreaterThan(0, count($locations['data']));
        $this->assertArrayHasKey('address_obj', $locations['data'][0]);
        $this->assertArrayHasKey('attraction_types', $locations['data'][0]);
        $this->assertArrayHasKey('web_url', $locations['data'][0]);
    }

    public function testLocationHotels()
    {
        $locations = $this->client->getLocationHotels(60745);
        $this->assertTrue(is_array($locations));
        $this->assertArrayHasKey('data', $locations);
        $this->assertGreaterThan(0, count($locations['data']));
        $this->assertArrayHasKey('address_obj', $locations['data'][0]);
        $this->assertArrayHasKey('category', $locations['data'][0]);
        $this->assertArrayHasKey('name', $locations['data'][0]);
    }

    public function testLocationRestaurants()
    {
        $locations = $this->client->getLocationRestaurants(
            60745,
            array(),
            array(
                \Vacatia\TripAdvisorBundle\Helper\Cuisine::GLOBAL_INTERNATIONAL
            )
        );
        $this->assertTrue(is_array($locations));
        $this->assertArrayHasKey('data', $locations);
        $this->assertGreaterThan(0, count($locations['data']));
        $this->assertArrayHasKey('address_obj', $locations['data'][0]);
        $this->assertArrayHasKey('category', $locations['data'][0]);
        $this->assertArrayHasKey('name', $locations['data'][0]);
    }

    public function testMap()
    {
        $locations = $this->client->getMap(
            42.33141,
            -71.099396
        );
        $this->assertTrue(is_array($locations));
        $this->assertArrayHasKey('data', $locations);
        $this->assertGreaterThan(0, count($locations['data']));
        $this->assertArrayHasKey('address_obj', $locations['data'][0]);
        $this->assertArrayHasKey('category', $locations['data'][0]);
        $this->assertArrayHasKey('name', $locations['data'][0]);
    }

    public function testMapAttractions()
    {
        $locations = $this->client->getMapAttractions(
            42.33141,
            -71.099396
        );
        $this->assertTrue(is_array($locations));
        $this->assertArrayHasKey('data', $locations);
        $this->assertGreaterThan(0, count($locations['data']));
        $this->assertArrayHasKey('address_obj', $locations['data'][0]);
        $this->assertArrayHasKey('category', $locations['data'][0]);
        $this->assertArrayHasKey('name', $locations['data'][0]);
    }

    public function testMapHotels()
    {
        $locations = $this->client->getMapHotels(
            42.33141,
            -71.099396
        );
        $this->assertTrue(is_array($locations));
        $this->assertArrayHasKey('data', $locations);
        $this->assertGreaterThan(0, count($locations['data']));
        $this->assertArrayHasKey('address_obj', $locations['data'][0]);
        $this->assertArrayHasKey('category', $locations['data'][0]);
        $this->assertArrayHasKey('name', $locations['data'][0]);
    }

    public function testMapRestaurants()
    {
        $locations = $this->client->getMapHotels(
            42.33141,
            -71.099396
        );
        $this->assertTrue(is_array($locations));
        $this->assertArrayHasKey('data', $locations);
        $this->assertGreaterThan(0, count($locations['data']));
        $this->assertArrayHasKey('address_obj', $locations['data'][0]);
        $this->assertArrayHasKey('category', $locations['data'][0]);
        $this->assertArrayHasKey('name', $locations['data'][0]);
    }
}
