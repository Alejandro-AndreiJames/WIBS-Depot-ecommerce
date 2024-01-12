document.addEventListener('DOMContentLoaded', function() {
    
    fetch('https://thefusionseller.online/api_endpoints/get_seller_account_details.php?seller_id=1')
    .then(response => response.json())
    .then(data => {
        // Check if data is an array and has at least one element
        if (Array.isArray(data) && data.length > 0) {
            // Access the first element of the array
            sellerDetails = data[0];
        } else {
            console.error('Response data is not an array or is empty');
        }
    })
    .catch(error => console.error('Error fetching seller details:', error));


    // Handle form submission
    document.querySelector('form').addEventListener('submit', function(event) {
        event.preventDefault();

        // Fetch transaction details from session or form
        var transactionAmount = document.getElementById('transactionAmount').innerText;
        var vrznAccountNo = document.getElementById('vrznAccountNo').innerText;
        var apexAccountNo = document.getElementById('apexAccountNo').innerText;
        const recipientAccountNo = sellerDetails.seller_account_number;
        const bankCode = sellerDetails.seller_bank_code;
        const redirectUrl = 'https://wibs.tech/App/pages/order_status.php';

        var selectedBank = document.querySelector('input[name="selected_bank"]:checked').value;
        var sourceAccountNo = selectedBank === 'vrzn' ? vrznAccountNo : apexAccountNo;

        // Send POST request
        fetch('https://projectvrzn.online/vrzn-bank/app/database/funds-transfer.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                transaction_amount: transactionAmount,
                source_account_no: sourceAccountNo,
                recipient_account_no: recipientAccountNo,
                bank_code: bankCode,
                redirect_url: redirectUrl
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                setTimeout(function () {
                    window.location.href = data.redirect_url;
                }, 2000);
            } else {
                console.error('Error recording transaction:', data.message);
            }
        })
        .catch(error => {
            console.error('Fetch error:', error);
        });
    });
});