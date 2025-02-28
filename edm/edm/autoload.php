<?
    function edm_autoload($class){
        $sinif          =   explode("\\",$class);
        $include_path   =   realpath(".")."/edm/";
        $dosya          =   $include_path.$sinif[1].".php";
        if (file_exists($dosya)){
            include_once($dosya);
            return true;
        }else{
            return false;
        }
    }
    spl_autoload_register("edm_autoload");
?>