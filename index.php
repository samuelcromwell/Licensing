<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Licensing Module</title>
    <link rel="stylesheet" href="index.css" />
  </head>
  <body>
    <header>
      <h1>Licensing Module</h1>
    </header>

    <div class="container">
      <div id="overview" class="license-summary">
        <h2>License Overview</h2>
        <p><strong>Status:</strong> <span id="licenseStatus">Inactive</span></p>
        <p><strong>Expiration Date:</strong> <span id="licenseExpiry">Not set</span></p>
        <p><strong>License Key:</strong> <span id="licenseKeyDisplay">Not entered</span></p>
        <p><strong>Licensed Users:</strong> <span id="licensedUsersCount">0</span></p>
        <button class="button" onclick="navigateTo('renew')">Renew License</button>
        <button class="button" onclick="navigateTo('activate')">Enter License Key</button>
        <button class="button" onclick="navigateTo('history')">View License History</button>
      </div>

      <div id="activate" class="form-section" style="display: none">
        <h2>Enter License Key</h2>
        <input type="text" id="licenseKey" placeholder="Enter License Key" />
        <button class="button" onclick="activateLicense()">Activate</button>
        <p id="activationMessage"></p>
      </div>

      <div id="renew" class="form-section" style="display: none">
        <h2>Renew License</h2>
        <input type="number" id="phoneNumber" name="phone_number" placeholder="Enter Phone Number" required />
        <input type="email" id="emailAddress" name="email_address" placeholder="Enter Email Address" required />
        <br>
        <button class="button" onclick="generateLicense()">Generate License Key</button>
        <p id="renewalMessage"></p>
      </div>

      <div id="history" class="form-section" style="display: none">
        <h2>License History</h2>
        <table>
          <tr>
            <th>Date of Activation</th>
            <th>License Key</th>
            <th>Expiration Date</th>
            <th>Status</th>
          </tr>
          <!-- Dynamic content would be inserted here -->
        </table>
      </div>
    </div>

    <script src="index.js"></script>
  </body>
</html>