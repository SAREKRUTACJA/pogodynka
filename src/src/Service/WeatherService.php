<?php

namespace App\Service;

use App\Exception\InvalidCityException;
use App\Exception\InvalidDateException;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;

readonly class WeatherService
{
    /**
     * @param HttpClientInterface $client
     * @param string $apiKey
     * @param string $apiUrl
     * @param int $precision
     */
    public function __construct(
        private HttpClientInterface $client,
        private string              $apiKey,
        private string              $apiUrl,
        private int                 $precision
    )
    {
    }

    /**
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface|InvalidCityException
     */
    private function fetchTemperatureForCity(string $city, string $date): float
    {
        try {
            $response = $this->client->request('GET', $this->apiUrl, [
                'query' => [
                    'key' => $this->apiKey,
                    'q'   => $city,
                    'dt'  => $date,
                ],
            ]);

            $weatherData = $response->toArray()['forecast']['forecastday'][0]['day']['avgtemp_c'];

            if (!isset($weatherData)) {
                throw new InvalidCityException("No temperature data available for: $city");
            }

            return $weatherData;
        } catch (ClientExceptionInterface|RedirectionExceptionInterface|ServerExceptionInterface|TransportExceptionInterface $e) {
            throw new InvalidCityException("Invalid city or error retrieving data for: $city");
        }
    }

    /**
     * @param array $temperatures
     * @return float
     */
    private function calculateAverage(array $temperatures): float
    {
        return number_format(array_sum($temperatures) / count($temperatures), $this->precision);
    }

    /**
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws InvalidDateException
     * @throws InvalidCityException
     */
    public function getAverageTemperature(array $cities, string $date): float
    {
        $this->validateDate($date);

        $temperatures = array_map(function ($city) use ($date) {
            $this->validateCity($city);
            return $this->fetchTemperatureForCity($city, $date);
        }, $cities);

        return $this->calculateAverage($temperatures);
    }


    /**
     * @throws InvalidDateException
     */
    private function validateDate(?string $date): void
    {
        if (empty($date)) {
            throw new InvalidDateException('Date is required');
        }

        $format = 'Y-m-d';
        $d      = \DateTime::createFromFormat($format, $date);

        if ($d === false || $d->format($format) !== $date) {
            throw new InvalidDateException();
        }
    }

    /**
     * @throws InvalidCityException
     */
    private function validateCity(?string $city): void
    {
        if (empty($city) || ctype_digit($city)) {
            throw new InvalidCityException();
        }

    }


}
