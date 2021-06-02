<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

    <title>Meter Viewer</title>
  </head>
  <body style="width:900px; margin: 30px auto;">
    <h1 style="text-align: center; font-weight: bolder;">Meter Viewer</h1><br />
    <!--
    <div class="dropdown" style="text-align:right;">
      <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="font-size: 16pt;">
        Select Meter
      </button>
      <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
        <a class="dropdown-item" href="?meter=8" style="font-size: 16pt;">Meter #8</a>
        <a class="dropdown-item" href="?meter=9" style="font-size: 16pt;">Meter #9</a>
      </div>
    </div>
    <br />
    -->
    <div id="table-box">
<?php

$meter = $_REQUEST['meter'];
if(1){
    $data1 = @file_get_contents("http://127.0.0.1/run.php?host=7");
    $data2 = @file_get_contents("http://127.0.0.1/run.php?host=8");
    $data3 = @file_get_contents("http://127.0.0.1/run.php?host=9");
    if($data1 && $data2 && $data3){
        $reading_arr1 = json_decode($data1);
        $reading_arr2 = json_decode($data2);
        $reading_arr3 = json_decode($data3);
        ?>
    <table class="table table-striped table-hover" style="font-size: 14pt;">
      <thead class="thead-dark">
        <tr>
          <th scope="col">Item</th>
          <th scope="col">Meter #7</th>
          <th scope="col">Meter #8</th>
          <th scope="col">Meter #9</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $count = count($reading_arr1);
        for($i=0; $i<$count; $i++){
            $val1 = number_shorten($reading_arr1[$i][2],2);
            $val2 = number_shorten($reading_arr2[$i][2],2);
            $val3 = number_shorten($reading_arr3[$i][2],2);
            echo "
        <tr>
          <th scope=\"row\">{$reading_arr1[$i][0]}</th>
          <td>{$val1}{$reading_arr1[$i][3]}</td>
          <td>{$val2}{$reading_arr2[$i][3]}</td>
          <td>{$val3}{$reading_arr3[$i][3]}</td>
        </tr>";
        }
        $pf_1 =  number_format( $reading_arr1[19][2] / $reading_arr1[21][2] ,3);
        $pf_2 =  number_format( $reading_arr2[19][2] / $reading_arr2[21][2] ,3);
        $pf_3 =  number_format( $reading_arr3[19][2] / $reading_arr3[21][2] ,3);
        echo "
        <tr>
          <th scope=\"row\">Power Factor Total</th>
          <td>{$pf_1}</td>
          <td>{$pf_2}</td>
          <td>{$pf_3}</td>
        </tr>";
        ?>
      </tbody>
    </table>
        <?php
    }
}
?>
    </div>

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
  </body>
</html>

<?php

// Shortens a number and attaches K, M, B, etc. accordingly
function number_shorten($number, $precision = 3, $divisors = null) {

    // Setup default $divisors if not provided
    if (!isset($divisors)) {
        $divisors = array(
            pow(1000, 0) => '', // 1000^0 == 1
            pow(1000, 1) => 'K', // Thousand
            pow(1000, 2) => 'M', // Million
            pow(1000, 3) => 'B', // Billion
            pow(1000, 4) => 'T', // Trillion
            pow(1000, 5) => 'Qa', // Quadrillion
            pow(1000, 6) => 'Qi', // Quintillion
        );    
    }

    // Loop through each $divisor and find the
    // lowest amount that matches
    foreach ($divisors as $divisor => $shorthand) {
        if (abs($number) < ($divisor * 1000)) {
            // We found a match!
            break;
        }
    }

    // We found our match, or there were no matches.
    // Either way, use the last defined value for $divisor.
    return number_format($number / $divisor, $precision) . " ".$shorthand;
}

