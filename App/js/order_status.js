function filterOrders(status) {
    // Update this function if needed
    console.log("Filtering orders by status: " + status);
}

function openModal(orderId) {
    var modal = document.getElementById('myModal');
    var orderDetailsDiv = document.getElementById('orderDetails');

    var orderDetails = {
        1: ['Item id: 7', 'Quantity: 1', 'Price: 7990', 'Total Price = 7990'],
        2: ['Item id: 14', 'Quantity: 2', 'Price: 5500', 'Total Price = 11000'],
        5: ['Item id: 4', 'Quantity: 2', 'Price: 35500', 'Total Price = 71000'],
        6: ['Item id: 6', 'Quantity: 1', 'Price: 6490', 'Total Price = 6490'],
        7: ['Item id: 46', 'Quantity: 1', 'Price: 12800', 'Total Price = 12800'],
        8: ['Item id: 1', 'Quantity: 1', 'Price: 7990', 'Total Price = 7990']
    };

    orderDetailsDiv.innerHTML = orderDetails[orderId].join('<br>');
    modal.style.display = 'block';
}

function closeModal() {
    var modal = document.getElementById('myModal');
    modal.style.display = 'none';
    var orderDetailsDiv = document.getElementById('orderDetails');
    orderDetailsDiv.innerHTML = ''; // Clear order details when closing modal
}
