<?php

namespace App\Contracts;

interface Filterable
{
    public function getSearchableFields(): array;
}