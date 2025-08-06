<?php

namespace App\Contracts;

interface Loggable
{
    public function getLoggableFields(): array;

    public function getLoggableFieldNames(): array; 
}
