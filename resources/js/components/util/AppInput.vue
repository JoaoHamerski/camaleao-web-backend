<template>
  <div>
    <label
      v-if="$slots.default"
      :for="id"
      class="font-weight-bold"
    >
      <slot /> <span
        v-if="optional"
        class="text-secondary small"
      >(opcional)</span>
    </label>

    <tippy
      v-if="disabledMessage.length"
      :duration="150"
      placement="bottom"
      arrow
      :to="'tippy-' + id"
    >
      {{ disabledMessage }}
    </tippy>

    <div
      :class="[{ 'input-group' : type === 'password' }, {'custom-file': type === 'file'}]"
      :name="'tippy-' + id"
    >
      <MaskedInput
        :id="id"
        ref="input"
        class="form-control"
        :disabled="disabled"
        :class="[{'is-invalid' : hasError}, inputClass, {'custom-file-input' : type === 'file'}]"
        :type="inputType"
        :name="name"
        :placeholder="placeholder"
        :value="value"
        :autofocus="autofocus"
        :mask="mask"
        :autocomplete="autocomplete"
        :multiple="type === 'file' && multiple"
        :accept="type === 'file' && accept !== '' ? accept : false"
        :guide="false"
        @input="$emit('input', $event)"
        @change="$emit('change', $event)"
      />

      <label
        v-if="type === 'file'"
        class="custom-file-label"
      >
        Escolher arquivos
      </label>

      <div
        v-if="type === 'password'"
        class="input-group-append"
      >
        <button
          v-if="isTypePassword"
          tabindex="-1"
          class="btn btn-outline-primary"
          @click.prevent="togglePasswordType"
        >
          <i class="fas fa-eye-slash fa-fw" />
        </button>
        <button
          v-else
          tabindex="-1"
          class="btn btn-outline-primary"
          @click.prevent="togglePasswordType"
        >
          <i class="fas fa-eye fa-fw" />
        </button>
      </div>
    </div>

    <div
      v-if="hasError"
      class="small text-danger justify"
    >
      {{ error }}
    </div>
  </div>
</template>

<script>
import MaskedInput from 'vue-text-mask'
import { TippyComponent } from 'vue-tippy'

export default {
  components: {
    MaskedInput,
    tippy: TippyComponent
  },
  props: {
    id: {
      type: [String, Number],
      required: true
    },
    optional: {
      type: Boolean,
      default: false
    },
    disabled: {
      type: Boolean,
      default: false
    },
    value: {
      type: String,
      default: ''
    },
    inputClass: {
      type: String,
      default: ''
    },
    multiple: {
      type: Boolean,
      default: false
    },
    accept: {
      type: String,
      default: ''
    },
    name: {
      type: null,
      default: false
    },
    mask: {
      type: undefined,
      default: false
    },
    placeholder: {
      type: undefined,
      default: false
    },
    type: {
      type: String,
      default: 'text'
    },
    autofocus: {
      type: Boolean,
      default: false
    },
    error: {
      type: String,
      default: ''
    },
    autocomplete: {
      type: String,
      default: ''
    },
    disabledMessage: {
      type: String,
      default: ''
    }
  },
  data: function() {
    return {
      inputType: this.type
    }
  },
  computed: {
    isTypePassword() {
      return this.inputType === 'password'
    },
    hasError () {
      return ! this.$helpers.isEmpty(this.error)
    }
  },
  watch: {
    // Atualiza a máscara do input após ele receber
    // alguma mudança programaticamente.
    // A biblioteca não atualiza a mascara sozinha.
    value(val, oldVal) {
      if (val === null || oldVal === null) {
        return
      }

      if (oldVal.length === 0 && val.length > 0) {
        setTimeout(() => {
          this.$refs.input.updateValue()
        }, 100)
      }
    }
  },
  methods: {
    togglePasswordType() {
      this.inputType = this.inputType === 'password'
        ? 'text'
        : 'password'

      this.focusInput()
    },
    focusInput() {
      const input = this.$refs.input.$el,
        length = input.value.length

      input.focus()

      setTimeout(() => { input.setSelectionRange(length, length) }, 0)
    }
  }
}
</script>
