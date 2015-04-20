<?php
require_once 'tools.php';
require_once 'db_helper.php';
require_once 'observers_manager.php';

$conn = DataConnection::getInstance()->getConnection();

filterPostVars('ac');
$query = $conn->prepare("INSERT INTO comments SET c_name=?, c_comment=?");
ObserverManager::getInstance($conn);
$ac_comment = EventManager::getInstance()->eventSubmit($ac_comment);
$query->bind_param("ss", $ac_name, $ac_comment);
$query->execute();

$title = "Comment added";
?>
<!DOCTYPE html>
<html>
<head>
  <title><?= $title ?></title>
</head>
<body>
Congrats, <?php echo $ac_name; ?>, Your comment has been published!<br />
<?php
  include('show_comments_lnk.php');
  include('add_comment_lnk.php');
?>
</body>
</html>
