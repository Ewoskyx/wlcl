/*=========================================================================================
    File Name: dashboard-ecommerce.js
    Description: dashboard ecommerce page content with Apexchart Examples
    ----------------------------------------------------------------------------------------
    Item Name: Frest HTML Admin Template
    Version: 1.0
    Author: PIXINVENT
    Author URL: http://www.themeforest.net/user/pixinvent
==========================================================================================*/

$(window).on("load", function () {

  var $primary = '#5A8DEE';
  var $danger = '#FF5B5C';
  var $warning = '#FDAC41';
  var $info = '#00CFDD';
  var $secondary = '#828D99';
  var $secondary_light = '#e7edf3';
  var $light_primary = "#E2ECFF";


  // Multi Radial Statistics
  // -----------------------
  var multiRadialOptions = {
    chart: {
      height: 210,
      type: "radialBar",
    },
    colors: [$primary, $danger, $warning],
    series: [75, 80, 85],
    plotOptions: {
      radialBar: {
        offsetY: -10,
        hollow: {
          size: "40%"
        },
        track: {
          margin: 10,
          background: '#fff',
        },
        dataLabels: {
          name: {
            fontSize: '15px',
            colors: [$secondary],
            fontFamily: "IBM Plex Sans",
            offsetY: 25,
          },
          value: {
            fontSize: '30px',
            fontFamily: "Rubik",
            offsetY: -15,
          },
          total: {
            show: true,
            label: 'Total Visits',
            fontSize: '15px',
            fontWeight: 400,
            fontFamily: "IBM Plex Sans",
            color: $secondary
          }
        }
      }
    },
    stroke: {
      lineCap: "round",
    },
    labels: ['Target', 'Mart', 'Ebay']
  };

  var multiradialChart = new ApexCharts(
    document.querySelector("#multi-radial-chart"),
    multiRadialOptions
  );
  multiradialChart.render();


  // revenue chart In a week
  var in_a_week = {
    series: [{
    name: 'Inflation',
    data: [2.3, 3.1, 4.0, 10.1, 4.0, 3.6]
  }],
    chart: {
    height: 350,
    type: 'bar',
  },
  stroke: {
    width: 2
  },
  
  grid: {
    row: {
      colors: ['#fff', '#f2f2f2']
    }
  },
  fill: {
    type: 'gradient',
    gradient: {
      shade: 'light',
      type: "horizontal",
      shadeIntensity: 0.25,
      gradientToColors: undefined,
      inverseColors: true,
      opacityFrom: 0.85,
      opacityTo: 0.85,
      stops: [50, 0, 100]
    },
  },
  plotOptions: {
    bar: {
      borderRadius: 20,
      dataLabels: {
        position: 'top', // top, center, bottom
      },
    }
  },
  dataLabels: {
    enabled: true,
    formatter: function (val) {
      return val + "$";
    },
    offsetY: -20,
    style: {
      fontSize: '12px',
      colors: ["#304758"]
    }
  },
  
  xaxis: {
    categories: ["Json", "Eva", "Simith", "Alen", "David","Martin"],
    position: 'bottom',
    axisBorder: {
      show: false
    },
    axisTicks: {
      show: false
    },
    crosshairs: {
      fill: {
        type: 'gradient',
        gradient: {
          colorFrom: '#D8E3F0',
          colorTo: '#BED1E6',
          stops: [0, 100],
          opacityFrom: 0.4,
          opacityTo: 0.5,
        }
      }
    },
    tooltip: {
      enabled: true,
    }
  },
  yaxis: {
    axisBorder: {
      show: false
    },
    axisTicks: {
      show: false,
    },
    labels: {
      show: false,
      formatter: function (val) {
        return val + "$";
      }
    }
  
  },
  };
  var chart_in_a_week = new ApexCharts(document.querySelector("#in_a_week"), in_a_week);
  chart_in_a_week.render();
  
  // revenue char by city
  var by_city = {
    series: [{
    name: 'Servings',
    data: [44, 55, 41, 67, 22, 43]
  }],
    annotations: {
    points: [{
      x: 'Bananas',
      seriesIndex: 0,
      label: {
        borderColor: '#775DD0',
        offsetY: 0,
        style: {
          color: '#fff',
          background: '#775DD0',
        },
        text: 'Bananas are good',
      }
    }]
  },
  chart: {
    height: 350,
    type: 'bar',
  },
  plotOptions: {
    bar: {
      borderRadius: 10,
      columnWidth: '50%',
    }
  },
  dataLabels: {
    enabled: false
  },
  stroke: {
    width: 2
  },
  
  grid: {
    row: {
      colors: ['#fff', '#f2f2f2']
    }
  },
  xaxis: {
    labels: {
      rotate: -45
    },
    categories: ['London', 'Liverpool', 'Oxford', 'Newcastle', 'Menchester', 'Lines'],
    tickPlacement: 'on'
  },
  yaxis: {
    title: {
      text: 'Servings',
    },
  },
  fill: {
    type: 'gradient',
    gradient: {
      shade: 'light',
      type: "horizontal",
      shadeIntensity: 0.25,
      gradientToColors: undefined,
      inverseColors: true,
      opacityFrom: 0.85,
      opacityTo: 0.85,
      stops: [50, 0, 100]
    },
  }
  };
  var chart_by_city = new ApexCharts(document.querySelector("#by_city"), by_city);
  chart_by_city.render();

  //revenue by the day of the week
  var by_the_day_of_the_week = {
    series: [{
    name: 'Inflation',
    data: [2.3, 3.1, 4.0, 10.1, 4.0, 3.6,5]
  }],
    chart: {
    height: 350,
    type: 'bar',
  },
  plotOptions: {
    bar: {
      borderRadius: 20,
      dataLabels: {
        position: 'top', // top, center, bottom
      },
    }
  },
  dataLabels: {
    enabled: true,
    formatter: function (val) {
      return val + "$";
    },
    offsetY: -20,
    style: {
      fontSize: '12px',
      colors: ["#304758"]
    }
  },
  stroke: {
    width: 2
  },
  
  grid: {
    row: {
      colors: ['#fff', '#f2f2f2']
    }
  },
  fill: {
    type: 'gradient',
    gradient: {
      shade: 'light',
      type: "horizontal",
      shadeIntensity: 0.25,
      gradientToColors: undefined,
      inverseColors: true,
      opacityFrom: 0.85,
      opacityTo: 0.85,
      stops: [50, 0, 100]
    },
  },
  xaxis: {
    categories: ["Mon", "Tue", "Wed", "Thurs", "Fri","Satur",'Sun'],
    position: 'bottom',
    axisBorder: {
      show: false
    },
    axisTicks: {
      show: false
    },
    crosshairs: {
      fill: {
        type: 'gradient',
        gradient: {
          colorFrom: '#D8E3F0',
          colorTo: '#BED1E6',
          stops: [0, 100],
          opacityFrom: 0.4,
          opacityTo: 0.5,
        }
      }
    },
    tooltip: {
      enabled: true,
    }
  },
  yaxis: {
    axisBorder: {
      show: false
    },
    axisTicks: {
      show: false,
    },
    labels: {
      show: false,
      formatter: function (val) {
        return val + "$";
      }
    }
  
  },
  };
  var chart_by_the_day_of_the_week = new ApexCharts(document.querySelector("#by_the_day_of_the_week"), by_the_day_of_the_week);
  chart_by_the_day_of_the_week.render();

  //top purchesed oriducts
  var top_purchesed_products = {
    series: [{
    name: 'Inflation',
    data: [2.3, 3.1, 4.0, 10.1, 4.0, 3.6]
  }],
    chart: {
    height: 350,
    type: 'bar',
  },
  plotOptions: {
    bar: {
      borderRadius: 20,
      dataLabels: {
        position: 'top', // top, center, bottom
      },
    }
  },
  dataLabels: {
    enabled: true,
    formatter: function (val) {
      return val + "%";
    },
    offsetY: -20,
    style: {
      fontSize: '12px',
      colors: ["#304758"]
    }
  },
  stroke: {
    width: 2
  },
  
  grid: {
    row: {
      colors: ['#fff', '#f2f2f2']
    }
  },
  fill: {
    type: 'gradient',
    gradient: {
      shade: 'light',
      type: "horizontal",
      shadeIntensity: 0.25,
      gradientToColors: undefined,
      inverseColors: true,
      opacityFrom: 0.85,
      opacityTo: 0.85,
      stops: [50, 0, 100]
    },
  },
  xaxis: {
    categories: ["Computer", "Cloths", "Game", "Fastfood", "Food","Fruits"],
    position: 'bottom',
    axisBorder: {
      show: false
    },
    axisTicks: {
      show: false
    },
    crosshairs: {
      fill: {
        type: 'gradient',
        gradient: {
          colorFrom: '#D8E3F0',
          colorTo: '#BED1E6',
          stops: [0, 100],
          opacityFrom: 0.4,
          opacityTo: 0.5,
        }
      }
    },
    tooltip: {
      enabled: true,
    }
  },
  yaxis: {
    axisBorder: {
      show: false
    },
    axisTicks: {
      show: false,
    },
    labels: {
      show: false,
      formatter: function (val) {
        return val + "%";
      }
    }
  
  },
  };
  var chart_top_purchesed_products = new ApexCharts(document.querySelector("#top_purchesed_products"), top_purchesed_products);
  chart_top_purchesed_products.render();

    //customer_purchasing_frequency
  var customer_purchasing_frequency = {
    series: [{
      data:[2.3, 3.1, 4.0, 10.1, 4.0, 3.6, 3.5, 4.5, 1.3, 3.3, 2.3, 3.4]
  }],
    chart: {
    type: 'bar',
    height: 500
  },
  plotOptions: {
    bar: {
      borderRadius: 4,
      horizontal: true,

    }
  },
  dataLabels: {
    enabled: false
  },
  stroke: {
    width: 2
  },
  
  grid: {
    row: {
      colors: ['#fff', '#f2f2f2']
    }
  },
  fill: {
    type: 'gradient',
    gradient: {
      shade: 'light',
      type: "horizontal",
      shadeIntensity: 0.25,
      gradientToColors: undefined,
      inverseColors: true,
      opacityFrom: 0.85,
      opacityTo: 0.85,
      stops: [50, 0, 100]
    },
  },
  xaxis: {
    categories: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September','October','November','December'],
  }
  };

  var chart_customer_purchasing_frequency = new ApexCharts(document.querySelector("#customer_purchasing_frequency"), customer_purchasing_frequency);
  chart_customer_purchasing_frequency.render();

  // Revenue Growth Chart
  // ---------------------
  var revenueChartOptions = {
    chart: {
      height: 100,
      type: 'bar',
      stacked: true,
      toolbar: {
        show: false
      }
    },
    grid: {
      show: false,
      padding: {
        left: 0,
        right: 0,
        top: -20,
        bottom: -15
      }
    },
    plotOptions: {
      bar: {
        horizontal: false,
        columnWidth: '20%',
        endingShape: 'rounded'
      },
    },
    legend: {
      show: false
    },
    dataLabels: {
      enabled: false
    },
    colors: [$info, $secondary_light],
    series: [{
      name: '2020',
      data: [50, 70, 100, 120, 140, 100, 70, 80, 90, 110, 50, 70, 35, 110, 100, 105, 125, 80]
    }, {
      name: '2019',
      data: [70, 50, 20, 30, 20, 90, 90, 60, 50, 0, 50, 60, 140, 50, 20, 20, 10, 0]
    }],
    xaxis: {
      categories: ['0', '', '', '', '', "10", '', '', '', '', '', '15', '', '', '', '', '', '20'],
      axisBorder: {
        show: false
      },
      axisTicks: {
        show: false
      },
      labels: {
        style: {
          colors: $secondary
        },
        offsetY: -5
      }
    },
    yaxis: {
      show: false,
      floating: true,
    },
    tooltip: {
      x: {
        show: false,
      },
    }
  }

  var revenueChart = new ApexCharts(
    document.querySelector("#revenue-growth-chart"),
    revenueChartOptions
  );

  revenueChart.render();

  // Order Summary Chart
  // --------------------
  var orderSummaryChartOptions = {
    series: [{
      name: 'Erken Teslimat',
      data: [44, 55, 57, 56, 61, 58, 63, 60, 66],
      color: "#36c5f4"
    }, {
      name: 'Zaman??nda Teslimat',
      data: [76, 85, 101, 98, 87, 105, 91, 114, 94],
      color: "#203866"
    }, {
      name: 'Ge?? Teslimat',
      data: [35, 41, 36, 26, 45, 48, 52, 53, 41],
      color: "#990000"
    }],
    chart: {
      type: 'bar',
      height: 350
    },
    plotOptions: {
      bar: {
        horizontal: false,
        columnWidth: '80%',
      },
    },
    dataLabels: {
      enabled: false
    },
    stroke: {
      show: false,
      width: 2,
      colors: ['transparent']
    },
    xaxis: {
      categories: ['Ocak', '??ubat', 'Mart', 'Nisan', 'May??s', 'Haziran', 'Temmuz', 'A??ustos', 'Eyl??l'],
    },
    fill: {
      opacity: .75
    },
    tooltip: {
      y: {
        formatter: function (val) {
          return val + " adet"
        }
      }
    }
  };

  var orderSummaryChart = new ApexCharts(
    document.querySelector("#order-summary-chart"),
    orderSummaryChartOptions
  );
  orderSummaryChart.render();

  // var orderSummaryCharCustomers = new ApexCharts(
  //   document.querySelector("#order-summary-chart-customers"),
  //   orderSummaryCharCustomersOptions
  // );

  orderSummaryCharCustomers.render();

  

  // Marketing Campaigns Chart - Success
  // -----------------------------------
  var donutSuccessChartOption = {
    chart: {
      width: 60,
      height: 75,
      type: 'donut'
    },
    dataLabels: {
      enabled: false
    },
    series: [70, 30, 40],
    labels: ["Installation", "Page Views", "Active Users"],
    stroke: {
      width: 2
    },
    colors: [$warning, $info, $primary],
    plotOptions: {
      pie: {
        offsetY: 0,
        donut: {
          size: '70%',
        }
      }
    },
    legend: {
      show: false
    }
  }
  var donutSuccessChart = new ApexCharts(
    document.querySelector("#donut-success-chart"),
    donutSuccessChartOption
  );
  donutSuccessChart.render();

  // Marketing Campaigns Chart - Danger
  // -----------------------------------
  var donutDangerChartOption = {
    chart: {
      width: 60,
      height: 75,
      type: 'donut'
    },
    dataLabels: {
      enabled: false
    },
    series: [70, 40, 30],
    labels: ["Installation", "Page Views", "Active Users"],
    stroke: {
      width: 2
    },
    colors: [$danger, $secondary, $primary],
    plotOptions: {
      pie: {
        offsetY: 0,
        donut: {
          size: '70%',
        }
      }
    },
    legend: {
      show: false
    }
  }
  var donutDangerChart = new ApexCharts(
    document.querySelector("#donut-danger-chart"),
    donutDangerChartOption
  );
  donutDangerChart.render();

  // Earnings Swiper - Perfect Scrollbar
  if ($(".widget-earnings-scroll").length > 0) {
    var widget_earnings = new PerfectScrollbar(".widget-earnings-scroll");
  }

  // Earnings Swiper - Perfect Scrollbar
  if ($(".dashboard-latest-update .card-body").length > 0) {
    var widget_earnings = new PerfectScrollbar(".dashboard-latest-update .card-body");
  }

  // User Details - Perfect Scrollbar
  if ($('.marketing-campaigns .table-responsive').length > 0) {
    var user_details = new PerfectScrollbar('.marketing-campaigns .table-responsive');
  }
  // Earnings Swiper
  // ---------------
  var swiperLength = $(".swiper-slide").length;
  if (swiperLength) {
    swiperLength = Math.floor(swiperLength / 2)
  }

  // Swiper js for this page
  var mySwiper = new Swiper('.widget-earnings-swiper', {
    slidesPerView: 'auto',
    initialSlide: swiperLength,
    centeredSlides: true,
    spaceBetween: 30,
    // active slide on click
    slideToClickedSlide: true,
  });

  activeSlide(swiperLength);

  // Active slide change on swipe
  mySwiper.on('slideChange', function () {
    activeSlide(mySwiper.realIndex);
  });

  //add class active content of active slide
  function activeSlide(index) {
    var slideEl = mySwiper.slides[index]
    var slideId = $(slideEl).attr('id');
    $(".wrapper-content").removeClass("active");
    $("[data-earnings=" + slideId + "]").addClass('active')
  };
});
