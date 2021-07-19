<?php

/*
 * This file is part of the symfony-pokeapi package.
 *
 * (c) Benjamin Georgeault
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Pokedex;

use App\Entity\Type;
use App\Repository\TypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Class TypeApi
 *
 * @author Benjamin Georgeault
 */
class TypeApi
{
    /**
     * Attribute to save the built HttpClient to use it in all methods.
     */
    private HttpClientInterface $client;

    private TypeRepository $typeRepository;

    private EntityManagerInterface $em;

    public function __construct(TypeRepository $typeRepository, EntityManagerInterface $em)
    {
        // Build the HttpClient with base URL from https://pokeapi.co/.
        $this->client = HttpClient::createForBaseUri('https://pokeapi.co/api/v2/');
        $this->typeRepository = $typeRepository;
        $this->em = $em;
    }

    public function getTypes(int $offset = 0, int $limit = 50): array
    {
        // Get types from https://pokeapi.co/ by offset.
        $response = $this->client->request('GET', 'type', [
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

        // Init types array.
        $types = [];

        // Parse all types return by https://pokeapi.co/ for the current HTTP request.
        foreach ($data['results'] as $type) {
            // Try to match type's id from the URL given. If no match, throw exception.
            if (!preg_match('/([0-9]+)\/?$/', $type['url'], $matches)) {
                throw new \RuntimeException('Cannot match given url for type ' . $type['name']);
            }

            // Get id from matches. index 0 get the full match, next indexes (1, 2, etc…) get data surround by ()
            // in the regex.
            $id = $matches[1]; //  => 25

            // Add type data to the types array.
            $type = [
                'id' => $id,
                'name' => $type['name'],
            ];

            $types[] = $this->convertPokeApiToType($type);
        }

        // Check if a next page exist.
        if ($data['next']) {
            // Try to retrieve the offset value from next URL. If no match, throw exception.
            if (!preg_match('/\?.*offset=([0-9]+)/', $data['next'], $matches)) {
                throw new \RuntimeException('Cannot match offset on next page.');
            }

            // Get next offset.
            $nextOffset = $matches[1];

            // Recurive call to getAlltype with the new next offset.
            $nexttypes = $this->getTypes($nextOffset, $limit);

            // Merge current types with the next types.
            $types = array_merge($types, $nexttypes);
        }

        return $types;
    }

    public function convertPokeApiToType(array $data): Type
    {
        $type = $this->typeRepository->findOneBy([
            'pokeapiId' => $data['id'],
        ]);

        if (null === $type) {
            $type = new Type();
            $type->setName($data['name']);
            $type->setPokeapiId($data['id']);

            $this->em->persist($type);
            $this->em->flush();
        }

        return $type;
    }
}
