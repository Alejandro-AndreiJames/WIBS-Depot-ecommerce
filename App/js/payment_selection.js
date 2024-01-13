document.addEventListener('DOMContentLoaded', function() {
    // Fetch seller account details
    fetch('https://thefusionseller.online/api_endpoints/get_seller_account_details.php?seller_id=1')
    .then(response => response.json())
    .then(data => {
        if (Array.isArray(data) && data.length > 0) {
            const sellerDetails = data[0];
            document.getElementById('bankCode').textContent = sellerDetails.seller_bank_code;
            document.getElementById('recipientNumber').textContent = sellerDetails.seller_account_number;
        } else {
            console.error('Response data is not an array or is empty');
        }
    })
    .catch(error => console.error('Error fetching seller details:', error));

    // Handle form submission for fund transfer
    document.querySelector('form').addEventListener('submit', function(event) {
        event.preventDefault();

        var transactionAmount = document.getElementById('transactionAmount').innerText;
        var vrznAccountNo = document.getElementById('vrznAccountNo').innerText;
        var apexAccountNo = document.getElementById('apexAccountNo').innerText;
        var selectedBank = document.querySelector('input[name="selected_bank"]:checked').value;
        var sourceAccountNo = selectedBank === 'vrzn' ? vrznAccountNo : apexAccountNo;
        
        // Assuming sellerDetails are fetched successfully
        const recipientAccountNo = sellerDetails.seller_account_number;
        const bankCode = sellerDetails.seller_bank_code;
        const redirectUrl = 'https://wibs.tech/App/pages/order_status.php';

        fetch('https://projectvrzn.online/vrzn-bank/app/database/fund-transfer-sample.php', {
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
                // Retrieve PO ID from a hidden element or similar
                const poId = document.getElementById('poIdElement').innerText; // Replace 'poIdElement' with your actual element ID

                // Update PO status
                fetch('update_status.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ po_id: poId })
                })
                .then(response => response.text())
                .then(statusUpdateResult => {
                    console.log(statusUpdateResult);
                    // Redirect or update UI
                    setTimeout(function () {
                        window.location.href = data.redirect_url;
                    }, 2000);
                })
                .catch(error => console.error('Error updating PO status:', error));
            } else {
                console.error('Error recording transaction:', data.message);
            }
        })
        .catch(error => console.error('Fetch error:', error));
    });
});
