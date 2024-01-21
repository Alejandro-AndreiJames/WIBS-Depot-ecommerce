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
        let baseRedirectUrl = 'https://wibs.tech/App/pages/order_status.php';
        let redirectUrl ='https://wibs.tech/App/pages/order_status.php?';
    
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
            requestBody.append('redirect_url', baseRedirectUrl);
            requestBody.append('transaction_amount', transactionAmount);
            requestBody.append('source_account_no', sourceAccountNo);
            requestBody.append('recipient_account_no', recipientAccountNo);
            requestBody.append('recipient_bank_code', "VRZN");
    
            let APIResponse = await fetchAPI(url, requestBody);
            handleResponse(APIResponse);
        }
    
        function handleResponse(data) {
            if (data.success) {
                setTimeout(function () {
                    window.location.href = data.redirect_url;
                }, 2000);             
            } else {
                console.error('Error recording transaction:', data.message);
            }
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
            window.location.href = data.location;
            return;
        }
    
        data.statusCode = statusCode;
        return data;
    }
    