<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Загружаем XML
$xml = simplexml_load_file("books.xml");

if ($xml === false) {
    die("Ошибка загрузки XML файла");
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Каталог книг</title>

    <!-- Yandex.Metrika counter -->
    <script>
        (function(m,e,t,r,i,k,a){
            m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};
            m[i].l=1*new Date();
            k=e.createElement(t),a=e.getElementsByTagName(t)[0];
            k.async=1;
            k.src=r;
            a.parentNode.insertBefore(k,a);
        })(window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");

        ym(108255752, "init", {
            clickmap:true,
            trackLinks:true,
            accurateTrackBounce:true,
            webvisor:true
        });
    </script>
    <noscript>
        <div>
            <img src="https://mc.yandex.ru/watch/108255752" style="position:absolute; left:-9999px;" alt="" />
        </div>
    </noscript>
    <!-- /Yandex.Metrika counter -->

</head>

<body>

<h1>Каталог книг</h1>

<table border="1" width="100%">
    <tr>
        <th>Автор</th>
        <th>Название</th>
        <th>Год</th>
        <th>Цена</th>
    </tr>

    <?php foreach ($xml->book as $book): ?>
    <tr>
        <td><?= htmlspecialchars((string)$book->author) ?></td>
        <td><?= htmlspecialchars((string)$book->title) ?></td>
        <td><?= htmlspecialchars((string)$book->pubyear) ?></td>
        <td><?= htmlspecialchars((string)$book->price) ?></td>
    </tr>
    <?php endforeach; ?>

</table>

</body>
</html>
