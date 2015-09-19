<?php

namespace DI\Test\IntegrationTest\Fixtures;

use DI\Annotation\Injectable;

/**
 * Fixture class
 * @Injectable(lazy=true)
 */
class LazyDependency
{
    /**
     * @return boolean
     */
    public function getValue()
    {
        return true;
    }
}
