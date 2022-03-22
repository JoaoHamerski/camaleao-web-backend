<table class="table table-striped">
  <thead>
    <tr>
      <td colspan="3" class="bg-primary text-white fw-bold text-center py-2">
        GASTOS COM CADA TIPO
      </td>
    </tr>
    <tr class="bg-primary text-white fw-bold">
      <td>TIPO</td>
      <td>QUANTIDADE</td>
      <td>TOTAL</td>
    </tr>
  </thead>
  <tbody>
    @foreach ($expensesByType as $expense)
    <tr>
      <td>{{ $expense->type->name }}</td>
      <td>{{ $expense->quantity }}</td>
      <td>{{ Mask::currencyBRL($expense->total) }}</td>
    </tr>
    @endforeach
    <tr class="fw-bold">
      <td class="bg-secondary-dark">TOTAL</td>
      <td class="bg-secondary-dark">{{ $expensesByType->sum('quantity') }}</td>
      <td class="bg-secondary-dark">{{ Mask::currencyBRL($expensesByType->sum('total')) }}</td>
    </tr>
  </tbody>
</table>
