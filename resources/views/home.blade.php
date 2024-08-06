@extends('layouts.user.default')

@section('title')
Dashboard
@endsection

@section('breadcrumbTitle')
<nav aria-label="breadcrumb">
   <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
      <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="{{ route('dashboardPage') }}">Home</a></li>
      <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Dashboard</li>
   </ol>
   <h6 class="font-weight-bolder mb-0">Dashboard</h6>
</nav>
@endsection

@section('customeStyle')
<!-- <script src="https://cdn.lordicon.com/libs/frhvbuzj/lord-icon-2.0.2.js"></script> -->
<!-- <link href="{{ storage_asset('/theme/vendor/bootstrap-select/dist/css/bootstrap-select.min.css') }}" rel="stylesheet"> -->
<style type="text/css">
    .dropdown.bootstrap-select.default-select {
        width: 130px !important;
    }

    body {
      background: #1B213B;
      color: #777;
      font-family: Montserrat, Arial, sans-serif;
    }

    .body-bg {
      background: #F3F4FA !important;
    }

    h1, h2, h3, h4, h5, h6, strong {
      font-weight: 600;
    }

    .box .apexcharts-xaxistooltip {
      background: #1B213B;
      color: #fff;
    }

    .content-area {
      max-width: 1280px;
      margin: 0 auto;
    }

    .box {
/*      background-color: #262D47;*/
      padding: 25px 25px; 
      border-radius: 4px; 
    }

    .columnbox {
      padding-right: 15px;
    }
    .radialbox {
      max-height: 333px;
      margin-bottom: 60px;
    }

    .apexcharts-legend-series tspan:nth-child(3) {
      font-weight: bold;
      font-size: 20px;
    }

    .edit-on-codepen {
      text-align: right;
      width: 100%;
      padding: 0 20px 40px;
      position: relative;
      top: -30px;
      cursor: pointer;
    }

    .apexcharts-legend-series{
        margin:5px 5px !important;
    }

    
</style>
@endsection

@section('content')
@php
$transactionPermission = 0;
$settingsPermission = 0;
@endphp
@if(Auth()->user()->main_user_id != '0')
@if(Auth()->user()->transactions == '1')
@php
$transactionPermission = 1;
@endphp
@endif
@if(Auth()->user()->settings == '1')
@php
$settingsPermission = 1;
@endphp
@endif
@endif
@if(!empty(Auth::user()->application))
@if(Auth::user()->application->status == 4 || Auth::user()->application->status == 5 ||
Auth::user()->application->status == 6 || Auth::user()->application->status == 10 || Auth::user()->application->status
== 11)
@php
$transactionPermission = 1;
$settingsPermission = 1;
@endphp
@endif
@endif


<div class="row mt-2">
    <div class="col-md-9">
        <div class="card chart-card-1">
            <div class="card-header">
                <h5>Overview</h5>
            </div>
            <div class="card-body">
                <div id="saleAnalytics" class="chart-dark"></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
      <div class="card-header">
        <h5>All Status Summary</h5>
      </div>
      <div class="card">
          
          <div class="card-body">
             <div class="row">
                <!-- <div class="col-12"> -->
                   <div class="numbers">
                      <p class="text-sm mb-0 text-capitalize font-weight-bold">Successful</p>
                      <h5 class="font-weight-bolder mb-0">
                         $ {{ $transaction->successfullV }}
                         <span class="text-success text-sm font-weight-bolder">+{{ round($transaction->successfullP,2) }}%</span>
                      </h5>
                   <!-- </div> -->
                </div>
                <!-- <div class="col-4 text-end">
                   <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                      <i class="ni ni-world text-lg opacity-10" aria-hidden="true"></i>
                   </div>
                </div> -->
             </div>
          </div>
       </div>
       <div class="card mt-2">
          <div class="card-body p-4">
             <div class="row">
                <div class="col-12">
                   <div class="numbers">
                      <p class="text-sm mb-0 text-capitalize font-weight-bold">Declined</p>
                      <h5 class="font-weight-bolder mb-0">
                         {{$transaction->declinedV}}
                         <span class="text-success text-sm font-weight-bolder">{{round($transaction->declinedP,2)}}%</span>
                      </h5>
                   </div>
                </div>
                <!-- <div class="col-4 text-end">
                   <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                      <i class="ni ni-world text-lg opacity-10" aria-hidden="true"></i>
                   </div>
                </div> -->
             </div>
          </div>
       </div>
       <div class="card mt-2">
          <div class="card-body p-4">
             <div class="row">
                <div class="col-12">
                   <div class="numbers">
                      <p class="text-sm mb-0 text-capitalize font-weight-bold">Refund</p>
                      <h5 class="font-weight-bolder mb-0">
                         {{$transaction->refundV}}
                         <span class="text-success text-sm font-weight-bolder">{{round($transaction->refundP,2)}}%</span>
                      </h5>
                   </div>
                </div>
                <!-- <div class="col-4 text-end">
                   <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                      <i class="ni ni-world text-lg opacity-10" aria-hidden="true"></i>
                   </div>
                </div> -->
             </div>
          </div>
       </div>
       <div class="card mt-2">
          <div class="card-body p-4">
             <div class="row">
                <div class="col-12">
                   <div class="numbers">
                      <p class="text-sm mb-0 text-capitalize font-weight-bold">Chargebacks</p>
                      <h5 class="font-weight-bolder mb-0">
                         {{$transaction->chargebackV}}
                         <span class="text-success text-sm font-weight-bolder">{{round($transaction->chargebackP,2)}}%</span>
                      </h5>
                   </div>
                </div>
                <!-- <div class="col-4 text-end">
                   <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                      <i class="ni ni-world text-lg opacity-10" aria-hidden="true"></i>
                   </div>
                </div> -->
             </div>
          </div>
       </div>
    </div>
</div>

<div class="row mt-2">
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
                <div class="mt-2">
                    <div id="progress5"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
      <div class="card mt-2">
          <div class="card-header">
                <h5>Chart Overview</h5>
            </div>
            <div class="card-body">
              <div id="pieChart"></div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('customScript')
<script type="text/javascript" src="{{ storage_asset('softtheme/js/plugins/chartjs.min.js')}}"></script>
<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function () {
          var options = {
              chart: {
                  type: 'pie'
              },
              series: [
                  {{ $transaction->successfullP }},
                  {{ $transaction->declinedP }},
                  {{ $transaction->refundP }},
                  {{ $transaction->chargebackP }}
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
                }
          }

          var chart = new ApexCharts(document.querySelector("#pieChart"), options);

          chart.render();
      });

    <?php 
        $transactionsDate = array_column($transactionsLine, 'date');
        $successTransactions = array_column($transactionsLine, 'successfullV');
        $declinedTransactions = array_column($transactionsLine, 'declinedV');
    ?>
    var transactionsDate = @json($transactionsDate);
    var successTransactions = @json($successTransactions);
    var declinedTransactions = @json($declinedTransactions);

    var successSeries = [];
    var declinedSeries = [];

    // Combine the date with the transaction data
    for (var i = 0; i < transactionsDate.length; i++) {
        successSeries.push([new Date(transactionsDate[i]).getTime(), successTransactions[i]]);
        declinedSeries.push([new Date(transactionsDate[i]).getTime(), declinedTransactions[i]]);
    }

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

    var optionsProgress5 = {
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
          name: "Block",
          data: [<?php echo $transaction->blockP; ?>]
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
        text: "Block"
      },
      subtitle: {
        floating: true,
        align: "right",
        offsetY: 0,
        text: "<?php echo $transaction->blockP; ?>%",
        style: {
          fontSize: "20px"
        }
      },
      tooltip: {
        enabled: false
      },
      xaxis: {
        categories: ["Block"]
      },
      yaxis: {
        max: 100
      }
    };

    var chartProgress5 = new ApexCharts(
      document.querySelector("#progress5"),
      optionsProgress5
    );
    chartProgress5.render();

    (function($) {
    'use strict';
    $(document).ready(function() {
      if($('#saleAnalytics').length) {
        var saleAnalyticsoptions = {
            series: [{
                name: 'Successful',
                color: '#cb0c9f',
                data: successTransactions
            }, {
                name: 'Declined',
                color: '#a9b4cc',
                data: declinedTransactions
            }],
            chart: {
                height: 354,
                type: 'area',
                toolbar: {
                    show: false
                },
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                width: 1,
                curve: 'smooth'
            },
            xaxis: {
                fill: '#FFFFFF',
                type: 'datetime',
                categories: transactionsDate,
                labels: {
                    format: 'dddd',
                },
                axisBorder: {
                    show: false,
                },
                axisTicks: {
                    show: false,
                },
            },
            grid: {
                borderColor: '#334652',
                strokeDashArray: 3,
                xaxis: {
                    lines: {
                        show: false,
                    }
                },
                padding: {
                    bottom: 15
                }
            },
            responsive: [{
                breakpoint: 479,
                options: {
                    chart: {
                        height: 250,
                    },
                },
            }]
        };
        var saleAnalytics = new ApexCharts(document.querySelector("#saleAnalytics"), saleAnalyticsoptions);
        saleAnalytics.render();
      }
    });
})(jQuery);

</script>
@endsection