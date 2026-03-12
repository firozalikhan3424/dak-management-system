(function () {
  if (window.dashboardStats && document.getElementById('dakChart')) {
    const c = document.getElementById('dakChart');
    new Chart(c, {
      type: 'bar',
      data: {
        labels: window.dashboardStats.labels,
        datasets: [{
          label: 'Incoming DAK',
          data: window.dashboardStats.values,
          backgroundColor: '#075985'
        }]
      },
      options: {responsive: true, plugins: {legend: {display: false}}}
    });
  }
})();
