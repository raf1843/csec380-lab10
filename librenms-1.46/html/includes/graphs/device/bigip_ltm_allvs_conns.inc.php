<?php
/*
 * LibreNMS module to display F5 LTM Virtual Server Details
 *
 * Copyright (c) 2016 Aaron Daniels <aaron@daniels.id.au>
 *
 * This program is free software: you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the
 * Free Software Foundation, either version 3 of the License, or (at your
 * option) any later version.  Please see LICENSE.txt at the top level of
 * the source code distribution for details.
 */

$component = new LibreNMS\Component();
$options = array();
$options['filter']['type'] = array('=','f5-ltm-vs');
$components = $component->getComponents($device['device_id'], $options);

// We only care about our device id.
$components = $components[$device['device_id']];

include "includes/graphs/common.inc.php";
$rrd_options .= " -l 0 -E ";
$rrd_options .= " COMMENT:'VS Total Connections       Now      Avg      Max\\n'";
$colours = array_merge($config['graph_colours']['mixed'], $config['graph_colours']['manycolours']);
$colcount = 0;
$count = 0;

// add all LTM VS on this device.
foreach ($components as $compid => $comp) {
    $label = $comp['label'];
    $hash = $comp['hash'];
    $rrd_filename = rrd_name($device['hostname'], array($comp['type'], $label, $hash));
    if (rrdtool_check_rrd_exists($rrd_filename)) {
        // Grab a colour from the array.
        if (isset($colours[$colcount])) {
            $colour = $colours[$colcount];
        } else {
            $colcount = 0;
            $colour = $colours[$colcount];
        }

        $rrd_options .= " DEF:DS" . $count . "=" . $rrd_filename . ":totconns:AVERAGE ";
        $rrd_options .= " LINE1.25:DS" . $count . "#" . $colour . ":'" . str_pad(substr($label, 0, 60), 60) . "'";
        $rrd_options .= " GPRINT:DS" . $count . ":LAST:%6.2lf%s ";
        $rrd_options .= " GPRINT:DS" . $count . ":AVERAGE:%6.2lf%s ";
        $rrd_options .= " GPRINT:DS" . $count . ":MAX:%6.2lf%s\l ";
        $count++;
        $colcount++;
    }
}
