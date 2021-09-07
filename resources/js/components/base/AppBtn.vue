<script>
import { isEmpty} from 'lodash-es'
import classNames from 'classnames'

function renderIsLoading (h, context) {
  if (context.isLoading) {
    return (
      <span>
        <span
          class="spinner-border spinner-border-sm"
          role="status"
          aria-hidden="true"
        />
        <span class="visually-hidden">Carregando...</span>
      </span>
    )
  }
}

function renderBtn (h, context) {
  return (
    <button
      disabled={context.isDisabled}
      class={context.btnClasses}
    >
      { renderIsLoading(h, context) }
      { context.$slots.default }
    </button>
  )
}

function renderDisabledMessageBtn (h, context) {
  return (
    <span
      tabindex="0"
      class="d-inline-block"
      vTippy={context.tippyConfig}
      content={context.disabledMessage}
    >
      { renderBtn(h, context) }
    </span>
  )
}

export default {
  props: {
    color: {
      type: String,
      default: ''
    },
    outlined: {
      type: Boolean,
      default: false
    },
    isLoading: {
      type: Boolean,
      default: false
    },
    disabled: {
      type: Boolean,
      default: false
    },
    disabledMessage: {
      type: String,
      default: null
    },
    btnClass: {
      type: String,
      default: ''
    }
  },
  computed: {
    btnClasses () {
      return classNames([
        'btn',
        {
          [`btn-${this.color}`]: !this.outlined,
          [`btn-outline-${this.color}`]: this.outlined
        },
        this.btnClass,
      ])
    },
    isDisabled () {
      return this.isLoading || this.disabled
    },
    tippyConfig () {
      return {
        duration: 150,
        arrow: true,
        placement: 'top'
      }
    }
  },
  render: function (h) {
    return !isEmpty(this.disabledMessage)
      ? renderDisabledMessageBtn(h, this)
      : renderBtn(h, this)
  }
}
</script>
