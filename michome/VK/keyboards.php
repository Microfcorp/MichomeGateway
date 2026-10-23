<?php

function Keyboard_VK($link, $peer_id){
	// --- 1. СБОР ВСЕХ КНОПОК В ПЛОСКИЙ МАССИВ ---
	$flatButtons = [];
	$results = mysqli_query($link, "SELECT * FROM `botcmd` WHERE `Enabled` = 1");

	if ($results) {
		while($row = mysqli_fetch_assoc($results)) {
			// Замените 'title' и 'command' на реальные названия ваших колонок
			$buttonTitle = $row['Desc'];   
			$buttonCommand = '/'. $row['Name']; 

			// Создаем одну кнопку (без лишней вложенности рядов на этом этапе)
			$flatButtons[] = [
				'action' => [
					'type' => 'text',
					'label' => $buttonTitle,
					'payload' => json_encode(['command' => $buttonCommand], JSON_UNESCAPED_UNICODE)
				],
				'color' => 'default' // primary (синий), default (белый), positive (зеленый), negative (красный)
			];
		}
	}

	// Защита от переполнения: VK меню под полем ввода поддерживает максимум 40 кнопок
	$flatButtons = array_slice($flatButtons, 0, 40);

	// --- 2. АВТОМАТИЧЕСКАЯ ГРУППИРОВКА В СЕТКУ ---
	// Разделяем плоский массив на ряды. Цифра 4 означает "4 кнопки в одном ряду"
	// Если названия кнопок очень длинные, можно поставить 3 или 2
	$gridButtons = array_chunk($flatButtons, 4);

	// Формируем структуру клавиатуры
	$keyboard = [
		'one_time' => false, // Клавиатура не закроется после клика
		'buttons'  => $gridButtons, // Передаем сгруппированные ряды
		'inline'   => false  // Клавиатура под полем ввода (поддерживает до 40 кнопок)
	];

	// Кодируем для отправки в VK
	$keyboardJson = json_encode($keyboard, JSON_UNESCAPED_UNICODE);

	return $keyboardJson;
}

function Keyboard_Main(){
    return array(
                    array(
                        array(
                            'action' => array(
                                "type" => "text",
                                "payload" => array(
                                    "button" => "1"
                                ),
                                "label" => "Место в рейтинге"
                            ),
                            'color' => 'positive'
                        ),
                    ),
                    array(
                        array(
                            'action' => array(
                                "type" => "text",
                                "payload" => array(
                                    "button" => "1"
                                ),
                                "label" => "Управление аккустикой"
                            ),
                            'color' => 'positive'
                        ),
                        array(
                            'action' => array(
                                "type" => "text",
                                "payload" => array(
                                    "button" => "1"
                                ),
                                "label" => "Управление телевизором"
                            ),
                            'color' => 'positive'
                        ),
                    ),
                    array(
                        array(
                            'action' => array(
                                "type" => "text",
                                "payload" => array(
                                    "button" => "1"
                                ),
                                "label" => "Прогноз погоды на сегодня"
                            ),
                            'color' => 'positive'
                        ),
                        array(
                            'action' => array(
                                "type" => "text",
                                "payload" => array(
                                    "button" => "1"
                                ),
                                "label" => "Прогноз погоды на завтра"
                            ),
                            'color' => 'positive'
                        ),
                        array(
                            'action' => array(
                                "type" => "text",
                                "payload" => array(
                                    "button" => "1"
                                ),
                                "label" => "Погода"
                            ),
                            'color' => 'positive'
                        ),
                    ),
                    /*array(
                        array(
                            'action' => array(
                                "type" => "text",
                                "payload" => array(
                                    "button" => "1"
                                ),
                                "label" => "Включить свет 1"
                            ),
                            'color' => 'positive'
                        ),
                        array(
                            'action' => array(
                                "type" => "text",
                                "payload" => array(
                                    "button" => "1"
                                ),
                                "label" => "Включить свет 2"
                            ),
                            'color' => 'positive'
                        ),
                        array(
                            'action' => array(
                                "type" => "text",
                                "payload" => array(
                                    "button" => "1"
                                ),
                                "label" => "Включить свет 3"
                            ),
                            'color' => 'positive'
                        ),
                    ),*/
                    /*array(
                        array(
                            'action' => array(
                                "type" => "text",
                                "payload" => array(
                                    "button" => "1"
                                ),
                                "label" => "Выключить свет 1"
                            ),
                            'color' => 'negative'
                        ),
                        array(
                            'action' => array(
                                "type" => "text",
                                "payload" => array(
                                    "button" => "1"
                                ),
                                "label" => "Выключить свет 2"
                            ),
                            'color' => 'negative'
                        ),
                        array(
                            'action' => array(
                                "type" => "text",
                                "payload" => array(
                                    "button" => "1"
                                ),
                                "label" => "Выключить свет 3"
                            ),
                            'color' => 'negative'
                        ),
                    ),*/
                    array(
                        array(
                            'action' => array(
                                "type" => "text",
                                "payload" => array(
                                    "button" => "1"
                                ),
                                "label" => "Температура на улице"
                            ),
                            'color' => 'primary'
                        ),
                        array(
                            'action' => array(
                                "type" => "text",
                                "payload" => array(
                                    "button" => "1"
                                ),
                                "label" => "Температура в комнате"
                            ),
                            'color' => 'primary'
                        ),
                    ),
                    array(
                        array(
                            'action' => array(
                                "type" => "text",
                                "payload" => array(
                                    "button" => "1"
                                ),
                                "label" => "Давление в комнате"
                            ),
                            'color' => 'primary'
                        ),
                    ),
                );
}

function Keyboard_RC58(){
    return array(
                    array(
                        array(
                            'action' => array(
                                "type" => "text",
                                "payload" => array(
                                    "button" => "1"
                                ),
                                "label" => "Включить аккустику"
                            ),
                            'color' => 'primary'
                        ),
                    ),
                    array(
                        array(
                            'action' => array(
                                "type" => "text",
                                "payload" => array(
                                    "button" => "1"
                                ),
                                "label" => "Увеличить громкость"
                            ),
                            'color' => 'positive'
                        ),
                        array(
                            'action' => array(
                                "type" => "text",
                                "payload" => array(
                                    "button" => "1"
                                ),
                                "label" => "Уменьшить громкость"
                            ),
                            'color' => 'negative'
                        ),
                    ),
                    array(
                        array(
                            'action' => array(
                                "type" => "text",
                                "payload" => array(
                                    "button" => "1"
                                ),
                                "label" => "Переключить вход"
                            ),
                            'color' => 'positive'
                        ),
                        array(
                            'action' => array(
                                "type" => "text",
                                "payload" => array(
                                    "button" => "1"
                                ),
                                "label" => "Переключить режим"
                            ),
                            'color' => 'positive'
                        ),
                    ),
                    array(
                        array(
                            'action' => array(
                                "type" => "text",
                                "payload" => array(
                                    "button" => "1"
                                ),
                                "label" => "Звук с малинки"
                            ),
                            'color' => 'primary'
                        ),
                        array(
                            'action' => array(
                                "type" => "text",
                                "payload" => array(
                                    "button" => "1"
                                ),
                                "label" => "Звук с пеки"
                            ),
                            'color' => 'primary'
                        ),
                    ),
                    array(
                        array(
                            'action' => array(
                                "type" => "text",
                                "payload" => array(
                                    "button" => "1"
                                ),
                                "label" => "Домой"
                            ),
                            'color' => 'secondary'
                        ),
                    ),
                );
}

function Keyboard_SamsungTV(){
    return array(
                    array(
                        /*array(
                            'action' => array(
                                "type" => "text",
                                "payload" => array(
                                    "button" => "1"
                                ),
                                "label" => "Включить ТВ"
                            ),
                            'color' => 'positive'
                        ),*/
                        array(
                            'action' => array(
                                "type" => "text",
                                "payload" => array(
                                    "button" => "1"
                                ),
                                "label" => "Выключить ТВ"
                            ),
                            'color' => 'negative'
                        ),
                    ),
					array(
                        array(
                            'action' => array(
                                "type" => "text",
                                "payload" => array(
                                    "button" => "1"
                                ),
                                "label" => "Канал +"
                            ),
                            'color' => 'positive'
                        ),
                        array(
                            'action' => array(
                                "type" => "text",
                                "payload" => array(
                                    "button" => "1"
                                ),
                                "label" => "Канал -"
                            ),
                            'color' => 'positive'
                        ),
                    ),
                    array(
                        array(
                            'action' => array(
                                "type" => "text",
                                "payload" => array(
                                    "button" => "1"
                                ),
                                "label" => "Переключить вход на HDMI"
                            ),
                            'color' => 'secondary'
                        ),
                        array(
                            'action' => array(
                                "type" => "text",
                                "payload" => array(
                                    "button" => "1"
                                ),
                                "label" => "Переключить выход на DTV"
                            ),
                            'color' => 'secondary'
                        ),
                    ),
                    array(
                        array(
                            'action' => array(
                                "type" => "text",
                                "payload" => array(
                                    "button" => "1"
                                ),
                                "label" => "Увеличить громкость ТВ"
                            ),
                            'color' => 'positive'
                        ),
                        array(
                            'action' => array(
                                "type" => "text",
                                "payload" => array(
                                    "button" => "1"
                                ),
                                "label" => "Выключить звук ТВ"
                            ),
                            'color' => 'secondary'
                        ),
                        array(
                            'action' => array(
                                "type" => "text",
                                "payload" => array(
                                    "button" => "1"
                                ),
                                "label" => "Уменьшить громкость ТВ"
                            ),
                            'color' => 'negative'
                        ),
                    ),
                    array(
                        array(
                            'action' => array(
                                "type" => "text",
                                "payload" => array(
                                    "button" => "1"
                                ),
                                "label" => "Домой"
                            ),
                            'color' => 'secondary'
                        ),
                    ),
                );
}
?>
