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
                
                // Create separate elements for date, time, and text
                const dateElement = document.createElement('div');
                dateElement.classList.add('history-date');
                const dateParts = entry.timestamp.split(' ')[0].split('-');
                const formattedDate = `${dateParts[1]}/${dateParts[2]}/${dateParts[0]}`;
                dateElement.innerText = formattedDate; // Format date as MM/DD/YYYY
                listItem.appendChild(dateElement);
    
                const timeElement = document.createElement('div');
                timeElement.classList.add('history-time');
                const militaryTime = entry.timestamp.split(' ')[1];
                const timeParts = militaryTime.split(':');
                const hours = parseInt(timeParts[0], 10);
                const ampm = hours >= 12 ? 'PM' : 'AM';
                const formattedHours = hours % 12 || 12;
                const formattedTime = `${formattedHours}:${timeParts[1]} ${ampm}`;
                timeElement.innerText = formattedTime; // Format time as hh:mm AM/PM
                listItem.appendChild(timeElement);
    
                const textElement = document.createElement('div');
                textElement.classList.add('history-text');
                textElement.innerText = `${entry.checkpoint_location} - ${entry.description}`;
                listItem.appendChild(textElement);
    
                historyList.appendChild(listItem);
            });
            orderDetailsDiv.appendChild(historyList);
        } else {
            orderDetailsDiv.innerHTML = 'Seller is preparing to ship your order';
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


// Function to toggle the dropdown
function toggleDropdown() {
    document.getElementById("myDropdown").classList.toggle("show");
}

// Close the dropdown menu if the user clicks outside of it
window.onclick = function(event) {
  if (!event.target.matches('.dropbtn')) {
    var dropdowns = document.getElementsByClassName("dropdown-content");
    for (var i = 0; i < dropdowns.length; i++) {
      var openDropdown = dropdowns[i];
      if (openDropdown.classList.contains('show')) {
        openDropdown.classList.remove('show');
      }
    }
  }
}
