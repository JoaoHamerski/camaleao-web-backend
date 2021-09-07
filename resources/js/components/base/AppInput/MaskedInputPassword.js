import MaskedInputElement from './MaskedInputElement'

function MaskedInputPassword (h, context) {
  function getEyeStateIcon (isTypePassword) {
    if (isTypePassword) {
      return (<i class="fas fa-eye-slash fa-fw"/>)
    }

    return (<i class="fas fa-eye fa-fw"/>)
  }

  return (
    <div class="input-group">
      { MaskedInputElement(h, context) }

      <button
        tabindex="-1"
        class={'btn btn-outline-primary ' + (context.isDisabled && 'cursor-disabled')}
        disabled={context.isDisabled}
        vOn:click_prevent={context.togglePassord}
      >
        { getEyeStateIcon(context.isTypePassword)}
      </button>
    </div>
  )
}

export default MaskedInputPassword
