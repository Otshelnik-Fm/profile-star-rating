<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


add_filter( 'admin_options_wprecall', 'psr_settings' );
function psr_settings( $content ) {
    $opt = new Rcl_Options( __FILE__ );

    $content .= $opt->options( 'Настройки Profile Star Rating', array(
        $opt->options_box( 'Настройки Profile Star Rating', array(
            [
                'title'  => 'Тип вывода рейтинга',
                'type'   => 'select',
                'slug'   => 'psr_type',
                'values' => [ '1' => 'Звёздный', '2' => 'Текстовый' ],
                'help'   => 'Вариант вывода рейтинга',
                'notice' => 'По умолчанию: "Звёздный"<hr>',
            ],
            [
                'title'  => 'Подсказка по наведению:',
                'type'   => 'text',
                'slug'   => 'psr_title',
                'help'   => 'Будет выводиться подсказка и рейтинг.<br/>К примеру: Рейтинг: -5',
                'notice' => 'По умолчанию: "Рейтинг пользователя:"<hr>',
            ],
            [
                'title'  => 'Выводим рейтинг',
                'type'   => 'select',
                'slug'   => 'psr_place',
                'values' => [ '1' => 'Справа от имени', '2' => 'Справа от кнопок actions' ],
                'help'   => 'Выбирайте место вывода персонального рейтинга',
                'notice' => 'По умолчанию: "Рядом с именем"',
            ],
            )
        ),
        ) );

    return $content;
}
