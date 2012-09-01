<?php
/** 
* Library lib_common
* @author  Werner Huysmans <werner.huysmans@skynet.be>
* @changed:   2010-07
* @access  public
* @version: 4.0
* @package mycmms40
* @subpackage library
* @filesource
*/
/**
* Convert NOW to a string
* @time boolean : indicate the time or not
*/
function now2string($time=false) {
    $now=getdate();
    if ($time) {
        return $now['year']."-".$now['mon']."-".$now['mday']." ".$now['hours'].":".$now['minutes'];
    } else {
        return $now['year']."-".$now['mon']."-".$now['mday'];
    }
}
/** 
* Log PDO transactions to PDO_ERRORLOG
* @message string : message to show
*/
function PDO_log($message) {
    $fh=fopen(PDO_ERRORLOG,"a");
    fwrite($fh,now2string(true).": ".$message."\n");
    fclose($fh);
    $_SESSION['PDO_ERROR']=$message;
}
/** 
* DEBUG javascript ALERT
* @pause boolean : show ALERT box
*/
function debug_pause($pause=false) {
if ($pause) {
?>    
<script type="text/javascript">
    alert("Pause");
</script>    
<?PHP
}}
?>
