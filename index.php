<?php

/*

  ╔═╗╔╦╗╔═╗╔╦╗
  ║ ║ ║ ╠╣ ║║║ https://otshelnik-fm.ru
  ╚═╝ ╩ ╚  ╩ ╩

 */

//
require_once 'inc/settings.php';


/* каркас */
function psr_get_box() {
    global $user_LK;

    $psr_sign = '';
    $matches  = [];

    $psr_title = (rcl_get_option( 'psr_title' )) ? rcl_get_option( 'psr_title' ) : 'Рейтинг пользователя:';

    $psr_rating = get_user_meta( $user_LK, 'psr_rating', true );
    if ( ! $psr_rating || $psr_rating === 'Не указано' ) {
        $psr_rating = '0';
        $psr_sign   = 'empty';
    } else {
        preg_match( '/(-)|(\d)/', $psr_rating, $matches );
        if ( isset( $matches[0] ) && $matches[0] == '-' ) {
            $psr_sign = 'minus';
        } else if ( isset( $matches[1] ) ) {
            $psr_sign = 'plus';
        }
    }

    $out = '<span class="psr_block psr_' . $psr_sign . ' psr_' . $psr_rating . '" title="' . $psr_title . ' ' . $psr_rating . '" data-psr="' . $psr_rating . '" style="align-self:center;vertical-align:middle;margin:0 3px;text-shadow:none;">';
    if ( rcl_get_option( 'psr_type', '1' ) === '1' ) {
        $out .= psr_star_box();
    } else {
        $out .= '<span style="margin: 0 0 0 6px;">' . $psr_title . ' </span><span>' . $psr_rating . '</span>';
    }

    $out .= '</span>';

    return $out;
}

function psr_star_box() {
    $out = '<i class="rcli fa-star-o"></i>';
    $out .= '<i class="rcli fa-star-o"></i>';
    $out .= '<i class="rcli fa-star-o"></i>';
    $out .= '<i class="rcli fa-star-o"></i>';
    $out .= '<i class="rcli fa-star-o"></i>';

    return $out;
}

// запрет удалением реколл данных если вырезали их во фронте
add_filter( 'rcl_pre_update_profile_field', 'psr_skip_delete_field_in_core' );
function psr_skip_delete_field_in_core( $field ) {
    if ( is_admin() )
        return $field;

    if ( $field['slug'] === 'psr_rating' ) {
        return false;
    }

    return $field;
}

// вывод перед счетчиками
add_action( 'wp_footer', 'psr_before_counters', 6 );
function psr_before_counters() {
    if ( ! rcl_is_office() )
        return false;

    // Справа от имени
    $place = rcl_get_option( 'psr_place', '1' );
    $div   = '.tcl_user,.office-title > h2,.cab_ln_title > h2,.office-content-top > h2,.ao_name_author_lk > h2,.cab_lt_title > h2, .cab_title > h2';

    // Справа от кнопок actions
    if ( $place == 2 )
        $div = '.tcl_bttn_act,.ln_bttn_act,.office-actions,.cab_bttn,.ao_content_lk_top,.aop_content_lk_top';

    $blk = psr_get_box();

// Поместим блок перед выбранным местом
    $out = "<script>
jQuery(document).ready(function(){
jQuery('$div').append('$blk');
});
</script>";
    echo $out;
}

// отдельно вырежем данные в фронте
add_action( 'wp_footer', 'psr_hide_data', 5 );
function psr_hide_data() {
    global $user_ID;

    if ( ! rcl_is_office( $user_ID ) )
        return false;

    $out = "<script>
rcl_add_action('rcl_footer','psr_hide');
rcl_add_action('rcl_upload_tab','psr_hide');
function psr_hide(){jQuery('#rcl-office #profile-field-psr_rating').remove();}
</script>";
    echo $out;
}

// стили
add_filter( 'rcl_inline_styles', 'psr_inline_styles', 10 );
function psr_inline_styles( $styles ) {
    if ( ! rcl_is_office() )
        return $styles;

    $star_css = '';
    if ( rcl_get_option( 'psr_type', '1' ) === '1' ) {
        $star_css = '
.psr_block .fa-star-o {
    color: #ccc;
    display: inline-block !important;
    font-size: 24px;
    line-height: 1;
    margin: 3px;
    vertical-align: middle;
    text-shadow: none;
}
.psr_-1 .fa-star-o:nth-child(1),
.psr_-2 .fa-star-o:nth-child(-n+2),
.psr_-3 .fa-star-o:nth-child(-n+3),
.psr_-4 .fa-star-o:nth-child(-n+4),
.psr_-5 .fa-star-o:nth-child(-n+5) {
    color: red;
}
.psr_1 .fa-star-o:nth-child(1),
.psr_2 .fa-star-o:nth-child(-n+2),
.psr_3 .fa-star-o:nth-child(-n+3),
.psr_4 .fa-star-o:nth-child(-n+4),
.psr_5 .fa-star-o:nth-child(-n+5) {
    color: #3fdb3f;
}
';
    } else {
        $star_css = '
.psr_block > span {
    font-size: 14px;
    font-weight: 400;
}
';
    }

    $styles .= '
#rcl-office #profile-field-psr_rating {
    display: none;
}
#ln_menu .rcli.fa-star-o {
    font-size: 20px;
}
';
    return $styles . $star_css;
}

function psr_add_data() {
    return [ 'Не указано', '-5', '-4', '-3', '-2', '-1', '1', '2', '3', '4', '5' ];
}

// добавим в админку
add_filter( 'rcl_default_profile_fields', 'psr_rating_profile', 10, 2 );
function psr_rating_profile( $fields ) {

    $fields[] = array(
        'type'   => 'select',
        'slug'   => 'psr_rating',
        'values' => psr_add_data(),
        'title'  => 'Рейтинг от администрации'
    );

    return $fields;
}

// и немного обрежем там что не надо
add_filter( 'rcl_custom_field_options', 'psr_exclude_variations', 10, 3 );
function psr_exclude_variations( $options, $field, $post_type ) {
    // это не страница "поля профиля"
    if ( $post_type !== 'profile' )
        return $options;

    // это не наше поле
    if ( isset( $field['slug'] ) && ( $field['slug'] !== 'psr_rating' ) )
        return $options;

    // что нам не нужно - удалим
    foreach ( $options as $option ) {
        // первое значение
        if ( $option['slug'] == 'empty-first' )
            continue;

        // подпись к полю
        if ( $option['slug'] == 'notice' )
            continue;

        // добавляемые значения
        if ( $option['slug'] == 'values' )
            continue;

        // отображать для других пользователей
        if ( $option['slug'] == 'req' )
            continue;
        //
        // редактируется администрацией
        if ( $option['slug'] == 'admin' ) {
            $option['values'] = array( 'Да' );
        }
        //    continue;
        //
        // отображать в заказе
        if ( $option['slug'] == 'order' )
            continue;

        // обязательное поле
        if ( $option['slug'] == 'required' )
            continue;

        // Макс. кол-во знаков
        if ( $option['slug'] == 'maxlength' )
            continue;

        // отображать в форме регистрации
        if ( $option['slug'] == 'register' )
            continue;

        // Фильтровать пользователей по значению этого поля
        if ( $option['slug'] == 'filter' )
            continue;

        $opt[] = $option;
    }

    return $opt;
}

// добавим в ЛК (таб профиль)
add_filter( 'rcl_profile_fields', 'psr_add_form', 10 );
function psr_add_form( $fields ) {
    foreach ( $fields as $field ) {
        if ( $field['slug'] === 'psr_rating' ) {
            $field['values'] = psr_add_data();
        }

        $opt[] = $field;
    }

    return $opt;
}
