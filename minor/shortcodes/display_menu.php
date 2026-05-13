<?php
/**
 * Display Menu Shortcode
 * 
 * 用法: [display_menu name="菜单名称"]
 * 例: [display_menu name="new_sideinfo"]
 * 
 * 此短代码将指定的菜单以多级列表形式展示，不显示列表项前的小点
 */

if ( ! function_exists( 'hy_display_menu_shortcode' ) ) {
    /**
     * 短代码处理函数
     */
    function hy_display_menu_shortcode( $atts ) {
        $atts = shortcode_atts( array(
            'name' => '',
        ), $atts, 'display_menu' );

        $menu_name = $atts['name'];

        if ( empty( $menu_name ) ) {
            return '<!-- 错误: 未指定菜单名称 -->';
        }

        // 获取菜单对象
        $menu = wp_get_nav_menu_object( $menu_name );

        if ( ! $menu ) {
            return '<!-- 错误: 菜单 "' . esc_attr( $menu_name ) . '" 不存在 -->';
        }

        // 获取菜单项
        $menu_items = wp_get_nav_menu_items( $menu->term_id );

        if ( ! $menu_items ) {
            return '<!-- 错误: 菜单 "' . esc_attr( $menu_name ) . '" 为空 -->';
        }

        // 输出内容
        ob_start();
        ?>
        <style>
            .hy-menu-display {
                list-style: none;
                padding-left: 0;
                margin: 0;
            }
            .hy-menu-display ul {
                list-style: none;
                padding-left: 20px;
                margin: 5px 0;
            }
            .hy-menu-display li {
                margin: 5px 0;
            }
        </style>

        <ul class="hy-menu-display">
            <?php
            // 构建菜单树形结构
            $menu_tree = hy_build_menu_tree( $menu_items );
            hy_render_menu_tree( $menu_tree );
            ?>
        </ul>

        <?php
        $output = ob_get_clean();
        return $output;
    }

    /**
     * 构建菜单树形结构
     * 
     * @param array $menu_items 菜单项数组
     * @return array 树形结构
     */
    function hy_build_menu_tree( $menu_items ) {
        $tree = array();
        $items_map = array();

        // 建立项目映射表
        foreach ( $menu_items as $item ) {
            $items_map[ $item->ID ] = $item;
        }

        // 构建树形结构
        foreach ( $menu_items as $item ) {
            if ( $item->menu_item_parent == 0 ) {
                $tree[ $item->ID ] = $item;
                $tree[ $item->ID ]->children = array();
            } else {
                // 找到父项并添加为子项
                if ( isset( $items_map[ $item->menu_item_parent ] ) ) {
                    $parent_id = $item->menu_item_parent;
                    hy_add_menu_child( $tree, $parent_id, $item );
                }
            }
        }

        return $tree;
    }

    /**
     * 递归添加菜单项到树形结构
     * 
     * @param array $tree 树形结构
     * @param int $parent_id 父项ID
     * @param object $item 要添加的菜单项
     */
    function hy_add_menu_child( &$tree, $parent_id, $item ) {
        foreach ( $tree as &$node ) {
            if ( $node->ID == $parent_id ) {
                $item->children = array();
                $node->children[ $item->ID ] = $item;
                return;
            }

            if ( ! empty( $node->children ) ) {
                hy_add_menu_child( $node->children, $parent_id, $item );
            }
        }
    }

    /**
     * 递归渲染菜单树
     * 
     * @param array $tree 菜单树
     */
    function hy_render_menu_tree( $tree ) {
        foreach ( $tree as $item ) {
            $link = isset( $item->url ) && ! empty( $item->url ) ? $item->url : '#';
            $title = isset( $item->title ) ? esc_html( $item->title ) : '';

            echo '<li>';
            echo '<a href="' . esc_url( $link ) . '">' . $title . '</a>';

            // 如果有子菜单，递归渲染
            if ( ! empty( $item->children ) ) {
                echo '<ul>';
                hy_render_menu_tree( $item->children );
                echo '</ul>';
            }

            echo '</li>';
        }
    }

    // 注册短代码
    add_shortcode( 'display_menu', 'hy_display_menu_shortcode' );
}
