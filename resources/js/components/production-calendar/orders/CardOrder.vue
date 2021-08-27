<script>
export default {
  props: {
    imagePath: undefined,
    order: {
      type: Object,
      required: true
    },
    viewerConfig: {
      type: Object,
      required: true
    }
  }
}
</script>

<template>
  <div class="card card-order">
    <div class="card-header py-1 font-weight-bold bg-camaleao text-white">
      {{ order.code }}
    </div>

    <div v-viewer="viewerConfig">
      <img
        v-if="imagePath"
        :src="imagePath"
        class="py-3 card-img-top clickable"
      >
      <div
        v-else
        class="text-center text-secondary small font-weight-bold my-4"
      >
        [PEDIDO SEM IMAGEM]
      </div>
    </div>

    <div
      v-if="order.name"
      class="card-body"
    >
      <p
        class="card-text"
      >
        {{ order.name }}
      </p>
    </div>

    <ul class="list-group list-group-sm list-group-flush">
      <li
        v-if="order.client"
        v-tippy="{placement: 'right', arrow: true, duration: 150}"
        class="list-group-item"
        content="Cliente"
      >
        <i class="fas fa-user fa-fw mr-1" /> <a
          class="font-weight-bold"
          :href="order.client.path"
          target="_blank"
        >{{ order.client.name }}</a>
      </li>

      <li
        v-if="order.price"
        v-tippy="{placement: 'right', arrow: true, duration: 150}"
        class="list-group-item"
        content="Valor total"
      >
        <i class="fas fa-dollar-sign fa-fw mr-1" />
        <strong>{{ $helpers.valueToBRL(order.price) }}</strong>
      </li>

      <li
        v-if="order.total_owing"
        v-tippy="{placement: 'right', arrow: true, duration: 150}"
        content="Falta pagar"
        class="list-group-item"
        :class="[order.total_owing > 0 ? 'text-danger' : 'text-success']"
      >
        <i class="fas fa-hand-holding-usd fw-fw mr-1" />
        <strong>{{ $helpers.valueToBRL(order.total_owing) }}</strong>
      </li>
    </ul>

    <div
      v-if="order.isPreRegistered && order.reminder"
      class="mx-2"
    >
      <div class="small font-weight-bold">
        Lembrete:
      </div>
      <div>{{ order.reminder }}</div>
    </div>

    <div class="card-footer text-center py-1 position-relative">
      <a
        target="_blank"
        :href="order.path"
        class="card-link stretched-link small font-weight-bold"
      >VER PEDIDO</a>
    </div>
  </div>
</template>
