<?php
/** 
* class printPage for PrintOuts
* 
* @author  Werner Huysmans <werner.huysmans@skynet.be>
* @changed:   2010-05
* @access  public
* @version: 4.0
* @package mycmms40
* @subpackage library
* @filesource
* @todo evaluate if this module remains necessary when using Smarty
*/
class printPage { // Class for printing reports
    public $PrintoutTitle;
    public $stylesheet;
    public $TableTitle;
    public $NumberColumns;
    public $sql;
    public $header;
    public $docPath;
    public $wiki;

public function getData() {
    $DB=DBC::get(); 
    $result=$DB->query($this->sql);   
    if ($result) {
        $data=array();
        $data=$result->fetch(PDO::FETCH_ASSOC);
        return $data;
    } else {
        return false;
    }
} // End getData: stores data for presentation in custom report;
public function getHeaders() {
    $DB=DBC::get();
    $result=$DB->query($this->sql);
    $nCols=$result->columnCount();
    for ($i=0; $i<$nCols; $i++) {
        $meta[]=$result->getColumnMeta($i);
        $header[]=_($meta[$i]['name']);
    }
    return $header;
}
public function PrintTable_raw($row) {
    $tbl_data=new HTML_Table;
    $tbl_data->addRow(array($this->TableTitle),"colspan='$this->NumberColumns'","TH");
    $tbl_data->addRow($this->header,"","TH");
    for ($i=0; $i < $this->NumberColumns; $i++) {
        $row[$i]=nl2br($row[$i]);
    }
    $tbl_data->addRow($row,NULL,"TD");
    echo $tbl_data->toHtml();
}
public function PrintTable() {   
    $DB=DBC::get();
    $result=$DB->query($this->sql);
    $this->NumberColumns=count($this->header);
    // DebugBreak();
    $rows=$result->rowCount();
    
    if ($rows > 0) {
        $data=array();
        $tbl_data=new HTML_Table;
        $tbl_data->addRow(array($this->TableTitle),"colspan='$this->NumberColumns'","TH");
        $tbl_data->addRow($this->header,"","TH");
        foreach ($result->fetchAll(PDO::FETCH_NUM) as $row) {   
            $tbl_data->addRow($row,NULL,"TD");
        }
        echo $tbl_data->toHtml();
    } else {
        ;   // Do Nothing
    }
} // End PrintTable: prints standard table 
public function PrintTable_nl2br() {
    $DB=DBC::get();
    $result=$DB->query($this->sql);
    $this->NumberColumns=count($this->header);
    if ($result) {
        $data=array();
        $tbl_data=new HTML_Table;
        $tbl_data->addRow(array($this->TableTitle),"colspan='$this->NumberColumns'","TH");
        $tbl_data->addRow($this->header,"","TH");
        foreach ($result->fetchAll(PDO::FETCH_NUM) as $row) {   
            for ($i=0; $i < $this->NumberColumns; $i++) {
                $row[$i]=nl2br($row[$i]);
            }
            $tbl_data->addRow($row,NULL,"TD");
        }
        echo $tbl_data->toHtml();
    } else {
        ;   // Do Nothing
    }
} 
public function PrintDocuLink() {   
    $DB=DBC::get();
    $result=$DB->query($this->sql);
    $this->NumberColumns=count($this->header);
    if ($result) {
        $data=array();
        $tbl_data=new HTML_Table;
        $tbl_data->addRow(array($this->TableTitle),"colspan='$this->NumberColumns'","TH");
        $tbl_data->addRow($this->header,"","TH");
        foreach ($result->fetchAll(PDO::FETCH_NUM) as $row) {
            $row[1] = "<A HREF='{$this->docPath}{$row[0]}'>{$row[1]}</A>";
            $tbl_data->addRow($row,NULL,"TD");
        }
        echo $tbl_data->toHtml();
    } else {
        ;   // Do Nothing
    }
} // End PrintDocuLink: Prints Hyperlink
public function HeaderPage() {
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<title>PrintOut <?PHP echo $this->PrintoutTitle; ?></title>
<style>
<?PHP
    require($this->stylesheet);    
?>
</style>
</head>
<body>   
<?PHP
} // End HeaderPage 
public function HeaderPage_AutoPrint() {
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<script type="text/javascript">
function printout() {
    window.print();
    window.parent.close();
}
</script>
<title>PrintOut <?PHP echo $this->PrintoutTitle; ?></title>
</head>
<body onload="printout()">   
<?PHP    
}
public function FooterPage() {
    $basename=basename($_SERVER['SCRIPT_NAME']);
    $wiki_locale="wiki_".$_SESSION['locale'];
    $this->wiki=DBC::fetchcolumn("SELECT $wiki_locale FROM sys_security WHERE functionality='$basename'",0);
?>
<p><a href="<?PHP echo WIKI.$this->wiki; ?>" target="new">documentation</a></p>
</body>
</html>
<?PHP
} // End FooterPage()   
} // End printPage
?>
