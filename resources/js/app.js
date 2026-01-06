import "./bootstrap";
import "preline";
import Alpine from "alpinejs";
import ApexCharts from "apexcharts";

// flatpickr
import flatpickr from "flatpickr";
import "flatpickr/dist/flatpickr.min.css";
// FullCalendar
import { Calendar } from "@fullcalendar/core";

import { Indonesian } from "flatpickr/dist/l10n/id.js";

window.Alpine = Alpine;
window.ApexCharts = ApexCharts;
window.flatpickr = flatpickr;
window.FullCalendar = Calendar;

Alpine.start();

// Initialize components on DOM ready
document.addEventListener("DOMContentLoaded", () => {
    // Global Date Picker (Flatpickr)
    flatpickr("input[type=date]", {
        locale: Indonesian,
        altInput: true,
        altFormat: "j F Y",
        dateFormat: "Y-m-d",
        allowInput: true, // Allow manual typing if needed
        disableMobile: "true", // Use native picker on mobile for better UX? Or force flatpickr? User said "pop up", maybe force it. Let's try default behavior first but ensure desktop has it.
        // Tailwind CSS class 'form-input' might differ, but flatpickr wraps it.
        // We might need to ensure the wrapper has classes.
        // But app.css already styles .flatpickr-calendar
    });

    // Map imports
    if (document.querySelector("#mapOne")) {
        import("./components/map").then((module) => module.initMap());
    }

    // ... rest of imports

    // Chart imports
    if (document.querySelector("#chartOne")) {
        import("./components/chart/chart-1").then((module) =>
            module.initChartOne()
        );
    }
    if (document.querySelector("#chartTwo")) {
        import("./components/chart/chart-2").then((module) =>
            module.initChartTwo()
        );
    }
    if (document.querySelector("#chartThree")) {
        import("./components/chart/chart-3").then((module) =>
            module.initChartThree()
        );
    }
    if (document.querySelector("#chartSix")) {
        import("./components/chart/chart-6").then((module) =>
            module.initChartSix()
        );
    }
    if (document.querySelector("#chartEight")) {
        import("./components/chart/chart-8").then((module) =>
            module.initChartEight()
        );
    }
    if (document.querySelector("#chartThirteen")) {
        import("./components/chart/chart-13").then((module) =>
            module.initChartThirteen()
        );
    }

    // Doughnut Chart
    if (document.querySelector("#hs-doughnut-chart")) {
        import("./components/chart/doughnut-chart").then((module) =>
            module.initDoughnutChart()
        );
    }

    // Calendar init
    if (document.querySelector("#calendar")) {
        import("./components/calendar-init").then((module) =>
            module.calendarInit()
        );
    }
});
