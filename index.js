// Navigate between different sections
function navigateTo(section) {
    document.querySelectorAll(".form-section").forEach(function (el) {
        el.style.display = "none";
    });
    document.getElementById(section).style.display = "block";
}

// Generate License Key and send to email
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
        console.log("Response Text (generateLicense):", this.responseText); // Log the full response text

        if (this.status === 200) {
            try {
                const contentType = this.getResponseHeader("content-type") || "";
                if (contentType.includes("application/json")) {
                    const response = JSON.parse(this.responseText);
                    if (response.success) {
                        renewalMessage.textContent = 'License key generated and sent to your email!';
                    } else {
                        renewalMessage.textContent = response.message || 'Failed to generate license key.';
                    }
                } else {
                    renewalMessage.textContent = 'Unexpected response format received from the server.';
                    console.error('Expected JSON but received:', this.responseText);
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

// Activate License Key
function activateLicense() {
    const key = document.getElementById("licenseKey").value;
    const message = document.getElementById("activationMessage");

    if (!key) {
        message.textContent = "Activation failed. Please enter a valid key.";
        message.style.color = "red";
        return;
    }

    fetch('license.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `license_key=${encodeURIComponent(key)}`,
    })
    .then(response => {
        console.log("Response Status (activateLicense):", response.status);
        console.log("Response Text (activateLicense):", response);
        
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        
        const contentType = response.headers.get("content-type") || "";
        if (!contentType.includes("application/json")) {
            throw new Error('Received unexpected content type');
        }
        
        return response.json();
    })
    .then(data => {
        if (data.success) {
            message.textContent = `License activated successfully! Expiry Date: ${data.expiry_date}, Status: ${data.status}`;
            message.style.color = "green";

            // Update license overview details
            document.querySelector('#licenseExpiry').innerText = data.expiry_date;
            document.querySelector('#licenseKeyDisplay').innerText = data.license_key;
            document.querySelector('#licenseStatus').innerText = data.status;
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

function viewLicenseHistory() {
    const emailAddress = document.getElementById('emailAddress').value;
    const historyTable = document.querySelector('.license-history-table tbody');

    if (!emailAddress) {
        alert('Please enter your email address to view the license history.');
        return;
    }

    fetch('license.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
            'view_license_history': true,
            'email_address': emailAddress
        }),
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Clear the table first
            historyTable.innerHTML = '';

            // Populate the table with license history
            data.licenses.forEach(license => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${license.created_at}</td>
                    <td>${license.license_key}</td>
                    <td>${license.expiry_date}</td>
                    <td>${license.status}</td>
                `;
                historyTable.appendChild(row);
            });
        } else {
            historyTable.innerHTML = `<tr><td colspan="4">${data.message}</td></tr>`;
        }
    })
    .catch(error => console.error('Error fetching license history:', error));
}


// Add event listener for form submission to generate license key
document.getElementById('renewLicenseForm').addEventListener('submit', function(event) {
    event.preventDefault();
    generateLicense();
});

// Add event listener to the activate button for activating the license
document.getElementById('activateButton').addEventListener('click', activateLicense);
