function filterOrders(status) {
    // Update this function if needed
    console.log("Filtering orders by status: " + status);
}

function openModal(orderId) {
    var modal = document.getElementById('myModal');
    var orderDetailsDiv = document.getElementById('orderDetails');

    var orderDetails = {
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
