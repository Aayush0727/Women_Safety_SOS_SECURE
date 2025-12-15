emailjs.init("-SXYaP4eQL6dR7zLP"); // Replace with your actual EmailJS User ID

document.getElementById("guardianForm").addEventListener("submit", function(event) {
    event.preventDefault();

    let name = document.getElementById("guardianName").value;
    let email = document.getElementById("guardianEmail").value;
    let contact = document.getElementById("guardianContact").value;

    if (name && email && contact) {
        let listItem = document.createElement("li");
        listItem.innerHTML = `<strong>${name}</strong> - ${email} (${contact}) <button onclick="removeGuardian(this)">Remove</button>`;
        document.getElementById("guardiansList").appendChild(listItem);

        document.getElementById("guardianName").value = "";
        document.getElementById("guardianEmail").value = "";
        document.getElementById("guardianContact").value = "";
    } else {
        alert("Please fill in all fields.");
    }
});

function removeGuardian(button) {
    button.parentElement.remove();
}

function getLocation(callback) {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            (position) => {
                let latitude = position.coords.latitude;
                let longitude = position.coords.longitude;
                let googleMapsLink = `https://www.google.com/maps?q=${latitude},${longitude}`;

                document.getElementById("liveLocation").innerHTML = `<a href="${googleMapsLink}" target="_blank">Click to View</a>`;

                if (callback) {
                    callback(googleMapsLink);
                }
            },
            () => {
                alert("Location access denied.");
            }
        );
    } else {
        alert("Geolocation not supported.");
    }
}

function sendSOS() {
    alert("SOS Sent!");
}

document.getElementById("sosBtn").addEventListener("click", sendSOS);

function logout() {
    alert("Logging out...");
    window.location.href = "login.html";
}
