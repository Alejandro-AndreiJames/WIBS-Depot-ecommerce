document.addEventListener('DOMContentLoaded', function() {
    
    async function updatePOStatus() {
        const poId = document.getElementById('poIdElement').getAttribute('data-po-id');
        let url = '../pages/update_status.php'; // URL to your PHP script that updates the PO status
        let response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: 'po_id=' + encodeURIComponent(poId)
        });
        let data = await response.json();
        if (!data.success) {
            console.error('Failed to update PO status:', data.message);
        }else {
            console.error('Error updating transaction status:', data.message);
        }
    }
    
    console.log("PO ID:", poId);
    console.log("Sending request to:", url);
    // and within the response handling
    console.log("Response data:", data);

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
        let recipientAccountNo = sellerDetails.seller_account_number;
        let bankCode = sellerDetails.seller_bank_code;
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
                    // Update po_id status in the database
                    updatePOStatus().then(() => {
                        setTimeout(function () {
                            window.location.href = data.redirect_url;
                        }, 2000);
                    });
                } else {
                    console.error('Error recording transaction:', data.message);
                }
            })
            .catch((error) => {
                console.error('Fetch error:', error);
            });
        } else if (bank === 'apex') {
            let sourceAccountNo = apexAccountNo;
            let url = "https://apexapp.tech/app/client/backend/api/fund-transfer-external.php";

            let requestBody = new FormData();
            requestBody.append('redirect_url', redirectUrl);
            requestBody.append('transaction_amount', transactionAmount);
            requestBody.append('source_account_no', sourceAccountNo);
            requestBody.append('recipient_account_no', recipientAccountNo);
            requestBody.append('recipient_bank_code', bankCode);

            let APIResponse = await fetchAPI(url, requestBody);
            handleResponse(APIResponse).then(() =>{
                // Update po_id status in the database
                updatePOStatus();
            });
        }
    }

    async function handleResponse(data) {
        if (data.success) {
            updatePOStatus().then(() => {
                setTimeout(function () {
                    window.location.href = data.redirect_url;
                }, 2000);
            });
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
            window.location.href = data.location;
            return;
        }

        data.statusCode = statusCode;
        return data;
    }
});
