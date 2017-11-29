<?php

require_once(__DIR__ . '/../common.php');

class entry
{
    public $entry_id;
    public $picks = array();
    public $transfer_cost = 0;
    public $team_value = 0;
    public $calc_cost;

    public function get($entry)
    {
        $this->entry_id = $entry;
        $resp = file_get_contents(URL_FPL . "entry/" . $this->entry_id . "/event/" . CURRENT_GW . "/picks");
        $arr = json_decode($resp, true);
        $this->picks = $arr['picks'];
        $this->transfer_cost = $arr['entry_history']['event_transfers_cost'];
        if ($arr['event']['highest_score'] > 0) {
            $this->calc_cost = false;
        } else {
            $this->calc_cost = true;
        }
        $this->team_value = '£ '.(($arr['entry_history']['value'] / 10)); // + ($arr['entry_history']['bank'] / 10));
        if (count($this->picks) > 0 ) {
            return true;
        } else {
            return false;
        }

    }
}