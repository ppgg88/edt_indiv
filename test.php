<?php
$ladate = new DateTime();
$ladate->setISOdate(strftime("%Y"), $_GET['semaine']);
$s = date_format($ladate, 'Y-m-d');
$ladate = strftime("%Y-%M-%d", $ladate->getTimestamp());
$d = new DateTime('Monday this week '.$s);
$start = $d
$d->add(new DateInterval('P7D'));
$end = $d;

?>