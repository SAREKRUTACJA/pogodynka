<?php

namespace App\Controller;

use App\Exception\InvalidCityException;
use App\Exception\InvalidDateException;
use App\Service\WeatherService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;

class WeatherController extends AbstractController
{
    public function __construct(
        private readonly WeatherService $weatherService
    )
    {
    }

    /**
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    #[Route('/average_temp', name: 'average_temp', methods: ['POST'])]
    public function getAverageTemperature(Request $request): JsonResponse
    {
        try {
            $data = $request->toArray();

            return $this->json(['average_temp' => $this->weatherService->getAverageTemperature($data['cities'], $data['date'])]);
        } catch (InvalidDateException|InvalidCityException $e) {
            return $this->json(['error' => $e->getMessage()], status: 400);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], status: 500);
        }
    }
}
