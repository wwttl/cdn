var b2del = new Vue({
  el: '#b2del',
  data:{
	'lock': false,
  },
  methods:{
    del(){
		if(this.lock==false){
			this.$message.error('请先确定');
			return;
		}
		let postdata = {
			content:'',
		}
		this.$https.post(b2_global.home_url+'/wp-json/b2delc/v1/delc',Qs.stringify(postdata)).then(res=>{
		if(res.status == 200){
            this.$message({
                message: '账号注销成功',
                type: 'success'
            });
            userTools.loginOut()
		}
		}).catch(err=>{
			this.$message.error(err.response.data.message);
		})
    }
  }
})