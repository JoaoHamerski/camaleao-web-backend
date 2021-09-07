<script>
export default {
  data () {
    return {
      form: {
        email: '',
        password: '',
        remember: false
      }
    }
  }
}
</script>

<template>
  <div class="col col-md-6 col-md-4 mx-auto mt-5">
    <div class="card">
      <div class="card-header bg-primary">
        <h6 class="font-weight-bold text-white mb-0">
          Entre com sua conta
        </h6>
      </div>

      <div class="card-body">
        <div class="text-center">
          <i class="fas fa-user-circle fa-5x text-dark my-2" />
        </div>
        <form>
          <AppInput
            id="email"
            v-model="form.email"
            placeholder="Seu email..."
          >
            E-mail
          </AppInput>

          <AppInput
            id="password"
            v-model="form.password"
            placeholder="Sua senha..."
          >
            Senha
          </AppInput>

          <AppCheckbox
            id="remember"
            v-model="form.remember"
          >
            Lembre-se de mim
          </AppCheckbox>

          <div class="d-grid">
            <AppBtn
              color="primary"
            >
              Entrar
            </AppBtn>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>
