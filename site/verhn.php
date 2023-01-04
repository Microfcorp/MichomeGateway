<!--<script type="text/javascript" src="//www.gstatic.com/firebasejs/3.6.8/firebase.js"></script>
<script type="text/javascript" src="/site/MicrofLibrary.js"></script>-->
<script>
/*// firebase_subscribe.js
firebase.initializeApp({
    messagingSenderId: '212264121998'
});

// браузер поддерживает уведомления
// вообще, эту проверку должна делать библиотека Firebase, но она этого не делает
if ('Notification' in window) {
    var messaging = firebase.messaging();

    // пользователь уже разрешил получение уведомлений
    // подписываем на уведомления если ещё не подписали
    if (Notification.permission === 'granted') {
        subscribe();
    }
}

function subscribe() {
    // запрашиваем разрешение на получение уведомлений
    messaging.requestPermission()
        .then(function () {
            // получаем ID устройства
            messaging.getToken()
                .then(function (currentToken) {
                    console.log(currentToken);

                    if (currentToken) {
                        sendTokenToServer(currentToken);
                    } else {
                        console.warn('Не удалось получить токен.');
                        setTokenSentToServer(false);
                    }
                })
                .catch(function (err) {
                    console.warn('При получении токена произошла ошибка.', err);
                    setTokenSentToServer(false);
                });
    })
    .catch(function (err) {
        console.warn('Не удалось получить разрешение на показ уведомлений.', err);
    });
}

// отправка ID на сервер
function sendTokenToServer(currentToken) {
    if (!isTokenSentToServer(currentToken)) {
        console.log('Отправка токена на сервер...');

        var url = '/michome/api/token.php'; // адрес скрипта на сервере который сохраняет ID устройства       
        
        postAjax(url, 'POST', "token="+currentToken, function(){});

        setTokenSentToServer(currentToken);
    } else {
        console.log('Токен уже отправлен на сервер.');
    }
}

// используем localStorage для отметки того,
// что пользователь уже подписался на уведомления
function isTokenSentToServer(currentToken) {
    return window.localStorage.getItem('sentFirebaseMessagingToken') == currentToken;
}

function setTokenSentToServer(currentToken) {
    window.localStorage.setItem(
        'sentFirebaseMessagingToken',
        currentToken ? currentToken : ''
    );
}*/
</script>
<div class = "menu">		
            <div class = "menu_items"><a href="index.php">Главная</a></div>
            <!-- <div class = "menu_items"><button onclick="subscribe();" type="button" id="subscribe">Включить уведомления</button></div> -->
			<div class = "menu_items"><a href="room.php">Комнаты</a></div>
			<div class = "menu_items"><a href="calendar.php?nonephoto=1">Календарь информации</a></div>
			<div class = "menu_items"><a href="logger.php?p=0">Логи</a></div>
			<div class = "menu_items"><a href="module.php">Модули</a></div>
            <div class = "menu_items"><a href="scenes.php">Сценарии</a></div>
            <div class = "menu_items"><a href="/">Выход</a></div>
</div> 
