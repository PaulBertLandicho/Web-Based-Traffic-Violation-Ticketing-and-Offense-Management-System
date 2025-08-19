function printOnlyCharts() {
    const originalCharts = document.querySelectorAll(
        "#printableReportCharts .mycard"
    );

    const printWindow = window.open("", "", "width=1200,height=1000");
    let lineAndBarCharts = "";
    let pieAndDoughnutCharts = "";

    originalCharts.forEach((card) => {
        const canvas = card.querySelector("canvas");
        const chartTitle = card.querySelector("h3")?.outerHTML || "";
        const summary = card.querySelector(".summary")?.outerHTML || "";
        const selects = card.querySelectorAll("select");

        if (canvas) {
            const imageURL = canvas.toDataURL("image/png");

            // Get month/year dropdown values
            let selectHTML = "";
            selects.forEach((select) => {
                const selectedText =
                    select.options[select.selectedIndex]?.text || "";
                const label = select.getAttribute("id")?.includes("Year")
                    ? "Year"
                    : "Month";
                selectHTML += `<div><strong>${label}:</strong> ${selectedText}</div>`;
            });

            const chartType = canvas.id;
            const isLineOrBar = [
                "issuedFineCount",
                "totalFineAmount",
                "violationsPerBarangayChart",
            ].includes(chartType);

            const cardHTML = `
<div class="${isLineOrBar ? "fullwidth-chart-card" : "chart-card"}">
${chartTitle}
${selectHTML}
<img class="chart-image" src="${imageURL}" />
${summary}
</div>
`;

            if (isLineOrBar) {
                lineAndBarCharts += cardHTML;
            } else {
                pieAndDoughnutCharts += cardHTML;
            }
        }
    });

    const printHTML = `
<html>
<head>
<title>Print Charts</title>
<style>
@media print {
body {
font-family: Arial, sans-serif;
margin: 20px;
text-align: center;
}
.section-title {
page-break-before: always;
text-align: center;
font-size: 18px;
margin: 20px 0 10px;
}
.chart-grid {
display: flex;
flex-wrap: wrap;
justify-content: center;
gap: 20px;
margin-top: 30px;
}
.chart-card {
width: 45%;
page-break-inside: avoid;
border: 1px solid #ccc;
padding: 12px;
border-radius: 8px;
box-shadow: 0 0 5px rgba(0,0,0,0.1);
margin-bottom: 25px;
text-align: center;
}
.fullwidth-chart-card {
width: 95%;
page-break-inside: avoid;
border: 1px solid #ccc;
padding: 12px;
border-radius: 8px;
box-shadow: 0 0 5px rgba(0,0,0,0.1);
margin-bottom: 25px;
text-align: center;
}
h3 {
font-size: 16px;
margin-bottom: 10px;
}
.summary {
font-size: 13px;
margin-top: 10px;
text-align: left;
padding: 0 10%;
}
img.chart-image {
max-width: 90%;
height: auto;
display: block;
margin: 0 auto;
}
}
</style>
</head>
<body>
<div class="section-title"><h2>Traffic Violation Charts Report</h2></div>
<div class="chart-grid">
${pieAndDoughnutCharts}
</div>

<div class="section-title"><h2>Traffic Violation Charts Report</h2></div>
<div>
${lineAndBarCharts}
</div>

<script>
window.onload = function () {
window.print();
window.onafterprint = function () {
window.close();
};
};
<\/script>
</body>
</html>
`;

    printWindow.document.open();
    printWindow.document.write(printHTML);
    printWindow.document.close();
}
