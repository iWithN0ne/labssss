<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$xml = simplexml_load_file("cloud.xml");

if ($xml === false) {
    die("Ошибка загрузки XML");
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Облачные сервисы</title>
</head>
<body>

<h1>Облачные сервисы</h1>

<table border="1">
<tr>
    <th>Название</th>
    <th>Описание</th>
</tr>

<?php
foreach ($xml->service as $srv) {
    echo "<tr>";
    echo "<td>" . $srv->name . "</td>";
    echo "<td>" . $srv->description . "</td>";
    echo "</tr>";
}
?>

</table>

</body>
</html>
