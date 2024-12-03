<?php
  error_reporting(0);
  $link  = mysqli_connect("localhost", "root", "", "gis");
  $query = $link->query("SELECT * FROM bencana_sebaran");
  
  // Check connection
  if (mysqli_connect_errno())
  {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
  }
  $data=[];
  $provinsi=[];
  while($row=$query->fetch_assoc()){
    $data[]=$row;
    $provinsi[]=$row['kecamatan'];
  }
  //hitung Euclidean Distance Space
  function jarakEuclidean($data=array(),$centroid=array()){
    return sqrt(pow(($data[0]-$centroid[0]),2) + pow(($data[1]-$centroid[1]),2));
  }

  function jarakTerdekat($jarak_ke_centroid=array(),$centroid){
    foreach ($jarak_ke_centroid as $key => $value) {
      if(!isset($minimum)){
        $minimum=$value;
        $cluster=($key+1);
        continue;
      }
      else if($value<$minimum){
        $minimum=$value;
        $cluster=($key+1);
      }
    }
    return array(
      'cluster'=>$cluster,
      'value'=>$minimum,
    );
  }

  function perbaruiCentroid($table_iterasi,&$hasil_cluster){
    $hasil_cluster=[];
    //looping untuk mengelompokan x dan y sesuai cluster
    foreach ($table_iterasi as $key => $value) {
      $hasil_cluster[($value['jarak_terdekat']['cluster']-1)][0][]= $value['data'][0];//data x
      $hasil_cluster[($value['jarak_terdekat']['cluster']-1)][1][]= $value['data'][1];//data y
    }
    $new_centroid=[];
    //looping untuk mencari nilai centroid baru dengan cara mencari rata2 dari masing2 data(0=x dan 1=y) 
    foreach ($hasil_cluster as $key => $value) {
      $new_centroid[$key]= [
        array_sum($value[0])/count($value[0]),
        array_sum($value[1])/count($value[1])
      ]; 
    }
    //sorting berdasarkan cluster
    ksort($new_centroid);
    return $new_centroid;
  }

  function centroidBerubah($centroid,$iterasi){
    $centroid_lama = flatten_array($centroid[($iterasi-1)]); //flattern array
    $centroid_baru = flatten_array($centroid[$iterasi]); //flatten array
    //hitbandingkan centroid yang lama dan baru jika berubah return true, jika tidak berubah/jumlah sama=0 return false
    $jumlah_sama=0;
    for($i=0;$i<count($centroid_lama);$i++){
      if($centroid_lama[$i]===$centroid_baru[$i]){
        $jumlah_sama++;
      }
    }
    return $jumlah_sama===count($centroid_lama) ? false : true; 
  }

  function flatten_array($arg) {
    return is_array($arg) ? array_reduce($arg, function ($c, $a) { return array_merge($c, flatten_array($a)); },[]) : [$arg];
  }

  function pointingHasilCluster($hasil_cluster){
    $result=[];
    foreach ($hasil_cluster as $key => $value) {
      for ($i=0; $i<count($value[0]);$i++) { 
        $result[$key][]=[$hasil_cluster[$key][0][$i],$hasil_cluster[$key][1][$i]];
      }
    }
    return ksort($result);
  }

  //start program
  // get data dari database
  $link  = mysqli_connect("localhost", "root", "", "gis");
  // cek koneksi
  if (mysqli_connect_errno())
  {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
    exit;
  }
  $query = $link->query("SELECT * FROM bencana_sebaran");
  $data=[];
  //masukan data jumlah guru dan siswa ke array data
  while($row=$query->fetch_assoc()){
    $data[]=[$row['tanah_longsor'],$row['angin_kencang']];
  }
  
  //jumlah cluster
  $cluster = 3;
  $variable_x = 'Tanah Longsor';
  $variable_y = 'Angin Kencang';

  $rand=[];
  //centroid awal ambil random dari data
  for($i=0;$i<$cluster;$i++){
    $temp=rand(0,(count($data)-1));
    while(in_array($rand, $temp)){
      $temp=rand(0,(count($data)-1));
    }
    $rand[]=$temp;
    $centroid[0][]=[
      $data[$rand[$i]][0],
      $data[$rand[$i]][1]
    ];
  }
  
  $hasil_iterasi=[];
  $hasil_cluster=[];

  //iterasi
  $iterasi=0;
  while(true){
    $table_iterasi=array();
    //untuk setiap data ke i (x dan y)
    foreach ($data as $key => $value) {
      //untuk setiap table centroid pada iterasi ke i
      $table_iterasi[$key]['data']=$value;
      foreach ($centroid[$iterasi] as $key_c => $value_c) {
        //hitung jarak euclidean 
        $table_iterasi[$key]['jarak_ke_centroid'][]=jarakEuclidean($value,$value_c);  
      }
      //hitung jarak terdekat dan tentukan cluster nya
      $table_iterasi[$key]['jarak_terdekat']=jarakTerdekat($table_iterasi[$key]['jarak_ke_centroid'],$centroid);
    }
    array_push($hasil_iterasi, $table_iterasi);
    $centroid[++$iterasi]=perbaruiCentroid($table_iterasi,$hasil_cluster);
    $lanjutkan=centroidBerubah($centroid,$iterasi);
    $boolval = boolval($lanjutkan) ? 'ya' : 'tidak';
    // echo 'proses iterasi ke-'.$iterasi.' : lanjutkan iterasi ? '.$boolval.'<br>';
    if(!$lanjutkan)
      break;
    //loop sampai setiap nilai centroid sama dengan nilai centroid sebelumnya
  } 
  ?>
<!DOCTYPE html>
<html lang="en" >
<head>
  <meta charset="UTF-8">
  <title>K-Means Clustering - Bencana Alam</title>
  <link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css'><link rel="stylesheet" href="./style.css">
  <style>
      a[aria-expanded="true"]{
        background-color: green;
      }
      .table th{
        padding: .25rem;
      }
      .table td{ 
        padding: .35rem;

      }
      /*wrapped

      .table
      .table td{
        padding: .25rem;
      }
      /*wrapper*/

    .wrapper {
        display: grid;
        grid-template-columns: repeat(6, 1fr);
        gap: 5px;
        /* grid-rows: 200px; */
    }

    .wrapper>div {
        /*border: 1px solid #F5F5F5;*/
        padding: 5px;
        font-size: 11px;
    }

    @media (max-width: 600px) {
        .wrapper {
            flex-direction: column;
        }

        .wrapper>div {
            margin-right: 2px;
            margin-bottom: 10px;
        }
    }

    .top {
            grid-column: 1 / 7;
            grid-row: 1;
        }

    .one {
        grid-column: 1 / 5;
        grid-row: 2;
    }

    .two {
        grid-column: 5 / 7;
        grid-row: 2;
    }
  </style>
</head>
<body>
<!-- partial:index.partial.html -->
<div class="tabs-to-dropdown">
  <div class="nav-wrapper d-flex align-items-center justify-content-between">
    <ul class="nav nav-pills d-none d-md-flex" id="pills-tab" role="tablist">
      <li class="nav-item" role="presentation">
        <a class="nav-link active" id="pills-contact-tab" data-toggle="pill" href="#pills-contact" role="tab" aria-controls="pills-contact" aria-selected="false">K-Means Clustering</a>
      </li>
      <li class="nav-item" role="presentation">
        <a class="nav-link" id="pills-product-tab" data-toggle="pill" href="#pills-product" role="tab" aria-controls="pills-product" aria-selected="false">Data Kecamatan</a>
      </li>
      <li class="nav-item" role="presentation">
        <a class="nav-link" id="pills-news-tab" data-toggle="pill" href="#pills-news" role="tab" aria-controls="pills-news" aria-selected="false">Dataset Bencana Alam</a>
      </li>
      <li class="nav-item" role="presentation">
        <a class="nav-link" id="pills-company-tab" data-toggle="pill" href="#pills-company" role="tab" aria-controls="pills-company" aria-selected="true">Tentang Aplikasi</a>
      </li>
    </ul>

  </div>

  <div class="tab-content" id="pills-tabContent">
    <div class="tab-pane fade" id="pills-company" role="tabpanel" aria-labelledby="pills-company-tab">
      <div class="container-fluid">
        <h2 class="mb-3 font-weight-bold">K-Means Clustering</h2>
        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce porttitor leo nec ligula viverra, quis facilisis nunc vehicula. Maecenas purus orci, efficitur in dapibus vel, rutrum in massa. Sed auctor urna sit amet eros mattis interdum.</p>
        <h2 class="mb-3 font-weight-bold">Reverse Geocoding</h2>
        <p>Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae; Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Aliquam tempus ac est convallis accumsan.</p>
        <h2 class="mb-3 font-weight-bold">Dataset Bencana Alam - Kecamatan Bogor Selatan</h2>
        <p>Pellentesque rutrum sit amet nunc sit amet faucibus. Ut id arcu tempus, varius erat et, ornare libero. In quis felis nunc. Aliquam euismod lacus a eros ornare, ut aliquam sem mattis. Cras non varius dui, quis commodo ante. Maecenas gravida est non nulla malesuada egestas. Proin tincidunt eros et lacus sodales lobortis.</p>
      </div>
    </div>
    <div class="tab-pane fade" id="pills-product" role="tabpanel" aria-labelledby="pills-product-tab">
      <div class="container-fluid">
        <h2 class="mb-3 font-weight-bold">Data Kecamatan</h2>
        <p>Sed auctor urna sit amet eros mattis interdum. Integer imperdiet ante in quam lacinia, a laoreet risus imperdiet. Ut a blandit elit, vitae volutpat nunc.</p>
        <p>
          <table class="table table-striped table-bordered data" width="100%" style="font-size: 14px;">
            <tr>
              <th>No</th>
              <th>Kode Kecamatan</th>
              <th>Nama Kecamatan</th>
              <th>Lat / Long</th>
            </tr>
            <?php
          
            </div>
<div class="tab-pane fade" id="pills-product" role="tabpanel" aria-labelledby="pills-product-tab">
      <div class="container-fluid">
        <h2 class="mb-3 font-weight-bold">Data Kecamatan</h2>
        <p>Sed auctor urna sit amet eros mattis interdum. Integer imperdiet ante in quam lacinia, a laoreet risus imperdiet. Ut a blandit elit, vitae volutpat nunc.</p>
        <p>
          <table class="table table-striped table-bordered data" width="100%" style="font-size: 14px;">
            <tr>
              <th>No</th>
              <th>Kode Kecamatan</th>
              <th>Nama Kecamatan</th>
              <th>Lat / Long</th>
            </tr>

            <?php
          include 'config.php';
          $no = 1;
          $sqlk = mysqli_query($config,"SELECT*FROM data_lokasi");
            while($dtKec = mysqli_fetch_array($sqlK)){
            ?>
            <tr>
            <td><?= $no++ ?></td>
            <td><?= $dtkect['id_kecamatan'] ?></td>
            <td><?= $dtkec['nama_tempat'] ?></td>
            <td><? dtkec['latlong'] ?></td>
            </tr>

          <?php } ?>
          </table>
        </p>
      </div>
    </div>
    <div class="tab-pane fade" id="pills-news" role="tabpanel" aria-labelledby="pills-news-tab">
      <div class="container-fluid">
        <h2 class="mb-3 font-weight-bold">Dataset Bencana Alam</h2>
        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce porttitor leo nec ligula viverra, quis facilisis nunc vehicula.</p>
        <p>
          <table class="table table-striped table-bordered data" width="100%" style="font-size: 14px;">
            <tr>
              <th>No</th>
              <th>Nama Kecamatan</th>
              <th>Tanah Longsor</th>
              <th>Banjir</th>
              <th>Kebakaran</th>
              <th>Angin Kencang</th>
              <th>Kekeringan</th>
              <th>Pergeseran Tanah</th>
              <th>Gempa Bumi</th>
              <th>Lain-Lain</th>
            </tr>
            <?php
            include 'config.php';
              $no = 1;
            $sqlB = mysqli_query($config,"SELECT*FROM sebaran_bencana");
            while($dtB = mysqli_fetch_array($sqlB)){
            ?>
            <tr>
              <td><?= $no++ ?></td>
              <td><?= $dtB['kecamatan'] ?></td>
              <td><?= $dtB['tanah_longsor'] ?></td>
              <td><?= $dtB['banjir'] ?></td>
              <td><?= $dtB['kebakaran'] ?></td>
              <td><?= $dtB['angin_kencang'] ?></td>
              <td><?= $dtB['kekeringan'] ?></td>
              <td><?= $dtB['pergeseran_tanah'] ?></td>
              <td><?= $dtB['gempa_bumi'] ?></td>
              <td><?= $dtB['lain_lain'] ?></td>
            </tr>
          <?php } ?>
          </table></p>
      </div>
    </div>
    <div class="tab-pane fade show active" id="pills-contact" role="tabpanel" aria-labelledby="pills-contact-tab">
      <div class="container-fluid">
        <h2 class="mb-2 font-weight-bold">Proses K-Means Clustering</h2>
        <p>
          <div class="wrapper">
            <div class="one">
              <a class="btn btn-primary" data-toggle="collapse" href="#multiCollapseExample" role="button" aria-expanded="false" aria-controls="multiCollapseExample">Inisialisasi</a>
              <?php foreach ($hasil_iterasi as $key => $value) { ?>
              <a class="btn btn-primary" data-toggle="collapse" href="#multiCollapseExample<?php echo $key ?>" role="button" aria-expanded="false" aria-controls="multiCollapseExample<?php echo $key ?>">Iterasi ke <?php echo ($key+1); ?></a>
              <?php }  ?>
              <p>
                <!-- <div class="col"> -->
                <div class="row justify-content-md-center">
                  <div class="col">
                    <div class="collapse multi-collapse" id="multiCollapseExample">
                      <div class="card card-body">
                        <h2>Inisialisasi</h2>
                        <div class="row">
                          <div class="col justify-content-md-center">
                            <table class="table table-bordered table-striped">
                          <thead>
                            <tr>
                              <th rowspan="1">Centroid</th>
                              <th rowspan="1"><?php echo $variable_x; ?></th>
                              <th rowspan="1"><?php echo $variable_y; ?></th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php foreach ($centroid[0] as $key_c => $value_c) { ?>
                            <tr>
                              <td><?php echo ($key_c+1); ?></td>
                              <td><?php echo $value_c[0]; ?></td>
                              <td><?php echo $value_c[1]; ?></td>
                            </tr>
                            <?php } ?>
                          </tbody>
                        </table>
                      </div>
                      <div class="col justify-content-md-center">
                        <table class="table table-bordered table-striped" style="display: inline-block;">
                          <thead>
                            <tr>
                              <th rowspan="1">Data ke-i</th>
                              <th rowspan="1">Kecamatan</th>
                              <th rowspan="1"><?php echo $variable_x; ?></th>
                              <th rowspan="1"><?php echo $variable_y; ?></th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php foreach ($data as $key_c => $value_c) { ?>
                            <tr>
                              <td><?php echo ($key_c+1); ?></td>
                              <td><?php echo $provinsi[$key_c]; ?></td>
                              <td><?php echo $value_c[0]; ?></td>
                              <td><?php echo $value_c[1]; ?></td>
                            </tr>
                            <?php } ?>
                          </tbody>
                        </table>
                      </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <?php
                foreach ($hasil_iterasi as $key => $value) { ?>
                <!-- <div class="col"> -->
                <div class="row justify-content-md-center">
                  <div class="col">
                    <div class="collapse multi-collapse" id="multiCollapseExample<?php echo $key; ?>">
                      <div class="card card-body">
                        <h2>Iterasi ke <?php echo ($key+1) ?></h2>
                        <div class="row">
                          <div class="col justify-content-md-center">
                            <table class="table table-bordered table-striped">
                          <thead>
                            <tr>
                              <th rowspan="1" class="text-center">Centroid</th>
                              <th rowspan="1" class="text-center"><?php echo $variable_x; ?></th>
                              <th rowspan="1" class="text-center"><?php echo $variable_y; ?></th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php foreach ($centroid[$key] as $key_c => $value_c) { ?>
                            <tr>
                              <td class="text-center"><?php echo ($key_c+1); ?></td>
                              <td class="text-center"><?php echo $value_c[0]; ?></td>
                              <td class="text-center"><?php echo $value_c[1]; ?></td>
                            </tr>
                            <?php } ?>
                          </tbody>
                        </table>
                      </div>
                      <div class="col justify-content-md-center">
                        <table class="table table-bordered table-striped">
                          <thead>
                            <tr>
                              <th rowspan="2" class="text-center">Data ke i</th>
                              <th rowspan="2" class="text-center">Kecamatan</th>
                              <th rowspan="2" class="text-center"><?php echo $variable_x; ?></th>
                              <th rowspan="2" class="text-center"><?php echo $variable_y; ?></th>
                              <th rowspan="1" class="text-center" colspan="<?php echo $cluster; ?>">Jarak ke centroid</th>
                              <th rowspan="2" class="text-center" >Jarak terdekat</th>
                              <th rowspan="2" class="text-center">Cluster</th>
                            </tr>
                            <tr>
                            <?php for ($i=1; $i <=$cluster ; $i++) { ?> 
                              <th rowspan="1" class="text-center"><?php echo $i; ?></th>
                            <?php }?>
                            </tr>
                          </thead>
                          <tbody>
                            <?php foreach ($value as $key_data => $value_data) { ?>
                            <tr>
                              <td class="text-center"><?php echo $key_data+1; ?></td>
                              <td class="text-center"><?php echo $provinsi[$key_data]; ?></td>
                              <td class="text-center"><?php echo $value_data['data'][0]; ?></td>
                              <td class="text-center"><?php echo $value_data['data'][1]; ?></td>
                              <?php
                              foreach ($value_data['jarak_ke_centroid'] as $key_jc => $value_jc) { ?>
                                <td class="text-center"><?php echo $value_jc; ?></td>
                              <?php 
                              }
                              ?>
                              <td class="text-center"><?php echo $value_data['jarak_terdekat']['value']; ?></td>
                              <td class="text-center"><?php echo $value_data['jarak_terdekat']['cluster']; ?></td>
                            </tr>

                            <?php } ?>
                          </tbody>
                        </table>
                      </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <?php
                }
                ?>
              </p>
            </div>
            <div class="two">
              <div id="chartContainer" style="min-width: 400px; height: 380px; max-width: 480px; margin: 0 auto"></div>
            </div>
            <div class="top">
              <form action="peta.php" method="POST">        
              <table class="table table-bordered table-striped" hidden="hidden">
              <thead>
                <tr>
                  <th rowspan="2" class="text-center">Kecamatan</th>
                  <th rowspan="2" class="text-center"><?php echo $variable_x; ?></th>
                  <th rowspan="2" class="text-center"><?php echo $variable_y; ?></th>
                  <th rowspan="2" class="text-center">Cluster</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($value as $key_data => $value_data) { ?>
                <tr>
                  <td class="text-center"><input type="text" name="nama_tempat[]" value="<?php echo $provinsi[$key_data]; ?>"></td>
                  <td class="text-center"><input type="text" name="tanah_longsor[]" value="<?php echo $value_data['data'][0]; ?>"></td>
                  <td class="text-center"><input type="text" name="angin_kencang[]" value="<?php echo $value_data['data'][1]; ?>"></td>
                  <td class="text-center"><input type="text" name="cluster[]" value="<?php echo $value_data['jarak_terdekat']['cluster']; ?>"></td>
                </tr>
              </tbody>
            <?php } ?>
            </table>
                <?php
                 $a = $key+1;
                 if($a == 5){
                  echo '<input type="submit" class="btn btn-success" name="submit" value=""lihat peta>';
                 } else{
                  echo '<input type="button" class="btn btn-warning" value="NEXT" onclick="window.location.reload(true);">';
                 }
                ?>
              </form>
            </div>
          </div>        
        </p>
      </div>
    </div>
  </div>
</div>

<footer class="page-footer">
  <span>Universitas Nasional - FTKI Informatika </span>
  <a href="https://ftki.unas.ac.id/" target="_blank">
    <img width="24" height="30" src="https://upload.wikimedia.org/wikipedia/commons/2/2e/Universitas_Nasional_Logo.png" alt="George Martsoukos logo">
  </a>&nbsp;
  <span> Farah | NPM </span>
</footer>
<!-- partial -->
  <script src='https://code.jquery.com/jquery-3.5.1.slim.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js'></script><script  src="./script.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/highcharts/6.0.4/highcharts.js" integrity="sha512-RAS+5JGl3QYmUWw7QBrGNw8VpxSxSD1w2nqqmaBdXUtM7i7/p8xRpi/aOI4kj2ZJVpesG0XnAStE73QucKNWFw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/highcharts/6.0.4/highcharts-more.js" integrity="sha512-S6UFdTKdV2svx/QoGL0XVvRQA5AL0e8XjG29msXqGS6puinHfyn0kdyOmSF9YD8IE/IQcO3JcVs/E0hrVnRX9g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script type="text/javascript">
var data=[];
var color=['red','green','blue'];
<?php foreach ($centroid[(count($centroid)-1)] as $key => $value) { ?>
    var dataPoints={
        name: "Centroid <?php echo ($key+1); ?>",
        color: 'yellow',
        data: [{
          x:<?php echo $value[0]; ?>,
          y:<?php echo $value[1]; ?>
      }]
    };
    data.push(dataPoints);
<?php } ?>
<?php 
  foreach ($hasil_cluster as $key => $value) { ?>
    var dataPoints={
        name: "Cluster <?php echo ($key+1); ?>",
        color: color[<?php echo $key; ?>],
        data: []
    };
<?php for ($i=0; $i <count($value[0]) ; $i++) { ?>
  <?php 
      $nama_provinsi='';
      foreach ($data as $key_d => $value_d) { 
        if($value_d[0]==$value[0][$i] && $value_d[1]==$value[1][$i]){
          $nama_provinsi=$provinsi[$key_d];
        }
      } ?>
      dataPoints.data.push({
        name:"<?php echo $nama_provinsi; ?>",
        x:<?php echo $value[0][$i]; ?>,
        y:<?php echo $value[1][$i]; ?>
      });
    <?php } (dataPoints);

  <?php for ($i=0; $i <count($value[0]) ; $i++) { ?>
  <?php 
      $nama_provinsi='';
      foreach ($data as $key_d => $value_d) { 
        if($value_d[0]==$value[0][$i] && $value_d[1]==$value[1][$i]){
          $nama_provinsi=$provinsi[$key_d];
        }
      } ?>
      dataPoints.data.push({
        name:"<?php echo $nama_provinsi; ?>",
        x:<?php echo $value[0][$i]; ?>,
        y:<?php echo $value[1][$i]; ?>
      });
    <?php } (dataPoints);
  
<?php   } ?>
    data.push(dataPoints);
<?php } ?>
console.log(data);
// break;
Highcharts.chart('chartContainer', {
    chart: {
        type: 'scatter',
        zoomType: 'xy'
    },
    title: {
        text: 'Chart Cluster Bencana Alam'
    },
    xAxis: {
        title: {
            enabled: true,
            text: 'Tanah Longsor'
        },
        startOnTick: true,
        endOnTick: true,
        showLastLabel: true
    },
    yAxis: {
        title: {
            text: 'Angin Kencang'
        }
    },
    plotOptions: {
        scatter: {
            marker: {
                radius: 5,
                states: {
                    hover: {
                        enabled: true,
                        lineColor: 'rgb(100,100,100)'
                    }
                }
            },
            states: {
                hover: {
                    marker: {
                        enabled: false
                    }
                }
            },
            tooltip: {
                headerFormat: '<b>{series.name} {point.key}</b><br>',
                pointFormat: '{point.x} TanahLongsor, {point.y} AnginKencang'
            }
        }
    },
    series: data
});


</script>

</body>
</html>
