<?php

namespace Vacatia\TripAdvisorBundle\Validator;

use Vacatia\TripAdvisorBundle\Helper\LengthUnit;

abstract class TripAdvisorRequestDataValidator
{
    public static function validate($class, $value)
    {
        $ref = new \ReflectionClass($class);
        foreach ($ref->getConstants() as $name => $const) {
            if ($const === $value) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param $currency
     * @throws \Exception
     */
    public static function validateCurrency($currency)
    {
        $valid = self::validate('Vacatia\\TripAdvisorBundle\\Helper\\Currency', $currency);
        if (!$valid) {
            throw new \Exception('Invalid currency ' . $currency);
        }
    }

    /**
     * @param $lang
     * @throws \Exception
     */
    public static function validateLanguage($lang)
    {
        $valid = self::validate('Vacatia\\TripAdvisorBundle\\Helper\\Language', $lang);
        if (!$valid) {
            throw new \Exception('Invalid language ' . $lang);
        }
    }

    /**
     * @param $subcategory
     * @throws \Exception
     */
    public static function validateAttractionSubcategory($subcategory)
    {
        $valid = self::validate('Vacatia\\TripAdvisorBundle\\Helper\\AttractionSubcategory', $subcategory);
        if (!$valid) {
            throw new \Exception('Invalid subcategory ' . $subcategory);
        }
    }

    /**
     * @param array $subcategories
     * @throws \Exception
     */
    public static function validateAttractionSubcategories(array $subcategories)
    {
        foreach ($subcategories as $subcategory) {
            self::validateAttractionSubcategory($subcategory);
        }
    }

    /**
     * @param $subcategory
     * @throws \Exception
     */
    public static function validateHotelSubcategory($subcategory)
    {
        $valid = self::validate('Vacatia\\TripAdvisorBundle\\Helper\\HotelSubcategory', $subcategory);
        if (!$valid) {
            throw new \Exception('Invalid subcategory ' . $subcategory);
        }
    }

    /**
     * @param array $subcategories
     * @throws \Exception
     */
    public static function validateHotelSubcategories($subcategories)
    {
        foreach ($subcategories as $subcategory) {
            self::validateHotelSubcategory($subcategory);
        }
    }

    /**
     * @param array $subcategories
     * @throws \Exception
     */
    public static function validateRestaurantSubcategories($subcategories)
    {
        foreach ($subcategories as $subcategory) {
            self::validateRestaurantSubcategory($subcategory);
        }
    }

    /**
     * @param $subcategory
     * @throws \Exception
     */
    public static function validateRestaurantSubcategory($subcategory)
    {
        $valid = self::validate('Vacatia\\TripAdvisorBundle\\Helper\\RestaurantSubcategory', $subcategory);
        if (!$valid) {
            throw new \Exception('Invalid subcategory ' . $subcategory);
        }
    }

    /**
     * @param array $cuisines
     * @throws \Exception
     */
    public static function validateCuisines($cuisines)
    {
        foreach ($cuisines as $cuisine) {
            self::validateCuisine($cuisine);
        }
    }

    /**
     * @param $cuisine
     * @throws \Exception
     */
    public static function validateCuisine($cuisine)
    {
        $valid = self::validate('Vacatia\\TripAdvisorBundle\\Helper\\Cuisine', $cuisine);
        if (!$valid) {
            throw new \Exception('Invalid cuisine ' . $cuisine);
        }
    }

    /**
     * @param array $prices
     * @throws \Exception
     */
    public static function validatePrices($prices)
    {
        foreach ($prices as $price) {
            self::validatePrice($price);
        }
    }

    /**
     * @param array $price
     * @throws \Exception
     */
    public static function validatePrice($price)
    {
        $price = (int) $price;
        if (!in_array($price, range(1, 4))) {
            throw new \Exception('Invalid price ' . $price);
        }
    }

    /**
     * @param $lengthUnit
     * @throws \Exception
     */
    public static function validateLengthUnit($lengthUnit)
    {
        $valid = self::validate('Vacatia\\TripAdvisorBundle\\Helper\\LengthUnit', $lengthUnit);
        if (!$valid) {
            throw new \Exception('Invalid length unit ' . $lengthUnit);
        }
    }

    /**
     * @param $distance
     * @param $lengthUnit
     * @throws \Exception
     */
    public static function validateDistanceLengthUnit($distance, $lengthUnit)
    {
        self::validateLengthUnit($lengthUnit);

        $distance = (int) $distance;

        if ($lengthUnit === LengthUnit::MILE) {
            $max = 25;
        } else {
            $max = 50;
        }

        if (!in_array($distance, range(1, $max))) {
            throw new \Exception('Invalid distance ' . $distance);
        }
    }

    /**
     * @param $data
     * @throws \Exception
     */
    public static function validateFloat($data)
    {
        if (filter_var($data, FILTER_VALIDATE_FLOAT | FILTER_VALIDATE_INT, array('flags' => FILTER_NULL_ON_FAILURE)) === null) {
            throw new \Exception('Invalid data ' . $data);
        }
    }
}
