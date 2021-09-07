import classNames from 'classnames'
import MaskedInput from 'vue-text-mask'

function MaskedInputElement (h, context) {
  return (
    <MaskedInput
      ref="input"
      class={classNames(['form-control', {
        'is-invalid': context.hasError
      }])}
      id={context.id}
      type={context.inputType}
      mask={context.mask}
      value={context.value}
      disabled={context.isDisabled}
      onInput={e => {context.$emit('input', e)}}
      aria-describedby={context.hintId}
      {...{attrs: context.$attrs}}
    />
  )
}

export default MaskedInputElement
