<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$xml = simplexml_load_file("books.xml");

if ($xml === false) {
    die("Ошибка загрузки XML");
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Книги</title>
</head>
<body>

<h1>Каталог книг</h1>

<table border="1">
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
