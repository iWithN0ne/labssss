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
<html>
<head>
    <meta charset="UTF-8">
    <title>Каталог книг</title>

    <!-- Yandex.Metrika counter -->
    <script type="text/javascript">
        (function(m,e,t,r,i,k,a){
            m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};
            m[i].l=1*new Date();
            for (var j = 0; j < document.scripts.length; j++) {
                if (document.scripts[j].src === r) { return; }
            }
            k=e.createElement(t),a=e.getElementsByTagName(t)[0],
            k.async=1,k.src=r,a.parentNode.insertBefore(k,a)
        })(window, document,'script','https://mc.yandex.ru/metrika/tag.js?id=108255752', 'ym');

        ym(108255752, 'init', {
            ssr:true,
            webvisor:true,
            clickmap:true,
            ecommerce:"dataLayer",
            referrer: document.referrer,
            url: location.href,
            accurateTrackBounce:true,
            trackLinks:true
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

<?php
foreach ($xml->book as $book) {
    echo "<tr>";
    echo "<td>" . $book->author . "</td>";
    echo "<td>" . $book->title . "</td>";
    echo "<td>" . $book->pubyear . "</td>";
    echo "<td>" . $book->price . "</td>";
    echo "</tr>";
}
?>

</table>

</body>
</html>
