<?php
/** 
* inputPage: class for input pages using Smarty Templates
* 
* @author  Werner Huysmans <werner.huysmans@skynet.be>
* @access  public
* @package mycmms40
* @subpackage library
* @filesource
*/
/**
* Class InputPageSmarty
*/
class inputPageSmarty {
    public $data_sql;  // SELECT statement for 
    public $input1;    // Set to $_REQUEST or $_SESSION
    public $input2;
    private $wiki;
/** 
* Constructor
*     
*/
function __construct() {
    $this->input1=$_SESSION['Ident_1'];
    $this->input2=$_SESSION['Ident_2'];
    $basename=basename($_SERVER['SCRIPT_NAME']);
    $wiki_locale="wiki_".$_SESSION['locale'];
    $this->wiki=DBC::fetchcolumn("SELECT $wiki_locale AS 'wiki' FROM sys_security WHERE functionality='$basename'",0);
} // EO __construct
/**
* Gets data
* 
* @param mixed $in1
* @param mixed $in2
* @return mixed
*/
public function get_data($in1, $in2) {
    $DB=DBC::get();
    $result=$DB->query($this->data_sql);
    if ($result) {
        $data=$result->fetch(PDO::FETCH_ASSOC);
    }
    return $data;
} // EO get_data
/**
* Validate the transmitted values -- will be overridden
* 
*/
public function validate_form() {
    // Must be overridden when used
    $errors = array();
    return $errors;
} // End validate_form
/**
* Page Content : see Smarty
* 
*/
public function page_content() {} // End page_content, this function is empty, since handled by Smarty
private function page_footer($errors) {
    $tpl=new smarty_mycmms();
    $tpl->assign("wiki",WIKI.$this->wiki);
    $tpl->assign("errors",$errors);
    $tpl->display("framework_inputPageFooter.tpl");
    unset($_SESSION['PDO_ERROR']);
} // End page_footer
public function display_form($errors) {
    $this->page_content();
    $this->page_footer($errors);
}
public function process_form() {} // End process_form
/**
* Process flow
* - If 1st time show Form
* - Verify input
* - Do data handling: form_save; process and reshow form
* - Do data handling: close; process but then close form
* 
*/
public function flow() {
    if ($_SERVER['REQUEST_METHOD']=='GET') 
    {   $this->display_form(array());      
    } else {
        $errors=$this->validate_form();
    if (count($errors)) {
        $this->display_form($errors);  // Redisplay 
    } else {
        if (isset($_REQUEST['form_save'])) {
            $data=$this->process_form($_REQUEST);
?>
<script type="text/javascript">
function reload() {    
    window.location="<?PHP echo $_SERVER['SCRIPT_NAME']; ?>"; 
} 
setTimeout("reload();", 250);
</script>
<?PHP        
        } 
        if (isset($_REQUEST['close'])) {
            $data=$this->process_form($_REQUEST);
?>
<script type="text/javascript">
    window.parent.close();
</script>
<?PHP            
}}}
} // End flow
}   // End class
?>
