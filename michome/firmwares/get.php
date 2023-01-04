<?

$f = scandir("modules/");
foreach ($f as $file){
    if(preg_match('/\.(mfir)/', $file)){ // Выводим только .png
        echo "modules/".$file.";";
    }
}

?>