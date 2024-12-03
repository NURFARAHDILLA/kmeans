<?php
$dbhost = 'localhost';
$dbuser = 'root';
$dbpass = '';
$dbname = 'gis';
 
$koneksi = new mysqli($dbhost,$dbuser,$dbpass,$dbname);
 
# check koneksi
if( $koneksi->connect_error )
{
    die('Oops!! koneksi Gagal : '. $koneksi->connect_error );
}
?>