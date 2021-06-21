<template>
	<div>
		<label v-if="$slots.default" :for="id" class="font-weight-bold">
			<slot></slot>
		</label>

		<div :class="{ 'input-group' : type === 'password' }">
			<MaskedInput ref="input" class="form-control"  
				:id="id"
				:class="[{'is-invalid' : hasError}, inputClass]"
				:type="inputType"
				:name="name"
				@input="$emit('input', $event)" 
				:placeholder="placeholder" 
				:value="value"
				:autofocus="autofocus"
				:mask="mask"
				:autocomplete="autocomplete"
				:guide="false"/>

			<div v-if="type === 'password'" class="input-group-append">
				<button tabindex="-1" @click.prevent="togglePasswordType" v-if="isTypePassword" class="btn btn-outline-primary">
					<i class="fas fa-eye-slash fa-fw"></i>
				</button>
				<button tabindex="-1" @click.prevent="togglePasswordType" v-else class="btn btn-outline-primary">
					<i class="fas fa-eye fa-fw"></i>
				</button>
			</div>
		</div>
		
		<div class="small text-danger justify" v-if="hasError">
			{{ error }}
		</div>
	</div>
</template>

<script>
	import MaskedInput from 'vue-text-mask'

	export default {
		components: {
			MaskedInput
		},
		props: {
			id: { required: true }, 
			value: '',
			inputClass: '',
			name: { default: false },
			mask: { default: false },
			placeholder: { default: false },
			type: { default: 'text' },
			autofocus: { default: false },
			error: { default: '' },
			autocomplete: { default: false }
		},
		data: function() {
			return {
				inputType: this.type
			}
		},
		methods: {
			togglePasswordType() {
				this.inputType = this.inputType === 'password' 
					? 'text' 
					: 'password';

				this.focusInput();
			},
			focusInput() {
				let input = this.$refs.input.$el,
					length = input.value.length;

				input.focus();

				setTimeout(() => { input.setSelectionRange(length, length) }, 0);
			}
		},
		computed: {
			isTypePassword() {
				return this.inputType === 'password';
			},
			hasError () {
				return ! this.$helpers.isEmpty(this.error);
			}
		}
	}
</script>