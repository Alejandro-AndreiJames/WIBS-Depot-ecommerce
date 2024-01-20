    function fetchDeliveryHistory(deliveryReferenceNumber) {
        fetch(`https://cybertechlogistic.online/app/controller/get-delivery-history-api.php?delivery_reference_number=${deliveryReferenceNumber}`)
            .then(response => response.json())
            .then(data => {
                displayDeliveryHistory(data);
            })
            .catch(error => {
                console.error('Error fetching delivery history:', error);
            });
    }

    function displayDeliveryHistory(historyData) {
        const orderDetailsDiv = document.getElementById('orderDetails');
        if (historyData && historyData.length > 0) {
            const historyList = document.createElement('ul');
            historyData.forEach(entry => {
                const listItem = document.createElement('li');
                listItem.innerHTML = `<strong>${entry.timestamp}</strong>: ${entry.checkpoint_location} - ${entry.description}`;
                historyList.appendChild(listItem);
            });
            orderDetailsDiv.appendChild(historyList);
        } else {
            orderDetailsDiv.innerHTML = 'No delivery history available.';
        }   
    }

function openModal(orderId, deliveryReferenceNumber) {
    var modal = document.getElementById('myModal');
    var orderDetailsDiv = document.getElementById('orderDetails');

    // Clear existing content
    orderDetailsDiv.innerHTML = '';

    // Fetch and display delivery history
    fetchDeliveryHistory(deliveryReferenceNumber);

    modal.style.display = 'block';
}

    function closeModal() {
        var modal = document.getElementById('myModal');
        modal.style.display = 'none';
        var orderDetailsDiv = document.getElementById('orderDetails');
        orderDetailsDiv.innerHTML = ''; // Clear order details when closing modal
    }