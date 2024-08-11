@extends($agentUserTheme)
@section('title')
    Dashboard
@endsection

@section('breadcrumbTitle')
    Dashboard
@endsection
@section('customStyle')
<style type="text/css">
    .merchantTxnCardMain{
        width: 20%;
    }
</style>
@endsection
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="row">
                <div class="col-md-6">
                  <div class="card mt-2">
                      <div class="card-header">
                            <h5>Transactions Overview</h5>
                        </div>
                        <div class="card-body">
                          <div id="pieChart"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                  <div class="card chart-card-1">
                      <div class="card-header">
                          <h5>Percentage Overview</h5>
                      </div>
                      <div class="card-body">
                          <div class="mt-2">
                              <div id="progress1"></div>
                          </div>
                          <div class="mt-2">
                              <div id="progress2"></div>
                          </div>
                          <div class="mt-2">
                              <div id="progress3"></div>
                          </div>
                          <div class="mt-2">
                              <div id="progress4"></div>
                          </div>
                      </div>
                  </div>
              </div>
            </div>
        </div>
    </div>    
@endsection
@section('customScript')
<script type="text/javascript" src="{{ storage_asset('softtheme/js/plugins/chartjs.min.js')}}"></script>
    <script>
         document.addEventListener('DOMContentLoaded', function () {
    var options = {
        chart: {
            type: 'pie'
        },
        series: [
            {{ $transaction->successfullV }},
            {{ $transaction->declinedV }},
            {{ $transaction->refundV }},
            {{ $transaction->chargebackV }}
        ],
        labels: ['Success', 'Declined', 'Refund', 'Chargeback'],
        fill: {
            type: 'gradient',
        },
        colors: ['#775DD0', '#FEB019', '#FF4560', '#775DD0'],
        gradient: {
            shade: 'dark',
            type: 'horizontal',
            shadeIntensity: 0.5,
            gradientToColors: ['#6078ea', '#6094ea', '#F86624', '#AA00FF'],
            inverseColors: true,
            opacityFrom: 1,
            opacityTo: 1,
            stops: [0, 100]
        },
        tooltip: {
            y: {
                formatter: function (val) {
                    return val + " $"; // Adjust "units" to whatever your amounts represent
                }
            }
        },
        dataLabels: {
            formatter: function (val, opts) {
                return opts.w.globals.series[opts.seriesIndex] + " $"; // Display the actual amount in data labels
            }
        }
    }

    var chart = new ApexCharts(document.querySelector("#pieChart"), options);

    chart.render();
});


      var optionsProgress1 = {
      chart: {
        height: 70,
        type: "bar",
        stacked: true,
        sparkline: {
          enabled: true
        }
      },
      plotOptions: {
        bar: {
          horizontal: true,
          barHeight: "20%",
          colors: {
            backgroundBarColors: ["#40475D"]
          }
        }
      },
      stroke: {
        width: 0
      },
      series: [
        {
          name: "Successful",
          data: [<?php echo $transaction->successfullP; ?>]
        }
      ],
      title: {
        floating: true,
        offsetX: -10,
        offsetY: 5,
        text: "Successful"
      },
      subtitle: {
        floating: true,
        align: "right",
        offsetY: 0,
        text: "<?php echo $transaction->successfullP; ?>%",
        style: {
          fontSize: "20px"
        }
      },
      tooltip: {
        enabled: false
      },
      xaxis: {
        categories: ["Successful"]
      },
      yaxis: {
        max: 100
      },
      fill: {
        opacity: 1
      }
    };

    var chartProgress1 = new ApexCharts(
      document.querySelector("#progress1"),
      optionsProgress1
    );
    chartProgress1.render();

    var optionsProgress2 = {
      chart: {
        height: 70,
        type: "bar",
        stacked: true,
        sparkline: {
          enabled: true
        }
      },
      plotOptions: {
        bar: {
          horizontal: true,
          barHeight: "20%",
          colors: {
            backgroundBarColors: ["#40475D"]
          }
        }
      },
      colors: ["#17ead9"],
      stroke: {
        width: 0
      },
      series: [
        {
          name: "Failed",
          data: [<?php echo $transaction->declinedP; ?>]
        }
      ],
      title: {
        floating: true,
        offsetX: -10,
        offsetY: 5,
        text: "Failed"
      },
      subtitle: {
        floating: true,
        align: "right",
        offsetY: 0,
        text: "<?php echo $transaction->declinedP; ?>%",
        style: {
          fontSize: "20px"
        }
      },
      tooltip: {
        enabled: false
      },
      xaxis: {
        categories: ["Failed"]
      },
      yaxis: {
        max: 100
      },
      fill: {
        type: "gradient",
        gradient: {
          inverseColors: false,
          gradientToColors: ["#6078ea"]
        }
      }
    };

    var chartProgress2 = new ApexCharts(
      document.querySelector("#progress2"),
      optionsProgress2
    );
    chartProgress2.render();

    var optionsProgress3 = {
      chart: {
        height: 70,
        type: "bar",
        stacked: true,
        sparkline: {
          enabled: true
        }
      },
      plotOptions: {
        bar: {
          horizontal: true,
          barHeight: "20%",
          colors: {
            backgroundBarColors: ["#40475D"]
          }
        }
      },
      colors: ["#f02fc2"],
      stroke: {
        width: 0
      },
      series: [
        {
          name: "Refund",
          data: [<?php echo $transaction->refundP; ?>]
        }
      ],
      fill: {
        type: "gradient",
        gradient: {
          gradientToColors: ["#6094ea"]
        }
      },
      title: {
        floating: true,
        offsetX: -10,
        offsetY: 5,
        text: "Refund"
      },
      subtitle: {
        floating: true,
        align: "right",
        offsetY: 0,
        text: "<?php echo $transaction->refundP; ?>%",
        style: {
          fontSize: "20px"
        }
      },
      tooltip: {
        enabled: false
      },
      xaxis: {
        categories: ["Refund"]
      },
      yaxis: {
        max: 100
      }
    };

    var chartProgress3 = new ApexCharts(
      document.querySelector("#progress3"),
      optionsProgress3
    );
    chartProgress3.render();

    var optionsProgress4 = {
      chart: {
        height: 70,
        type: "bar",
        stacked: true,
        sparkline: {
          enabled: true
        }
      },
      plotOptions: {
        bar: {
          horizontal: true,
          barHeight: "20%",
          colors: {
            backgroundBarColors: ["#40475D"]
          }
        }
      },
      colors: ["#f02fc2"],
      stroke: {
        width: 0
      },
      series: [
        {
          name: "Chargeback",
          data: [<?php echo $transaction->chargebackP; ?>]
        }
      ],
      fill: {
        type: "gradient",
        gradient: {
          gradientToColors: ["#6094ea"]
        }
      },
      title: {
        floating: true,
        offsetX: -10,
        offsetY: 5,
        text: "Chargeback"
      },
      subtitle: {
        floating: true,
        align: "right",
        offsetY: 0,
        text: "<?php echo $transaction->chargebackP; ?>%",
        style: {
          fontSize: "20px"
        }
      },
      tooltip: {
        enabled: false
      },
      xaxis: {
        categories: ["Chargeback"]
      },
      yaxis: {
        max: 100
      }
    };

    var chartProgress4 = new ApexCharts(
      document.querySelector("#progress4"),
      optionsProgress4
    );
    chartProgress4.render();

    
</script>
@endsection
