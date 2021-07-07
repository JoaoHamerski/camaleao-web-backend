<template>
	<div>
		<label v-if="$slots.default" :for="id" class="font-weight-bold">
			<slot></slot> <span v-if="optional" class="text-secondary small">(opcional)</span>
		</label>

		<tippy v-if="disabledMessage.length" 
			:duration="150" 
			placement="bottom" 
			arrow
			:to="'tippy-' + id"
		>
			{{ disabledMessage }}
		</tippy>
		
		<div :class="[{ 'input-group' : type === 'password' }, {'custom-file': type === 'file'}]"
			:name="'tippy-' + id"
		>
			<MaskedInput ref="input" class="form-control"  
				:id="id"
				:disabled="disabled"
				:class="[{'is-invalid' : hasError}, inputClass, {'custom-file-input' : type === 'file'}]"
				:type="inputType"
				:name="name"
				@input="$emit('input', $event)" 
				@change="$emit('change', $event)"
				:placeholder="placeholder" 
				:value="value"
				:autofocus="autofocus"
				:mask="mask"
				:autocomplete="autocomplete"
				:multiple="type === 'file' && multiple"
				:accept="type === 'file' && accept !== '' ? accept : false"
				:guide="false"
			/>

			<label class="custom-file-label" v-if="type === 'file'">
				Escolher arquivos
			</label>

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
	import { TippyComponent } from "vue-tippy";
	
	export default {
		components: {
			MaskedInput,
			'tippy': TippyComponent
		},
		props: {
			id: { required: true }, 
			optional: { default: false },
			disabled: { default: false },
			value: '',
			inputClass: '',
			multiple: { default: false },
			accept: { default: '' },
			name: { default: false },
			mask: { default: false },
			placeholder: { default: false },
			type: { default: 'text' },
			autofocus: { default: false },
			error: { default: '' },
			autocomplete: { default: false },
			disabledMessage: { default: ''}
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