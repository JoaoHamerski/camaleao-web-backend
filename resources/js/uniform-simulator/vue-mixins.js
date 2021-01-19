window.shirtDefaultStateAfterClose = {
	mounted() {
		$(this.$parent.$el).on(
	        'hidden.bs.dropdown',
	    	() => { EventBus.$emit('SHIRT_DEFAULT_STATE') }
	    );
	}
}