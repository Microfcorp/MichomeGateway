<?
session_start();

$CurrentURLNOAuth = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

$ipHost = explode('.', $_SERVER['REMOTE_ADDR']);

if(empty($_GET['auth'])){
    
    if((!empty($_SESSION['login']) && !empty($_SESSION['password'])) || ($ipHost[0] == "192" && $ipHost[1] == "168" && $ipHost[2] == "0")){
        if(isset($_SESSION['login']) && isset($_SESSION['password']) && $_SESSION['login'] == "admin" && $_SESSION['password'] == "michome147"){

        }
		elseif($ipHost[0] == "192" && $ipHost[1] == "168" && $ipHost[2] == "0"){
			unset($_SESSION['login']);
            unset($_SESSION['password']);
			$_SESSION['login'] = "admin"; 
			$_SESSION['password'] = "michome147";
		}
        else{header('Location: /site/secur.php?auth=1&url='.$CurrentURLNOAuth,true, 200); exit();}
    }

    elseif(!empty($_POST['login']) & !empty($_POST['password'])){
        if($_POST['login'] == "admin" & $_POST['password'] == "michome147"){
            unset($_SESSION['login']);
            unset($_SESSION['password']);
            
            //header('Location: http://localhost/redirect2.php',true, 200);		
            $_SESSION['login'] = $_POST['login'];
            $_SESSION['password'] = $_POST['password'];
            echo($_GET['url']);
            //header('Location: '.$_GET['url'],true, 200);		
        }
        else{exit("Error");}
    }
    else{header('Location: /site/secur.php?auth=1&url='.$CurrentURLNOAuth,true, 200); exit();}
}
?>
<?php if (!empty($_GET['auth'])) { ?>
<html>
<head>
<title>Авторизация</title>
<script src="/site/MicrofLibrary.js"></script>
<script>
function Login(frm){
	postAjax("secur.php?url=<?php echo $_GET['url']; ?>", "POST", getRequestBody(frm), function(d){if(d!="Error"){document.location=d;}else{alert("Error");}});
}
</script>
</head>

<body>
<H2>Для доступа к данной странице ил действию вам необходимо авторизоваться</H2>
<form id="form" name="form">
<p><input type="text" name="login" placeholder="Ваш логин" /></p>
<p><input type="Password" name="password" placeholder="Ваш пароль" /></p>
<p><input type="Submit" OnClick="Login(document.getElementById('form'))" name="button" value="Войти" /></p>
</form>
</body>
</html>
<?php exit(); }?>