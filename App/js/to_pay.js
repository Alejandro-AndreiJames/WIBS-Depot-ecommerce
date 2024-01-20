function updateTransactionStatus() {
    var poIdElement = document.getElementById('poIdElement');
    if (!poIdElement) {
        console.error('PO ID element not found');
        return;
    }

    const poId = poIdElement.getAttribute('data-po-id');
    if (!poId) {
        console.error('PO ID not set');
        return;
    }

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

function openPaymentModal(poId, grandTotal) {
    document.getElementById('modalGrandTotal').textContent = 'Grand Total: ' + grandTotal;

    // Fetch the seller details
    fetch('https://thefusionseller.online/api_endpoints/get_seller_account_details.php?seller_id=1')
        .then(response => response.json())
        .then(data => {
            if (Array.isArray(data) && data.length > 0) {
                const sellerDetails = data[0];
                document.getElementById('modalBankCode').textContent = 'Bank Code: ' + sellerDetails.seller_bank_code;
                document.getElementById('modalRecipientNumber').textContent = 'Recipient Number: ' + sellerDetails.seller_account_number;
            } else {
                console.error('Response data is not an array or is empty');
            }
        })
        .catch(error => {
            console.error('Error fetching seller details:', error);
        });

    // Display the modal
    var modal = document.getElementById('paymentModal');
    modal.style.display = "block";


    var poIdElement = document.getElementById('poIdElement');
    if (poIdElement) {
        poIdElement.setAttribute('data-po-id', poId);
    }

    console.log(poId);

}
    function closePaymentModal() {
        var modal = document.getElementById('paymentModal');
        modal.style.display = "none";
    }
    
    // For closing the modal when clicking outside of it
    window.onclick = function(event) {
        var modal = document.getElementById('paymentModal');
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
    
    async function processPayment(selectedBank) {
        var transactionAmount = document.getElementById('modalGrandTotal').textContent;
        // Assume these elements contain the necessary account numbers
        var vrznAccountNo = document.getElementById('vrznAccountNo').innerText;
        var apexAccountNo = document.getElementById('apexAccountNo').innerText;
        var recipientAccountNo = document.getElementById('modalRecipientNumber').textContent;
        var bankCode = document.getElementById('modalBankCode').textContent;
        var redirectUrl = 'https://wibs.tech/App/pages/order_status.php';  // Your redirect URL
    
        if (selectedBank === 'vrzn') {
            let sourceAccountNo = vrznAccountNo;
            const formData = new FormData();

            formData.append('source_account_no', sourceAccountNo);
            formData.append('recipient_account_no', recipientAccountNo);
            formData.append('transaction_amount', transactionAmount);
            formData.append('recipient_bank_code', bankCode);
            formData.append('redirect_url', redirectUrl);

            fetch('https://projectvrzn.online/vrzn-bank/app/database/transfer.php', {
                method: 'POST',
                body: formData // Use the FormData object as the body
              })
                .then((response) => response.json())
                .then((data) => {
                  console.log('Fetch success:', data);
                  if (data.success) {
                    updateTransactionStatus();
                    window.location.href = data.redirect_url;
                  } else {
                    console.error('Transfer Failed:', data.message);
                  }
                })
                .catch((error) => {
                  console.error('Fetch Error:', error);
                });
        } else if (selectedBank === 'apex') {
            let sourceAccountNo = apexAccountNo;
            let url = "https://apexapp.tech/app/client/backend/api/fund-transfer-external.php";
    
            let requestBody = new FormData();
            requestBody.append('redirect_url', redirectUrl);
            requestBody.append('transaction_amount', transactionAmount);
            requestBody.append('source_account_no', sourceAccountNo);
            requestBody.append('recipient_account_no', recipientAccountNo);
            requestBody.append('recipient_bank_code', "VRZN");
    
            let APIResponse = await fetchAPI(url, requestBody);
            handleResponse(APIResponse);
        }
    
        function handleResponse(data) {
            if (data.success) {
                updateTransactionStatus();
                setTimeout(function () {
                    window.location.href = data.redirect_url;
                }, 2000);             
            } else {
                console.error('Error recording transaction:', data.message);
            }
        }
    
        function handleError(error) {
            console.error('Fetch error:', error);
        }
    }
    
    async function fetchAPI(url, requestBody) {
        let response = await fetch(url, {
            method: 'POST',
            body: requestBody
        });
    
        let statusCode = response.status;
        let data = await response.json();
    
        if (statusCode === 302) {
            updateTransactionStatus();
            window.location.href = data.location;
            return;
        }
    
        data.statusCode = statusCode;
        return data;
    }
    