<template>
  <div class="dropdown-menu dropdown-shadowed py-0">
    <h6 class="bg-success text-white font-weight-bold p-2 text-center">
      Detalhes da comissão
      <div class="small">
        ({{ commission.pivot.role_id == 4 ? 'costura' : 'estampa' }})
      </div>
    </h6>
    <div v-if="commission.pivot.role_id == 4">
      <table class="table table-sm table-prevent-style">
        <thead>
          <th nowrap>
            Tipo de peça
          </th>
          <th class="text-center">
            Quantidade
          </th>
          <th wrap>
            Comissão por peça
          </th>
          <th>Total</th>
        </thead>

        <tbody>
          <tr
            v-for="clothing in JSON.parse(commission.seam_commission)"
            :key="clothing.key"
          >
            <td>{{ clothing.name }}</td>
            <td class="text-center">
              {{ clothing.quantity }}
            </td>
            <td>{{ $helpers.valueToBRL(clothing.commission) }}</td>
            <td>{{ $helpers.valueToBRL(clothing.quantity * clothing.commission) }}</td>
          </tr>
          <tr class="font-weight-bold">
            <td colspan="3">
              TOTAL
            </td>
            <td>{{ $helpers.valueToBRL(commission.pivot.commission_value) }}</td>
          </tr>
        </tbody>
      </table>
    </div>

    <div
      v-else-if="commission.pivot.role_id == 5"
      class="p-2"
    >
      <div class="font-weight-bold text-center">
        {{ $helpers.valueToBRL(commission.print_commission) }} x {{ commission.order.quantity }}
      </div>
      <div class="text-center">
        {{ $helpers.valueToBRL(commission.pivot.commission_value) }}
      </div>
    </div>
  </div>
</template>

<script>
export default {
    props: {
        commission: {
            type: String,
            default: ''
        },
    },
    computed: {

    }
}
</script>
