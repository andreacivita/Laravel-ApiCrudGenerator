<?php

namespace AndreaCivita\ApiCrudGenerator\Interfaces;

interface Generator
{
    /**
     * Generate file from data
     * @return mixed
     */
    public function generate();
}
