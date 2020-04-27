<div class="container">
    <table id="projectTable" class="display" style="width:100%">
        <thead>
        <tr>
            <th>Name</th>
            <th>Budget</th>
            <th>Currency</th>
            <th>User Name</th>
            <th>User Login</th>
        </tr>
        </thead>
        <tfoot>
        <tr>
            <th>Name</th>
            <th>Budget</th>
            <th>Currency</th>
            <th>User Name</th>
            <th>User Login</th>
        </tr>
        </tfoot>
    </table>
</div>
<script>
    $(document).ready(function() {
        $('#projectTable').DataTable( {
            "processing": true,
            "serverSide": true,
            "ajax": "/home/getProjects"
        } );
    } );
</script>
<?php
//$dataPoints = array(
//array("label"=> "Food + Drinks", "y"=> 590),
//array("label"=> "Activities and Entertainments", "y"=> 261),
//array("label"=> "Health and Fitness", "y"=> 158),
//array("label"=> "Shopping & Misc", "y"=> 72),
//array("label"=> "Transportation", "y"=> 191),
//array("label"=> "Rent", "y"=> 573),
//array("label"=> "Travel Insurance", "y"=> 126)
//);

?>
    <script>
        window.onload = function () {

            var chart = new CanvasJS.Chart("chartContainer", {
                animationEnabled: true,
                exportEnabled: true,
                title:{
                    text: "Project budgets"
                },
                subtitles: [{
                    text: "Currency Used: Ukrainian (UAH)"
                }],
                data: [{
                    type: "pie",
                    showInLegend: "true",
                    legendText: "{label}",
                    indexLabelFontSize: 16,
                    indexLabel: "{label} - #percent%",
                    yValueFormatString: "฿#,##0",
                    dataPoints: <?php echo json_encode($dataForChart, JSON_NUMERIC_CHECK); ?>
                }]
            });
            chart.render();

        }
    </script>
<div id="chartContainer" style="height: 370px; width: 100%;"></div>
<script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
