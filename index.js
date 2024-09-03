function navigateTo(section) {
    document.querySelectorAll(".form-section").forEach(function (el) {
        el.style.display = "none";
    });
    document.getElementById(section).style.display = "block";
}

function generateLicense() {
    const phoneNumber = document.getElementById('phoneNumber').value;
    const emailAddress = document.getElementById('emailAddress').value;
    const renewalMessage = document.getElementById('renewalMessage');

    if (!phoneNumber || !emailAddress) {
        renewalMessage.textContent = 'Please fill in all fields.';
        return;
    }

    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'license.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xhr.onload = function() {
        console.log("Response Text:", this.responseText); // Debugging: Log the full response text

        if (this.status === 200) {
            try {
                const response = JSON.parse(this.responseText);
                if (response.success) {
                    renewalMessage.textContent = 'License key generated and sent to your email!';
                } else {
                    renewalMessage.textContent = response.message || 'Failed to generate license key.';
                }
            } catch (e) {
                console.error('Error parsing JSON:', e);
                renewalMessage.textContent = 'An error occurred while processing your request.';
            }
        } else {
            renewalMessage.textContent = 'An error occurred while processing your request.';
        }
    };

    const params = `generate_license=1&phone_number=${encodeURIComponent(phoneNumber)}&email_address=${encodeURIComponent(emailAddress)}`;
    xhr.send(params);
}

function activateLicense() {
    const key = document.getElementById("licenseKey").value;
    const message = document.getElementById("activationMessage");

    // Make sure the license key is not empty
    if (!key) {
        message.textContent = "Activation failed. Please enter a valid key.";
        message.style.color = "red";
        return;
    }

    // Send the license key to the PHP script
    fetch('license.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `license_key=${encodeURIComponent(key)}`,
    })
    .then(response => {
        console.log("Response Text:", response); // Debugging: Log the full response
        return response.json();
    })
    .then(data => {
        if (data.success) {
            message.textContent = `License activated successfully! Expiry Date: ${data.expiry_date}, Status: ${data.status}`;
            message.style.color = "green";

            // Optionally, update the overview section with the new details
            document.querySelector('.license-summary p:nth-child(2)').innerText = `Expiration Date: ${data.expiry_date}`;
            document.querySelector('.license-summary p:nth-child(3)').innerText = `License Key: ${data.license_key}`;
            document.querySelector('.license-summary p:nth-child(4)').innerText = `Status: ${data.status}`;
        } else {
            message.textContent = `Activation failed: ${data.message}`;
            message.style.color = "red";
        }
    })
    .catch(error => {
        console.error('Error:', error);
        message.textContent = 'An error occurred while activating the license.';
        message.style.color = "red";
    });
}

// Add event listeners to buttons or form submissions as needed
document.getElementById('activateButton').addEventListener('click', activateLicense);
