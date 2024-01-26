let cartItemCount = 0;
document.addEventListener('DOMContentLoaded', function() {
    const itemsPerPage = 15;
    let currentPage = 1;

    async function fetchItems(offset, limit) {
        return fetch(`https://thefusionseller.online/api_endpoints/get_item_list.php?offset=${offset}&limit=${limit}`)
            .then(response => response.json());
    }

    function createItemDiv(item) {
        const itemDiv = document.createElement('div');
        itemDiv.className = 'item';
        itemDiv.innerHTML = `
            <h2>${item.item_name}</h2>
            <p>Price: ₱${item.item_price}</p>
            <img src="${item.item_image}" alt="${item.item_name}">
            <div class="item-id" style="display: none;">${item.item_id}</div>
        `;
        itemDiv.addEventListener('click', function() {
            displayItemDetails(item);
        });
        return itemDiv;
    }

    function displayInitialItems() {
        fetchItems(15, 20).then(data => {
            const contentWrapper1 = document.querySelector('.content-wrapper-1');
            const contentWrapper2 = document.querySelector('.content-wrapper-2');

            for (let i = 15; i < 17 && i < data.length; i++) {
                contentWrapper1.appendChild(createItemDiv(data[i]));
            }

            for (let i = 17; i < 20 && i < data.length; i++) {
                contentWrapper2.appendChild(createItemDiv(data[i]));
            }
        })
        .catch(error => {
            console.error('Error fetching data: ', error);
        });
    }

    function displayAllDeals() {
        fetchItems(0, itemsPerPage).then(data => {
            const contentWrapper = document.querySelector('.content-wrapper-3');
            contentWrapper.innerHTML = ''; 
            data.forEach(item => {
                contentWrapper.appendChild(createItemDiv(item));
            });
            updatePagination(fetchItems);
        })
        .catch(error => {
            console.error('Error fetching data: ', error);
        });
    }

    function updatePagination(fetchItems) {
    const pagination = document.querySelector('.pagination');
    pagination.innerHTML = '';

    const prevButton = document.createElement('button');
    prevButton.innerText = 'Previous';
    prevButton.disabled = currentPage === 1;
    prevButton.addEventListener('click', (e) => {
        e.preventDefault();
        if (currentPage > 1) {
            currentPage--;
            fetchAndDisplayItems();
        }
    });
    pagination.appendChild(prevButton);

    const nextButton = document.createElement('button');
    nextButton.innerText = 'Next';
    nextButton.disabled = fetchItems < itemsPerPage;
    nextButton.addEventListener('click', (e) => {
        e.preventDefault();
        if (!nextButton.disabled) {
            currentPage++;
            fetchAndDisplayItems();
        }
    });
    pagination.appendChild(nextButton);
}

function fetchAndDisplayItems() {
    let offset = (currentPage - 1) * itemsPerPage;

    fetchItems(offset, itemsPerPage)
        .then(data => {
            const container = document.querySelector('.content-wrapper-3');
            container.innerHTML = '';
            data.forEach(item => {
                container.appendChild(createItemDiv(item));
            });
            updatePagination(data.length);
        })
        .then(scrollToAllDealsSection)
        .catch(error => console.error('Error:', error));
}

    function scrollToAllDealsSection() {
        const allDealsSection = document.querySelector('#all_deals_section');
        allDealsSection.scrollIntoView();
    }

    displayInitialItems();
    displayAllDeals();

    function displayItemDetails(item) {
        document.querySelector('#popup_item_name').innerText = item.item_name;
        document.querySelector('#popup_item_price').innerText = `Price: ₱${item.item_price}`;
        document.querySelector('#popup_item_image').src = item.item_image;
        document.querySelector('#popup_item_description').innerText = item.item_description;
        document.querySelector('.item-id').innerText = item.item_id;
        openPopup();
    }

    function addToCart(item) {
        const quantity = document.querySelector('#quantity').value;

        const itemName = item.item_name;
        const itemPrice = item.item_price;
        const itemImage = item.item_image;
        const itemId = item.item_id;

        if (itemName !== undefined && itemPrice !== undefined && itemImage !== undefined) {
            const formData = new FormData();
            formData.append('item_name', itemName);
            formData.append('quantity', quantity);
            formData.append('item_price', itemPrice);
            formData.append('item_image', itemImage);
            formData.append('item_id', itemId);

            console.log('FormData:', formData);

            fetch('cart.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                console.log(data);
                showAlert(`Successfully added ${quantity} x ${itemName} to your cart.`);  
                updateCartIconWithRedDot(); 
                closePopup();
            })
            .catch(error => console.error('Error:', error));
        }
        function updateCartIconWithRedDot() {
            const cartLink = document.querySelector('.nav-links a[href="cart.php"]');
            if (!cartLink.querySelector('.red-dot')) {
                const redDot = document.createElement('span');
                redDot.className = 'red-dot';
                cartLink.appendChild(redDot);
            }
        }

        function showAlert(message) {
            const alertBox = document.getElementById('customAlert');
            const alertMessage = document.getElementById('alertMessage');
            const overlay = document.querySelector('.overlay');

            alertMessage.innerText = message;
            alertBox.classList.add('show');
            overlay.classList.add('visible'); 

            setTimeout(() => {
                alertBox.classList.remove('show');
                overlay.classList.remove('visible');
            }, 1500);
        }
    }
    
    document.querySelector('.close-alert').addEventListener('click', function() {
        document.getElementById('customAlert').classList.remove('show');
        document.querySelector('.overlay').classList.remove('visible');
    });

    document.querySelector('#add_to_cart_btn').addEventListener('click', function() {
        const selectedItemName = document.querySelector('#popup_item_name').innerText;
        const selectedItemPrice = parseFloat(document.querySelector('#popup_item_price').innerText.replace('Price: ₱', ''));
        const selectedItemImage = document.querySelector('#popup_item_image').src;
        const selectedItemID = document.querySelector('.item-id').innerText; 
    
        const selectedItem = {
            item_name: selectedItemName,
            item_price: selectedItemPrice,
            item_image: selectedItemImage,
            item_id: selectedItemID
        };
    
        addToCart(selectedItem);
    });
    
    function openPopup() {
        document.querySelector('.overlay').classList.add('visible');
        document.querySelector('#item_detail_popup').classList.add('open');
    }
    
    function closePopup() {
        document.querySelector('.overlay').classList.remove('visible');
        document.querySelector('#item_detail_popup').classList.remove('open');
    }

    document.querySelector('.close-btn').addEventListener('click', closePopup);
});

function toggleDropdown() {
    document.getElementById("myDropdown").classList.toggle("show");
}

window.onclick = function(event) {
  if (!event.target.matches('.dropbtn')) {
    var dropdowns = document.getElementsByClassName("dropdown-content");
    for (var i = 0; i < dropdowns.length; i++) {
      var openDropdown = dropdowns[i];
      if (openDropdown.classList.contains('show')) {
        openDropdown.classList.remove('show');
      }
    }
  }
}