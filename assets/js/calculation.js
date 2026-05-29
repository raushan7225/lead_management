// Get the input fields
var quantityInputs = document.getElementsByName('lead_product_quantity[]');
var offerPriceInputs = document.getElementsByName('lead_product_offer_price[]');
var subtotalInputs = document.getElementsByName('lead_sub_total[]');
var otherChargesInputs = document.getElementsByName('lead_other_charges[]');
var grandTotalInputs = document.getElementsByName('lead_grand_total[]');

// Function to calculate subtotal for each product
function calculateSubtotal(index) {
    var quantity = parseFloat(quantityInputs[index].value);
    var offerPrice = parseFloat(offerPriceInputs[index].value);
    var subtotalValue = quantity * offerPrice;
    subtotalInputs[index].value = subtotalValue.toFixed(2);
}

// Function to calculate total subtotal
function calculateTotalSubtotal() {
    var totalSubtotal = 0;
    for (var i = 0; i < quantityInputs.length; i++) {
        totalSubtotal += parseFloat(subtotalInputs[i].value);
    }
    return totalSubtotal;
}

// Function to calculate grand total
function calculateGrandTotal() {
    var totalSubtotal = calculateTotalSubtotal();
    var otherCharges = 0;
    for (var i = 0; i < otherChargesInputs.length; i++) {
        otherCharges += parseFloat(otherChargesInputs[i].value);
    }
    var grandTotal = totalSubtotal + otherCharges;
    for (var i = 0; i < grandTotalInputs.length; i++) {
        grandTotalInputs[i].value = grandTotal.toFixed(2);
    }
}

// Function to export data to Excel
function exportToExcel() {
    var workbook = new ExcelJS.Workbook();
    var worksheet = workbook.addWorksheet('Sheet1');

    worksheet.addRow(['Product', 'Quantity', 'Offer Price', 'Subtotal']);
    for (var i = 0; i < quantityInputs.length; i++) {
        worksheet.addRow([
            'Product ' + (i + 1),
            quantityInputs[i].value,
            offerPriceInputs[i].value,
            subtotalInputs[i].value
        ]);
    }

    worksheet.addRow(['Other Charges', otherChargesInputs[0].value]);
    worksheet.addRow(['Grand Total', grandTotalInputs[0].value]);

    workbook.xlsx.writeBuffer().then(function (buffer) {
        var blob = new Blob([buffer], {
            type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        });
        var url = window.URL.createObjectURL(blob);
        var a = document.createElement('a');
        a.href = url;
        a.download = 'data.xlsx';
        a.click();
    });
}

// Add event listeners to input fields
for (var i = 0; i < quantityInputs.length; i++) {
    (function (index) {
        quantityInputs[index].addEventListener('input', function () {
            calculateSubtotal(index);
            calculateGrandTotal();
        });
        offerPriceInputs[index].addEventListener('input', function () {
            calculateSubtotal(index);
            calculateGrandTotal();
        });
    })(i);
}
for (var i = 0; i < otherChargesInputs.length; i++) {
    otherChargesInputs[i].addEventListener('input', calculateGrandTotal);
}

// Add event listener to export button
document.getElementById('export-button').addEventListener('click', exportToExcel);

// Initialize subtotal and grand total
for (var i = 0; i < quantityInputs.length; i++) {
    calculateSubtotal(i);
}
calculateGrandTotal();