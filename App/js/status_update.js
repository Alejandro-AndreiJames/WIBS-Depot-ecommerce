document.addEventListener("DOMContentLoaded", function() {
    if (typeof poId !== 'undefined') {
        // Function to submit data using fetch
        function submitUpdateStatusForm() {
            const formData = new FormData();
            formData.append('po_id', poId);
            console.log("Sending PO ID:", poId); 

            fetch('update_status.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                console.log(data);
                window.location.reload();
            })
            .catch(error => console.error('Error:', error));
        }

        // Call the function to submit the form
        submitUpdateStatusForm();
    }
});