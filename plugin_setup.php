<?php
//$DEBUG=true;

include_once "/opt/fpp/www/common.php";
include_once "functions.inc.php";
include_once 'commonFunctions.inc.php';

$pluginName = "FPP-GPIO-Toggle";
$pluginVersion = "1.0";

//$DEBUG=true;
$myPid = getmypid();

$gitURL = "https://github.com/jaredb7/FPP-GPIO-Toggle.git";

$pluginUpdateFile = $settings['pluginDirectory'] . "/" . $pluginName . "/" . "pluginUpdate.inc";

$logFile = $settings['logDirectory'] . "/" . $pluginName . ".log";

logEntry("plugin update file: " . $pluginUpdateFile);


if (isset($_POST['updatePlugin'])) {
    $updateResult = updatePluginFromGitHub($gitURL, $branch = "master", $pluginName);

    echo $updateResult . "<br/> \n";
}


if (isset($_POST['submit'])) {
    WriteSettingToFile("ENABLED", urlencode($_POST["ENABLED"]), $pluginName);
    WriteSettingToFile("TOGGLE_TIME", urlencode($_POST["TOGGLE_TIME"]), $pluginName);
}
sleep(1);
$TOGGLE_TIME = 1000;
$pluginConfigFile = $settings['configDirectory'] . "/plugin." . $pluginName;
if (file_exists($pluginConfigFile)) {
    $pluginSettings = parse_ini_file($pluginConfigFile);

    $ENABLED = $pluginSettings['ENABLED'];
    $TOGGLE_TIME = $pluginSettings['TOGGLE_TIME'];
}

//$ENABLED = ReadSettingFromFile('ENABLED', $pluginName);


//$CNTRL_LIST = array_map('trim', explode(",", $pluginSettings['CNTRL_LIST']));
//$CNTRL_LOG_FILE = urldecode($pluginSettings['CNTRL_LOG_FILE']);


//Set a default value
if (trim($CNTRL_LOG_FILE) == "") {
    $CNTRL_LOG_FILE = "/tmp/FPP.GPIO-Toggle.log";
}

?>

<html>
<head>
    <script>
        function ToggleGpio(gpio_pin, toggle, toggleval, toggletime) {
            //set default toggle time if none set
            if (typeof toggletime === 'undefined' || toggletime === null) {
                //set default
                toggletime = 1000
            }
            //default to toggle
            if (typeof toggle === 'undefined' || toggle === null) {
                //set default
                toggle = true
            }

            var xmlhttp = new XMLHttpRequest();
            var url = "fppxml.php?command=extGPIO&gpio=" + gpio_pin + "&mode=Output&val=" + toggleval;
            xmlhttp.open("GET", url, true);
            xmlhttp.setRequestHeader('Content-Type', 'text/xml');
            xmlhttp.send();

            //If we should actually toggle
            if (toggle) {
                //sleep then toggle
                setTimeout(
                    function () {
                        //invert the output value
                        if (toggleval === 0) {
                            toggleval = 1
                        } else {
                            toggleval = 0
                        }

                        url = "fppxml.php?command=extGPIO&gpio=" + gpio_pin + "&mode=Output&val=" + toggleval;
                        //Toggle back
                        xmlhttp.open("GET", url, true);
                        xmlhttp.setRequestHeader('Content-Type', 'text/xml');
                        xmlhttp.send();
                    }
                    , toggletime);
            }
        }
    </script>
</head>

<div id="GPIO-Toggle" class="settings">
    <fieldset>
        <legend>FPP-GPIO-Toggle Support Instructions</legend>

        <p>Known Issues:
        <ul>
            <li>None</li>
        </ul>

        <p>Configuration:
        <ul>
            <li>This Plugin uses the GPIO Outputs set under Channel Outputs</li>
            <li>1. Setup Your GPIO Output under <a href="http://<? echo $_SERVER['SERVER_NAME'] ?>/channeloutputs.php#tab-other">Channel Outputs</a> > Other
            </li>
            <li>2. Return here and there should be buttons to toggle the state of the GPIO Output, it will toggle from
                the current setting to either LOW or HIGH depending on the current GPIO value.
            </li>
            <li>3. Optionally Set the toggle time (delay between on and off), default 2000ms ( 2 seconds )</li>

        </ul>
        <p>

        <p>To report a bug, please file it against the FPP-GPIO-Toggle plugin project on Git: <?echo $gitURL?></p>

        <form method="post"
              action="http://<? echo $_SERVER['SERVER_NAME'] ?>/plugin.php?plugin=<? echo $pluginName; ?>&page=plugin_setup.php">
            <?
            $restart = 0;
            $reboot = 0;

            echo "<b>ENABLE PLUGIN:</b> ";
            //if($ENABLED== 1 || $ENABLED == "on") {
            //		echo "<input type=\"checkbox\" checked name=\"ENABLED\"> \n";
            PrintSettingCheckbox(" Plugin" . $pluginName, "ENABLED", $restart = 0, $reboot = 0, "ON", "OFF", $pluginName, $callbackName = "");
            //	} else {
            //		echo "<input type=\"checkbox\"  name=\"ENABLED\"> \n";
            //}
            ?>

            <br>
            <br>

            <b>Toggle Time (in ms)</b>
            <input type="text" size="15" value="<? if ($TOGGLE_TIME != "") {
                echo $TOGGLE_TIME;
            } else {
                echo "2000";
            } ?>" name="TOGGLE_TIME" id="TOGGLE_TIME">
            <small>Set the toggle time (delay between on and off)</small>

            <br>
            <br>
            <input id="submit_button" name="submit" type="submit" class="buttons" value="Save Config">

            <br>
            <br>
            <?
            if (file_exists($pluginUpdateFile)) {
                //echo "updating plugin included";
                include $pluginUpdateFile;
            }
            ?>
        </form>

        <br>
        <br>

        <table id="tblGpioOutputs" class="channelOutputTable">
            <tr class="tblheader">
                <td width="5%" align="left">Output #</td>
                <td width="10%" align="left">Active</td>
                <td width="10%" align="left">Output Type</td>
                <td width="10%" align="left">FPP Start Channel</td>
                <td width="5%" align="left">Inverted</td>
                <td width="10%" align="left">Current Value</td>
                <td width="10%" align="left">Toggle</td>
            </tr>

            <?
            //Read the channelouputs file in the root of the fpp storage media
            // ./channeloutputs
            //Write a copy locally as well
            $channeloutputs_file_contents = file_get_contents($settings['channelOutputsFile']);

            //Explode the contents by new lines first, then process each fo the entries separately
            $exploded_channel_output_data = explode("\n", $channeloutputs_file_contents);
            //loop over each line and extract info
            foreach ($exploded_channel_output_data as $row => $channel_config_data) {

                $channel_config_explode = explode(",", $channel_config_data);
                //first entry is if that output is enabled or now
                $channel_enabled = $channel_config_explode[0];
                //channel type
                $channel_type = $channel_config_explode[1];
                //channel start
                $channel_start_channel = $channel_config_explode[2];
                //channel length/range
                $channel_length = $channel_config_explode[3];
                //gpio & invert
                $channel_gpio_pin = explode("=", explode(";", $channel_config_explode[4])[0])[1];
                $channel_gpio_invert = explode("=", explode(";", $channel_config_explode[4])[1])[1];

                //get current GPIO value, so we have somewhere to start the toggle
                //use a different method for the BBB
                if ($settings['Platform'] == "BeagleBone Black"){
                    $current_gpio_value = shell_exec("cat /sys/class/gpio/gpio$channel_gpio_pin/value");
                }else{
                    $current_gpio_value = shell_exec("/usr/local/bin/gpio -g read $channel_gpio_pin");
                }

                // attempt to only set valid values
                if (isset($current_gpio_value) && in_array($current_gpio_value, array('1', '0'))) {
                    $toggle_output_val = $current_gpio_value;
                } else{
                    $toggle_output_val = 0;
                }

                //if the channel should be inverted start with a LOW (opposite of the current value), it'll toggle LOW then HIGH
                //basically toggle opposite
                if ($channel_gpio_invert == 1) {
                    $toggle_output_val = 0;
                }

                //If enabled generate a button
                if (strtolower($channel_type) == 'gpio') {
                    ?>
                    <tr class="rowGpioDetails <? echo $channel_enabled == 0 ? 'rowDisabled' : '' ?>">
                        <td align="center"><? echo $channel_gpio_pin ?></td>
                        <td align="center"><? echo $channel_enabled == 1 ? '<b>Yes</b>' : 'No' ?></td>
                        <td align="center"><? echo $channel_type ?></td>
                        <td align="center"><? echo $channel_start_channel ?></td>
                        <td align="center"><? echo $channel_gpio_invert == 1 ? 'Yes' : 'No' ?></td>
                        <td align="center"><? echo $current_gpio_value == 1 ? 'HIGH (1)' : 'LOW (0)' ?></td>
                        <td>
                            <input class="button"
                                   onClick="ToggleGpio(<? echo $channel_gpio_pin; ?>,true,<? echo $toggle_output_val; ?>,<? echo $TOGGLE_TIME; ?>);"
                                   type="submit"
                                   value="Toggle Pin #<? echo $channel_gpio_pin; ?>"
                                <? echo $channel_enabled == 0 ? 'disabled' : '' ?>
                            />

                            <input class="button"
                                   onClick="ToggleGpio(<? echo $channel_gpio_pin; ?>,false,0,0);"
                                   type="submit"
                                   value="Set LOW Pin #<? echo $channel_gpio_pin; ?>"
                                <? echo $channel_enabled == 0 ? 'disabled' : '' ?>

                            />

                            <input class="button"
                                   onClick="ToggleGpio(<? echo $channel_gpio_pin; ?>,false,1,0);"
                                   type="submit"
                                   value="Set HIGH Pin #<? echo $channel_gpio_pin; ?>"
                                <? echo $channel_enabled == 0 ? 'disabled' : '' ?>
                            />
                        </td>
                    </tr>
                    <?
                }
            }
            ?>
        </table>

    </fieldset>
</div>
<br/>
</html>