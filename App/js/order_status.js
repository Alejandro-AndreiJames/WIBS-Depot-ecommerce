document.addEventListener('DOMContentLoaded', function() {
    checkTransactionStatus();
    function checkTransactionStatus() {
        alert("checkTransactionStatus function is called");
        const urlParams = new URLSearchParams(window.location.search);
        const transactionStatus = urlParams.get('fund_transfer_success');

        if (transactionStatus === 'true') {
                updateTransactionStatus();
        } else {
            console.error('User indicated transaction was not completed.');
            }
        }

        function updateTransactionStatus() {
            const poId = document.getElementById('poIdElement').getAttribute('data-po-id');
    
            fetch('update_status.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: 'po_id=' + poId
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                } else {
                    console.error('Error updating transaction status:', data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }        

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
});