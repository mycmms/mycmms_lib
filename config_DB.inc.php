<?php
/**
* DB configuration: easy switching the environment between databases.
* Usage: Add a database to the $databases[] array, in the switch statement, the connection parameters are given.
* @author  Werner Huysmans <werner.huysmans@skynet.be>
* @changed:   2010-06
* @access  public
* @package mycmms40
* @subpackage DEBUG
* @filesource
*/
if (isset($_SESSION['customer'])) {
    $ini_array = parse_ini_file("databases.inc", true);
    $mycmms=array(
        "host"=>$ini_array[$_SESSION['customer']]["host"],
        "DB"=>$ini_array[$_SESSION['customer']]["DB"],
        "uid"=>$ini_array[$_SESSION['customer']]["uid"],
        "pwd"=>$ini_array[$_SESSION['customer']]["pwd"]);
} else {
    $ini_array = parse_ini_file("databases.inc", true);
    $mycmms=array(
        "host"=>$ini_array['DEFAULT']["host"],
        "DB"=>$ini_array['DEFAULT']["DB"],
        "uid"=>$ini_array['DEFAULT']["uid"],
        "pwd"=>$ini_array['DEFAULT']["pwd"]);
}
/**
* Old System
*/
$databases=array(
    "ASCO",
    "CMMS_3_3",
    "CMMS_4_0",
    "DESTROOPER",
    "SARALEE",
    "CRH",
    "HOMENET");
switch(CMMS_DB) {
    case "CMMS_PMS":
        $mycmms= array("host"=>"localhost","DB"=>"mycmms_pms","uid"=>"root","pwd"=>"ibmhuy");
        break;
    case "CMMS_4_0":
        $mycmms= array("host"=>"localhost","DB"=>"mycmms40","uid"=>"root","pwd"=>"ibmhuy");
        break;
    case "CMMS_3_3":
        $mycmms= array("host"=>"localhost","DB"=>"mycmms33","uid"=>"root","pwd"=>"ibmhuy");
        break;
    case "REFERENCE":
        $mycmms= array("host"=>"localhost","DB"=>"mycmms33_reference","uid"=>"root","pwd"=>"ibmhuy");
        break;
    case "DESTROOPER":
        $mycmms= array("host"=>"localhost","DB"=>"mycmms32_ds","uid"=>"root","pwd"=>"ibmhuy");
        break;
    case "SARALEE":
        $mycmms= array("host"=>"localhost","DB"=>"mycmms32_de","uid"=>"root","pwd"=>"ibmhuy");
        break;
    case "CRH":
        $mycmms= array("host"=>"localhost","DB"=>"mycmms32_crh","uid"=>"root","pwd"=>"ibmhuy");
        break;
    case "INEOS":
        $mycmms= array("host"=>"localhost","DB"=>"mycmms32_ineos","uid"=>"root","pwd"=>"ibmhuy");
        break;
    case "ASCO":
        $mycmms= array("host"=>"localhost","DB"=>"mycmms40_asco","uid"=>"root","pwd"=>"ibmhuy");
        break;
    case "AF":
        $mycmms= array("host"=>"localhost","DB"=>"mycmms40_af","uid"=>"root","pwd"=>"ibmhuy");
        break;
    case 'HOMENET':
        $mycmms= array("host"=>"localhost","DB"=>"homenet","uid"=>"root","pwd"=>"ibmhuy");
        break;
    default:
        break;
} // EOF switch
?>
