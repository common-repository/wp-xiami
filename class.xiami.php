<?php

/**
 * wp-xiami 主类
 */
class xiami{
	public function __construct(){
		$this->config = get_option('wp_xiami_settings');
        /**
         * 事件绑定
         */
        add_action('admin_menu', array($this, 'admin_menu'));
        add_action('admin_head', array($this, 'admin_head') );
        add_action('wp_enqueue_scripts', array($this, 'wp_xiami_scripts'), 20, 1);
        add_action('template_redirect', array($this, 'wp_xiami_template'), 1 );
    }


    /**
     * 显示后台菜单
     */
    function admin_menu(){
        add_menu_page('同步预览', '虾米音乐同步', 'manage_options', 'wp-xiami', array($this,'main'), WP_XIAMI_URL . '/static/images/icon.png');
        add_submenu_page('wp-xiami', '基础设置', '基础设置', 'manage_options', 'wp-xiami-option', array($this, 'option'));
    }

    /**
     * 预览页面
     */
    function main(){
        @include 'include/wp-xiami.php';
    }

    /**
     * 基础设置页面
     */
    function option(){
        @include 'include/wp-xiami-option.php';
    }

    function admin_head(){
        if( isset($_GET['page']) && $this->is_admin_access() ){
            if( $_GET['page'] == 'wp-xiami' ){
                wp_enqueue_style( 'wp-xiami', $this->url('/static/css/style.css') );

                wp_enqueue_script( 'angular.min', $this->url('/static/js/angular.min.js') );
                wp_enqueue_script( 'angular-resource.min', $this->url('/static/js/angular-resource.min.js') );
                wp_enqueue_script( 'wp-xiami', $this->url('/static/js/sync.js') );
                wp_localize_script( 'wp-xiami', 'global', array(
                    'tmpl_url' => $this->url('/static/tmpl/'),
                    'ajax_url' => WP_XIAMI_AJAX_URL,
                    'user_id' => $this->get_settings("user_id"),
                    'user_type' => $this->get_settings("user_type"),
                    'version' => WP_XIAMI_VERSION
                ));
            }
        }

    }

    function wp_xiami_scripts(){
        $page_id = $this->config['user_page'];

        if( !$page_id ){
            return ;
        }

        if( is_page($page_id) ){
            wp_enqueue_style( 'wp-xiami', $this->url('/static/css/wp-xiami.css'), array(), WP_XIAMI_VERSION );

            wp_enqueue_script('jquery');
            wp_enqueue_script( 'wp-xiami', $this->url('/static/js/wp-xiami.js'), array(), WP_XIAMI_VERSION, true);
            wp_localize_script( 'wp-xiami', 'global', array(
                'remote' => WP_XIAMI_AJAX_URL,
                'static' => $this->url('/static/'),
                'jquery' => includes_url('/js/jquery/jquery.js'),
                'user_width' => $this->get_settings('user_width'),
                'user_id' => $this->get_settings("user_id"),
                'user_type' => $this->get_settings("user_type"),
                'user_fullwidth' => $this->get_settings("user_fullwidth"),
                'user_authorshow' => $this->get_settings("user_authorshow"),
                'version' => WP_XIAMI_VERSION
            ));
        }

    }


    function wp_xiami_template(){
        $page_id = $this->config['user_page'];

        if( !$page_id ){
            return ;
        }

        if( !is_page($page_id) ){
            return ;
        }

        include( WP_XIAMI_PATH . '/static/tmpl/tpl-wp-xiami.php' );
        exit();
    }


    function display(){?>
        <!-- WP_XiaMi start V<?php echo WP_XIAMI_VERSION;?> -->
        <div id="wpxm_rapier">
            <div id="wpxm_music-tip-area"><div class="wpxm_music-content"><div id="wpxm_music-tip"><div class="wpxm_music-tip-show"><span class="wpxm_music-info">点击专辑图，展开详细的专辑播放列表</span></div><i class="wpxm_music-arrow"></i><i class="wpxm_music-arrow upper"></i></div></div></div>
            <div class="wpxm_music-content wpxm_main_conent"></div>
            <div id="wpxm_music-preview"></div>
        </div>
        <div id="wpxmplayer-box">
            <div class="wpxmplayer-prosess"></div>
            <div class="wpxm_music-content">
                <div class="wpxmplayer-info">
                    <span class="wpxmplayer-title"></span>
                    <span class="wpxmplayer-timer"></span>
                </div>
                <div class="wpxmplayer-control">
                    <ul>
                        <li><a id="wpxmplayer-prev" href="javascript:;"></a></li>
                        <li><a id="wpxmplayer-button" href="javascript:;"></a></li>
                        <li><a id="wpxmplayer-next" href="javascript:;"></a></li>
                    </ul>
                </div>
            </div>
        </div>
        <script id="wpxm_tpl_1" type="text/template">
            <ul class="wpxm-ul">
                {@each collects as it, index}
                    {@if it.collect_cover}
                        <li class="wpxm_music" data-id="${it.collect_id}" data-index="${index}">
                            <div class="wpxm_music-image">
                                <img src="{@if it.collect_cover}${it.collect_cover|parseCover}{@/if}" alt="${it.collect_title}">
                                <span class="wpxm_music-mask"></span>
                            </div>
                            <span class="wpxm_music-title">${it.collect_title|parseText}</span>
                            {@if !hideauthor}<span class="wpxm_music-author">${it.collect_author}</span>{@/if}
                        </li>
                    {@/if}
                {@/each}
            </ul>
        </script>
        <script id="wpxm_tpl_2" type="text/template">
            <div class="wpxm_music-player">
                <div class="wpxm_music-songs-info">${collect_title} - ${collect_author}</div>
                <ol class="wpxm_music-song-list" type="1">
                    {@each songs as it, index}
                    <li class="wpxm_music-song" data-songid="${it.song_id}" data-cid="${cid}" data-index="${index}">
                        <span class="wpxm_music-song-icon">${index|indexPlus}</span>
                        <span class="wpxm_music-song-title">${it.song_title|parseText} - ${it.song_author}</span>
                        <span class="wpxm_music-song-length">${it.song_length|parseTime}</span>
                    </li>
                    {@/each}
                </ol>
                <div class="wpxm_music-songs-tip">* 单击播放，空格键暂停播放，← 键 上一首， → 键 下一首。</div>
                <div class="wpxm_music-close"></div>
            </div>
        </script>
        <!-- WP_XiaMi end V<?php echo WP_XIAMI_VERSION;?> -->
    <?php }

    /**
     * 判断当前用户是否有设置权限
     */
    public function is_admin_access(){
        return current_user_can('manage_options');
    }

    function url($url){
        if( !$url ) return;
        return WP_XIAMI_URL . $url;
    }

    public function get_settings($key){
        return $key ? $this->config[$key] : $this->config;
    }

    public function update_settings($array){
        update_option('wp_xiami_settings', $array);
        $this->config = get_option('wp_xiami_settings');
    }

    public function get_page_link(){
        $page_id = $this->config['user_page'];

        if( !$page_id ){
            return false;
        }

        $page_link = get_permalink($page_id);
        $page_link = rtrim($page_link,'/\\');

        return $page_link;
    }

}