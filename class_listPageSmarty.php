<?php
/**  
* Class definition for listPage
* @author  Werner Huysmans <werner.huysmans@skynet.be>
* @access  public
* @package mycmms40
* @subpackage library
* @filesource
*/
/**
* Class listPage 
*/
class ListPageSmarty {
    public $PageTitle; // SET in list.php     
    public $authorisation; // SET in list.php
    public $PageLogo; // SET in list.php
//    public $stylesheet; // SET in list.php
//    public $stylesheet_exc; // SET in list.php
//    public $Fld_Task; // SET in list.php
//    public $Fld_Prio; // SET in list.php
//    public $Fld_Status; // SET in list.php
    public $SQLname; // $_SESSION['query_name']
    private $SQL_RAW;   // Edit what is stored in the database
    private $num_cols;  // Number of columns
    private $numRecords; // Number of Records CALCULATED getTableData
    private $numPages; // Total number of pages CALCULATED getTableData
    private $actualPage; // Actual page
    private $gtb_table; // Table 
    private $navbar; // Navigation Bar
    public $rootdirs; // Root directories
    public $title; // Tab 
    public $DEBUG;
    public $wiki;
    private $smarty_rows;
    public $template;
/**
* Page initialization
* Storing temporary variables in SESSION.
*/
private function initPage() { // Page initialisation
    $_SESSION['last_query']=sprintf("%s%s%s","http://",$_SERVER['HTTP_HOST'],$_SERVER['REQUEST_URI']);
    if(empty($_GET['query_name']) && empty($_SESSION['query_name']) && !empty($_SESSION['group'])) {    
        exit;     
    }
    if(!empty($_GET['query_name']) || !empty($_POST['query_name'])) {    
        $_SESSION['query_name']=$_REQUEST['query_name'];
        unset($_GET['query_name']);    
    } 
    if(isset($_REQUEST['order_by'])) {
        if (!isset($_SESSION['order_by'])) {
            $_SESSION['order_by']=$_REQUEST['order_by']." ASC";    
        } else {
            if (strpos($_SESSION['order_by'],"ASC") != 0) {
                $_SESSION['order_by']=$_REQUEST['order_by']." DESC";    
            } else {
                $_SESSION['order_by']=$_REQUEST['order_by']." ASC";                                    
            }
        }
    } // Ordering the list
    if(!empty($_REQUEST['criteria'])) {
        $_SESSION['criteria']=$_REQUEST['criteria'];    
    } // Search criteria
    if(empty($_REQUEST['page'])) {
        $this->actualPage=0;
    } else {
        $this->actualPage=$_REQUEST['page'];
    }
    $this->SQLname=$_SESSION['query_name'];
    $this->getTableData(); 
} // End initPage       
/**
* Getting table data from table sys_queries
* 
*/
private function get_table_blueprint() {    
    $DB=DBC::get();
    $result=$DB->query("SELECT * FROM sys_queries WHERE name like '{$this->SQLname}'");
    if(!$result)
    {   
?>
<p>Unknown Query</p>
url: <?PHP echo $url; ?><br>
query_name: <?PHP echo $query_name; ?><br>
query_sql: <?PHP echo $blueprint_sql; ?><br>
<hr>
<?PHP 
        exit;
    }
    $gtb_table=$result->fetch(PDO::FETCH_OBJ);
    eval('$gtb_table->mysql="'.$gtb_table->mysql.'";');
    $this->SQL_RAW=$gtb_table->mysql;  
    # Get column attributes         
    $gtb_table->col_attributes=unserialize($gtb_table->col_attributes); 
    $gtb_table->specific_attributes=unserialize($gtb_table->specific_attributes);
    $fields=substr($gtb_table->mysql,0,strpos($gtb_table->mysql,' FROM')); 
    $needle = "|.*AS '(.*)'|U"; //Regexp to match all aliases
    preg_match_all($needle, $fields, $col_names, PREG_PATTERN_ORDER ); //Store the aliases in the array '$col_names'
    $gtb_table->col_names=$col_names[1]; //make the array of column names a property of our table for consistency
    $keys=array_keys($gtb_table->col_names);
    $gtb_table->col_link = array();
    foreach($keys as $key)
    {   $col=$gtb_table->col_names[$key];
        $gtb_table->col_link[$key] = "<a href='".$_SERVER['PHP_SELF']."?order_by=".urlencode($col)."'>"._($col)."</a>";
    }
    return $gtb_table;
} // End get_table_blueprint    
/** 
* Purge all non-aliased columns.
* 
* @param mixed $pc_all_rows
* @param mixed $pc_table
*/
private function purge_columns($pc_all_rows,$pc_table) { // Purge   
    $i=0;
    foreach($pc_all_rows as $ele) //iterate through each record returned
    {   $pc_rows[$i] = array();
        foreach($pc_table->col_names as $column) { //iterate through each column of each record
            array_push($pc_rows[$i], $ele[$column]); 
        }
        $i++; 
    }    
    return $pc_rows;
} // End function purge_columns
/**
* Ordering the list when clicking a column
* @param mixed $ob_sql
* @param mixed $ob_col
*/
private function order_by($ob_sql, $ob_col) { // Ordering 
    if(stristr($ob_sql, 'ORDER BY')) {  //if there already is an order by cluase
        $first = substr($ob_sql, 0, strpos($ob_sql, 'ORDER BY')); //Chop everything after 'ORDER BY';
        $regexp = "/ORDER BY.*(ASC|DESC)(.*)/";
        if(preg_match($regexp, $ob_sql, $end)) {
            $last = $end[2]; }
            $sql=$first." ORDER BY $ob_col ".$last;
        } else {
            $sql=$ob_sql." ORDER BY $ob_col"; 
        }
    return $sql;
} // End order_by   
/**
* Limiting the number of records
* 
* @param mixed $ob_sql
* @param mixed $from
* @param mixed $number
*/
private function limit_on($ob_sql,$from,$number) { // Limit
    if(stristr($ob_sql, 'LIMIT')) { //if there already is an order by cluase
        $first = substr($ob_sql, 0, strpos($ob_sql, 'LIMIT')); //Chop everything after 'ORDER BY';
        $regexp = "/LIMIT *(.*)/";
        if(preg_match($regexp, $ob_sql, $end))
        {    $last = $end[2]; }
        $sql = $first . " LIMIT $from, $number " . $last;
    } else {    
        $sql = $ob_sql . " LIMIT $from, $number";
    }
    return $sql;
} // End limit_on  
/**
* Get data on the table data
* 
*/
private function getTableData() {
    $this->gtb_table=$this->get_table_blueprint();
    $this->numRecords=DBC::numrows($this->gtb_table->mysql);
    $this->numPages=ceil($this->numRecords/ITEMS_PER_PAGE);
    if(!empty($_SESSION['order_by'])) {    
        $this->gtb_table->mysql=$this->order_by($this->gtb_table->mysql,$_SESSION['order_by']); 
    }
    if($this->numPages > 1) {
        $this->gtb_table->mysql=$this->limit_on($this->gtb_table->mysql,$this->actualPage,ITEMS_PER_PAGE); 
    }
} // End getTableData 
/** 
* Print NavigationBar
*/
private function NavigationBar() {
    require("pagination.php");
    return PHPagination($this->actualPage,$this->numRecords,$_SERVER['PHP_SELF']."?page=",'',ITEMS_PER_PAGE,true);
}
/** 
* Get the list navigation options
*/
private function GetNavigationOptions() {
    $DB=DBC::get();
    $rootdirs=$this->rootdirs;
    $sql="SELECT action,caption FROM sys_navoptions WHERE mode='{$this->gtb_table->mode}' ORDER BY menuorder";
    $result_options=$DB->query($sql);
    $numrecs=DBC::numrows($sql);
    $i=0;
    if ($numrecs>0) {
        foreach ($result_options->fetchAll(PDO::FETCH_OBJ) as $optionrow) {
            eval('$optionrow->action = "'.$optionrow->action.'";'); 
            $options[$i]['action']=$optionrow->action;
            $options[$i]['caption']=$optionrow->caption;
            $i++;
        }
    }
    return $options;  
}
/**
* Display Data
* 
*/
private function GetDataRecords() {
    $DB=DBC::get();
    $result=$DB->query($this->gtb_table->mysql);
    if ($this->numRecords == 0) {    
        $empty = TRUE; 
    } 
    $all_rows = array();
    foreach ($result->fetchAll(PDO::FETCH_ASSOC) as $row) {    
        array_push($all_rows, $row); 
    }
    $this->smarty_rows=$this->purge_columns($all_rows,$this->gtb_table); 
}
// Public functions
/**
* Display Page with Template
*/
public function DisplayPageSmarty($template_section) {
    $this->initPage();
    $this->GetDataRecords();
    require ("setup.php");
    $tpl=new smarty_mycmms();
    $tpl->debugging=false;
    $tpl->caching=false;
    $tpl->assign('DEBUG',true);
    $tpl->assign('temp',$this->SQLname);
    $tpl->assign('stylesheet',STYLE_PATH."/".CSS_LISTS);
    $tpl->assign('stylesheet_exception',STYLE_PATH."/".CSS_LIST_TD);
    $tpl->assign('column_titles',$this->gtb_table->col_link);
    $tpl->assign('TableData',(array)$this->gtb_table);
    $tpl->assign('SQL_RAW',$this->SQL_RAW);
    $tpl->assign('data_records',$this->smarty_rows);
    $tpl->assign('list_options',$this->GetNavigationOptions());
    $tpl->assign('column_formats',$this->gtb_table->specific_attributes);
    $tpl->assign('navigation_bar',$this->NavigationBar());
    $tpl->assign('session_data',$_SESSION);
    $tpl->assign('session_id',session_id());
    // $tpl->assign('locale_data',$_SERVER['HTTP_ACCEPT_LANGUAGE']);
    $tpl->assign('locale_data',getenv("LC_ALL"));
    $tpl->assign('wiki_documentation',WIKI."/list");
    // Use the specific template
    if (empty($template_section)) {
        $tpl->assign('template_section',"framework_list_section.tpl");
    } else {
        $tpl->assign('template_section',$template_section);
    }
    // 
    if ($this->template=="STANDARD") {
        $tpl->display("framework_list.tpl");
    } else {
        $tpl->display("framework_list.tpl");    
    }
}
} // Enc Class
?>