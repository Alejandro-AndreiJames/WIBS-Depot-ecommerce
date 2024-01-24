
document.addEventListener("DOMContentLoaded", function () {
    var editForm = document.getElementById("editForm");
    var firstnameInput = document.getElementById("firstname");
    var lastnameInput = document.getElementById("lastname");
    var emailInput = document.getElementById("email");
    var mobileNumInput = document.getElementById("mobile_num");
    var vrznNumInput = document.getElementById("vrzn_num");
    var apexNumInput = document.getElementById("apex_num"); 

    editForm.addEventListener("submit", function (event) {
        var valid = true;

        // First Name and Last Name validation (at least one alphabet character and optional spaces)
        var namePattern = /^[A-Za-z ]+$/;
        if (!namePattern.test(firstnameInput.value)) {
            valid = false;
            firstnameInput.style.borderColor = "red";
        } else {
            firstnameInput.style.borderColor = "";
        }

        if (!namePattern.test(lastnameInput.value)) {
            valid = false;
            lastnameInput.style.borderColor = "red";
        } else {
            lastnameInput.style.borderColor = "";
        }

        // Email validation
        var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailPattern.test(emailInput.value)) {
            valid = false;
            emailInput.style.borderColor = "red";
        } else {
            emailInput.style.borderColor = "";
        }

        // Mobile Number validation (11 digits)
        var mobileNumPattern = /^\d{10,11}$/;
        if (!mobileNumPattern.test(mobileNumInput.value)) {
            valid = false;
            mobileNumInput.style.borderColor = "red";
        } else {
            mobileNumInput.style.borderColor = "";
        }

        // VRZN and APEX Number validation (numeric)
        var numericPattern = /^[0-9]+$/;
        if (!numericPattern.test(vrznNumInput.value)) {
            valid = false;
            vrznNumInput.style.borderColor = "red";
        } else {
            vrznNumInput.style.borderColor = "";
        }

        if (!numericPattern.test(apexNumInput.value)) {
            valid = false;
            apexNumInput.style.borderColor = "red";
        } else {
            apexNumInput.style.borderColor = "";
        }

        if (!valid) {
            event.preventDefault(); // Prevent form submission if validation fails
        }
    });
});

document.addEventListener("DOMContentLoaded", function() {
    const profileMessage = document.querySelector(".profile-message");

    if (profileMessage) {
        setTimeout(function() {
            profileMessage.style.display = "none";
        }, 1500); // 1200 milliseconds = 1.2 seconds
    }
});

function cancelEdit() {
    window.location.href = "profile.php";
}

function updateCartIconWithRedDot() {
    const cartLink = document.querySelector('.nav-links a[href="cart.php"]');
    if (!cartLink.querySelector('.red-dot')) {
        const redDot = document.createElement('span');
        redDot.className = 'red-dot';
        cartLink.appendChild(redDot);
    }
}

// Function to toggle the dropdown
function toggleDropdown() {
    document.getElementById("myDropdown").classList.toggle("show");
}

// Close the dropdown menu if the user clicks outside of it
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
