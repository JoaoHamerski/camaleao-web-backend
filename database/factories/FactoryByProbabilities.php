<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

abstract class FactoryByProbabilities extends Factory
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
    protected $methodsByProbability = [];

    public function executeMethodsByProbability($model): void
    {
        foreach ($this->methodsByProbability as $method) {
            if ($this->faker->boolean($method['chance'])) {
                $this->{$method[0]}($model);
            }
        }
    }
}
