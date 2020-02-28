<?php

namespace App\Request;

interface IRequestFacade
{
    public function generateRequests(RequestConditions $conditions): void;
}
