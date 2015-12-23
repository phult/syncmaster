<?php
foreach ( Config::get("module") as $module ) {
    if ( array_key_exists("activeWhen", $module) && array_key_exists("shortcutMenu", $module) ) {
        eval('$activeWhenValue = ' . $module["activeWhen"] . ';');
        if ( $activeWhenValue == 1 ) {
            echo View::make($module["shortcutMenu"]);
            break;
        }
    }
}