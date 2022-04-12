<?php

namespace Database\Seeders;

use Error;

trait ExecuteByProbabilitiesTrait
{
    /**
     * Mapa de métodos para serem executados
     * de acordo com as probabilidades atribuídas,
     * utilizando o método "executeMethods"
     *
     * @var array
     *  [
     *      [methodName, 'chance' => 0...100]...
     *  ]
     */
    // protected static $methodsByProbability = [];

    public function executeMethodsByProbability($model): void
    {
        if (!isset(static::$methodsByProbability)) {
            throw new Error('A variável $methodsByProbability não foi definida.');
        }

        foreach (static::$methodsByProbability as $method) {
            if ($this->faker->boolean($method['chance'])) {
                $this->{$method[0]}($model);
            }
        }
    }
}
