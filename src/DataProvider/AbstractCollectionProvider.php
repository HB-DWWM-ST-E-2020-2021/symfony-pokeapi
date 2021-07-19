<?php

/*
 * This file is part of the symfony-pokeapi package.
 *
 * (c) Benjamin Georgeault
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\DataProvider;

use ApiPlatform\Core\Bridge\Doctrine\Orm\CollectionDataProvider;
use App\PokeAPI\AbstractApi;
use Doctrine\ORM\EntityManagerInterface;

/**
 * DataProvider are used to load data from source. By default, API platform use your database as source.
 * In our case, we want to populate database with data from PokeAPI before using DB to generate JSON.
 * @see https://api-platform.com/docs/core/data-providers/
 *
 * @author Benjamin Georgeault
 */
abstract class AbstractCollectionProvider extends CollectionDataProvider
{
    private AbstractApi $abstractApi;
    private EntityManagerInterface $em;

    /** @required */
    public function setEm(EntityManagerInterface $em): void
    {
        $this->em = $em;
    }

    public function getCollection(string $resourceClass, string $operationName = null, array $context = [])
    {
        if (!$this->abstractApi->checkIfCollectionIsInitialized()) {
            $collection = $this->abstractApi->getCollection();

            foreach ($collection as $element) {
                $this->em->persist($element);
            }

            $this->em->flush();
        }

        return parent::getCollection($resourceClass, $operationName, $context);
    }

    protected function setAbstractApi(AbstractApi $abstractApi): void
    {
        $this->abstractApi = $abstractApi;
    }
}
