<html>
<body>
<?php
if ($this->remainingShips > 0) {
if ($this->message != false) {
    echo $this->message.NEW_LINE;
}
echo '<pre>';
echo NEW_LINE . NEW_LINE . $data;
?>
</pre>
<form name="input" action="index.php" method="post">
    <?php echo Messages::getMessage('prompt'); ?>
    <input type="input" size="5" name="coord" autocomplete="off" autofocus>
    <input type="submit">
</form>
<?php } else { ?>
<p><?php echo $this->message;?></p>
<p><a href="index.php"><?php  echo Messages::getMessage('play_again'); ?></a></p>
<?php } ?>
</body>
</html>
