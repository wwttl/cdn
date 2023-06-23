var qukuai = new Vue({
	el:'.sort-mine-wrap',
	data:{
	
	},
	computed:{
        userData(){
            return this.$store.state.userData;
        }
    }
})