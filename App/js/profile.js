function toggleEditForm() {
    var form = document.getElementById('editForm');
    var viewDiv = document.getElementById('viewDiv');
    if (form.style.display === 'none') {
        form.style.display = 'block';
        viewDiv.style.display = 'none';
    } else {
        form.style.display = 'none';
        viewDiv.style.display = 'block';
    }
}