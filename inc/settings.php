<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_filter( 'rcl_options', 'pstrr_addon_options' );
function pstrr_addon_options( $options ) {
    // создаем блок
    $options->add_box( 'pstrr_box_id', array(
        'title' => 'Настройки Profile Star Rating',
        'icon'  => 'fa-star-o'
    ) );

    // создаем группу 1
    $options->box( 'pstrr_box_id' )->add_group( 'pstrr_group_1', array(
        'title' => '<span class="dashicons dashicons-star-empty"></span> Настройки Profile Star Rating:'
    ) )->add_options( array(
        [
            'title'   => 'Тип вывода рейтинга',
            'type'    => 'radio',
            'slug'    => 'psr_type',
            'values'  => [ '1' => 'Звёздный', '2' => 'Текстовый' ],
            'default' => '1',
            'help'    => 'Вариант вывода рейтинга',
            'notice'  => 'По умолчанию: "Звёздный"',
        ],
        [
            'title'  => 'Подсказка по наведению:',
            'type'   => 'text',
            'slug'   => 'psr_title',
            'help'   => 'Будет выводиться подсказка и рейтинг.<br>К примеру: Рейтинг: -5',
            'notice' => 'По умолчанию: "Рейтинг пользователя:"',
        ],
        [
            'title'   => 'Выводим рейтинг',
            'type'    => 'radio',
            'slug'    => 'psr_place',
            'values'  => [ '1' => 'Справа от имени', '2' => 'Справа от кнопок actions' ],
            'default' => '1',
            'help'    => 'Выбирайте место вывода персонального рейтинга',
            'notice'  => 'По умолчанию: "Рядом с именем"',
        ],
    ) );

    return $options;
}
