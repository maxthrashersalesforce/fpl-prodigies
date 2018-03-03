<?php

require_once(__DIR__ . '/../common.php');
require_once(__DIR__ . '/../db/db.php');

class entry
{
    public $entry_id;
    public $picks = array();
    public $transfer_cost = 0;
    public $team_value = 0;
    public $calc_cost;
    public $transfers = array();
    public $event_transfers = 0;
    public $points_so_far = 0;
    public $chip = '';

    public $def_count = 0;
    public $mid_count = 0;
    public $fwd_count = 0;

    public function get($entry, $first_of_the_week = false)
    {
        $this->entry_id = $entry;
        if ($first_of_the_week) {
            // $db = new db();
            // $this->picks = $db -> select('select * from entry_picks where round = ;');
        } else {
            $resp = file_get_contents(URL_FPL . "entry/" . $this->entry_id . "/event/" . CURRENT_GW . "/picks");
            $arr = json_decode($resp, true);
            $this->picks = $arr['picks'];
            $this->transfer_cost = $arr['entry_history']['event_transfers_cost'];

            $this->points_so_far = $arr['entry_history']['points'];
            $this->chip = $arr['active_chip'];
            if ($this->chip == 'wildcard') {
                $this->event_transfers = 'WC';
            } else if ($this->chip == 'freehit') {
                $this->event_transfers = 'FH';
            } else {
                $this->event_transfers = $arr['entry_history']['event_transfers'];
            }
            if ($arr['event']['highest_score'] > 0) {
                $this->calc_cost = false;
            } else {
                $this->calc_cost = true;
            }
            $this->team_value = '£ '.(($arr['entry_history']['value'] / 10)); // + ($arr['entry_history']['bank'] / 10));
        }

        if (count($this->picks) > 0 ) {
            return true;
        } else {
            return false;
        }
    }

    public function get_positions($db_players) {
        foreach ($this->picks as $pick) {

        }
    }

    public function get_transfers($entry) {
        $db = new db();
        $players = $db -> select('select p.web_name from players p;');

        $this->entry_id = $entry;
        $resp = file_get_contents(URL_FPL . "entry/" . $this->entry_id . "/transfers");
        $arr = json_decode($resp, true);
        $transfers = $arr['history'];
        $output = '';
        $i = 0;
        if (count($transfers) > 0) {
            for ($k = (count($transfers) - 1); $k >= 0; $k--) {
                $t = $transfers[$k];
                if ($t['event'] == CURRENT_GW) {
                    $output .= $players[$t['element_out'] - 1]['web_name'] . ' (' . $this->gbp($t['element_out_cost']) . ')';
                    $output .= ' > ';
                    $output .= $players[$t['element_in'] - 1]['web_name'] . ' (' . $this->gbp($t['element_in_cost']) . ')';
                    $output .= '<br>';
                    $i++;
                } else {
                    if ($i == 0) {
                        $output = 'No transfers!';
                    }
                    break;
                }
            }
        } else {
            $output = 'No transfers!';
        }
        $this->transfers = $output;
    }

    private function gbp($figure) {
        return '£'.($figure / 10);
    }

    // return as an object. i switched to returning a table with the above function.
    public function get_transfers_($entry) {
        $db = new db();
        $players = $db -> select('select p.web_name from players p;');

        $this->entry_id = $entry;
        $resp = file_get_contents(URL_FPL . "entry/" . $this->entry_id . "/transfers");
        $arr = json_decode($resp, true);
        $transfers = $arr['history'];
        $output = array();
        $i = 0;
        for ($k = (count($transfers) - 1); $k >= 0; $k--) {
            if ($transfers[$k]['event'] == CURRENT_GW) {
                $transfers[$k]['player_in'] = $players[$transfers[$k]['element_in'] - 1]['web_name'];
                $transfers[$k]['player_out'] = $players[$transfers[$k]['element_out'] - 1]['web_name'];
                $output[$i] = $transfers[$k];
                $i++;
            } else {
                break;
            }
        }
        $this->transfers = $output;
    }
}