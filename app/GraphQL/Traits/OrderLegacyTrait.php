<?php

namespace App\GraphQL\Traits;

use App\Models\Order;

trait OrderLegacyTrait
{
    /**
     * Métodos legados que precisam ser mantidos
     * para garantir a retrocompatibilidade com pedidos antigos
     * que possuem features antigas.
     */

    /**
     * Calcula a quantidade total dos tipos de roupas informados.
     *
     * @param array $data
     * @return int|null
     */
    private function ctEvaluateQuantity(array $data, Order $order = null)
    {
        $total = $this->evaluateClothingTypesQuantity($data);

        if ($total === 0 && $order && $order->isPreRegistered()) {
            return null;
        }

        return $total;
    }

    private function evaluateClothingTypesQuantity($data)
    {
        $INITIAL_VALUE = 0;
        $clothingTypes = [];

        if (!isset($data['clothing_types'])) {
            return null;
        }

        $clothingTypes = collect($data['clothing_types']);

        return $clothingTypes->reduce(
            function ($total, $type) {
                $value = $type["value"];
                $quantity = $type["quantity"];

                if (!empty($value)) {
                    return bcadd($total, $quantity);
                }

                return $total;
            },
            $INITIAL_VALUE
        );
    }

    /**
     * Calcula o valor total dos tipos de roupas informados.
     *
     * @param array $data
     * @return float|null
     */
    private function evaluateClothingTypesValue($data)
    {
        $INITIAL_VALUE = 0;
        $clothingTypes = [];

        if (!isset($data['clothing_types'])) {
            return null;
        }

        $clothingTypes = collect($data['clothing_types']);

        return $clothingTypes->reduce(
            function ($total, $type) {
                $value = $type["value"];
                $quantity = $type["quantity"];

                if (!empty($quantity)) {
                    $typeTotal = bcmul($quantity, $value, 2);

                    return bcadd($total, $typeTotal, 2);
                }

                return $total;
            },
            $INITIAL_VALUE
        );
    }

    private function ctEvaluateOrderAttributes($data, Order $order = null)
    {
        $price = $this->ctEvaluatePrice($data, $order);
        $quantity = $this->ctEvaluateQuantity($data, $order);

        if ($price) {
            $data['price'] = $price;
        }

        if ($quantity) {
            $data['quantity'] = $quantity;
        }

        return $data;
    }

    /**
     * Calcula o valor total do produto cadastrado.
     *
     * @param float $clothingTypesValue
     * @param array $data
     * @param \App\Models\Order|null $order
     * @return float|null
     */
    private function ctEvaluateTotalPrice($clothingTypesValue, $data, $order)
    {
        if (
            $clothingTypesValue <= 0
            && isset($data['discount'])
            && $order
        ) {
            return bcsub(
                $order->original_price,
                $data['discount'],
                2
            );
        }

        if ($order && floatval($clothingTypesValue) === 0.0) {
            return null;
        }

        $price = bcsub($clothingTypesValue, $data['discount'] ?? 0, 2);

        return bcadd($price, $data['shipping_value'] ?? 0, 2);
    }

    /**
     * Calcula o preço final do produto cadastrado.
     *
     * @param array $data,
     * @param App\Models\Order|null $order
     * @return float|null
     */
    private function ctEvaluatePrice(array $data, Order $order = null)
    {
        $clothingTypesValue = $this->evaluateClothingTypesValue($data);

        $total = $this->ctEvaluateTotalPrice($clothingTypesValue, $data, $order);

        if (floatval($total) === 0.0 && $order && $order->isPreRegistered()) {
            return null;
        }

        if (floatval($total) === 0.0 && $order) {
            return $order->original_price;
        }

        if (floatval($total) === 0.0) {
            return null;
        }

        return $total;
    }

    private function getFilledClothingTypes(array $data)
    {
        $filled = [];

        $uploadedClothingTypes = $data['clothing_types'];
        $clothingsSearch = array_column($uploadedClothingTypes, 'key');

        foreach ($this->clothingTypes as $type) {
            $index = array_search($type->key, $clothingsSearch);

            if ($index === false) {
                continue;
            }

            $currentType = $uploadedClothingTypes[$index];

            if (
                !empty($currentType['quantity'])
                && !empty($currentType['value'])
            ) {
                $filled[$type->id] = [
                    'quantity' => $currentType['quantity'],
                    'value' => $currentType['value']
                ];
            }
        }

        return $filled;
    }
}
