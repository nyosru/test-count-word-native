<?php

$memory = memory_get_usage();
$start = microtime(true);

// книжка
$bookFile = './book/kastanieda-vnutrenniy.fire.txt';
// словарь
$wordsFile = './listWordSearch.txt';

$words = explode("\n", file_get_contents($wordsFile));

echo '<h2>Слова что будем искать в тексте</h2>';
echo '<pre style="display; block; max-height:200px; padding: 10px; border: 1px solid green; overflow: auto;" >', print_r($words, true), '</pre>';

echo '<style> div{ display:inline-block; width: 250px; } </style>';

// сразу показываем что за слова будем искать
flush();

function readTheFile($path)
{
    // читаем по 4 строки .. получается норм по времени .. 30 сек
    $file = new SplFileObject($path);
    while (!$file->eof()) {
        $str = $file->current();
        $file->next();

        for ($i = 0; $i < 3; $i++) {
            $str .= ' ' . $file->current();
            $file->next();
        }
        yield $str;
    }
}

$buffer = "";

// тут соберём сколько каких слов найдено
$result = [];
foreach ($words as $word) {
    if (!empty($word))
        $result[$word] = 0;
}

$iterator = readTheFile($bookFile);

// прокручиваем весь файл (так для экономии памяти)
foreach ($iterator as $str) {

    // прокручиваем все слова и ищем регулярками вхождения
    foreach ($words as $word) {

        if (empty($word))
            continue;

        // ищем
        preg_match_all("/\b" . trim($word) . "\b/ui", $str, $matches);

        // если найдены .. то плюсуем к результату
        if (!empty($matches[0])) {
            $result[$word] += sizeof($matches[0]);
        }
    }
    // echo ' . ';
    // echo ' <div>. '. strlen($str) .' +++ '. round(( $last - ( round( microtime(true) - $start, 4) ) ),4).' </div> ';
    // $last = round(microtime(true) - $start, 4);
    // flush();
}

echo '<h2>Каких слов сколько нашли</h2>';
echo '<pre style="display; block; max-height:200px; padding: 10px; border: 1px solid green; overflow: auto;" >', print_r($result, true), '</pre>';
echo '<Br/>';
echo '<Br/>';
echo '<Br/>';
echo 'Размер файла книжки: ' . round(filesize($bookFile) / 1024 / 1024, 2) . ' Мб';
echo '<Br/>';
echo 'Сколько слов ищем: ' . sizeof($words);
echo '<Br/>';
echo 'Время выполнения скрипта: ' . round(microtime(true) - $start, 4) . ' сек.';
echo '<br/>';
echo 'Использовано памяти: ' . round((memory_get_usage() - $memory) / 1024, 2) . ' Kбайт';
