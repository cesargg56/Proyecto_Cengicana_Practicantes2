import fs from "node:fs/promises";
import { SpreadsheetFile, Workbook } from "@oai/artifact-tool";

const outputDir = "C:/xampp/htdocs/visual/outputs/compensacion_asistentes";
await fs.mkdir(outputDir, { recursive: true });

const workbook = Workbook.create();
const sheet = workbook.worksheets.add("Plan compensacion");
sheet.showGridLines = false;

const title = "PLAN DE COMPENSACION ASISTENTES";
sheet.getRange("A1:D1").merge();
sheet.getRange("A1:D1").values = [[title]];
sheet.getRange("A1:D1").format = {
  fill: "#E7E5E4",
  font: { bold: true, size: 16, color: "#111827" },
  horizontalAlignment: "center",
  verticalAlignment: "center",
};
sheet.getRange("A1:D1").format.rowHeightPx = 34;

sheet.getRange("A3:D3").values = [["Concepto", "Rango / Requisito", "Monto", "Observaciones"]];
sheet.getRange("A3:D3").format = {
  fill: "#404040",
  font: { bold: true, color: "#FFFFFF" },
  horizontalAlignment: "center",
  verticalAlignment: "center",
};

const rows = [
  ["Base", "Base 1: de 1 a 8 contratos", 700, ""],
  ["", "Base 2: de 1 a 10 contratos", 1000, ""],
  ["", "Base 3: de 1 a 15 contratos", 1500, ""],
  ["Contratos", "De 1 a 2", 50, ""],
  ["", "De 1 a 4", 75, ""],
  ["", "De 5 en adelante", 100, "Transcrito de la foto"],
  ["", "De 10 en adelante", 150, ""],
  ["Actividad", "89%", 400, ""],
  ["", "90%", 600, ""],
  ["", "95%", 700, ""],
  ["Cobro", "99%", 400, "Si cobran"],
  ["", "100%", 500, ""],
  ["Crecimiento", "5", 300, "Real"],
  ["", "7", 500, ""],
  ["PB Retencion", "85%", 400, ""],
  ["", "95%", 500, ""],
  ["Reingresos", "De 1 a 3", 25, ""],
  ["", "De 4 en adelante", 75, "25 primer pedido / 50 segundo pedido"],
];

sheet.getRange("A4:D21").values = rows;
sheet.getRange("C4:C21").format.numberFormat = '"Q"#,##0.00';
sheet.getRange("A4:A21").format.font = { bold: true };
sheet.getRange("A4:D21").format = {
  font: { size: 11, color: "#111827" },
  verticalAlignment: "center",
  wrapText: true,
};

for (const range of ["A4:A6", "A7:A10", "A11:A13", "A14:A15", "A16:A17", "A18:A19", "A20:A21"]) {
  sheet.getRange(range).merge();
  sheet.getRange(range).format = {
    fill: "#F5F5F4",
    font: { bold: true, color: "#111827" },
    verticalAlignment: "center",
  };
}

for (const row of [4, 7, 11, 14, 16, 18, 20]) {
  sheet.getRange(`A${row}:D${row}`).format = {
    fill: row === 4 ? "#FFFFFF" : "#FAFAF9",
    font: { size: 11, color: "#111827" },
    verticalAlignment: "center",
    wrapText: true,
  };
  sheet.getRange(`A${row}`).format = {
    fill: "#F5F5F4",
    font: { bold: true, color: "#111827" },
    verticalAlignment: "center",
  };
}

sheet.getRange("A:A").format.columnWidthPx = 145;
sheet.getRange("B:B").format.columnWidthPx = 235;
sheet.getRange("C:C").format.columnWidthPx = 135;
sheet.getRange("D:D").format.columnWidthPx = 280;
sheet.getRange("A4:D21").format.rowHeightPx = 30;
sheet.getRange("D18:D21").format.rowHeightPx = 42;
sheet.freezePanes.freezeRows(3);

const noteRange = sheet.getRange("A23:D24");
noteRange.merge();
noteRange.values = [["Nota: La tabla fue creada con los datos legibles de la segunda foto. El renglón de reingresos se conservó como observación por estar escrito en formato de nota."]];
noteRange.format = {
  fill: "#FFF7ED",
  font: { italic: true, color: "#7C2D12", size: 10 },
  wrapText: true,
  verticalAlignment: "center",
};
noteRange.format.rowHeightPx = 42;

const preview = await workbook.render({
  sheetName: "Plan compensacion",
  autoCrop: "all",
  scale: 1,
  format: "png",
});
await fs.writeFile(`${outputDir}/preview.png`, new Uint8Array(await preview.arrayBuffer()));

const xlsx = await SpreadsheetFile.exportXlsx(workbook);
await xlsx.save(`${outputDir}/plan_compensacion_asistentes.xlsx`);

const check = await workbook.inspect({
  kind: "table",
  range: "Plan compensacion!A1:D24",
  include: "values,formulas",
  tableMaxRows: 30,
  tableMaxCols: 6,
});
console.log(check.ndjson);

const errors = await workbook.inspect({
  kind: "match",
  searchTerm: "#REF!|#DIV/0!|#VALUE!|#NAME\\?|#N/A",
  options: { useRegex: true, maxResults: 50 },
  summary: "formula error scan",
});
console.log(errors.ndjson);

console.log(`${outputDir}/plan_compensacion_asistentes.xlsx`);
