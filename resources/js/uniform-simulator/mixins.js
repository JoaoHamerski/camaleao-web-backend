export const shirtDefaultStateAfterClose = {
	mounted() {
		$(this.$parent.$el).on(
	        'hidden.bs.dropdown',
	    	() => { 
	    		this.$store.commit('update', { isFront: this.$store.state.isFrontState }) 
	    	}
	    );
	}
}
