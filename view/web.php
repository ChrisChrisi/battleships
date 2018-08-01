<html>
<body>

<pre>
<?php
    if ($this->message != false) {
        echo $this->message;
    }
    echo NEW_LINE.NEW_LINE.$data;
?>
</pre>
<br>
<form name="input" action="index.php" method="post">
    <?php echo Messages::getMessage('prompt')?> <input type="input" size="5" name="coord" autocomplete="off" autofocus>
    <input type="submit">
</form>
</body>
</html>
