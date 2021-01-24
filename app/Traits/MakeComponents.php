<?php

namespace App\Traits;

trait MakeComponents {
    private function keyboardBtn($option) {
        $keyboard = [
            'keyboard'          => $option,
            'resize_keyboard'   => true,
            'one_time_keyboard' => true,
            'selective'         => true
        ];

        $keyboard = json_encode($keyboard);
        return $keyboard;
    }
}
