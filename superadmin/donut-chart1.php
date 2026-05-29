<?php
############ Get Filter On Lead List ##############
$lead_for_branch = $_GET['lead_for_branch'];
$lead_list = "SELECT 
        COUNT(CASE WHEN `lead_type` = 'Hot' THEN 1 END) AS hot_count,
        COUNT(CASE WHEN `lead_type` = 'Cold' THEN 1 END) AS cold_count,
        COUNT(CASE WHEN `lead_type` = 'Warm' THEN 1 END) AS warm_count,
        COUNT(CASE WHEN `lead_type` = 'Cancel' THEN 1 END) AS cancel_count,
        COUNT(CASE WHEN `lead_type` = 'Completed' THEN 1 END) AS complete_count,
        COUNT(CASE WHEN `lead_type` = 'Place Order' THEN 1 END) AS place_order_count
        FROM `leads` WHERE `lead_for_branch` = '$lead_for_branch' AND `lead_assign_to`!='' AND `lead_updatedby` != ''";

$lead_list_result = $conn->query($lead_list);
if ($lead_list_result && mysqli_num_rows($lead_list_result)) {
    $lead_data = mysqli_fetch_assoc($lead_list_result);

    // Get the counts from the result
    $hot_count = $lead_data['hot_count'];
    $cold_count = $lead_data['cold_count'];
    $warm_count = $lead_data['warm_count'];
    $cancel_count = $lead_data['cancel_count'];
    $complete_count = $lead_data['complete_count'];
    $place_order_count = $lead_data['place_order_count'];
}
############# Filter Applied on Lead #################
?>


<script>
    // SIMPLE DONUT CHART
    var colors = ["#f0c541", "#4e9de6", "#2ecd99", "#ed6f56", "#f1a1c7", "#004c5f"];
    var options = {
        chart: {
            height: 420,
            type: 'donut',
        },

        // Use dynamic PHP values for the chart series
        series: [<?php echo $hot_count; ?>, <?php echo $cold_count; ?>, <?php echo $warm_count; ?>,
            <?php echo $cancel_count; ?>, <?php echo $complete_count; ?>, <?php echo $place_order_count; ?>
        ],

        legend: {
            show: true,
            position: 'bottom',
            horizontalAlign: 'center',
            verticalAlign: 'middle',
            floating: false,
            fontSize: '14px',
            offsetX: 20,
            offsetY: 5
        },

        labels: ["Hot (<?php echo $hot_count; ?>)", "Cold (<?php echo $cold_count; ?>)", "Warm (<?php echo $warm_count; ?>)",
            "Cancel (<?php echo $cancel_count; ?>)", "Completed (<?php echo $complete_count; ?>)",
            "Place Order (<?php echo $place_order_count; ?>)"
        ],
        colors: colors,
        responsive: [{
            breakpoint: 600,
            options: {
                chart: {
                    height: 420
                },
                legend: {
                    show: true,
                    offsetX: 0,
                    position: 'bottom',
                },
            }
        }]
    }
    var chart = new ApexCharts(
        document.querySelector("#simple-donut"),
        options
    );
    chart.render();
</script>