<script>
import { isEmpty } from 'lodash-es'
import {
  renderInput,
  renderInputLabel,
  renderErrorMessage,
  renderHintMessage
} from './renders'

export default {
  inheritAttrs: false,
  props: {
    id: {
      type: [String, Number],
      required: true
    },
    label: {
      type: [String, Number],
      default: undefined
    },
    mask: {
      type: Boolean,
      default: false
    },
    value: {
      type: [Number, String],
      default: ''
    },
    type: {
      type: String,
      default: 'text'
    },
    disabled: {
      type: Boolean,
      default: false
    },
    disabledMessage: {
      type: String,
      default: ''
    },
    error: {
      type: [Boolean, String],
      default: false
    },
    optional: {
      type: Boolean,
      default: false
    },
    hint: {
      type: String,
      default: null
    }
  },
  data () {
    return {
      inputType: this.type,
      tippyConfig: {
        placement: 'bottom',
        duration: '150',
        arrow: true
      }
    }
  },
  computed: {
    isDisabled () {
      return this.disabled || !isEmpty(this.disabledMessage)
    },
    isTypePassword () {
      return this.inputType === 'password'
    },
    inputLabel () {
      return this.$slots.default || this.label
    },
    shouldRenderDisabledMessage () {
      return !isEmpty(this.disabledMessage)
    },
    hasError () {
      return !!this.error
    },
    hintId () {
      return !isEmpty(this.hint)
        ? this.hint + 'Hint'
        : null
    }
  },
  methods: {
    focusInput () {
      const input = this.$refs.input.$el,
        length = input.value.length

      input.focus()

      // Fix cursor not be placed at end of input on focus
      setTimeout(() => { input.setSelectionRange(length, length) }, 0)
    },
    togglePassord () {
      this.isTypePassword
        ? this.inputType = 'text'
        : this.inputType = 'password'

      this.focusInput()
    }
  },
  render: function (h) {
    return (
      <div class="mb-3">
        { renderInputLabel(h, this) }
        { renderInput(h, this) }
        { renderErrorMessage(h, this) }
        { renderHintMessage(h, this) }
      </div>
    )
  }
}
</script>
