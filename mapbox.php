<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reverse Geocoding</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css"
        integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.6.0/dist/leaflet.css" integrity="sha512-xwE/Az9zrjBIphAcBb3F6JVqxf46+CDLwfLMHloNu6KEQCAWi6HcDUbeOfBIptF7tcCzusKFjFw2yuvEpDL9wQ==" crossorigin="">
    <script src="https://unpkg.com/leaflet@1.6.0/dist/leaflet.js" integrity="sha512-gZwIG9x3wUXg2hdXF6+rVkLF/0Vi9U8D2Ntg4Ga5I5BZpVkVxlJWbSQtXPSiUTtC0TjtGOmxa1AJPuV0CPthew==" crossorigin=""></script>
    <style>
        html, body {
            height: 95%;
            margin: 0;
        }

        body {
            padding: 0; margin: 0;
        }

        #map {
            height: 100%; width: 100%;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <!-- navbar -->
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
          <a class="navbar-brand" href="#">Peta Clustering Daerah Rawan Bencana</a>
          <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarText" aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>
          <div class="collapse navbar-collapse" id="navbarText">
            <ul class="navbar-nav mr-auto">
              <li class="nav-item active">
                <a class="nav-link" href="#"><span class="sr-only"></span></a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="#"></a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="#"></a>
              </li>
            </ul>
            <span class="navbar-text">
              Reverse Geocoding | <a href="index.php">Kembali</a>
            </span>
          </div>
        </nav>
        <!-- MapBox -->
        <div id="map"></div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous"></script>


    <script type="text/javascript">
        var mymap = L.map('map', { zoomControl: true }).setView([-6.655156, 106.847238], 13);

        L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token=pk.eyJ1IjoicHVybmExMTc3IiwiYSI6ImNsenV1d3ptNTAwdWEybXNiZDl5Zzl4eW8ifQ.NTQY3A83jNkStjcNticKOg', {
            maxZoom: 20,
            attribution: 'Map data © OpenStreetMap contributors.',
            id: 'mapbox/streets-v11',
            tileSize: 512,
            zoomOffset: -1
        }).addTo(mymap);

        <?php
        
        $mysqli = mysqli_connect('localhost', 'root', '', 'gis');
        $tampil = mysqli_query($mysqli, "SELECT data_lokasi.id_kecamatan, data_lokasi.latlong, lokasi.nama_tempat, lokasi.cluster FROM data_lokasi INNER JOIN lokasi ON data_lokasi.nama_tempat = lokasi.nama_tempat");
        while($hasil = mysqli_fetch_array($tampil)){ ?>

        var cluster1 = L.icon({
            iconUrl: 'rendah.png',
            iconSize: [30, 50]
        });

        var cluster2 = L.icon({
            iconUrl: 'sedang.png',
            iconSize: [30, 50]
        });

        var cluster3 = L.icon({
            iconUrl: 'tinggi.png',
            iconSize: [30, 50]
        });

        //menggunakan fungsi L.marker(lat, long) untuk menampilkan latitude dan longitude
        //menggunakan fungsi str_replace() untuk menghilankan karakter yang tidak dibutuhkan
        L.marker([<?php echo str_replace(['[', ']', 'LatLng', '(', ')'], '', $hasil['latlong']); ?>],{ icon: <?php $cluster = $hasil['cluster']; if($cluster == '1'){echo 'cluster1';}else if($cluster == '2'){echo 'cluster2';}else{echo 'cluster3';}?> }).addTo(mymap)

                //data ditampilkan di dalam bindPopup( data ) dan dapat dikustomisasi dengan html
                .bindPopup('<?php if($cluster == '1'){echo 'Cluster 1';}else if($cluster == '2'){echo 'Cluster 2';}else{echo 'Cluster 3';} echo ' | ' .$hasil['id_kecamatan'].' | '.$hasil['nama_tempat']?>');

        <?php } ?>

    </script>
</body>
</html>
