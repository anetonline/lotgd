<?php
function checkday() {
    global $session;
    if (!isset($session['user']) || !is_array($session['user'])) return;
    $today = date("Y-m-d");
    if (!isset($session['user']['laston']) || $session['user']['laston'] != $today) {
        $session['user']['laston'] = $today;
        $session['user']['playerfights'] = 3;
        $session['user']['turns'] = isset($session['user']['maxturns']) ? $session['user']['maxturns'] : 10;
        if (isset($session['user']['alive']) && !$session['user']['alive']) {
            $session['user']['alive'] = true;
            $session['user']['hitpoints'] = isset($session['user']['maxhitpoints']) ? $session['user']['maxhitpoints'] : 10;
            if (function_exists('output')) {
                output("`&You have been resurrected for a new day!`n");
            }
        }
    }
}
?>
