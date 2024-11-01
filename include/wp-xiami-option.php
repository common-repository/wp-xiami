<?php 
	global $plugin_page;

	$alert = array();

	if( $_SERVER['REQUEST_METHOD'] == 'POST' ){

		if ( !wp_verify_nonce($_POST[$plugin_page], 'wp-xiami-setting') ){
			ob_clean();
			wp_die('非法操作');
		}
		
		$user_id = $_POST['user_id'];
		
		if( !is_numeric($user_id) ){
			$alert = array(
				'type' => 'error',
				'msg' => 'wrong user id'
			);
		}else{
			$alert = array(
				'type' => 'updated',
				'msg' => 'sueecss'
			);

			$args = array('user_id', 'user_type', 'user_page', 'user_width', 'user_fullwidth', 'user_authorshow');
			$array = array();

			foreach ($args as $key) {
				$array[$key] = $_POST[$key];
			}

			$this->update_settings($array);	
		}
	}

    $page_link = $this->get_page_link();
?>
<div class="wrap">
	<h2>基础设置</h2>
	<?php if(!empty($alert)){?>
			<div class="<?php echo $alert['type'];?>"><p><?php echo $alert['msg'];?></p></div>
		<?php }
	?>
	<form method="post" action="<?php echo admin_url('admin.php?page='.$plugin_page); ?>">
		<table class="form-table">
			<tbody>
				<tr valign="top">
					<th scope="row"><label for="user_id">虾米用户ID</label></th>
					<td>
						<input name="user_id" type="text" id="user_id" value="<?php echo $this->get_settings("user_id");?>" class="regular-text">
                        <p class="description">（只需填写数字部分）虾米用户ID：http://www.xiami.com/u/<code>33663442</code>。</p>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="user_type">同步选择</label></th>
					<td>
						<select name="user_type" id="user_type">
						    <option value="all" <?php selected( $this->get_settings("user_type"), 'all' ); ?>>全部</option>
						    <option value="collects" <?php selected( $this->get_settings("user_type"), 'collects' ); ?>>精选集</option>
						    <option value="albums" <?php selected( $this->get_settings("user_type"), 'albums' ); ?>>专辑</option>
						</select>
						<p class="description">默认选择“全部”，同步精选集和专辑。</p>
					</td>
				</tr>
                <tr valign="top">
                    <th scope="row"><label for="user_page">选择页面</label></th>
                    <td>
                        <?php wp_dropdown_pages(array(
                            'selected'         => $this->get_settings("user_page"),
                            'echo'             => 1,
                            'name'             => 'user_page')); ?>
                        <p class="description">当前页面：<a href="<?php echo $page_link;?>" target="_blank"><?php echo $page_link;?></a></p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><label for="user_width">页面宽度</label></th>
                    <td>
                        <input name="user_width" type="number" min="600" step="1" id="user_width" value="<?php echo $this->get_settings("user_width");?>" class="small-text"> px
                        <p class="description">如果与主题宽度不一致可以试着调节此项。默认使用主题上层结构宽度。</p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><label for="user_fullwidth">专辑预览宽度</label></th>
                    <td>
                    	<label for="user_fullwidth_0">
                    		<input type="radio" name="user_fullwidth" id="user_fullwidth_0" value="0" <?php if($this->get_settings("user_fullwidth")!=1) echo 'checked="checked"';?>>
                    		页面宽度
                    	</label>
                    	<br />
                        <label for="user_fullwidth_1">
                    		<input type="radio" name="user_fullwidth" id="user_fullwidth_1" value="1" <?php if($this->get_settings("user_fullwidth")==1) echo 'checked="checked"';?>>
                    		浏览器宽度
                    	</label>
                        <p class="description">如果与主题宽度不一致可以试着调节此项。默认选择页面宽度。</p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><label for="user_authorshow">附加选项</label></th>
                    <td>
                    	<label for="user_authorshow">
                    		<input type="checkbox" name="user_authorshow" id="user_authorshow" value="1" <?php if($this->get_settings("user_authorshow")==1) echo 'checked="checked"';?>>不显示作者名称
                    	</label>
                        <p class="description">勾选之后会隐藏 <code>精选集/专辑</code> 作者名称。默认选择显示。</p>
                    </td>
                </tr>
			</tbody>
		</table>
		<div class="submit_form">
			<input type="submit" class="button-primary" name="save" value="<?php _e('Save Changes') ?>"/>
		</div>
		<?php wp_nonce_field('wp-xiami-setting', $plugin_page); ?>
	</form>
	<br class="clear">
	<br class="clear">
	<h3>清除缓存</h3>
	<div>
		<p>本插件不管理音乐, 只做同步, 如果有曲目修改, 请前往虾米修改, 由于有6小时缓存, 修改过后的专辑等不会立即改变.</p>
		<p>如果专辑或精选集未能及时更新, 可以点击下面的按钮, 手动清空服务器缓存.</p>
		<button id="clear-button" class="button">清空服务器缓存</button>
		<script>jQuery(document).ready(function(){var a=!1;jQuery("#clear-button").click(function(){var b=jQuery(this);if(!b.hasClass("disabled")&&!a){var c=jQuery("#user_id").val(),d=jQuery("#user_type").val();jQuery.ajax({url:"http://goxiami.sinaapp.com/clear/"+c+"/"+d,dataType:"jsonp",beforeSend:function(){b.addClass("disabled");a=!0},success:function(c){b.removeClass("disabled");a=!1;200==c.msg?alert("\u6e05! \u7a7a! \u670d! \u52a1! \u5668! \u7f13! \u5b58! \u6210! \u529f! \u611f! \u89c9! \u840c! \u840c! \u54d2!"):
alert("\u518d\u8bd5\u4e00\u6b21\u5427,\u840c\u840c\u54d2!")},error:function(){b.removeClass("disabled");a=!1;alert("\u518d\u8bd5\u4e00\u6b21\u5427,\u840c\u840c\u54d2!")}})}})});</script>
	</div>
</div>