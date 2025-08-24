document.addEventListener("DOMContentLoaded", function () {
    const charts = document.querySelectorAll(".chart-animate");

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add("visible"); // Fade in
                } else {
                    entry.target.classList.remove("visible"); // Reset when out of view
                }
            });
        },
        {
            threshold: 0.2,
        }
    ); // Trigger when 20% is visible

    charts.forEach((chart) => {
        observer.observe(chart);
    });
});
