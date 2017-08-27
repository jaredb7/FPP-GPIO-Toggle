<?php

//update plugin

function updatePluginFromGitHub($gitURL, $branch = "master", $pluginName)
{
    global $settings;
    logEntry("updating plugin: " . $pluginName);

    logEntry("settings: " . $settings['pluginDirectory']);

    //create update script
    //$gitUpdateCMD = "sudo cd ".$settings['pluginDirectory']."/".$pluginName."/; sudo /usr/bin/git git pull ".$gitURL." ".$branch;

    $pluginUpdateCMD = "/opt/fpp/scripts/update_plugin " . $pluginName;

    logEntry("update command: " . $pluginUpdateCMD);


    exec($pluginUpdateCMD, $updateResult);

    //logEntry("update result: ".print_r($updateResult));

    //loop through result
    return;// ($updateResult);
}

function logEntry($data)
{
    global $logFile, $myPid;

    $data = $_SERVER['PHP_SELF'] . " : [" . $myPid . "] " . $data;

    $logWrite = fopen($logFile, "a") or die("Unable to open file!");
    fwrite($logWrite, date('Y-m-d h:i:s A', time()) . ": " . $data . "\n");
    fclose($logWrite);
}

?>