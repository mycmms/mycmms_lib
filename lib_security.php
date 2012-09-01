<?php
/** 
* Library Security
* @author  Werner Huysmans <werner.huysmans@skynet.be>
* @changed:   2010-05
* @access  public
* @version: 4.0
* @package mycmms40
* @subpackage library
* @filesource
*/
/** 
* Puts a warning screen on the window when the user is not allowed to execute an action.
* @action string: PHP module
*/
function warning_screen($action) {   
    if (empty($_SESSION['group'])) {
        $login_error="This operation is not allowed. Please log in!";
    } else {
        $login_error=$_SESSION['user']." with profile ".$_SESSION['profile']." is not allowed to execute ".$action;
    }
?>
<script>
    alert('<?PHP echo $login_error; ?>');
</script>
<?PHP    
    exit;
}
/** 
* Operation_allowed? Check if user is allowed to do this. 
* Function was rewritten to handle INTEGER profiles.
* $functionality string: PHP module
*/
function operation_allowed($functionality) {    
    $DB=DBC::get();
    $profile=$_SESSION['profile'];
    $path_parts=pathinfo($functionality);
    $action=$path_parts['basename'];  // Only want wo-basic.php ...
    if ($profile==1) {  // Backdoor
        $bPermission=TRUE;
    } else {    // Normal security check
        $numrecs=DBC::fetchcolumn("SELECT COUNT(*) FROM sys_security WHERE (profile & {$_SESSION['profile']}) <> 0 AND functionality like '$action'",0);
        if ($numrecs!=0) {
            $bPermission = TRUE; 
        } else { 
            $bPermission = FALSE; 
            warning_screen($action);
        }    
    }
    // Is the user logged in ?    If not permission is also refused !
    if (empty($profile)) {
        $bPermission = FALSE;
    }
    if (!$bPermission)
    {   warning_screen($login,$group,$action);
    } else {
        return $bPermission;
    }
} // End operation_allowed
?>
