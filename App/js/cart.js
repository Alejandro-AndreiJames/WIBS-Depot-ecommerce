function confirmRemove() {
    return confirm('Are you sure you want to remove this item from the cart?');
}

document.addEventListener('DOMContentLoaded', (event) => {
    var itemRemoved = document.getElementById('itemRemoved').getAttribute('data-removed');
    if (itemRemoved === "true") {
        showNotification("Item removed from cart successfully");
    }
});

document.querySelectorAll('.quantity-change').forEach(button => {
    button.addEventListener('click', function() {
        let quantityInput = this.parentNode.querySelector('.quantity-input');
        let currentQuantity = parseInt(quantityInput.value);
        if (this.getAttribute('data-change') === 'plus') {
            quantityInput.value = currentQuantity + 1;
        } else if (currentQuantity > 1) {
            quantityInput.value = currentQuantity - 1;
        }
        // Trigger form submission for update
        this.parentNode.parentNode.submit();
    });
});

function showNotification(message) {
    // Create the notification element
    var notification = document.createElement('div');
    notification.className = 'notification show';
    notification.innerHTML = '<span class="close-btn">&times;</span>' + message;
    
    // Append notification to the body
    document.body.appendChild(notification);

    // Set up the close button
    var closeBtn = notification.querySelector('.close-btn');
    closeBtn.onclick = function() {
        notification.classList.remove('show');
        setTimeout(function() {
            document.body.removeChild(notification);
        }, 200);
    };

    // Automatically remove the notification after 3 seconds
    setTimeout(function() {
        notification.classList.remove('show');
        setTimeout(function() {
            document.body.removeChild(notification);
        }, 200);
    }, 1500);
}
