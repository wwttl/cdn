<?php
get_header();
?>
<div class="b2-single-content">
<div id="b2del" class="wrapper single-article b2-radius box" v-cloak>
        <h1>账户注销</h1>

		<div class="content">
			<p>注意，您正在进行账户注销删除操作。</p>
			<p>请仔细阅读以下提示，如因操作不当而引起的其他问题本站概不负责。</p>
			<ul>
				<li>①自愿放弃账户中的资产和虚拟权益（包括但不限于账号会员权益、钱包余额、积分、充值卡、优惠券、文章等）；</li>
				<li>②账户一旦被注销将不可恢复，请您在操作之前自行备份账户相关的所有信息和数据</li>
				<li>③注销账户后，您将无法再使用本账户，也将无法找回您账户中及与账户相关的任何内容或信息</li>
				<li>④注销本账户并不代表您注销前的账户行为和相关责任得到豁免或减轻。</li>
			</ul>
		</div>
		<el-checkbox v-model="lock">我已知悉</el-checkbox>
		<el-button size="small" type="danger" :disabled="!lock" @click="del()">注销并删除</el-button>
</div>
</div>
<style>
    #b2del{
        background: white;
        padding: 18px;
    }
    #b2del h1{
        text-align: center;
        margin: 20px;
        font-size: 1.5rem;
    }
    #b2del .content{
        line-height: 2.5;
        font-size: 17px;
        margin: 20px;
        height: auto;
    }
    #b2del .el-checkbox{
        display: block;
        text-align: center;
        margin: 15px;
    }
    #b2del .el-button{
        display: block;
        margin: 0 auto;
    }
    
</style>
<?php
get_footer();
?>