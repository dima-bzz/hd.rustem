<?php
$base = dirname(dirname(__FILE__));
include($base ."/conf.php");

$dbConnection = new PDO(
    'mysql:host='.$CONF_DB['host'].';dbname='.$CONF_DB['db_name'],
    $CONF_DB['username'],
    $CONF_DB['password'],
    array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")
);
$dbConnection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
$dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    for ($i = 1; $i <= 60; $i++)  {
            $stmt = $dbConnection->prepare('SELECT id, lock_by, ok_by, work_t, date_create
			    from tickets
			    where lock_by!=:n1 and ok_by =:n2');
	    $stmt->execute(array(':n1' => '0',':n2' => '0'));
	    $res1 = $stmt->fetchAll();
foreach($res1 as $row) {
    if (strtotime($row['date_create']) > strtotime("2016-04-26 16:00:00") ){
    $m=$row['id'];
    $j = $row['work_t'] + 1;

                $stmt = $dbConnection->prepare('update tickets set work_t=:j  where id=:m');
	$stmt->execute(array(':m' => $m, ':j'=>$j));
}
}
sleep(1);
}

?>
