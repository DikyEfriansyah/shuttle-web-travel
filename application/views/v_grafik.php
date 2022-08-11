<!DOCTYPE html>
<html>
<head>
    <title>Grafik Data User</title>
 
    <?php
        foreach($data as $data){
            $transportation_name[] = $data->transportation_name;
            $seat_qty[] = (float) $data->seat_qty;
        }
    ?>
</head>
<body>
 
    <canvas id="canvas" width="1000" height="280"></canvas>
 
    <!--Load chart js-->
    <script type="text/javascript" src="<?php echo base_url().'assets/chartjs/chart.min.js'?>"></script>
    <script>
 
            var lineChartData = {
                labels : <?php echo json_encode($merk);?>,
                datasets : [
                     
                    {
                        fillColor: "rgba(60,141,188,0.9)",
                        strokeColor: "rgba(60,141,188,0.8)",
                        pointColor: "#3b8bba",
                        pointStrokeColor: "#fff",
                        pointHighlightFill: "#fff",
                        pointHighlightStroke: "rgba(152,235,239,1)",
                        data : <?php echo json_encode($stok);?>
                    }
 
                ]
                 
            }
 
        var myLine = new Chart(document.getElementById("canvas").getContext("2d")).Line(lineChartData);
         
    </script>
</body>
</html>