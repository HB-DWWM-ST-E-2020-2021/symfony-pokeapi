<?php

namespace App\Command;

use App\PokeAPI\TypeApi;

class DataTypeCommand extends AbstractDataCommand
{
    protected static $defaultName = 'app:data-type';
    protected static $defaultDescription = 'Load types data from PokeAPI if your DB is empty.';

    public function __construct()
    {
        parent::__construct('type');
    }

    /** @required */
    public function setTypeApi(TypeApi $typeApi): void
    {
        $this->setAbstractApi($typeApi);
    }
}
