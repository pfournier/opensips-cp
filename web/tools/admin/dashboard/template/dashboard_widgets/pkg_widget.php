<?php
require_once(__DIR__."/../../../../admin/dashboard/template/widget/widget.php");

class pkg_widget extends widget
{
    public $cdr_entries;
	public $top_loaded_info;
	public $top_fragmented_info;

    function __construct($array) {
        parent::__construct($array['panel_id'], $array['widget_title'], 7, 6, $array['widget_title']);
        $this->compute_info();
        $this->color = "rgb(225, 232, 239)";
    }


    function get_name() {
        return "PKG memory widget";
    }
    function display_test() {
        echo ('
		<table class="ttable" width="100%" cellspacing="2" cellpadding="2" border="0" style="margin: auto;">
		<tr align="center">
		<th class="listTitle" style="text-shadow: 0px 0px 0px #000;">OpenSIPS ID</th>
		<th class="listTitle" style="text-shadow: 0px 0px 0px #000;">PID</th>
		<th class="listTitle" style="text-shadow: 0px 0px 0px #000;">Description</th>
		<th class="listTitle" style="text-shadow: 0px 0px 0px #000;">Fragments</th>
		</tr>');
		foreach ($this->top_fragmented_info as $info) {
			echo ('
			<tr>
			<td class="rowEven">&nbsp;'.$info['id'].'</td>
			<td class="rowEven">&nbsp;'.$info['PID'].'</td>
			<td class="rowEven">&nbsp;'.$info['desc'].'</td>
			<td class="rowEven">&nbsp;'.$info['value'].'</td>
			</tr>  ');
		}
		
		echo('</table><br>');
		echo ('
		<table class="ttable" width="100%" cellspacing="2" cellpadding="2" border="0" style="margin: auto;">
		<tr align="center">
		<th class="listTitle"  style="text-shadow: 0px 0px 0px #000;">OpenSIPS ID</th>
		<th class="listTitle" style="text-shadow: 0px 0px 0px #000;">PID</th>
		<th class="listTitle" style="text-shadow: 0px 0px 0px #000;">Description</th>
		<th class="listTitle" style="text-shadow: 0px 0px 0px #000;">Load</th>
		</tr>');
		foreach ($this->top_loaded_info as $info) {
			echo ('
			<tr>
			<td class="rowEven">&nbsp;'.$info['id'].'</td>
			<td class="rowEven">&nbsp;'.$info['PID'].'</td>
			<td class="rowEven">&nbsp;'.$info['desc'].'</td>
			<td class="rowEven">&nbsp;'.$info['value'].'%</td>
			</tr>  ');
		}

		echo('</table>');
    }



    function echo_content() {
        echo ('<div id="'.$this->id.'_old"><br>'.$this->title.'<br><br>');
        $this->display_test();
        echo('</div>');
    }

    function compute_info() {
		$pkg = mi_command("get_statistics", array("statistics" => array("pkmem:")), $_SESSION['boxes'][0]['mi_conn'], $errors);
		$top_fragmented = $this->get_top_fragmented($pkg);
		$this->top_fragmented_info = $this->get_proc_infos($top_fragmented);
		$top_loaded = $this->get_top_loaded($pkg);
		$this->top_loaded_info = $this->get_proc_infos($top_loaded);

    }

	function get_proc_infos($top_procs) {
		$procs = mi_command("ps", array(), $_SESSION['boxes'][0]['mi_conn'], $errors);
		foreach($top_procs as $key => $top_proc) {
			$found = false;
			foreach($procs['Processes'] as $proc) {
				if ($proc['ID'] == $top_proc['id']) {
					$top_procs[$key]['PID'] = $proc['PID'];
					$top_procs[$key]['desc'] = $proc['Type'];
					$found = true;
					break;
				}
				if ($found) break;
			}
		}
		return $top_procs;
	}

	function get_top_fragmented($pkg) {
		$top = [];
		foreach ($pkg as $id=>$value) {
			if (strpos($id, "fragments")) {
				preg_match('/(pkmem:(?<id>\d+)-fragments)/', $id, $matches);
				$entry['id'] = $matches['id'];
				$entry['value'] = $value;
				$top[] = $entry;
			}
		}
		function cmp($a, $b) {
			return $b['value'] - $a['value'];
		}
		usort($top, "cmp");
		return array_slice($top, 0 , 4);
	}

	function get_top_loaded($pkg) {
		$top = [];
		foreach ($pkg as $id=>$value) {
			$entry = [];
			if (strpos($id, "fragments")) {
				preg_match('/(pkmem:(?<id>\d+)-fragments)/', $id, $matches);
				$entry['id'] = $matches['id'];
				if ($pkg['pkmem:'.$entry['id'].'-max_used_size'] == 0 ) continue;
				$entry['value'] = $pkg['pkmem:'.$entry['id'].'-used_size'] / $pkg['pkmem:'.$entry['id'].'-max_used_size'];
				$entry['value'] = round($entry['value'] * 100, 2);
				$top[] = $entry;
			}
		}
		function cmp_load($a, $b) {
			return $b['value'] - $a['value'];
		}
		usort($top, "cmp_load");
		return array_slice($top, 0 , 4);
	}

    function get_as_array() {
        return array($this->get_html(), $this->get_sizeX(), $this->get_sizeY());
    }

    public static function new_form($params = null) {  
        form_generate_input_text("Title", "", "widget_title", null, $params['widget_title'], 20,null);
    }

}

?>