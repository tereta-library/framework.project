<?php declare(strict_types=1);

namespace Builder\Theme\Api;

use Framework\Api\Interface\Api;

/**
 * @class Builder\Theme\Api\Theme
 */
class Theme implements Api
{
    /**
     * @param string $number
     * @return array
     * @api GET /^site\/configuration\/theme\/(-?[0-9]+)$/Usi
     */
    public function getThemeByNumber(string $number): array
    {
        if ($number < 0) {
            $number = 9;
        }

        return [
            'number' => $number,
            'name' => 'Theme ' . $number,
            'description' => 'Description of theme ' . $number,
            'price' => 0.00,
            'currency' => 'USD',
            'imageDesktop' => 'https://via.placeholder.com/150',
            'imageMobile' => 'https://via.placeholder.com/150',
            'preview' => 'https://via.placeholder.com/600',
        ];
    }
}