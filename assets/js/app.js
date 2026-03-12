(function () {
  const el = document.getElementById('monthlyChart');
  if (el && window.dashboardMonthlyData) {
    new Chart(el, {
      type: 'line',
      data: {
        labels: window.dashboardMonthlyData.labels,
        datasets: [{
          label: 'Monthly DAK',
          data: window.dashboardMonthlyData.values,
          borderColor: '#0ea5e9',
          backgroundColor: 'rgba(14,165,233,.15)',
          tension: 0.3,
          fill: true
        }]
      }
    });
  }
})();
