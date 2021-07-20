<?php

/*
 * This file is part of the symfony-pokeapi package.
 *
 * (c) Benjamin Georgeault
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\PokeAPI;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpClient\ScopingHttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * All derived classes are use to ONLY get data from PokeAPI and convert them to an entity.
 * If on of PokeAPI data already exist in DB, it will not create a new entity but use the one in DB.
 *
 * @author Benjamin Georgeault
 */
abstract class AbstractApi
{
    /** Store the path to the real element that will be query from PokeAPI (eg. pokemon, type, move, etc...) */
    private string $elementPath;
    private HttpClientInterface $client;

    public function __construct(string $elementPath)
    {
        $this->elementPath = $elementPath;
        // Build the HttpClient with base URL from https://pokeapi.co/.
        $this->client = HttpClient::createForBaseUri('https://pokeapi.co/api/v2/');
    }

    /** @required */
    public function setClient(HttpClientInterface $client): self
    {
        $this->client = ScopingHttpClient::forBaseUri($client, 'https://pokeapi.co/api/v2/');
        return $this;
    }

    public function getCollection(int $offset = 0, int $limit = 50): array
    {
        // Get elements from https://pokeapi.co/ by offset.
        $response = $this->client->request('GET', $this->elementPath, [
            'query' => [
                'offset' => $offset,
                'limit' => $limit,
            ],
        ]);

        // If the response does not have 200 for status code, throw exception.
        if (200 !== $response->getStatusCode()) {
            throw new \RuntimeException('Error from Pokeapi.co');
        }

        // Return data from response as PHP Array. The method read JSON data and convert it to PHP array.
        $data = $response->toArray();

        // Init elements array.
        $elements = [];

        // Parse all elements return by https://pokeapi.co/ for the current HTTP request.
        foreach ($data['results'] as $element) {
            // Send the HTTP request to https://pokeapi.co/ for ONE element.
            $elementResponse = $this->client->request('GET', $element['url']);

            // If the response does not have 200 for status code, throw exception.
            if (200 !== $elementResponse->getStatusCode()) {
                throw new \RuntimeException('Error from Pokeapi.co on ' . $element['url']);
            }

            $elements[] = $elementResponse->toArray();
        }

        // Check if a next page exist.
        if ($data['next']) {
            // Try to retrieve the offset value from next URL. If no match, throw exception.
            if (!preg_match('/\?.*offset=([0-9]+)/', $data['next'], $matches)) {
                throw new \RuntimeException('Cannot match offset on next page.');
            }

            // Get next offset.
            $nextOffset = $matches[1];

            // Recursive call to getCollection with the new next offset.
            $nextElements = $this->getCollection($nextOffset, $limit);

            // Merge current elements with the next elements.
            $elements = array_merge($elements, $nextElements);
        }

        return $elements;
    }

    /**
     * Implement this method to create your entity.
     */
    abstract public function convertPokeApiToElement(array $data): object;

    /**
     * Check if you already have some data in your database for the given element.
     */
    abstract public function checkIfCollectionIsInitialized(): bool;
}
