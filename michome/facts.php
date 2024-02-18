<?php include_once(__DIR__."//../site/mysql.php"); ?>
<?php include_once(__DIR__."//../site/secur.php"); ?>
<?php require_once("lib/michom.php"); 
      $API = new MichomeAPI('localhost', $link);
	  header("Michome-Page: Facts-Page");
	  
	  $module = $_GET['module'];
	  $type = isset($_GET['type']) ? $_GET['type'] : "temp";
	  
      $year = $API->MaxMinValue($module, $type, (Date("Y")).'-01-01 00:00:00');
      $mount = $API->MaxMinValue($module, $type, (Date("Y")).'-'.(Date("m")).'-01 00:00:00');
?>
<!Doctype html>
<html>
	<head>
		<title>Интересные факты</title>
		<link rel="stylesheet" type="text/css" href="styles/style.css"/>
        <script type="text/javascript" src="/site/MicrofLibrary.js"></script>      
	</head>
	<body>
		<div class = "body_alfa"></div>
		<div class = "body">
			<div class = "title_menu">Управление Michome. Календарь информации. Интересные факты</div>
			<div style="text-align: left;" class = "com">
                <div style="width: 60%; height: 100%; text-align: left;" class = "components">
					<div class = "components_alfa">
						<div style="width: 100%" class = "components_text">
                            <p style="color: red;">Максимальное значение в этом году равно <?php echo round($year[0], 2); ?></p>
                            <p style="color: aqua;">Минимальное значение в этом году равно <?php echo round($year[1], 2); ?></p>
                            <br/>
                            <p style="color: red;">Максимальное значение в этом месяце равно <?php echo round($mount[0], 2); ?></p>
                            <p style="color: aqua;">Минимальное значение в этом месяце равно <?php echo round($mount[1], 2); ?></p>
                        </div>
					</div>
				</div>               
			</div>
		</div>
        
		<?php require_once(__DIR__."//../site/verhn.php");?>        
	</body>
</html>	
