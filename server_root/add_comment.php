<!DOCTYPE html> 
<html>
<head>
</head>
<body>
 <form action="comment.php" method="post">
  <input type="text" name="ac_name" />
  <textarea name="ac_comment"></textarea>
  <input type="submit" value="Post comment" name="ac_act" />
 </form>
<?php
  include('show_comments_lnk.php');
  include('add_comment_lnk.php');
?>
</body>

</html>

