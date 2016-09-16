$( function () {

	$( "div#addon_survey_main" ).html( '<canvas id="myChart" style="width:400px; height:400px;"></canvas>' );

	var ctx = document.getElementById("myChart");

	var myChart = new Chart(ctx, {
		type: 'pie',
		data: {
    labels: [
        "Red",
        "Blue",
        "Yellow",
    ],
    datasets: [
        {
            data: [300, 50, 100],
            backgroundColor: [
                "#FF6384",
                "#36A2EB",
                "#FFCE56",
            ]
        }]
}
	});

});
