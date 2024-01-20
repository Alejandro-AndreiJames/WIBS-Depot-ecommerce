document.addEventListener('DOMContentLoaded', function() {
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

    fetch('https://thefusionseller.online/api_endpoints/get_seller_account_details.php?seller_id=1')
    .then(response => response.json())
    .then(data => {
        // Check if data is an array and has at least one element
        if (Array.isArray(data) && data.length > 0) {
            // Access the first element of the array
            sellerDetails = data[0];
            document.getElementById('bankCode').textContent = sellerDetails.seller_bank_code;
            document.getElementById('recipientNumber').textContent = sellerDetails.seller_account_number;
        } else {
            console.error('Response data is not an array or is empty');
        }
    })
    .catch(error => console.error('Error fetching seller details:', error));

    document.querySelector('form').addEventListener('submit', async function(event) {
        checkTransactionStatus();
        event.preventDefault();
        // Fetch transaction details from session or form
        let transactionAmount = document.getElementById('transactionAmount').innerText;
        let vrznAccountNo = document.getElementById('vrznAccountNo').innerText;
        let apexAccountNo = document.getElementById('apexAccountNo').innerText;
        let recipientAccountNo = sellerDetails.seller_account_number;
        let bankCode = sellerDetails.seller_bank_code;
        let baseRedirectUrl = 'https://wibs.tech/App/pages/order_status.php';
        let redirectUrl ='https://wibs.tech/App/pages/order_status.php?';

        let selectedBank = event.submitter.value; // This line is changed

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
                    //updateTransactionStatus();
                    window.location.href = data.redirect_url;
                  } else {
                    console.error('Transfer Failed:', data.message);
                    window.location.href = data.redirect_url;
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

        async function handleResponse(data) {
            if (data.success) {
                setTimeout(function () {
                    //updateTransactionStatus();
                    window.location.href = data.redirect_url;
                }, 2000);
            } else {
                console.error('Error recording transaction:', data.message);
            }
        }

    });

    async function fetchAPI(url, requestBody) {
        let response = await fetch(url, {
            method: 'POST',
            body: requestBody
        });

        let statusCode = response.status;
        let data = await response.json();

        if (statusCode === 302) {
            //updateTransactionStatus();
            window.location.href = data.location;
            return;
        }

        data.statusCode = statusCode;
        return data;
    }

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
});