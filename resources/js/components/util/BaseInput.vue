<script>
import MaskedInput from 'vue-text-mask'

function MaskedInputElement (h, context) {
  return (
    <MaskedInput
      class="form-control"
      attrs={context.data.attrs}
      mask={context.props.mask}
    />
  )
}

export default {
  functional: true,
  props: {
    mask: {
      type: Boolean,
      default: false
    }
  },
  render: (h, context) => {
    return MaskedInputElement(h, context)
  }
}
</script>
