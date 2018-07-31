<html>
<body>

<pre>
<?php
    if ($message != false) {
        echo $message;
    }
    echo chr(10).chr(10).$data;
?>
</pre>
<br>
<form name="input" action="index.php" method="post">
    Enter coordinates (row, col), e.g. A5 <input type="input" size="5" name="coord" autocomplete="off" autofocus>
    <input type="submit">
</form>
</body>
</html>
