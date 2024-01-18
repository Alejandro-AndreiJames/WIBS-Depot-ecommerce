document.addEventListener('DOMContentLoaded', function() {
    function checkSuccessParameter() {
        const urlParams = new URLSearchParams(window.location.search);
        const success = urlParams.get('fund_transfer_success');
        return success === 'true';
    }

    // Call updateTransactionStatus if 'success=true' is present in the URL
    if (checkSuccessParameter()) {
        updateTransactionStatus()
            .then(() => console.log('Transaction status updated successfully'))
            .catch(() => console.error('Failed to update transaction status'));
    }

    async function updateTransactionStatus() {
        return new Promise((resolve, reject) => {
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
                    resolve(); // Resolve the promise on success
                } else {
                    console.error('Error updating transaction status:', data.message);
                    reject(); // Reject the promise on failure
                }
            })
            .catch(error => {
                console.error('Error:', error);
                reject(); // Reject the promise on fetch error
            });
        });
    }
    

    fetch('https://thefusionseller.online/api_endpoints/get_seller_account_details.php?seller_id=1')
    .then(response => response.json())
    .then(data => {
        if (Array.isArray(data) && data.length > 0) {
            sellerDetails = data[0];
            document.getElementById('bankCode').textContent = sellerDetails.seller_bank_code;
            document.getElementById('recipientNumber').textContent = sellerDetails.seller_account_number;
        } else {
            console.error('Response data is not an array or is empty');
        }
    })
    .catch(error => console.error('Error fetching seller details:', error));

    const vrznButton = document.getElementById('vrznButton');
    const apexButton = document.getElementById('apexButton');

    vrznButton.addEventListener('click', async function(event) {
        event.preventDefault();
        await processTransaction('vrzn');
    });

    apexButton.addEventListener('click', async function(event) {
        event.preventDefault();
        await processTransaction('apex');
    });

    async function processTransaction(bank) {
        let transactionAmount = document.getElementById('transactionAmount').innerText;
        let vrznAccountNo = document.getElementById('vrznAccountNo').innerText;
        let apexAccountNo = document.getElementById('apexAccountNo').innerText;
        //let recipientAccountNo = sellerDetails.seller_account_number;
        let recipientAccountNo = 189960486386;
        //let bankCode = sellerDetails.seller_bank_code;
        let bankCode = "APEX";
        let redirectUrl = 'https://wibs.tech/App/pages/order_status.php';
        
        if (bank === 'vrzn') {
            let sourceAccountNo = vrznAccountNo;
            fetch('https://projectvrzn.online/vrzn-bank/app/database/transfer.php', {
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
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    checkSuccessParameter();
                    setTimeout(function () {
                        window.location.href = data.redirect_url;
                    }, 2000);
                } else {
                    console.error('Error recording transaction:', data.message);
                }
            })
            .catch((error) => {
                console.error('Fetch error:', error);
            });
        } else if (bank === 'apex') {
            let sourceAccountNo = apexAccountNo;
            //let url = "https://apexapp.tech/app/client/backend/api/fund-transfer-external.php";
            let url = "https://apexapp.tech/app/client/backend/api/fund-transfer.php";

            let requestBody = new FormData();
            requestBody.append('redirect_url', redirectUrl);
            requestBody.append('transaction_amount', transactionAmount);
            requestBody.append('source_account_no', sourceAccountNo);
            requestBody.append('recipient_account_no', recipientAccountNo);
            requestBody.append('recipient_bank_code', bankCode);

            let APIResponse = await fetchAPI(url, requestBody);
            handleResponse(APIResponse);
        }
    }

    async function handleResponse(data) {
        if (data.success) {
            checkSuccessParameter();
            setTimeout(() => {
                window.location.href = data.redirect_url;
            }, 2000);
        } else {
            console.error('Error recording transaction:', data.message);
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
            checkSuccessParameter();
            window.location.href = data.location;
            return;
        }

        data.statusCode = statusCode;
        return data;
    }
});
