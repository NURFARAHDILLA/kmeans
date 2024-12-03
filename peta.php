<?php

include 'pdo.php';

if(isset($_POST['submit'])){

	//kosongkan table
	mysqli_query($koneksi,"TRUNCATE lokasi");

	$nama_tempat = $_POST['nama_tempat'];
	$tanah_longsor = $_POST['tanah_longsor'];
	$angin_kencang = $_POST['angin_kencang'];
	$cluster = $_POST['cluster'];
	$count = count($nama_tempat);

	$sql = "INSERT INTO lokasi (nama_tempat, tanah_longsor, angin_kencang, cluster) VALUES ";

for($i=0; $i < $count; $i++){
	$sql .= "('{$nama_tempat[$i]}','{$tanah_longsor[$i]}','{$angin_kencang[$i]}','{$cluster[$i]}')";
	$sql .= ",";
}

$sql = rtrim($sql,",");

$insert = $koneksi->query($sql);

	if($insert>0){
		echo "<script>alert('Anda akan membuka halaman Peta !'); window.location = './mapbox.php';</script>";
	}else{
		// echo "<script>alert('Silahkan Ulangi !'); window.location = './index.php';</script>";
		die('Query Error : '.mysqli_errno($koneksi). 
        ' - '.mysqli_error($koneksi));
	}

}

?>