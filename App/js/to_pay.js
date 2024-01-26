function openPaymentModal(poId, grandTotal) {
        document.getElementById('modalGrandTotal').textContent = grandTotal;
        var poIdElement = document.getElementById('poIdElement');
        poIdElement.dataset.poId = poId;
        console.log(poIdElement.dataset.poId);

        fetch('https://thefusionseller.online/api_endpoints/get_seller_account_details.php?seller_id=1')
        .then(response => response.json())
        .then(data => {
            if (Array.isArray(data) && data.length > 0) {
                const sellerDetails = data[0];
                document.getElementById('modalBankCode').textContent = sellerDetails.seller_bank_code;
                document.getElementById('modalRecipientNumber').textContent = sellerDetails.seller_account_number;
            } else {
                console.error('Response data is not an array or is empty');
            }
        })
        .catch(error => {
            console.error('Error fetching seller details:', error);
        });

        var modal = document.getElementById('paymentModal');
        modal.style.display = "block";

        // For closing the modal when clicking outside of it
        window.onclick = function(event) {
            var modal = document.getElementById('paymentModal');
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    }

    function closePaymentModal() {
        var modal = document.getElementById('paymentModal');
        if (modal) {
            modal.style.display = "none";
        }
    }

    function deleteOrder(poId, event) {
        event.stopPropagation(); // Prevent event from bubbling up to parent elements
    
        if (!confirm("Are you sure you want to delete this order?")) {
            return;
        }
        fetch('delete_order.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'po_id=' + poId
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Order deleted successfully.');
                location.reload(); // Reload the page to update the list
            } else {
                alert('Failed to delete order: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }
    

    async function payment(selectedBank) {
        var poId = poIdElement.dataset.poId;
        var transactionAmount = document.getElementById('modalGrandTotal').textContent;
        // Assume these elements contain the necessary account numbers
        var vrznAccountNo = document.getElementById('vrznAccountNo').innerText;
        var apexAccountNo = document.getElementById('apexAccountNo').innerText;
        var recipientAccountNo = document.getElementById('modalRecipientNumber').textContent;
        var bankCode = document.getElementById('modalBankCode').textContent;
        let baseRedirectUrl = `https://www.wibs.tech/App/pages/order_status.php?po_id=${poId}`;
        let redirectUrl =`https://www.wibs.tech/App/pages/order_status.php?po_id=${poId}`;
    
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