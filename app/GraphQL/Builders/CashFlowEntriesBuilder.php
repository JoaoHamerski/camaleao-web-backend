<?php

namespace App\GraphQL\Builders;

use App\GraphQL\Traits\PaymentsExpensesQueryTrait;

class CashFlowEntriesBuilder
{
    use PaymentsExpensesQueryTrait;

    /**
     * Faz um join nas tabelas "payments" e "expenses"
     * para exibir o fluxo de caixa.
     *
     * O campo "description" é usado para exibir
     * o código do pedido do pagamento,
     * bem como a descrição da despesa
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function __invoke()
    {
        $payments = $this->paymentsQuery();
        $expenses = $this->expensesQuery();


        return $this->mergePaymentsExpensesQueries($payments, $expenses);
    }
}
