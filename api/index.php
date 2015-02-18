<?php

session_start();
$apiURL = "/components/";
require 'Slim/Slim.php';
\Slim\Slim::registerAutoloader();
$app = new \Slim\Slim();

function getConnection() {
    $dbhost = "127.0.0.1";
    $dbuser = "root";
    $dbpass = "pass12345";
    $dbname = "kamus_quran";
    $dbh = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    return $dbh;
}

/**
 * Senarai Surah
 */
$app->post($apiURL . 'senarai_surah', function () {
    $request = \Slim\Slim::getInstance()->request();
    $sql = "SELECT * FROM senarai_surah WHERE surah_nama LIKE :surah ORDER BY surah_id ASC";
    $jsonArray = array();
    try {
        $db = getConnection();
		$surah = ($request->params('surah') != null) ? "%" . $request->params('surah') . "%" : '%%';
		$stmt = $db->prepare($sql);
        $stmt->bindParam("surah", $surah, PDO::PARAM_STR);
		$stmt->execute();
        $db = null;
        $row_count = $stmt->rowCount();
        $result = $stmt->fetchAll(PDO::FETCH_OBJ);
        if ($row_count >= 1) {
            foreach ($result as $result) {
                $jsonArray[] = $result;
            }
        }
        echo json_encode(array('success' => true, 'surah' => $jsonArray));
    } catch (PDOException $e) {
        echo json_encode(array('success' => false, 'msg' => $e->getMessage()));
    }
});

/**
 * Carian Perkataan dalam tafsiran quran
 */
$app->post($apiURL . 'carian_tafsiran', function(){
	$request = \Slim\Slim::getInstance()->request();
	$sql = 'SELECT tafsir.ayat_id, tafsir.surah_id, tafsir.ayat_no, tafsir.ayat, surah.surah_nama '
		. 'FROM ms_basmeih AS tafsir '
		. 'JOIN senarai_surah AS surah ON tafsir.surah_id = surah.surah_id'
		. ' WHERE tafsir.ayat LIKE :carian ORDER BY tafsir.surah_id, tafsir.ayat_no';
	try {
        $db = getConnection();
		$carian = ($request->params('carian') != null) ? "%" . $request->params('carian') . "%" : '%%';
		$stmt = $db->prepare($sql);
        $stmt->bindParam("carian", $carian, PDO::PARAM_STR);
		$stmt->execute();
        $db = null;
        $row_count = $stmt->rowCount();
        $result = $stmt->fetchAll(PDO::FETCH_OBJ);
        if ($row_count >= 1) {
            foreach ($result as $result) {
                $jsonArray[] = $result;
            }
        }
        echo json_encode(array('success' => true, 'ayat' => $jsonArray));
    } catch (PDOException $e) {
        echo json_encode(array('success' => false, 'msg' => $e->getMessage()));
    }
});


$app->notFound(function () {
    echo file_get_contents('404.html');
});

$app->run();
