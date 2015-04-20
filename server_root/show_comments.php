<?php

require_once 'db_helper.php';

$conn = DataConnection::getInstance()->getConnection();

$query = $conn->prepare("SELECT c_name, c_comment, c_added_at FROM comments");
$query->execute();

$result = $query->get_result();

$title = "Comments";

?>
<!DOCTYPE html>
<html>
<head>
  <title><?= $title ?></title>
</head>
<body>
  <div>
<?php
    while ($row = $result->fetch_assoc()) {
        echo "<p>Name: {$row['c_name']} ({$row['c_added_at']})<br>{$row['c_comment']}</p>";
    }
?>
  </div>
<?php
    include('show_comments_lnk.php');
    include('add_comment_lnk.php');
?>
</body>
</html>
