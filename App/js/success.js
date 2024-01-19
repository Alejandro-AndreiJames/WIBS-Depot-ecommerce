document.addEventListener('DOMContentLoaded', function() {
    // Function to get URL parameters
    function getURLParameter(name) {
        return decodeURIComponent((new RegExp('[?|&]' + name + '=' + '([^&;]+?)(&|#|;|$)').exec(location.search) || [null, ''])[1].replace(/\+/g, '%20')) || null;
    }

    // Check the transaction status from URL parameters
    let transactionStatus = getURLParameter('fund_transfer_success');

    if (transactionStatus === 'true') {
        // Call updateTransactionStatus if the transaction was successful
        updateTransactionStatus();
        document.getElementById('transactionStatus').textContent = 'Transaction Successful';
    } else {
        // Handle other statuses (failed, cancelled, etc.)
        document.getElementById('transactionStatus').textContent = 'Transaction Failed: ';
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

});