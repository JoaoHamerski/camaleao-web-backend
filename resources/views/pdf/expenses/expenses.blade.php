<table class="table table-striped mt-3">
  <thead>
    <tr>
      <td colspan="5" class="bg-primary fw-bold text-white text-center py-3">DESPESAS</td>
    </tr>
    <tr class="bg-primary fw-bold text-white">
      <td>DESCRIÇÃO</td>
      <td>TIPO</td>
      <td>VIA</td>
      <td>VALOR</td>
      <td>DATA</td>
    </tr>
  </thead>

  <tbody>
    @foreach ($expenses as $expense)
    <tr>
      <td>{{ $expense->description }}</td>
      <td>{{ $expense->type->name }}</td>
      <td>{{ $expense->via->name }}</td>
      <td>{{ Mask::currencyBRL($expense->value) }}</td>
      <td>{{ Mask::date($expense->date) }}</td>
    </tr>
    @endforeach
  </tbody>
</table>
