import ApexCharts from "apexcharts";

const initDoughnutChart = () => {
    const chartElement = document.querySelector("#hs-doughnut-chart");

    if (!chartElement) return;

    const options = {
        series: [70, 20, 10], // Data: Tersedia, Dipinjam, Rusak/Hilang
        labels: ["Tersedia", "Dipinjam", "Rusak/Hilang"],
        colors: ["#2563eb", "#06b6d4", "#d1d5db"], // Blue-600, Cyan-500, Gray-300
        chart: {
            type: "donut",
            height: 220,
            width: "100%",
            fontFamily: "Outfit, sans-serif",
            toolbar: {
                show: false,
            },
        },
        plotOptions: {
            pie: {
                donut: {
                    size: "72%",
                    labels: {
                        show: true,
                        name: {
                            show: true,
                            fontSize: "12px",
                            fontWeight: 500,
                            color: "#64748b",
                            offsetY: -5,
                        },
                        value: {
                            show: true,
                            fontSize: "20px",
                            fontWeight: 700,
                            color: undefined,
                            offsetY: 5,
                            formatter: function (val) {
                                return val + "%";
                            },
                        },
                        total: {
                            show: true,
                            label: "Buku",
                            fontSize: "12px",
                            fontWeight: 500,
                            color: "#9ca3af",
                            formatter: function (w) {
                                return "100%";
                            },
                        },
                    },
                },
            },
        },
        stroke: {
            show: true,
            width: 2,
            colors: [
                document.documentElement.classList.contains("dark")
                    ? "#0f172a"
                    : "#ffffff",
            ],
        },
        dataLabels: {
            enabled: false,
        },
        legend: {
            show: false,
        },
        tooltip: {
            enabled: true,
            theme: document.documentElement.classList.contains("dark")
                ? "dark"
                : "light",
            y: {
                formatter: function (value) {
                    return value + "%";
                },
            },
        },
    };

    // Cleanup existing chart if any
    chartElement.innerHTML = "";
    const chart = new ApexCharts(chartElement, options);
    chart.render();
};

export { initDoughnutChart };
