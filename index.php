<?php 

include_once('SimpleImage.php');

$page1 = 'p1.jpg';
$page2 = 'p2.jpg';
$page3 = 'p3.jpg';
$page4 = 'p4.jpg';

$page1Thumbnail = "p1-t.jpg";
$page2Thumbnail = 'p2-t.jpg';
$page3Thumbnail = "p3-t.jpg";
$page4Thumbnail = 'p4-t.jpg';

$page1SimpleImage = new SimpleImage($page1);
$result = $page1SimpleImage->generateThumbnail($page1Thumbnail);

if ($result) {
	echo "t1 success";
} else {
	echo "t1 fail";
}

echo "<br />";

$page2SimpleImage = new SimpleImage($page2);
$result = $page2SimpleImage->generateThumbnail($page2Thumbnail);

if ($result) {
	echo "t2 success";
} else {
	echo "t2 fail";
}

echo "<br />";

$page1ThumbnailSimpleImage = new SimpleImage($page1Thumbnail);
$t1t2 = $page1ThumbnailSimpleImage->makeCopy('t1t2.jpg');

$result = $t1t2->rightAppend($page2Thumbnail);

if ($result) {
	echo "append t1 and t2 success";
} else {
	echo "append t1 and t2 fail";
}

echo "<br />";


$page3SimpleImage = new SimpleImage($page3);
$result = $page3SimpleImage->generateThumbnail($page3Thumbnail);

if ($result) {
	echo "t3 success";
} else {
	echo "t3 fail";
}

echo "<br />";

$page4SimpleImage = new SimpleImage($page4);
$result = $page4SimpleImage->generateThumbnail($page4Thumbnail);

if ($result) {
	echo "t4 success";
} else {
	echo "t4 fail";
}

echo "<br />";

$page3ThumbnailSimpleImage = new SimpleImage($page3Thumbnail);
$t3t4 = $page3ThumbnailSimpleImage->makeCopy('t3t4.jpg');

$result = $t3t4->rightAppend($page4Thumbnail);

if ($result) {
	echo "append t3 and t4 success";
} else {
	echo "append t3 and t4 fail";
}

echo "<br />";

$t1t2 = new SimpleImage('t1t2.jpg');
$superThumb = $t1t2->makeCopy('superthumb.jpg');
$result = $superThumb->downAppend('t3t4.jpg');
if ($result) {
	echo "woot!!";
} else {
	echo "last hurdle";
}

echo "<br />";