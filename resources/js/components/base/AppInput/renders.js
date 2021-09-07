import { isEmpty, isBoolean } from 'lodash-es'
import MaskedInputElement from './MaskedInputElement'
import MaskedInputPassword from './MaskedInputPassword'

export const renderInputLabel = function (h, context) {
  function renderOptional (h, context) {
    if (context.optional) {
      return (
        <span class="small text-secondary"> (opcional)</span>
      )
    }
  }

  if (context.inputLabel) {
    return (
      <label for={context.id}
        class="fw-bold form-label"
      >
        {context.inputLabel}
        { renderOptional(h, context) }
      </label>
    )
  }
}

export const renderDisabledMessage = function (h, context, element) {
  return (
    <div
      content={context.disabledMessage}
      v-tippy={context.tippyConfig}
    >
      { element }
    </div>
  )
}

export const renderInput = function (h, context) {
  let element = null

  if (context.type === 'password') {
    element = MaskedInputPassword(h, context)
  } else {
    element = MaskedInputElement(h, context)
  }

  return context.shouldRenderDisabledMessage
    ? renderDisabledMessage(h, context, element)
    : element
}

export const renderErrorMessage = function (h, context) {
  if (context.hasError && !isBoolean(context.error)) {
    return (
      <div class="small text-danger">
        { context.error }
      </div>
    )
  }
}

export const renderHintMessage = function (h, context) {
  if (!isEmpty(context.hint)) {
    return (
      <small
        class="form-text"
        id={context.hintId}
      >
        { context.hint }
      </small>
    )
  }
}
