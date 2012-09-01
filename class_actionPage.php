<?php
/** 
* actionPage: class for action pages 
* 
* @author  Werner Huysmans <werner.huysmans@skynet.be>
* @access  public
* @package mycmms40
* @subpackage library
* @filesource
*/
/**
* ActionPage: handling several sequential actions...
*/
class actionPage {
    public $stylesheet;
    public $table_sql;
    public $table_header;
    public $exec_sql;
    public $exec_array;
    public $calendar;
    public $wiki;
    
/**
* Show Table based on preset SQL, Header
*     
*/
public function show_table() {
    $DB=DBC::get();
    $result=$DB->query($this->table_sql);
    if ($result) {   
        $data=array();
        $tbl_data=new HTML_Table();
        $tbl_data->addRow($this->table_header,'','TH');   
        foreach ($result->fetchAll(PDO::FETCH_NUM) as $row) {
            $tbl_data->addRow($row);
        } 
        for ($i=0;$i < $tbl_data->getRowCount(); $i++) {
        if ($i%2) {    
            $tbl_data->setRowAttributes($i,array("BgColor"=>"#C0FFC0"),true);
        }}
        echo $tbl_data->toHtml();
    }
} // End show_table
public function get_data() {
    $DB=DBC::get();
    $result=$DB->query($this->exec_sql);
    if ($result) {
        return $result->fetch(PDO::FETCH_OBJ);
    } else {
        return "";
    }
}   // End get_data
public function execute() {
    $DB=DBC::get();
    try {
        $st=$DB->prepare($this->exec_sql);
        $st->execute($this->exec_array);
        // echo $st->rowCount();
    } catch (PDOException $e) {
        print("<p class='error'>PDO Error: ".$e->getMessage()."</p>"); 
    }
} // End execute
public function HeaderPage($header) {
?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<?php
    if ($this->calendar) {
?>
    <script src="../libraries/functions.js"></script>
    <script src="../libraries/calendar.js" type="text/javascript"></script>
    <script src="../libraries/calendar-en.js" type="text/javascript"></script>
    <script src="../libraries/calendar-setup.js" type="text/javascript"></script>
<?PHP
    }
?>
<style>
<?PHP
    require($this->stylesheet);
?>
</style>    
</head> 
<body>
<?php
    if ($this->calendar) {
        require(CMMS_STYLESHEET."/calendar-win2K-1.css");
    }
?>
<h1 class="action"><?PHP echo _($header); ?></h1>    
<?PHP    
} // End HeaderPage
public function ActionHeader($header) {
?>
<h3 class="action"><?PHP echo $header; ?></h3>
<?PHP        
} // End ActionHeader
public function FooterPage() {
    $basename=basename($_SERVER['SCRIPT_NAME']);
    $wiki_locale="wiki_".$_SESSION['locale'];
    $this->wiki=DBC::fetchcolumn("SELECT $wiki_locale FROM sys_security WHERE functionality='$basename'",0);
?>
<p><a href="<?PHP echo WIKI.$this->wiki; ?>" target="new">documentation</a></p>
<div class="location" id="title"><?PHP echo $_SESSION['PDO_ERROR']; ?></div>
</body>
</html>
<?PHP    
    unset($_SESSION['PDO_ERROR']);
} // End FooterPage   
}   // End class
?>
